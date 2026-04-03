<?php
session_start();

$usersFile = "users.xml";

// file create agar nahi hai
if (!file_exists($usersFile)) {
    $xml = new SimpleXMLElement("<users></users>");
    $xml->asXML($usersFile);
}

// --- LOGIN ---
if (isset($_POST['login'])) {
    $username = trim($_POST['user']);
    $password = trim($_POST['pass']);

    // ADMIN LOGIN
    if ($username == "admin" && $password == "1234") {
        $_SESSION['loggedin'] = true;
        $_SESSION['role'] = "admin";
        header("Location: inde.php");
        exit;
    }

    // USER LOGIN
    $xml = simplexml_load_file($usersFile);

    foreach ($xml->user as $u) {
        if ($u->email == $username && password_verify($password, $u->password)) {
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = (string)$u->id;
            $_SESSION['name'] = (string)$u->name;
            $_SESSION['role'] = "user";

            header("Location: inde.php");
            exit;
        }
    }

    $error = "❌ Wrong Email or Password!";
}

// --- REGISTER ---
if (isset($_POST['register'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass = trim($_POST['pass']);

    // empty check
    if ($name == "" || $email == "" || $pass == "") {
        $error = "⚠️ Sab field bharna zaroori hai!";
    } else {

        $xml = simplexml_load_file($usersFile);

        // duplicate email check
        foreach ($xml->user as $u) {
            if ($u->email == $email) {
                $error = "⚠️ Email already registered!";
                break;
            }
        }

        // new user add
        if (!isset($error)) {
            $newUser = $xml->addChild("user");
            $newUser->addChild("id", time());
            $newUser->addChild("name", $name);
            $newUser->addChild("email", $email);
            $newUser->addChild("password", password_hash($pass, PASSWORD_DEFAULT));

            $xml->asXML($usersFile);

            $success = "✅ Register ho gaya! Ab login karo.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login - Book Portfolio</title>

<style>
body {
    font-family: Arial;
    background: #2c3e50;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    color: white;
}

.login-card {
    background: #b41e64;
    padding: 30px;
    border-radius: 10px;
    width: 280px;
}

input {
    width: 100%;
    padding: 10px;
    margin: 8px 0;
    border-radius: 5px;
    border: none;
}

button {
    width: 100%;
    padding: 10px;
    background: #e67e22;
    border: none;
    color: white;
    border-radius: 5px;
    cursor: pointer;
}

.toggle {
    text-align: center;
    margin-top: 10px;
    cursor: pointer;
    color: yellow;
}
</style>
</head>

<body>

<div class="login-card">

<!-- LOGIN -->
<div id="loginForm">
    <h2>Login</h2>
    <form method="POST">
        <input type="text" name="user" placeholder="Email / admin" required>
        <input type="password" name="pass" placeholder="Password" required>
        <button name="login">Login</button>
    </form>

    <?php 
    if(isset($error)) echo "<p style='color:red'>$error</p>"; 
    if(isset($success)) echo "<p style='color:lightgreen'>$success</p>";
    ?>

    <div class="toggle" onclick="showRegister()">New user? Register</div>
</div>

<!-- REGISTER -->
<div id="registerForm" style="display:none;">
    <h2>Register</h2>
    <form method="POST">
        <input type="text" name="name" placeholder="Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="pass" placeholder="Password" required>
        <button name="register">Register</button>
    </form>

    <div class="toggle" onclick="showLogin()">Already have account? Login</div>
</div>

</div>

<script>
function showRegister() {
    document.getElementById("loginForm").style.display = "none";
    document.getElementById("registerForm").style.display = "block";
}

function showLogin() {
    document.getElementById("loginForm").style.display = "block";
    document.getElementById("registerForm").style.display = "none";
}
</script>

</body>
</html>