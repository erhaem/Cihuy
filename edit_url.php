<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("location: login.php");
    die;
}

require_once __DIR__."/auto_logout.php";

if (empty($_GET['id'])) {
    http_response_code(403);
    die("id is needed");
}

$pdo = require_once __DIR__."/pdo.php";

$stmt = $pdo->prepare("SELECT `id`, `destination_url`, `slug_url`, `user_id` FROM `urls` WHERE id = :id");
$stmt->execute(["id" => $_GET['id']]);

$data = $stmt->fetch();

if (empty($data)) {
    http_response_code(404);
    die("No data");
}

/* Check if the url belongs to the active user  */
if ($data['user_id'] !== $_SESSION['user_id']) {
    http_response_code(403);
    die("You're not allowed to edit this url");
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

    /* URL sanitization */
    $isInvalidUrl = !filter_var($_POST['destination_url'],
        FILTER_VALIDATE_URL);

    if ($isInvalidUrl) {
        showInfo(message: "Invalid URL");
        goto SHOW_HTML;
    }

    /* Check if slug already exists */
    $stmt = $pdo->prepare("SELECT `slug_url` FROM `urls` WHERE `slug_url` = :slug");
    $stmt->execute(["slug" => $_POST['slug']]);

    $result = $stmt->fetch();

    if (!empty($result) 
        && $_POST['slug'] !== $data['slug_url']
    ) {
        showInfo(message: "Slug already exists");
        goto SHOW_HTML;
    }
    /* End of checking slug */

    /* Update url data */
    $stmt = $pdo->prepare(<<<SQL
        UPDATE `urls` SET
            `destination_url` = :destination_url,
            `slug_url` = :slug_url
        WHERE `id` = :id
    SQL);
    $stmt->execute([
        "destination_url" => $_POST['destination_url'],
        "slug_url" => $_POST['slug'],
        "id" => $data['id']
    ]);
    /* End of update url data */

    /* Show info success */
    /*FIXME: alert showed after clicking "back" button*/
    /*solution: isi atribut type pada button menjadi "button" -
     tag button berada dalam tag a, bukan tag a dalam tag button*/
    showInfo(message: "URL successfully edited", redirect_to: "dashboard.php");
    
}

SHOW_HTML:
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit URL | Cihuy URL Shortener</title>

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
                        <input class="input" type="text" name="destination_url" placeholder="https://..." value="<?=$data['destination_url']?>" required="1">
                    </div>
                </div>
                <div class="field">
                    <label class="label" for="slug">Slug or URL Postfix (e.g: <?=$_SERVER['SERVER_NAME']?>/qq)</label>
                    <div class="control">
                        <input class="input" type="text" name="slug" placeholder="place your slug" value="<?=$data['slug_url']?>" required="1">
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
</body>
</html>