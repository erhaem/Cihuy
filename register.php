<?php

session_start();

if (isset($_SESSION['user_id'])) {
    header("location: dashboard.php");
    die;
}

$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $isInvalidRequest = empty($_POST['username'])
        && empty($_POST['email'])
        && empty($_POST['password'])
        && empty($_POST['confirm_password'])
        && !is_string($_POST['username'])
        && !is_string($_POST['email'])
        && !is_string($_POST['password'])
        && !is_string($_POST['confirm_password']);

    if ($isInvalidRequest) {
        $errorMessage = "Invalid request";
        goto SHOW_HTML;
    }

    /* Username, email, and password sanitization */
    $usernameLength = strlen($_POST['username']);

    if ($usernameLength < 4) {
        $errorMessage = "Username must be at least 4 characters";
        goto SHOW_HTML;
    }

    if ($usernameLength > 30) {
        $errorMessage = "Username couldn't be longer than 30 characters";
        goto SHOW_HTML;
    }

    $emailLength = strlen($_POST['email']);

    if ($emailLength > 64) {
        $errorMessage = "Email address couldn't be longer than 64 characters";
        goto SHOW_HTML;
    }

    $isInvalidEmail = !filter_var($_POST['email'], 
        FILTER_VALIDATE_EMAIL);

    if ($isInvalidEmail) {
        $errorMessage = "Invalid email address";
        goto SHOW_HTML;
    }

    $passwordLength = strlen($_POST['password']);

    if ($passwordLength < 6) {
        $errorMessage = "Password must be at least 6 characters";
        goto SHOW_HTML;
    }

    if ($passwordLength > 255) {
        $errorMessage = "Password couldn't be longer than 255 characters";
        goto SHOW_HTML;
    }

    if ($_POST['confirm_password'] !== $_POST['password']) {
        $errorMessage = "Password mismatch";
        goto SHOW_HTML;
    }
    /* End of sanitization */

    $pdo = require_once __DIR__."/pdo.php";

    /* Check if username already exists */
    $stmt = $pdo->prepare("SELECT 1 FROM `users` WHERE `username` = :username");
    $stmt->execute(["username" => $_POST['username']]);
    $isUsernameExisted = (bool) $stmt->fetch();

    if ($isUsernameExisted) {
        $errorMessage = "Username already exists. Please use another username";
        goto SHOW_HTML;
    }

    /* check if email already exists */
    $stmt = $pdo->prepare("SELECT 1 FROM `users` WHERE `email` = :email");
    $stmt->execute(["email" => $_POST['email']]);
    $isEmailExisted = (bool) $stmt->fetch();

    if ($isEmailExisted) {
        $errorMessage = "Email address already exists. Please use another email address";
        goto SHOW_HTML;
    }

    /* Start inserting data */
    $stmt = $pdo->prepare(<<<SQL
        INSERT INTO `users`
        (`username`, `email`, `password`)
        VALUES (:username, :email, :password)
    SQL);
    $stmt->execute([
        "username" => $_POST['username'],
        "email" => $_POST['email'],
        "password" => password_hash($_POST['password'], PASSWORD_BCRYPT)
    ]);

    /* Telling login page that registration is successful */
    $_SESSION['register_ok'] = true;

    /* Redirecting when registration is successful */
    header("location: login.php");
    die;
}

SHOW_HTML:
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

    <?php if ($errorMessage): ?>
        <script>alert("<?=htmlspecialchars($errorMessage)?>")</script>
    <?php endif; ?>

    <section class="hero is-fullheight">
        <div class="hero-body">
            <div class="container">
                <div class="columns is-centered">
                    <div class="column is-5-tablet is-4-desktop is-3-widescreen">
                        <form action="" method="POST" class="box">
                            <div class="field">
                                <label for="" class="label">Username</label>
                                <div class="control has-icons-left">
                                    <input type="text" name="username" 
                                    pattern=".{4,30}" title="Username must be at least 4 to 30 characters" 
                                    placeholder="zuck" class="input" required>
                                    <span class="icon is-small is-left">
                                        <i class="fa fa-user"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="field">
                                <label for="" class="label">Email</label>
                                <div class="control has-icons-left">
                                    <input type="email" name="email" placeholder="zuck@facebook.com" class="input" required>
                                    <span class="icon is-small is-left">
                                        <i class="fa fa-envelope"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="field">
                                <label for="" class="label">Password</label>
                                <div class="control has-icons-left">
                                    <input type="password" name="password" placeholder="*******" 
                                    pattern=".{6,255}" title="Password must be at least 6 to 255 characters" 
                                    class="input" required>
                                    <span class="icon is-small is-left">
                                        <i class="fa fa-lock"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="field">
                                <label for="" class="label">Confirm Password</label>
                                <div class="control has-icons-left">
                                    <input type="password" name="confirm_password" placeholder="*******" 
                                    pattern=".{6,255}" title="Password must be at least 6 to 255 characters" 
                                    class="input" required>
                                    <span class="icon is-small is-left">
                                        <i class="fa fa-lock"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="field">
                                <button class="button is-primary">
                                    Register
                                </button>
                                </div>
                            <p>Already have an account? <a href="login.php">Login</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
</body>
</html>