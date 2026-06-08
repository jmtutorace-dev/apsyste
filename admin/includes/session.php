<?php
session_start();

// FIXED PATH (go OUT of admin/includes first)
include __DIR__ . '/../includes/conn.php';

if(!isset($_SESSION['admin']) || trim($_SESSION['admin']) == ''){
    header('location: ../index.php');
    exit();
}

$admin_id = intval($_SESSION['admin']);
$stmt = $conn->prepare("SELECT * FROM admin WHERE id = ?");
$stmt->bind_param('i', $admin_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Current page filename — drives the sidebar active-link highlighting in menubar.php
$page = basename($_SERVER['PHP_SELF']);
?>