<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NameServer;


class NameServerController extends Controller
{

    public function index()
    {
        $nameServers = NameServer::all();
        return view('layouts.name-servers', compact('nameServers'));
    }


    public function create()
    {
        return view('name-servers.create');
    }


    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',  // Hostname (e.g., ns1.rajvirsingh.com)
        'ip_address' => 'required|ip',        // IP Address (e.g., 192.168.1.1)
    ]);

    // Create the NameServer in the database
    $nameServer = NameServer::create([
        'name' => $request->name,
        'ip_address' => $request->ip_address,
    ]);


    $hostname = $nameServer->name;

    // Split the hostname into parts and take the last two parts as the domain
    $parts = explode('.', $hostname);
    $domain = implode('.', array_slice($parts, -2)); // Take the last two parts, e.g., 'rajvirsingh.com'

    // Generate the content for the .zone file
    $zoneContent = "
    \$TTL 3600
    @   IN  SOA ns1.{$nameServer->name}. admin.{$domain}. (
            2024012801 ; Serial
            3600       ; Refresh
            1800       ; Retry
            1209600    ; Expire
            3600 )     ; Minimum TTL

    IN  NS  ns1.{$nameServer->name}.
    
    ns1 IN  A   {$nameServer->ip_address}
    ";

    // Determine the filename as <domain>.zone (e.g., rajvirsingh.com.zone)
    $filename = storage_path("app/{$domain}.zone");

    // Create and write the .zone file to storage
    file_put_contents($filename, $zoneContent);

    return response()->json([
        'success' => true,
        'id' => $nameServer->id,
        'message' => 'Name Server created successfully and zone file generated.'
    ]);
}





    public function show(string $id)
    {
        /* $nameServer = NameServer::findOrFail($id);
        return view('name-servers.show', compact('nameServer')); */
    }

    public function edit(string $id)
    {
        $nameServer = NameServer::findOrFail($id);
        return view('name-servers.edit', compact('nameServer'));
    }


    public function update(Request $request, $id)
{
    $server = NameServer::findOrFail($id);
    $server->update([
        'name' => $request->name,
        'ip_address' => $request->ip_address,
    ]);

    return response()->json(['success' => true, 'message' => 'Name Server updated successfully']);
}

public function destroy($id)
{
    $server = NameServer::findOrFail($id);
    $server->delete();

    return redirect()->route('name-servers.index')->with('success', 'Name Server deleted successfully');
}


}
