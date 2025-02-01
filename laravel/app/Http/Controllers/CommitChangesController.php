<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class CommitChangesController extends Controller
{
    protected $dns_url;

    public function __construct()
    {
        // Initialize the DNS URL from environment configuration
        $this->dns_url = env('DNS_APP_URL');
    }
    public function index()
    {
        // Call the external API
        $response = Http::get($this->dns_url .'/reload-coredns');

        // Check if the API call was successful
        if ($response->successful()) {
            $message = "The changes have been committed successfully.";
        } else {
            $message = "Failed to commit changes. Please try again.";
        }

        // Return the view with the message
        return view('layouts.commit-changes', compact('message'));
    }
    public function reload()
    {
        // Call the external API
        $response = Http::get ($this->dns_url .'/reload-coredns');


        // Return the response directly (as JSON, for example)
        return response()->json($response->json());
    }
}
