<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\MarketplaceProduct;
use Laravel\Passport\Contracts\OAuthenticatable;
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'fullname',
        'email',
        'password',
        'admission',
        'course',
        'year',
        'phone',
        'bio',
        'avatar',
        'cover_photo',
        'verification_status',
        'store_categories',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
  public function articles()
    {
        return $this->hasMany(Article::class);
    }
  public function comments(){
    return $this->hasManyThrough(Comment::class, Article::class);
  }
  public function groups(){
    return $this ->belongsToMany(Group::class,'group_user')
                -> withPivot('role','title')
                -> withTimestamps();
  }
  public function marketplaceProducts()
    {
        
        return $this->hasMany(MarketplaceProduct::class, 'user_id'); 
    }
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
