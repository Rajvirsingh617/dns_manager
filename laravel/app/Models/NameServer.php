<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class NameServer extends Model
{
    use HasFactory;

    protected $fillable = ['nameserver_name', 'ip_address', 'host', 'ttl'];

    // Specify the table name
    protected $table = 'nameservers'; // This should match the table name in the migration
}
