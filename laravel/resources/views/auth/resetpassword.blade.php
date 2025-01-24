<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - DNS Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"> <!-- Font Awesome -->
    <style>
        body {
            background: url('{{ asset('images/background.jpg') }}') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .reset-password-card {
            width: 600px;
            background: rgba(255, 255, 255, 0.8); /* Slight transparency for the reset password card */
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px); /* Apply the blur effect to the background */
        }
        .reset-password-card h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            position: relative;
            margin-bottom: 20px;
        }
        .form-group i {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            color: #007bff;
        }
        .form-control {
            padding-left: 35px;
        }
        .reset-password-card .content-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .reset-password-card .content-wrapper p {
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="reset-password-card">
        <h2 style="text-align: center; margin-bottom: 20px; background-color: #007bff; color: white; padding: 10px; border-radius: 8px; border: 2px solid #0056b3;">
            Reset Password
        </h2>
        <div class="content-wrapper">
            <p>Please enter your new password to reset it.</p>
            <img src="/images/dnss.png" alt="Logo" style="width: 80px; height: auto;">
        </div>

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

        <form action="/reset-password" method="POST">
            @csrf
            
            <div class="form-group">
                <i class="fa fa-lock"></i>
                <input type="password" class="form-control" name="new_password" placeholder="New Password" required>
            </div>
            <div class="form-group">
                <i class="fa fa-lock"></i>
                <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Reset Password</button>
        </form>
        <div class="mt-3 text-center">
            <a href="/login">Back to Login</a> | 
            <a href="/">Create an account</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
</body>
{{-- @include('layouts.footer') --}}

</html>
