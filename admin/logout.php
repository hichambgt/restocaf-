<?php
require_once '../config/config.php';

// Destroy session and redirect to login
session_destroy();
header('Location: index.php?msg=logged_out');
exit();
?>