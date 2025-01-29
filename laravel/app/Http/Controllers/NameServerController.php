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
        'name' => 'required|string|max:255',
        'ip_address' => 'required|ip',
    ]);

    $nameServer = NameServer::create([
        'name' => $request->name,
        'ip_address' => $request->ip_address,
    ]);

    return response()->json(['success' => true, 'id' => $nameServer->id, 'message' => 'Name Server created successfully']);
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
