<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("location: login.php");
    die;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    require_once __DIR__."/helper.php";

    $isInvalidRequest = empty($_POST['destination_url'])
        && empty($_POST['slug'])
        && !is_string($_POST['destination_url'])
        && !is_string($_POST['slug']);

    if ($isInvalidRequest) {
        showInfo(message: "Invalid request");
        goto SHOW_HTML;
    }

/* TODO: filtering "self" (c-huy.my.id) url - the same way in edit_url.php */
    $isInvalidUrl = !filter_var($_POST['destination_url'],
        FILTER_VALIDATE_URL);

    if ($isInvalidUrl) {
        http_response_code(403);
        die("Invalid URL");
    }

    /* remove domain when user puts the slug includes it */
    $slug = str_replace("{$_SERVER['SERVER_NAME']}/", "", $_POST['slug']);

    $pdo = require_once __DIR__."/pdo.php";

    /* Check if slug already exists */
    $stmt = $pdo->prepare("SELECT 1 FROM `urls` WHERE `slug_url` = :slug LIMIT 1");
    $stmt->execute(["slug" => $slug]);

    $doesSlugExist = !empty($stmt->fetch());

    if ($doesSlugExist) {
        http_response_code(403);
        die("Slug already exists");
    }
    /* End of checking slug */

    /* Inserting data */
    $stmt = $pdo->prepare(<<<SQL
        INSERT INTO `urls`
        (`destination_url`, `slug_url`, `user_id`)
        VALUES (:destination_url, :slug_url, :user_id)
    SQL);
    $stmt->execute([
        "destination_url" => $_POST['destination_url'],
        "slug_url" => $_POST['slug'],
        "user_id" => $_SESSION['user_id']
    ]);
    /* End of inserting data */

    /* Show info success */
    showInfo(message: "URL successfully added", redirect_to: "dashboard.php");
}

SHOW_HTML:
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New URL | Cihuy URL Shortener</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<header class="hero">
    <div class="hero-head">
      <nav class="navbar has-shadow" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
          <a class="navbar-item is--brand" href="index.php"><strong>Cihuy</strong></a>
          <button class="button navbar-burger" data-target="navMenu"><span></span> <span></span> <span></span></button>
        </div>
        <div id="navMenu" class="navbar-menu">
          <div class="navbar-end">
            <div class="navbar-item">
              <a class="button is-danger" href="logout.php">
                <span class="icon is-small">
                  <i class="fa fa-sign-out" aria-hidden="true"></i>
                </span>
                <strong>Log Out</strong>
              </a>
            </div>
          </div>
        </div>
      </nav>
    </div>
  </header>

  <div class="container mt-6">
    <div class="column is-multiline bd-is-size-1">
        <div class="column">
            <form action="" method="post">
                <div class="field">
                    <label class="label" for="destination_url">Destination URL</label>
                    <div class="control">
                        <input class="input" type="text" name="destination_url" placeholder="https://..." required="1">
                    </div>
                </div>
                <div class="field">
                    <label class="label" for="slug">Slug or URL Postfix (e.g: <?=$_SERVER['SERVER_NAME']?>/qq)</label>
                    <div class="control">
                        <input class="input" type="text" name="slug" placeholder="place your slug" required="1">
                    </div>
                </div>
                <div class="field">
                    <div class="control">
                        <button class="button is-link" type="submit">Submit</button>
                        <button class="button is-danger" type="reset">Reset</button>
                        <a href="dashboard.php">
                          <button class="button is-light" type="button">Back</button>
                        </a>
                    </div>
                </div>
            </form>
        </div>
      </div>
    </div>
    <script>
       document.addEventListener('DOMContentLoaded', () => {

      // Get all "navbar-burger" elements
        const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);

        // Add a click event on each of them
        $navbarBurgers.forEach( el => {
          el.addEventListener('click', () => {

            // Get the target from the "data-target" attribute
            const target = el.dataset.target;
            const $target = document.getElementById(target);

            // Toggle the "is-active" class on both the "navbar-burger" and the "navbar-menu"
            el.classList.toggle('is-active');
            $target.classList.toggle('is-active');

          });
        });
      });
    </script>
</body>
</html>