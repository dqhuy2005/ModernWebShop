<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OauthAccount extends Model
{
    protected $table = 'oauth_accounts';
    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
        'provider_token',
        'provider_refresh_token',
        'avatar',
        'email',
        'name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
