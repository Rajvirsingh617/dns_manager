@extends('layouts.app')
@section('content')
<div class="container">
    <h4 class="mb-4">Manage NameServers</h4>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('name-servers.store') }}" method="POST" id="name-server-form">
                @csrf

                <div class="table-responsive">
                    <table class="table table-bordered" id="name-server-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>NameServer</th>
                                <th>Host Name</th>
                                <th>IP Address</th>
                                <th>TTL</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($nameServers as $index => $server)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <input type="text" name="name_servers[{{ $server->id }}][nameserver_name]" class="form-control" value="{{ $server->nameserver_name }}" readonly>
                                    </td>
                                    <td>
                                        <input type="text" name="name_servers[{{ $server->id }}][host]" class="form-control" value="{{ $server->host }}">
                                    </td>
                                    <td>
                                        <input type="text" name="name_servers[{{ $server->id }}][ip_address]" class="form-control" value="{{ $server->ip_address }}">
                                    </td>
                                    <td>
                                        <input type="text" name="name_servers[{{ $server->id }}][ttl]" class="form-control" value="{{ $server->ttl }}">
                                    </td>
                                    <td>
                                        <button type="submit" class="btn btn-sm btn-primary" name="save_id" value="{{ $server->id }}">Save</button>
                                        {{-- <form action="{{ route('name-servers.destroy', $server->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form> --}}
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td>New</td>
                                <td><input type="text" name="new[nameserver_name]" class="form-control" placeholder="Eg. ns1.example.com"></td>
                                <td><input type="text" name="new[host]" class="form-control" placeholder="Eg. example.com"></td>
                                <td><input type="text" name="new[ip_address]" class="form-control" placeholder="Eg. 192.168.1.1"></td>
                                <td><input type="text" name="new[ttl]" class="form-control" placeholder="Eg. 86000"></td>
                                <td><button type="submit" class="btn btn-sm btn-success" name="add">Add</button></td>
                            </tr>
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
        const rowCount = table.rows.length + 1;

        const row = table.insertRow();
        row.innerHTML = `
            <td>${rowCount}</td>
            <td><input type="text" name="new[${rowCount}][nameserver_name]" class="form-control" placeholder="Eg. ns1.example.com"></td>
            <td><input type="text" name="new[${rowCount}][host]" class="form-control" placeholder="Eg. example.com"></td>
            <td><input type="text" name="new[${rowCount}][ip_address]" class="form-control" placeholder="Eg. 192.168.1.1"></td>
            <td><input type="text" name="new[${rowCount}][ttl]" class="form-control" placeholder="Eg. 86000"></td>
            <td>
                  <button type="button" class="btn btn-success btn-sm save-btn">Save</button>
                <button type="button" class="btn btn-danger btn-sm delete-btn">Delete</button>

            </td>
        `;
    });
</script>

<script>

@endsection
