<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
if (!empty($_SESSION['user'])) {
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}
exit;
