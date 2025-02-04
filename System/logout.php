<?php
session_start();

session_unset();

session_destroy();

header("Location: research1.php?message=logged_out");
exit();
?>
