<?php
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST["email"];
    $password = $_POST["password"];

    // Demo admin credentials
    if ($email === "admin@aquaguard.com" && $password === "admin123") {

        $_SESSION["admin_logged_in"] = true;
        $_SESSION["admin_name"] = "Admin User";

        header("Location: dashboard.php");
        exit();

    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login | AquaGuard</title>

    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, #e8f8ff, #f0fffb);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            width: 420px;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 35px rgba(0,0,0,0.12);
        }

        .logo {
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            color: #0785a8;
            margin-bottom: 10px;
        }

        .subtitle {
            text-align: center;
            color: #607d8b;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #123f50;
        }

        input {
            width: 100%;
            padding: 14px;
            margin-bottom: 20px;
            border: 1px solid #cbdde4;
            border-radius: 10px;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            background: #0785a8;
            color: white;
            font-size: 17px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #056f8c;
        }

        .error {
            background: #ffe5e5;
            color: #c62828;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .demo {
            margin-top: 25px;
            padding: 15px;
            background: #f1f7f9;
            border-radius: 10px;
            color: #455a64;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #0785a8;
            text-decoration: none;
        }
    </style>
</head>

<body>

<div class="login-box">

    <div class="logo">💧 AquaGuard</div>

    <div class="subtitle">
        Water Department Admin Login
    </div>

    <?php if ($error != ""): ?>
        <div class="error">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <label>Email</label>

        <input
            type="email"
            name="email"
            placeholder="Enter admin email"
            required
        >

        <label>Password</label>

        <input
            type="password"
            name="password"
            placeholder="Enter password"
            required
        >

        <button type="submit">
            Login
        </button>

    </form>

    <div class="demo">
        <strong>Demo Admin Account</strong><br><br>

        Email: admin@aquaguard.com<br>
        Password: admin123
    </div>

    <a class="back" href="../index.php">
        ← Back to AquaGuard
    </a>

</div>

</body>
</html>