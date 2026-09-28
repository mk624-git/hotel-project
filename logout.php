<?php
session_start();
session_destroy();
header('Location: components/index.php');
exit();
?>