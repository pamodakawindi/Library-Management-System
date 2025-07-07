<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        if ($hashed_password === $password) {
            $_SESSION['admin'] = $username;
            header("Location: ./dashboard.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(to right,rgb(55, 21, 91),rgba(10, 235, 89, 0.8));
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 420px;
        }
        .login-logo {
            font-size: 2rem;
            font-weight: bold;
            color: #2575fc;
            text-align: center;
            margin-bottom: 1rem;
        }
        .form-floating > label {
            left: 1rem;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-logo">
        <i class="bi bi-shield-lock-fill"></i> Admin Portal
    </div>
    <form method="POST" novalidate>
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
            <label for="username">Username</label>
        </div>
        <div class="form-floating mb-3">
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
            <label for="password">Password</label>
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-lg">Sign In</button>
        </div>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger mt-3 mb-0" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
    </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
