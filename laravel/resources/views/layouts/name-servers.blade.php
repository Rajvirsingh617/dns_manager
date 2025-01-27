@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Name Servers</h1>

    @if($nameServers->isEmpty())
        <p>No name servers found.</p>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>IP Address</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($nameServers as $server)
                    <tr>
                        <td>{{ $server->id }}</td>
                        <td>{{ $server->name }}</td>
                        <td>{{ $server->ip_address }}</td>
                        <td>{{ $server->created_at }}</td>
                        <td>
                            <a href="{{ route('zones.show', $server->id) }}" class="btn btn-info btn-sm">View</a>
                            <a href="{{ route('zones.editzone', $server->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('zones.destroy', $server->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
