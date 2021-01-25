<?php
    session_start();
    session_unset();
    session_destroy();
    setcookie('auth', '', time()-1, '/', null, false, true);
    header('location: index.php');
    exit();
?>