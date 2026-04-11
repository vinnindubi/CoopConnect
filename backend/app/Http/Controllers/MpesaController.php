<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpesaController extends Controller
{
    private $env;
    private $shortcode;
    private $passkey;

    public function __construct()
    {
        $this->env = env('MPESA_ENV', 'sandbox');
        $this->shortcode = env('MPESA_SHORTCODE');
        $this->passkey = env('MPESA_PASSKEY');
    }

    /**
     * 1. Generate M-Pesa Access Token
     */
    private function getAccessToken()
    {
        $url = $this->env === 'live' 
            ? 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials' 
            : 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

        $credentials = base64_encode(env('MPESA_CONSUMER_KEY') . ':' . env('MPESA_CONSUMER_SECRET'));

        // Add ->withoutVerifying() right here!
        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Basic ' . $credentials
        ])->get($url);

        return $response->json()['access_token'];
    }

    /**
     * 2. Initiate STK Push (Trigger the PIN prompt on the phone)
     */
    public function stkPush(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'amount' => 'required|numeric|min:1'
        ]);

        // Safaricom requires numbers in the format 2547XXXXXXXX
        $phone = $request->phone;
        if (str_starts_with($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        }

        $timestamp = date('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

        $url = $this->env === 'live'
            ? 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest'
            : 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

        $response = Http::withoutVerifying()->withToken($this->getAccessToken())->post($url, [
            'BusinessShortCode' => $this->shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $request->amount,
            'PartyA' => $phone,
            'PartyB' => $this->shortcode,
            'PhoneNumber' => $phone,
            'CallBackURL' => env('MPESA_CALLBACK_URL'),
            'AccountReference' => 'CoopConnect Fund',
            'TransactionDesc' => 'Feeding a Comrade'
        ]);

        $resData = $response->json();

        // If successful, Safaricom returns a CheckoutRequestID. We save this in our DB!
        if (isset($resData['ResponseCode']) && $resData['ResponseCode'] == '0') {
            Donation::create([
                'checkout_request_id' => $resData['CheckoutRequestID'],
                'phone_number' => $phone,
                'amount' => $request->amount,
                'status' => 'pending'
            ]);

            return response()->json([
                'message' => 'STK Push sent successfully. Please enter your PIN.',
                'checkout_request_id' => $resData['CheckoutRequestID']
                ]);
        }

        return response()->json(['message' => 'Failed to initiate STK push.', 'error' => $resData], 500);
    }

    /**
     * 3. Handle the Callback (Safaricom sends the receipt here)
     */
    public function callback(Request $request)
    {
        // Log the response so you can see it in storage/logs/laravel.log during testing!
        Log::info('M-Pesa Callback Data:', $request->all());

        $data = $request->input('Body.stkCallback');

        if (!$data) return response()->json(['message' => 'Invalid data'], 400);

        $checkoutRequestId = $data['CheckoutRequestID'];
        $resultCode = $data['ResultCode'];

        $donation = Donation::where('checkout_request_id', $checkoutRequestId)->first();

        if (!$donation) return response()->json(['message' => 'Donation record not found'], 404);

        if ($resultCode == 0) {
            // Payment was successful! Extract the receipt number.
            $callbackMetadata = $data['CallbackMetadata']['Item'];
            $receiptNumber = '';

            foreach ($callbackMetadata as $item) {
                if ($item['Name'] == 'MpesaReceiptNumber') {
                    $receiptNumber = $item['Value'];
                }
            }

            $donation->update([
                'status' => 'completed',
                'mpesa_receipt_number' => $receiptNumber
            ]);

        } else {
            // Payment failed or was cancelled by the user
            $donation->update(['status' => 'failed']);
        }

        // Safaricom expects a simple JSON response to know we received the callback
        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }
    public function checkTransactionStatus($checkoutRequestId)
    {
        $donation = Donation::where('checkout_request_id', $checkoutRequestId)->first();

        if (!$donation) {
            return response()->json(['status' => 'not_found'], 404);
        }

        return response()->json([
            'status' => $donation->status, // pending, completed, or failed
            'receipt' => $donation->mpesa_receipt_number
        ]);
    }
}