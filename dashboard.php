<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("location: login.php");
    die;
}

$pdo = require_once __DIR__."/pdo.php";

$stmt = $pdo->prepare(<<<SQL
    SELECT `destination_url`, `slug_url`,`hits`, `id` 
    FROM `urls` 
    WHERE `user_id` = :user_id
SQL);
$stmt->execute(["user_id" => $_SESSION['user_id']]);

$fetchData = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | Cihuy URL Shortener</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bulma.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
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
    <div class="column is-multiline">
      <div class="column">
        <a class="button is-primary" href="add_new_url.php"><span class="icon is-small"><i class="fa fa-plus"></i> </span><span>Add New URL</span></a>
      </div>
      <div class="column">
        <table id="urls" class="table is-bordered is-striped is-narrow is-fullwidth mt-4">
          <thead>
            <tr>
              <th>Destination URL</th>
              <th>Shortened URL</th>
              <th>Hits</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach($fetchData as $data): ?>
            <tr>
              <td><a href="<?=$data['destination_url']?>" target="_blank"><?=$data['destination_url']?></a></td>
              <td><a href="http://<?=$_SERVER['SERVER_NAME']?>/<?=$data['slug_url']?>" target="_blank">http://<?=$_SERVER['SERVER_NAME']?>/<?=$data['slug_url']?></a></td>
              <td><?=$data['hits']?></td>
              <td>
                <a href="edit_url.php?id=<?=$data['id']?>" class="button is-primary is-small"><span class="icon is-small"><i class="fa fa-edit"></i> </span><span>Edit</span></a>
                <a href="delete_url.php?id=<?=$data['id']?>" 
                   onclick="return confirm('Are you sure?')"
                   class="button is-danger is-small"><span class="icon is-small"><i class="fa fa-trash-o"></i> </span><span>Delete</span>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
  <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bulma.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>

  <script type="text/javascript">
      $(document).ready(function() {
        $('#urls').DataTable({
          dom: 'Bfrtip',
          buttons: [
            {
                extend: 'excelHtml5',
                exportOptions: {
                    columns: [ 0, 1, 2 ]
                },
                className: 'button is-info is-light'
            },
            {
                extend: 'pdfHtml5',
                exportOptions: {
                    columns: [ 0, 1, 2 ]
                },
                className: 'button is-success is-light'
            }
          ]
        });
      });

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