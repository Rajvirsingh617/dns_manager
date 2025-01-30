<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NameServer;
use Illuminate\Support\Facades\Log;


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
        // Define validation rules
        $rules = [
            'new.nameserver_name' => 'required|unique:nameservers,nameserver_name',
            'new.host' => 'required',
            'new.ip_address' => 'required|ip',
            'new.ttl' => 'required|integer|min:60',
        ];

        $messages = [
            'new.nameserver_name.required' => 'The nameserver name is required.',
            'new.nameserver_name.unique' => 'This nameserver name already exists.',
            'new.host.required' => 'The host name is required.',
            'new.ip_address.required' => 'The IP address is required.',
            'new.ip_address.ip' => 'Please enter a valid IP address.',
            'new.ttl.required' => 'The TTL value is required.',
            'new.ttl.integer' => 'TTL must be a valid integer.',
            'new.ttl.min' => 'TTL must be at least 60 seconds.',
        ];

        $request->validate($rules, $messages);

        if ($request->has('save_id')) {
            // Update existing record
            $server = NameServer::findOrFail($request->save_id);
            $server->update($request->name_servers[$server->id]);
        

        } elseif ($request->has('add')) {
            // Create new record
            $server = NameServer::create($request->new);

            $domain = $server->host;
            $filePath = storage_path("app/coredns/{$domain}.zone");

            // Fetch all name servers for the given domain
            $nameServers = NameServer::where('host', $domain)->get();

            // Determine the primary nameserver for SOA
            $primaryNS = $nameServers->first()->nameserver_name ?? $server->nameserver_name;
            $adminEmail = "admin.{$domain}.";

            // Constructing the zone file
            $zoneContent = "";
            $zoneContent .= "\$TTL 3600\n";
            $zoneContent .= "@   IN  SOA {$primaryNS}. {$adminEmail} (\n";
            $zoneContent .= "            2024012801 ; Serial\n";
            $zoneContent .= "            3600       ; Refresh\n";
            $zoneContent .= "            1800       ; Retry\n";
            $zoneContent .= "            1209600    ; Expire\n";
            $zoneContent .= "            3600 )     ; Minimum TTL\n\n";

            // Append NS records first
            foreach ($nameServers as $ns) {
                $zoneContent .= "     IN  NS  {$ns->nameserver_name}.\n";
            }
            $zoneContent .= "\n";

            // Append A records for each nameserver in the desired order
            foreach ($nameServers as $ns) {
                // Get the hostname from the nameserver name
                $hostname = explode('.', $ns->nameserver_name)[0];
                $zoneContent .= "{$hostname}  IN  A   {$ns->ip_address}\n";
            }

            // Ensure directory exists
            if (!file_exists(storage_path('app/coredns'))) {
                mkdir(storage_path('app/coredns'), 0777, true);
            }

            // Save the updated zone file
            file_put_contents($filePath, $zoneContent);
        }

        return redirect()->route('name-servers.index')->with('success', 'Changes saved.');
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
        'nameserver_name' => $request->nameserver_name,
        'ip_address' => $request->ip_address,
        'host' => $request->host,
        'ttl' => $request->ttl,
    ]);


    return response()->json(['success' => true, 'message' => 'Name Server updated successfully']);
}

public function destroy($id)
{
    // Find the record
    $server = NameServer::findOrFail($id);
    $domain = $server->host;

    // Delete the record from the database
    $server->delete();

    // Construct the file path
    $filePath = storage_path("app/coredns/{$domain}.zone");

    // Delete the file if it exists
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    return redirect()->route('name-servers.index')->with('success', 'Record and associated file deleted.');
}






}
