@extends('layouts.app')
@section('content')

<div class="col-sm-15">
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="index.php">Main</a></li>
        <li class="breadcrumb-item">Main</li>
    </ol>
</div>
<div class="col-sm-6">
    <h1 class="m-0 text-dark" style="text-align: left !important; margin-bottom: 20px;">Main</h1>
</div>

<div class="custom-box" style="margin-bottom:50px;margin-top:20px;border-top: 5px solid #007bff ">
    <div class="col-sm-15">
        <div class="row">
            <div class="col-md-6">
                @if(Auth::check())
                    <h3>Welcome, <b>{{ Auth::user()->username }}</b></h3>
                @else
                    <h3>Welcome, Guest</h3>
                @endif
            </div>
        </div>
    </div>
    <div class="container-fluid">
    <div class="row justify-content-center">
        <!-- First Info Box -->
        <div class="col-md-4 col-sm-6 col-12">
            <div class="info-box bg-gradient-success">
                <span class="info-box-icon"><i class="fa fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-number" style="font-weight: bold;">DNS Services Status</span>
                    <span class="info-box-text" style="font-weight: bold;">Started</span>
                </div>
            </div>
        </div>
        
        <!-- Second Info Box -->
        <div class="col-md-4 col-sm-6 col-12">
            <div class="info-box bg-gradient-warning">
                <span class="info-box-icon" style="color: black;">
                    <i class="fas fa-table"></i>
                </span>
                <div class="info-box-content">
                    @if(auth()->user()->role === 'admin')
                        <span class="info-box-number" style="font-weight: bold; color: black;">All Zones</span>
                        <span class="info-box-text" style="font-weight: bold; color: black;">
                            {{ $totalZones }} Zones
                        </span>
                    @else
                    <a class="nav-link" href="{{ route('zones.index') }}">
                        <span class="info-box-number" style="font-weight: bold; color: black;">You Maintain</span>
                        <span class="info-box-text" style="font-weight: bold; color: black;">
                            {{ $zoneCount }} Zones
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
    @if(session('success'))
        <script>
            Swal.fire({
                title: 'Success!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonText: 'Okay'
            });
        </script>
        @endif
@endsection

