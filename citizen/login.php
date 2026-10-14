<?php

session_start();

require_once "../config/database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $sql = "SELECT * FROM users
            WHERE email = ?
            AND role = 'CITIZEN'
            LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $password === $user["password"]) {

        $_SESSION["user_id"] = $user["user_id"];
        $_SESSION["user_name"] = $user["name"];
        $_SESSION["user_role"] = $user["role"];

        header("Location: dashboard.php");
        exit;

    } else {

        $error = "Invalid email or password.";

    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Citizen Login | AquaGuard</title>

<style>

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
}

body {

    min-height: 100vh;

    display: flex;

    justify-content: center;

    align-items: center;

    background:
        linear-gradient(
            135deg,
            #e8f8ff,
            #f4fffc
        );
}

.login-container {

    width: 400px;

    background: white;

    padding: 40px;

    border-radius: 18px;

    box-shadow:
        0 10px 35px
        rgba(0,0,0,0.12);

}

.logo {

    text-align: center;

    font-size: 30px;

    font-weight: bold;

    color: #087ea4;

    margin-bottom: 10px;

}

.logo span {

    color: #00a896;

}

.subtitle {

    text-align: center;

    color: #65727b;

    margin-bottom: 30px;

}

label {

    display: block;

    margin-bottom: 7px;

    font-weight: bold;

    color: #073b4c;

}

input {

    width: 100%;

    padding: 13px;

    margin-bottom: 18px;

    border: 1px solid #ccd9df;

    border-radius: 8px;

    font-size: 15px;

}

input:focus {

    outline: none;

    border-color: #087ea4;

}

button {

    width: 100%;

    padding: 14px;

    border: none;

    border-radius: 8px;

    background: #087ea4;

    color: white;

    font-size: 16px;

    font-weight: bold;

    cursor: pointer;

}

button:hover {

    background: #066b8c;

}

.error {

    background: #ffe7e7;

    color: #b00020;

    padding: 12px;

    border-radius: 8px;

    margin-bottom: 18px;

    text-align: center;

}

.back {

    display: block;

    text-align: center;

    margin-top: 20px;

    text-decoration: none;

    color: #087ea4;

}

.demo {

    margin-top: 25px;

    padding: 15px;

    background: #f3f8fa;

    border-radius: 10px;

    font-size: 13px;

    color: #52616b;

}

</style>

</head>

<body>

<div class="login-container">

    <div class="logo">
        💧 Aqua<span>Guard</span>
    </div>

    <p class="subtitle">
        Citizen Login
    </p>

    <?php if ($error): ?>

        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>


    <form method="POST">

        <label>
            Email
        </label>

        <input
            type="email"
            name="email"
            placeholder="Enter your email"
            required
        >


        <label>
            Password
        </label>

        <input
            type="password"
            name="password"
            placeholder="Enter your password"
            required
        >


        <button type="submit">
            Login
        </button>

    </form>


    <div class="demo">

        <strong>Demo Citizen Account</strong><br><br>

        Email: rahul@gmail.com<br>

        Password: 123456

    </div>


    <a href="../index.php" class="back">
        ← Back to AquaGuard
    </a>

</div>

</body>

</html>