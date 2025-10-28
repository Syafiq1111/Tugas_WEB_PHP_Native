// logout.php
<?php
    session_start();
    $_SESSION = array();
    session_destroy();
    header('Location: login/login.php?status=logout_success');
    exit;
?>