<?php
require_once 'bootstrap.php';
session_destroy();
header("Location: login.php");
exit;