<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DNS Manager</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            height: 100%;
            overflow: auto;
            /* Prevent scrolling */
        }

        .sidebar {
            height: 100vh;
            /* Set sidebar to full height */
            background-color: #343a40;
            color: #fff;
            padding: 0;
            position: fixed;
            width: 250px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            top: 0;
            /* Align with the header */
            left: 0;
            transition: left 0.3s ease;
        }

        .sidebar h4 {
            font-size: 18px;
            margin-bottom: 30px;
            padding: 10px 15px;
        }

        .sidebar .nav-item {
            margin-bottom: 15px;
        }

        .sidebar a {
            color: #fff;
            display: block;
            text-decoration: none;
            font-size: 16px;
            padding: 10px 15px;
        }

        .sidebar a:hover {
            color: #17a2b8;
            background-color: #495057;
        }

        .sidebar a.active {
            font-weight: bold;
            color: #17a2b8;
            background-color: #495057;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
            width: 100%;
            background-color: #f8f9fa;
            min-height: 100vh;
            box-sizing: border-box;
            transition: margin-left 0.3s ease;
        }

        .navbar-brand {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .horizontal-line {
            width: 100%;
            border: 0;
            border-top: 2px solid #6C757D;
            margin: 0;
            padding: 0;
        }

        .header {
            background-color: #343a40;
        }

        .header .navbar {
            padding: 10px 15px;
            margin-left: 250px;
            /* Offset for sidebar */
            z-index: 1030;
            /* Ensure it appears above the sidebar */
        }

        .header .navbar-nav {
            margin-left: 0;
        }

        .header .navbar-nav .nav-link {
            color: #fff;
        }

        .header .navbar-nav .nav-link:hover {
            color: #17a2b8;
        }

        .sidebar-header {
            background-color: #333;
            height: 60px;
            padding: 10px 15px 8px 12px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        .sidebar-header img {
            width: 270px;
            height: 45px;
            margin-right: 10px;
        }

        .sidebar-header h6 {
            margin: 0;
            font-size: 18px;
        }

        .info-box {
            display: flex;
            align-items: center;
            padding: 15px;
            border-radius: 8px;
            color: #fff;
            margin-bottom: 20px;
        }

        .bg-gradient-success {
            background: linear-gradient(to right, #36AD51, #36AD51);
        }

        .bg-gradient-warning {
            background: linear-gradient(to right, #FFC414, #FFC414);
        }

        .bg-gradient-info {
            background: linear-gradient(to right, #2CABBF, #2CABBF);
        }

        .info-box-icon {
            font-size: 2rem;
            margin-right: 15px;
        }

        .info-box-content {
            flex: 1;
        }

        .sidebar.collapsed {
            width: 60px;
            htr transition: width 0.3s ease, left 0.3s ease;
            overflow: hidden;
        }

        .sidebar.collapsed .nav-link span {
            display: none;
        }

        .sidebar.collapsed .nav-link i {
            font-size: 20px;
            margin: 0 auto;
        }

        .content {
            transition: margin-left 0.3s ease;
            width: calc(100% - 250px);
        }

        .sidebar.collapsed+.content {
            margin-left: 70px;
            width: calc(100% - 70px);
        }

        .sidebar.right {
            left: auto;
            right: 0;
            transition: right 0.3s ease, left 0.3s ease;
        }

        .custom-box {
            border-top: 5px solid blue;
            background: #fff;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            color: #000;
            padding: 15px;
            margin-bottom: 30px;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
        }

        .info-box {
            flex: 1 1 100%;
            max-width: 100%;
        }

        .footer {
            border-top: 2px solid #fff;
            background-color: #343a40;
            color: #fff;
            text-align: center;
            padding: 10px 0;
            position: relative;
            width: 100%;
        }

        .footer {
            flex-shrink: 0;
        }

        table {
            table-layout: auto;
            width: 100%;
        }

        .custom-box {
            border: 1px solid #ddd;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .text-right {
            text-align: right;
            margin-bottom: 20px;
        }

        .btn-flat {
            margin: 0 5px;
        }

        .table {
            margin-top: 20px;
            background-color: #fff;
            border-collapse: collapse;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .table th,
        .table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.9rem;
        }

        .btn-flat {
            border: none;
            border-radius: 4px;
            font-size: 14px;
            padding: 10px 20px;
            margin: 0 5px;
            text-transform: uppercase;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .btn-flat:hover {
            background-color: #0056b3;
            color: #fff;
            text-decoration: none;
        }

        .gray-background li {
            background-color: #6E777F;
            padding: 1px;
            margin-bottom: 5px;
            border-radius: 5px;
            list-style-type: none;
            color: #fff;
            width: 210px;
            font-weight: bold;

        }

    </style>
</head>

<body>
    @include('layouts.header')

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <img id="sidebarLogo" src="{{ asset('images/logo.png') }}" alt="DNS Manager Logo" class="logo mb-3 mt-3">
           {{--  <h7>OCEAN DNS Manager</h7> --}}

        </div>

        <ul class="nav flex-column container mt-4">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="fa fa-home"></i>
                    <span>Main</span> <!-- This is the text that will be hidden -->
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('zones.index') }}">
                    <i class="fa fa-table"></i>
                    <span>Zones</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('password.change') }}">
                    <i class="fa fa-key"></i>
                    <span>Change Password</span>
                </a>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('auth.key') }}">
                    <i class="fa fa-lock "></i>
                    <span> Auth Key</span>
                </a>
                        @auth
            @if (Auth::user()->role === 'admin')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('name-servers.index') }}">
                        <i class="fa-solid fa-server"></i>
                        <span>Name Server</span>
                    </a>
                </li>
            @endif
        @endauth

            <li class="nav-item">
                <a class="nav-link text-danger" href="{{ route('commit.changes') }}">
                    <i class="fa fa-code-branch"></i>
                    <span>Commit Changes</span>
                </a>
            </li>

        </ul>
    </div>

    <!-- Main Content -->
    <div class="content">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
            </div>
        </nav>

        <main>
            @yield('content')
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.6.0/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.querySelector('.sidebar');
            const toggleButton = document.getElementById('sidebarToggle'); // Ensure this button exists
            const sidebarText = document.getElementById('sidebarText');

            toggleButton.addEventListener('click', function () {
                // Toggle 'collapsed' class for sidebar collapse behavior
                sidebar.classList.toggle('collapsed');

                // Change the text based on the sidebar state
                if (sidebar.classList.contains('collapsed')) {
                    sidebarText.textContent = "DNS"; // Change to your collapsed text
                } else {
                    sidebarText.textContent = "DNS Manager"; // Change to your expanded text
                }
            });
        });
    </script>

    <script>
        function showDeleteConfirmation(zoneId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to undo this action!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit the form associated with this delete button
                    document.getElementById('deleteForm-' + zoneId).submit();
                }
            });
        }

    </script>

    <script>
        document.getElementById('searchInput').addEventListener('keyup', function () {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#dataTable tbody tr');

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });

    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const entriesCount = document.getElementById("entriesCount");
            const dataTable = document.getElementById("dataTable");
            const rows = dataTable.querySelectorAll("tbody tr");

            function updateTable() {
                const count = parseInt(entriesCount.value, 10);
                rows.forEach((row, index) => {
                    row.style.display = index < count ? "" : "none";
                });
            }

            updateTable();
            entriesCount.addEventListener("change", updateTable);
        });

    </script>
</body>

</html>
