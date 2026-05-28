<?php
session_start();

// FIXED PATH (go OUT of admin/includes first)
include __DIR__ . '/../includes/conn.php';

if(!isset($_SESSION['admin']) || trim($_SESSION['admin']) == ''){
    header('location: ../index.php');
    exit();
}

$sql = "SELECT * FROM admin WHERE id = '".$_SESSION['admin']."'";
$query = $conn->query($sql);
$user = $query->fetch_assoc();
?>