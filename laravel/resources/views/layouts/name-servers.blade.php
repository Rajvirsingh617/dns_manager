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
                                <tr data-id="{{ $server->id }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <input type="text" name="name_servers[{{ $server->id }}][name]" class="form-control" value="{{ $server->name }}" readonly>
                                    </td>
                                    <td>
                                        <input type="text" name="name_servers[{{ $server->id }}][ip_address]" class="form-control" value="{{ $server->ip_address }}">
                                    </td>
                                    <td>
                                        <!-- Save Button -->
                                        <button type="submit" class="btn btn-primary btn-sm save-btn" data-id="{{ $server->id }}">Save</button>

                                        <!-- Delete Button -->
                                        <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="{{ $server->id }}">Delete</button>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.getElementById('add-more-btn').addEventListener('click', function() {
        const table = document.getElementById('name-server-table').getElementsByTagName('tbody')[0];
        const rowCount = table.rows.length + 1;

        // Create new row
        const row = table.insertRow();
        row.innerHTML = `
            <td>${rowCount}</td>
            <td><input type="text" name="name_servers[new][${rowCount}][name]" class="form-control" placeholder="Eg. ns1.example.com"></td>
            <td><input type="text" name="name_servers[new][${rowCount}][ip_address]" class="form-control" placeholder="Eg. 192.168.1.1"></td>
            <td>
                <button type="button" class="btn btn-success btn-sm save-btn">Save</button>
                <button type="button" class="btn btn-danger btn-sm delete-btn">Delete</button>
            </td>
        `;
    });

    document.addEventListener('click', function(event) {
    if (event.target.classList.contains('delete-btn')) {
        const row = event.target.closest('tr');
        const id = event.target.getAttribute('data-id');

        Swal.fire({
            title: "Are you sure?",
            text: "This will permanently delete the name server!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                if (id) {
                    fetch(`/name-servers/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: "Deleted!",
                                text: "The name server has been deleted.",
                                icon: "success",
                                timer: 2000,
                                showConfirmButton: false
                            });

                            setTimeout(() => {
                                row.remove();
                                location.reload(); // Force reload to ensure record is removed from DB
                            }, 2000);
                        } else {
                            Swal.fire("Error!", data.message, "error");
                        }
                    })
                    .catch(error => {
                        Swal.fire("Error!", "Something went wrong!", "error");
                    });
                } else {
                    row.remove();
                    Swal.fire("Deleted!", "The new row has been removed.", "success");
                }
            }
        });
    }



        if (event.target.classList.contains('save-btn')) {
            const row = event.target.closest('tr');
            const id = event.target.getAttribute('data-id');
            const name = row.querySelector('input[name*="[name]"]').value;
            const ip = row.querySelector('input[name*="[ip_address]"]').value;

            if (!name || !ip) {
                Swal.fire("Error!", "Please fill in both fields.", "error");
                return;
            }

            fetch(id ? `/name-servers/${id}` : '{{ route('name-servers.store') }}', {
                method: id ? 'PATCH' : 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ name, ip_address: ip })
            }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: "Success!",
                        text: "Saved successfully!",
                        icon: "success",
                        timer: 2000,
                        showConfirmButton: false
                    });

                    if (!id) {
                        event.target.setAttribute('data-id', data.id);
                    }
                } else {
                    Swal.fire("Error!", "Could not save the name server.", "error");
                }
            });
        }
    });
</script>
@endsection
