<?php
include 'includes/session.php';

if(isset($_POST['save'])){

    $company_name    = trim($_POST['company_name']);
    $company_address = trim($_POST['company_address']);
    $rest_day        = intval($_POST['rest_day']);
    $working_days    = intval($_POST['working_days']);
    $thirteenth_mode = ($_POST['thirteenth_mode'] === 'split') ? 'split' : 'full';
    $midyear         = intval($_POST['midyear_percentage']);

    // Keep values sane
    if($rest_day < 0 || $rest_day > 6){ $rest_day = 0; }
    if($working_days < 1 || $working_days > 31){ $working_days = 26; }
    if($midyear < 0 || $midyear > 100){ $midyear = 50; }
    if($company_name === ''){ $company_name = 'ALLIED CARE EXPERTS (ACE) MEDICAL CENTER - BUTUAN, INC.'; }

    // Single-row settings table (id = 1)
    $stmt = $conn->prepare(
        "UPDATE payroll_settings
         SET company_name = ?, company_address = ?, rest_day = ?, working_days = ?,
             thirteenth_mode = ?, midyear_percentage = ?
         WHERE id = (SELECT id FROM (SELECT MIN(id) AS id FROM payroll_settings) t)"
    );
    $stmt->bind_param('ssiisi', $company_name, $company_address, $rest_day, $working_days, $thirteenth_mode, $midyear);

    if($stmt->execute()){
        $_SESSION['success'] = 'Settings saved.';
    }else{
        $_SESSION['error'] = 'Could not save settings. Please try again.';
    }
}

header('location: settings.php');
exit();