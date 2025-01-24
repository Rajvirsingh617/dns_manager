<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Zone;

class ZoneRecord extends Model
{
    use HasFactory;

    protected $fillable = ['host', 'type', 'destination', 'valid', 'zone_id'];

    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }

    public function records()
{
    return $this->hasMany(Record::class);
}
}
