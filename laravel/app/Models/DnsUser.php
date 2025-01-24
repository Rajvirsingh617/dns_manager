<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class DnsUser extends Model
{
    use HasFactory;

    protected $table = 'dns_users'; // Specify the table name

    protected $fillable = ['username', 'email', 'password','role', 'api_token'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function findForPassport($token)
    {
        return $this->where('api_token', $token)->first();
    }
    protected static function booted()
    {
        static::creating(function ($dnsUser) {
            $dnsUser->uuid = Str::uuid();
        });
    }
    
}
