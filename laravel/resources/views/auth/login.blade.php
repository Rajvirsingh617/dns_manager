<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DNS Manager Login</title>
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
        .login-card {
            width: 400px;
            background: rgba(255, 255, 255, 0.2); /* Semi-transparent background */
            border: 1px solid rgba(255, 255, 255, 0.3); /* Subtle border */
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Soft shadow */
            backdrop-filter: blur(15px); /* Background blur effect */
            -webkit-backdrop-filter: blur(15px); /* Fallback for Safari */
        }
        .login-card h2 {
            text-align: center;
            margin-bottom: 20px;
            background-color: rgba(0, 123, 255, 0.8);
            color: white;
            padding: 10px;
            border-radius: 8px;
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
        .login-card .content-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .login-card .content-wrapper p {
            flex: 1;
        }
        .logo {
            max-width: 310px; /* Adjust size */
            height: auto;
            display: block;
            margin-left: 25px;
        }

    </style>
</head>
<body>
    <div class="login-card">
        <img src="{{ asset('images/logo.jpeg') }}" alt="DNS Manager Logo" class="logo mb-3">
        <h2 style="text-align: center; margin-bottom: 20px; background-color: #007bff; color: white; padding: 10px; border-radius: 8px; border: 2px solid #0056b3;">
            Login
        </h2>
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

        <form action="/login" method="POST">
            @csrf
            <div class="form-group">
                <i class="fa fa-user"></i>
                <input type="text" class="form-control" name="username" placeholder="Username" value="{{ old('username') }}" required>
            </div>
            <div class="form-group">
                <i class="fa fa-lock"></i>
                <input type="password" class="form-control" id="passwordField" name="password" placeholder="Password" required>
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="showPassword" onclick="togglePasswordVisibility()">
                <label class="form-check-label" for="showPassword">Show Password</label>
            </div>
            <button type="submit" class="btn btn-primary w-100">Sign In</button>
        </form>
        <div class="mt-3 text-center">
            <strong><a href="/resetpassword">Forgot Password ?</a></strong> |
            <strong><a href="/register">Create an account</a></strong>
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
        <script>
    function togglePasswordVisibility() {
        const passwordField = document.getElementById('passwordField');
        passwordField.type = (passwordField.type === 'password') ? 'text' : 'password';
    }
</script>
        </body>
</html>
