<?php
    session_start();
    if(isset($_SESSION['employee'])){
        header('location: home.php');
        exit();
    }
?>
<?php include 'includes/header.php'; ?>

<body class="hold-transition login-page">

<style>
    body.login-page{
        background: linear-gradient(135deg, #0f172a 0%, #134e4a 45%, #115e59 100%);
    }
    .login-box{ width: 400px; margin: 6% auto; }
    .login-logo .icon-circle{
        width:84px; height:84px; border-radius:50%; margin:0 auto 14px auto;
        background:linear-gradient(135deg,#14b8a6,#0d9488);
        display:flex; align-items:center; justify-content:center;
        color:#fff; font-size:34px; box-shadow:0 12px 30px rgba(13,148,136,0.35);
    }
    .brand-title{ color:#fff; font-size:26px; font-weight:700; letter-spacing:1px; }
    .brand-subtitle{ color:rgba(255,255,255,0.75); font-size:13px; letter-spacing:2px; margin-top:4px; }
    .login-box-body{ background:#fff; border-radius:18px; padding:32px; box-shadow:0 15px 45px rgba(0,0,0,0.25); border-top:6px solid #0d9488; }
    .login-box-msg{ font-size:16px; color:#475569; margin-bottom:22px; text-align:center; font-weight:600; }
    .form-control{ height:50px; border-radius:12px; }
    .btn-login{ height:50px; border:none; border-radius:12px; font-size:15px; font-weight:700; color:#fff;
        background:linear-gradient(135deg,#14b8a6,#0d9488); width:100%; }
    .btn-login:hover{ color:#fff; opacity:.92; }
    .login-footer{ text-align:center; margin-top:18px; color:rgba(255,255,255,0.7); font-size:12px; }
    .callout{ border-radius:10px; border:none; margin-top:16px; font-weight:600; }
    .callout-danger{ background:#fee2e2 !important; color:#b91c1c !important; }
</style>

<div class="login-box">

    <div class="login-logo">
        <div class="icon-circle"><i class="fa fa-user-md"></i></div>
        <div class="brand-title">ACE MEDICAL CENTER</div>
        <div class="brand-subtitle">EMPLOYEE PORTAL</div>
    </div>

    <div class="login-box-body">

        <p class="login-box-msg">Sign in to your account</p>

        <form action="login.php" method="POST">

            <div class="form-group has-feedback">
                <input type="text" class="form-control" name="employee_id" placeholder="Employee ID" required autofocus>
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
            </div>

            <div class="form-group has-feedback">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>

            <button type="submit" class="btn btn-login" name="login">
                <i class="fa fa-sign-in"></i> LOGIN
            </button>

        </form>

        <?php
            if(isset($_SESSION['error'])){
                echo "<div class='callout callout-danger text-center'>".htmlspecialchars($_SESSION['error'])."</div>";
                unset($_SESSION['error']);
            }
        ?>

    </div>

    <div class="login-footer">ACE Medical Center &copy; <?php echo date('Y'); ?></div>

</div>

<?php include 'includes/scripts.php'; ?>
</body>
</html>
