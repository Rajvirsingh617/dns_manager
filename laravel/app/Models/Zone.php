<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ZoneRecord;

class Zone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'refresh', 'retry', 'expire', 'ttl', 'pri_dns', 'sec_dns', 'www', 'mail', 'ftp', 'owner',
    ];
    
  
    
public function user()
{
    return $this->belongsTo(User::class, 'owner');
}
 public function records()
    {
        return $this->hasMany(ZoneRecord::class, 'zone_id');
    }
}