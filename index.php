<?php session_start(); ?>
<?php include 'header.php'; ?>
<body class="hold-transition login-page" style="background:linear-gradient(135deg,#0f172a 0%,#134e4a 45%,#115e59 100%);">

<style>
    .landing-box{ width:470px; max-width:92%; margin:5% auto; }
    .landing-logo{ text-align:center; margin-bottom:20px; color:#fff; }
    .icon-circle{
        width:84px; height:84px; border-radius:50%; margin:0 auto 14px auto;
        background:linear-gradient(135deg,#14b8a6,#0d9488);
        display:flex; align-items:center; justify-content:center;
        color:#fff; font-size:34px; box-shadow:0 12px 30px rgba(13,148,136,.35);
    }
    .brand-title{ font-size:26px; font-weight:700; letter-spacing:1px; }
    .brand-subtitle{ color:rgba(255,255,255,.75); font-size:13px; letter-spacing:2px; margin-top:4px; }

    .landing-clock{ text-align:center; color:rgba(255,255,255,.9); margin-bottom:18px; }
    .landing-clock #date{ margin:0; font-size:13px; letter-spacing:1px; }
    .landing-clock #time{ font-size:22px; font-weight:700; }

    .landing-card{
        background:#fff; border-radius:18px; padding:26px;
        box-shadow:0 15px 45px rgba(0,0,0,.25); border-top:6px solid #0d9488;
    }
    .choice{
        display:block; border-radius:14px; padding:18px 20px; margin-bottom:14px;
        text-decoration:none; color:#fff; transition:.2s;
    }
    .choice:last-child{ margin-bottom:0; }
    .choice:hover{ transform:translateY(-2px); color:#fff; text-decoration:none; box-shadow:0 12px 25px rgba(0,0,0,.18); }
    .choice .ico{ font-size:26px; width:42px; text-align:center; }
    .choice .t{ font-size:17px; font-weight:700; line-height:1.2; }
    .choice .d{ font-size:12px; opacity:.92; }
    .choice-emp{ background:linear-gradient(135deg,#14b8a6,#0d9488); }
    .choice-adm{ background:linear-gradient(135deg,#2563eb,#1d4ed8); }

    .landing-footer{ text-align:center; margin-top:20px; color:rgba(255,255,255,.7); font-size:12px; letter-spacing:1px; }
</style>

<div class="landing-box">

    <div class="landing-logo">
        <div class="icon-circle"><i class="fa fa-heartbeat"></i></div>
        <div class="brand-title">ACE MEDICAL CENTER</div>
        <div class="brand-subtitle">ATTENDANCE &amp; PAYROLL SYSTEM</div>
    </div>

    <div class="landing-clock">
        <p id="date"></p>
        <span id="time"></span>
    </div>

    <div class="landing-card">

        <a href="employee/" class="choice choice-emp">
            <table><tr>
                <td><i class="fa fa-user ico"></i></td>
                <td>
                    <div class="t">Employee Portal</div>
                    <div class="d">View payslips, attendance &amp; 13th month pay</div>
                </td>
            </tr></table>
        </a>

        <a href="admin/" class="choice choice-adm">
            <table><tr>
                <td><i class="fa fa-lock ico"></i></td>
                <td>
                    <div class="t">HR / Admin</div>
                    <div class="d">Manage employees, attendance &amp; payroll</div>
                </td>
            </tr></table>
        </a>

    </div>

    <div class="landing-footer">ACE Medical Center &copy; <?php echo date('Y'); ?></div>

</div>

<?php include 'scripts.php' ?>
<script type="text/javascript">
$(function() {
    setInterval(function() {
        var m = moment();
        $('#date').html(m.format('dddd').substring(0,3).toUpperCase() + ' - ' + m.format('MMMM DD, YYYY'));
        $('#time').html(m.format('hh:mm:ss A'));
    }, 1000);
});
</script>
</body>
</html>