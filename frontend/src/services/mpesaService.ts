import { apiClient } from "./myapi";

export const initiateMpesaDonation = (phone: string, amount: number) => {
  return apiClient.post('/donate/stkpush', {
    phone: phone,
    amount: amount
  });
};
export const checkMpesaStatus = (checkoutRequestId: string) => {
  return apiClient.get(`/donate/status/${checkoutRequestId}`);
};