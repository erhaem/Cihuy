<?php

if (isset($_SESSION['login_at'])) {
    if (($_SESSION['login_at'] + 3600) < time()) {
        ?>
            <script>
            alert("Session expired");
            window.location.href = "logout.php";
            </script>
        <?php
    }
}