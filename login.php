<?php

session_start();

if (isset($_SESSION['user_id'])) {
    header("location: dashboard.php");
    die;
}

$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $isInvalidRequest = empty($_POST['username'])
        && empty($_POST['password'])
        && !is_string($_POST['username'])
        && !is_string($_POST['password']);

    if ($isInvalidRequest) {
        $errorMessage = "Invalid request";
        goto SHOW_HTML;
    }

    $usernameQuery = "WHERE";

    if (filter_var($_POST['username'], FILTER_VALIDATE_EMAIL)) {
        $usernameQuery .= "`email` = ?";
    } else {
        $usernameQuery .= "`username` = ?";
    }

    $pdo = require_once __DIR__."/pdo.php";

    $stmt = $pdo->prepare("SELECT `id`, `password` FROM `users` {$usernameQuery}");
    $stmt->execute([$_POST['username']]);
    $user = $stmt->fetch();

    if (empty($user)) {
        $errorMessage = "Invalid email";
        goto SHOW_HTML;
    }

    $verifyPassword = password_verify($_POST['password'], $user['password']);

    if (!$verifyPassword) {
        $errorMessage = "Invalid password";
        goto SHOW_HTML;
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['login_at'] = time();

    header("location: dashboard.php");
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
    <title>Login</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <?php if (isset($_SESSION['register_ok'])): ?>
        <script>alert("Registration successful")</script>
    <?php 
        unset($_SESSION['register_ok']); 
        endif; 
    ?>

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
                            <label for="" class="label">Username or Email</label>
                            <div class="control has-icons-left">
                                <input type="text" name="username" placeholder="zuck" class="input" required>
                                <span class="icon is-small is-left">
                                    <i class="fa fa-user"></i>
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
                            <button class="button is-link">
                                Login
                            </button>
                            </div>
                            <p>Don't have an account? <a href="register.php">Register</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
