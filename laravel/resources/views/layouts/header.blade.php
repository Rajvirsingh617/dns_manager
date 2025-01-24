{{-- @extends('layouts.app')

@section('content') --}}
<div class="header">
    <!-- Horizontal line -->
    <nav class="main-header navbar navbar-expand navbar-dark navbar-gray">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></a>
            </li>
        </ul>
    
        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-user"></i>&nbsp;
                    {{ Auth::user()->username }}
                    <span class="badge badge-info">{{ Auth::user()->role }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="fa fa-sign-out-alt"></i> LogOut
                        </button>
                    </form>
                </div>
                
            </li>
        </ul>
    </nav>
    
    <hr class="horizontal-line">
</div>
{{-- @endsection --}}

