<?php
include_once 'inc/ioUtil.php';
session_start();
logout();
header('Location: .');
?>