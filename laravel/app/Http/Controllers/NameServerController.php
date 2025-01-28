<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NameServer;


class NameServerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $nameServers = NameServer::all();
        return view('layouts.name-servers', compact('nameServers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('name-servers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    dd($request->all());
    // Validate the input data
    $validatedData = $request->validate([
        'name_servers.*.name' => 'required|string|max:255',
        'name_servers.*.ip_address' => 'required|ip',
    ]);

    // Loop through the submitted name servers and create them
    foreach ($validatedData['name_servers'] as $serverData) {
        NameServer::create([
            'name' => $serverData['name'],
            'ip_address' => $serverData['ip_address'],
        ]);
    }

    return redirect()->route('name-servers.index')->with('success', 'Name Servers created successfully');
}



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $nameServer = NameServer::findOrFail($id);
        return view('name-servers.show', compact('nameServer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $nameServer = NameServer::findOrFail($id);
        return view('name-servers.edit', compact('nameServer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|ip',
        ]);

        $nameServer = NameServer::findOrFail($id);
        $nameServer->update([
            'name' => $request->name,
            'ip_address' => $request->ip_address,
        ]);

        return redirect()->route('name-servers.index')->with('success', 'Name Server updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $nameServer = NameServer::findOrFail($id);
        $nameServer->delete();

        return redirect()->route('name-servers.index')->with('success', 'Name Server deleted successfully');
    }


    /* public function showNameServers()
    {
        // Retrieve all name servers from the database
        $nameServers = Zone::all(); // Adjust the model if needed (e.g., use a dedicated NameServer model)

        return view('layouts.name-servers', compact('nameServers'));
    } */

}
