<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"> <!-- Font Awesome -->
    <style>
        body {
            background: url('{{ asset('images/background2.jpg') }}') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .register-card {
            width: 500px;
            background: rgba(255, 255, 255, 0.2); /* Semi-transparent background */
            border: 1px solid rgba(255, 255, 255, 0.3); /* Subtle border */
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Soft shadow */
            backdrop-filter: blur(15px); /* Background blur effect */
            -webkit-backdrop-filter: blur(15px); /* Fallback for Safari */
        }
        .register-card h2 {
            text-align: center;
            margin-bottom: 20px;
            background-color: rgba(0, 123, 255, 0.8);
            color: white;
            padding: 10px;
            border-radius: 8px;
        }
        .form-icon {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            color: #007bff;
        }
        .form-control {
            padding-left: 35px;
        }
        .form-check-label {
            margin-left: 5px;
        }
        .login-link {
            text-align: center;
            margin-top: 10px;
        }
        .eye-icon {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
        }
        .position-relative {
            position: relative;
        }
    </style>
</head>
<body>
    <div class="register-card">
        <h2 style="text-align: center; margin-bottom: 20px; background-color: #007bff; color: white; padding: 10px; border-radius: 8px; border: 2px solid #0056b3;">
            Register</h2>
            <!-- Display validation errors if any -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="/register" method="POST">
                @csrf
                <div class="mb-3 position-relative">
                    <i class="fa fa-user form-icon"></i>
                    <input type="text" class="form-control" name="username" placeholder="Username" value="{{ old('username') }}" required>
                </div>
                <div class="mb-3 position-relative">
                    <i class="fa fa-lock form-icon"></i>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    <i class="fa fa-eye eye-icon" id="togglePassword"></i>
                </div>
                <div class="mb-3 position-relative">
                    <i class="fa fa-lock form-icon"></i>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" required>
                    <i class="fa fa-eye eye-icon" id="toggleConfirmPassword"></i>
                </div>
                <div class="mb-3 position-relative">
                    <i class="fa fa-envelope form-icon"></i>
                    <input type="email" class="form-control" name="email" placeholder="Email Address" value="{{ old('email') }}" required>
                </div>
                {{-- <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="robotCheck" required>
                    <label class="form-check-label" for="robotCheck">I am not a robot</label>
                </div> --}}
                <button type="submit" class="btn btn-primary w-100">Sign In</button>
            </form>
        <div class="login-link">
        <strong><a href="/login">Click here to Login</a></strong>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordField = document.getElementById('password');
            const type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;
            this.classList.toggle('fa-eye-slash');
        });

        document.getElementById('toggleConfirmPassword').addEventListener('click', function () {
            const confirmPasswordField = document.getElementById('password_confirmation');
            const type = confirmPasswordField.type === 'password' ? 'text' : 'password';
            confirmPasswordField.type = type;
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
{{-- @include('layouts.footer') --}}
</html>
