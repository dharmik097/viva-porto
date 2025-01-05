<?php
session_start();

// Check if already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

// Get error message if any
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Viva Porto</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --deep-blue: #2C3E50;
            --emerald-green: #16A085;
            --ochre-yellow: #F39C12;
            --terracotta-red: #E74C3C;
            --soft-white: #ECF0F1;
            --light-gray: #BDC3C7;
        }
       body {
            background: linear-gradient(135deg, var(--deep-blue) 0%, var(--emerald-green) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            color: var(--deep-blue);
        }
        .login-container {
            max-width: 400px;
            margin: 0 auto;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-logo i {
            font-size: 48px;
            color: var(--emerald-green);
        }
        .error-message {
            color: var(--terracotta-red);
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 8px;
            background-color: var(--light-gray);
            border: 1px solid var(--terracotta-red);
        }
        .form-control {
            border-radius: 8px;
            padding: 12px;
            border: 1px solid var(--light-gray);
            color: var(--deep-blue);
        }
        .btn-login {
            border-radius: 8px;
            padding: 12px;
            font-weight: 500;
            background-color: var(--emerald-green);
            color: var(--soft-white);
            border: none;
            transition: background-color 0.3s ease;
        }
        .btn-login:hover {
            background-color: var(--terracotta-red);
        }
        .back-link {
            position: absolute;
            top: 20px;
            left: 20px;
            color: var(--soft-white);
            text-decoration: none;
        }
        .back-link:hover {
            color: rgba(255, 255, 255, 0.8);
        }

    </style>
</head>
<body>
    <a href="../" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Website
    </a>
    
    <div class="container">
        <div class="login-container">
            <div class="login-logo">
                <i class="fas fa-user-shield"></i>
                <h2 class="mt-3">Admin Login</h2>
            </div>
            
            <?php if (!empty($error)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            
            <form action="../api/admin_login.php" method="POST">
                <div class="mb-4">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 btn-login">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
