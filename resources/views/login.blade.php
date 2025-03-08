<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Global Styles */
        body {
            background-color: #9FBD93;
            /* Soft gray background */
            font-family: 'Arial', sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            width: 400px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card-header {
            background-color: #006400;
            /* Primary Green */
            color: white;
            text-align: center;
            padding: 20px;
        }

        .card-header h3 {
            margin: 0;
            font-size: 24px;
        }

        .card-header small {
            font-size: 14px;
            color: #F0F0F0;
        }

        .nav-tabs {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            border-bottom: none;
        }

        .nav-tabs .nav-link {
            border: none;
            border-radius: 20px;
            padding: 10px 20px;
            font-weight: bold;
            color: #6c757d;
            transition: all 0.3s ease-in-out;
        }

        .nav-tabs .nav-link.active {
            background-color: #006400;
            color: white;
        }

        .form-control {
            height: 45px;
            border-radius: 5px;
            border: 1px solid #ccc;
            padding: 10px;
            transition: border 0.3s ease-in-out;
        }

        .form-control:focus {
            border-color: #006400;
            box-shadow: 0 0 5px rgba(42, 208, 0, 0.5);
            outline: none;
        }

        .btn-primary {
            background-color: #006400;
            border: none;
            height: 45px;
            border-radius: 15px;
            font-weight: bold;
            transition: background-color 0.3s ease-in-out;
        }

        .btn-primary:hover {
            background-color: #249C00;
        }

        .text-center a {
            color: #006400;
            text-decoration: none;
            font-size: 14px;
        }

        .text-center a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="card-header">
            <h3>Welcome </h3>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs mb-3" id="authTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login"
                        type="button" role="tab">Login</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register"
                        type="button" role="tab">Register</button>
                </li>
            </ul>
            <div class="tab-content">
                <!-- Login Form -->
                <div class="tab-pane fade show active" id="login" role="tabpanel">
                    <form method="POST" action="{{ route('login.post') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control"
                                placeholder="Enter your email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" id="password" name="password" class="form-control"
                                    placeholder="Enter your password" required>
                                </button>
                            </div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                </div>
                <!-- Register Form -->
                <div class="tab-pane fade" id="register" role="tabpanel">
                    <form method="POST" action="{{ route('register.post') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" id="name" name="name" class="form-control"
                                placeholder="Enter your full name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email-register" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control"
                                placeholder="Enter your email" required>
                        </div>
                        <div class="mb-3 row">
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" name="password" class="form-control"
                                placeholder="Create a password" required>
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Confirmation Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="form-control" placeholder="Confirm your password" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select name="role" id="role" class="form-select">
                                <option value="admin">Admin</option>
                                <option value="peneliti">Peneliti</option>
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Register</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
