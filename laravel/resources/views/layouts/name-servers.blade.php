@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Manage Name Servers</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('name-servers.store') }}" method="POST" id="name-server-form">
                @csrf
                
                <div class="table-responsive">
                    <table class="table table-bordered" id="name-server-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Host Name</th>
                                <th>IP Address</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($nameServers as $index => $server)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <input type="text" name="name_servers[{{ $server->id }}][name]" class="form-control" value="{{ $server->name }}" readonly>
                                    </td>
                                    <td>
                                        <input type="text" name="name_servers[{{ $server->id }}][ip_address]" class="form-control" value="{{ $server->ip_address }}">
                                    </td>
                                    <td>
                                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                        <a href="{{ route('name-servers.destroy', $server->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
            <button class="btn btn-info mt-3" id="add-more-btn">Add More</button>
        </div>
    </div>
</div>

<script>
    document.getElementById('add-more-btn').addEventListener('click', function() {
        const table = document.getElementById('name-server-table').getElementsByTagName('tbody')[0];
        const rowCount = table.rows.length + 1; // Get next row number

        // Create new row
        const row = table.insertRow();
        row.innerHTML = `
            <td>${rowCount}</td>
            <td>
                <input type="text" name="name_servers[new][${rowCount}][name]" class="form-control" placeholder="Eg. ns1.example.com">
            </td>
            <td>
                <input type="text" name="name_servers[new][${rowCount}][ip_address]" class="form-control" placeholder="Eg. 192.168.1.1">
            </td>
            <td>
                <button type="submit" class="btn btn-success btn-sm">Save</button>
            </td>
        `;
    });
</script>

@endsection
