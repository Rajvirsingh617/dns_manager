@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mt-4 mb-4">
        <div class="col-md-6">
            <h1 class="text-dark">Change Password</h1>
        </div>
        <div class="col-md-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('zones.index') }}">Main</a></li>
                <li class="breadcrumb-item active">Change Password</li>
            </ol>
        </div>
    </div>
    
    <!-- Change Password Modal -->
    
            <div class="custom-box" style="margin-bottom:50px;margin-top:20px;border-top: 5px solid #007bff">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <!-- Change Password Form -->
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <!-- Old Password -->
                        <div class="form-group row">
                            <label for="password_old" class="col-md-3 col-form-label"><strong>Old
                                    Password</strong></label>
                            <div class="col-md-9">
                                <input type="password" class="form-control @error('password_old') is-invalid @enderror"
                                    id="password_old" name="password_old" placeholder="Enter your old password">
                                @error('password_old')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- New Password -->
                        <div class="form-group row">
                            <label for="password_one" class="col-md-3 col-form-label"><strong>New
                                    Password</strong></label>
                            <div class="col-md-9">
                                <input type="password" class="form-control @error('password_one') is-invalid @enderror"
                                    id="password_one" name="password_one" placeholder="Enter a new password">
                                @error('password_one')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Confirm New Password -->
                        <div class="form-group row">
                            <label for="password_confirmation" class="col-md-3 col-form-label"><strong>Confirm
                                    Password</strong></label>
                            <div class="col-md-9">
                                <input type="password"
                                    class="form-control @error('password_confirmation') is-invalid @enderror"
                                    id="password_confirmation" name="password_one_confirmation"
                                    placeholder="Confirm your new password">
                                @error('password_confirmation')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="text-center mb-4">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                <i class="fa fa-key"></i> <strong>Change Password</strong>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            showConfirmButton: true,
            timer: 5000
        });
    </script>
    @endif
    @endsection
