<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>ACE MEDICAL CENTER HR PAYROLL</title>

    <!-- Responsive -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <link rel="stylesheet" href="../admin/css/custom.css">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="../bower_components/bootstrap/dist/css/bootstrap.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="../bower_components/font-awesome/css/font-awesome.min.css">

    <!-- Ionicons -->
    <link rel="icon" type="image/png" href="../images/ACEMC.png">

    <!-- AdminLTE -->
    <link rel="stylesheet" href="../dist/css/AdminLTE.min.css">

    <!-- DataTables -->
    <link rel="stylesheet" href="../bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">

    <!-- Daterange Picker -->
    <link rel="stylesheet" href="../bower_components/bootstrap-daterangepicker/daterangepicker.css">

    <!-- Timepicker -->
    <link rel="stylesheet" href="../plugins/timepicker/bootstrap-timepicker.min.css">

    <!-- Datepicker -->
    <link rel="stylesheet" href="../bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">

    <!-- AdminLTE Skins -->
    <link rel="stylesheet" href="../dist/css/skins/_all-skins.min.css">

    <!-- Google Font -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700">

<style>

/* =========================================================
   GLOBAL
========================================================= */

body{

    font-family:'Poppins', sans-serif;

    background:#eef3f8;

    color:#2b3445;
}

.content-wrapper{

    background:
        linear-gradient(
            180deg,
            #eef3f8 0%,
            #f7f9fc 100%
        );

    min-height:100vh;

    padding-bottom:20px;
}

.mt20{
    margin-top:20px;
}

.bold{
    font-weight:600;
}

/* =========================================================
   NAVBAR
========================================================= */

.skin-blue .main-header .navbar{

    background:#ffffff !important;

    box-shadow:
        0 2px 8px rgba(0,0,0,0.03);

    border:none;
}

.skin-blue .main-header .logo{

    background:#14324b !important;

    color:#ffffff !important;

    font-weight:700;

    letter-spacing:1px;

    border-right:none;
}

.skin-blue .main-header .navbar .sidebar-toggle{

    color:#1f3c57 !important;
}

.skin-blue .main-header .navbar .sidebar-toggle:hover{

    background:#f0f4f8 !important;

    color:#14324b !important;
}

/* USER TOP RIGHT */

.navbar-nav > .user-menu > a{

    color:#2c3e50 !important;

    font-weight:600;
}

.navbar-nav > .user-menu > a:hover{

    background:#f3f7fb !important;

    color:#14324b !important;
}

/* =========================================================
   SIDEBAR
========================================================= */

.main-sidebar{

    background:
        linear-gradient(
            180deg,
            #17324d 0%,
            #204765 100%
        ) !important;

    box-shadow:
        2px 0 10px rgba(0,0,0,0.04);
}

/* HEADER */

.skin-blue .sidebar-menu > li.header{

    color:rgba(255,255,255,0.55) !important;

    background:transparent !important;

    font-size:11px;

    letter-spacing:1.5px;

    font-weight:600;

    padding-top:22px;
}

/* NORMAL MENU */

.skin-blue .sidebar-menu > li > a{

    color:rgba(255,255,255,0.84);

    padding:13px 15px;

    font-size:14px;

    transition:.20s;

    border-left:3px solid transparent;
}

/* HOVER FIX */

.skin-blue .sidebar-menu > li:hover > a{

    background:rgba(255,255,255,0.10) !important;

    color:#ffffff !important;

    border-left:3px solid #59b7ff;

    padding-left:20px;
}

/* ACTIVE */

.skin-blue .sidebar-menu > li.active > a{

    background:
        linear-gradient(
            90deg,
            #2f8ed6,
            #3aa0ea
        ) !important;

    color:#fff !important;

    border-radius:0 24px 24px 0;

    margin-right:10px;

    border-left:none;

    box-shadow:none;
}

/* TREEVIEW */

.skin-blue .treeview-menu{

    background:rgba(0,0,0,0.10) !important;
}

.skin-blue .treeview-menu > li > a{

    color:rgba(255,255,255,0.76);

    padding:11px 15px 11px 45px;

    transition:.2s;
}

/* TREEVIEW HOVER FIX */

.skin-blue .treeview-menu > li:hover > a{

    background:rgba(255,255,255,0.08) !important;

    color:#ffffff !important;
}

/* COLLAPSED SIDEBAR HOVER FIX */

.sidebar-mini.sidebar-collapse .sidebar-menu > li:hover > a > span,
.sidebar-mini.sidebar-collapse .sidebar-menu > li:hover > .treeview-menu{

    background:#1f425f !important;

    color:#ffffff !important;
}

/* =========================================================
   USER PANEL
========================================================= */

.user-panel{

    padding:22px 15px;
}

.user-panel > .info{

    padding-top:8px;
}

.user-panel > .info > p{

    color:#ffffff;

    font-size:14px;

    font-weight:600;
}

.user-panel > .info > a{

    color:rgba(255,255,255,0.72);

    font-size:12px;
}

/* =========================================================
   BOXES / CARDS
========================================================= */

.box{

    border:none;

    border-radius:18px;

    overflow:hidden;

    background:#ffffff;

    box-shadow:
        0 8px 25px rgba(17,24,39,0.05);

    transition:.20s;
}

.box:hover{

    transform:translateY(-1px);

    box-shadow:
        0 12px 28px rgba(17,24,39,0.08);
}

.box-header{

    border-bottom:1px solid #eef2f7;

    padding:18px 22px;

    background:#ffffff;
}

.box-title{

    font-size:17px;

    font-weight:700;

    color:#183b56;
}

.box-body{

    padding:22px;
}

/* =========================================================
   TABLES  (FIXED NOT TOO WHITE)
========================================================= */

.table{

    margin-bottom:0;

    border-collapse:separate;

    border-spacing:0 8px;
}

/* HEADER */

.table > thead > tr > th{

    border:none !important;

    background:#e9f1f8;

    color:#446176;

    font-size:12px;

    text-transform:uppercase;

    letter-spacing:1px;

    font-weight:700;

    padding:14px 12px;
}

/* ROW */

.table > tbody > tr{

    background:#fdfefe;

    box-shadow:
        0 2px 6px rgba(0,0,0,0.03);

    transition:.2s;
}

/* HOVER */

.table > tbody > tr:hover{

    background:#f2f8fd;

    transform:none;

    box-shadow:
        0 6px 14px rgba(0,0,0,0.05);
}

/* CELL */

.table > tbody > tr > td{

    border-top:none !important;

    padding:15px 12px;

    vertical-align:middle;

    font-size:13px;

    color:#32475b;

    background:#ffffff;
}

/* ROUNDED */

.table > tbody > tr > td:first-child{

    border-top-left-radius:12px;
    border-bottom-left-radius:12px;
}

.table > tbody > tr > td:last-child{

    border-top-right-radius:12px;
    border-bottom-right-radius:12px;
}

/* =========================================================
   BUTTONS
========================================================= */

.btn{

    border:none !important;

    border-radius:12px !important;

    font-weight:600;

    font-size:12px;

    padding:9px 15px;

    transition:.20s;

    letter-spacing:.3px;
}

.btn-primary{

    background:
        linear-gradient(
            135deg,
            #36a2eb,
            #2196f3
        ) !important;

    box-shadow:
        0 6px 14px rgba(33,150,243,0.16);
}

.btn-success{

    background:
        linear-gradient(
            135deg,
            #22c55e,
            #16a34a
        ) !important;

    box-shadow:
        0 6px 14px rgba(34,197,94,0.16);
}

.btn-danger{

    background:
        linear-gradient(
            135deg,
            #ef4444,
            #dc2626
        ) !important;

    box-shadow:
        0 6px 14px rgba(239,68,68,0.16);
}

.btn-warning{

    background:
        linear-gradient(
            135deg,
            #f59e0b,
            #d97706
        ) !important;

    color:#fff !important;
}

.btn-default{

    background:#edf2f7 !important;

    color:#32475b !important;
}

.btn:hover{

    transform:translateY(-1px);

    opacity:.96;
}

/* BUTTON SPACING */

td .btn{

    margin-right:6px;

    margin-bottom:6px;

    min-width:74px;
}

/* =========================================================
   FORMS
========================================================= */

.form-control{

    height:44px;

    border-radius:12px;

    border:1px solid #dde6f0;

    box-shadow:none;

    font-size:13px;

    background:#fbfdff;
}

.form-control:focus{

    border-color:#36a2eb;

    box-shadow:
        0 0 0 4px rgba(54,162,235,0.10);

    background:#fff;
}

/* =========================================================
   MODALS
========================================================= */

.modal-content{

    border:none;

    border-radius:22px;

    overflow:hidden;

    box-shadow:
        0 16px 45px rgba(0,0,0,0.14);
}

.modal-header{

    border:none;

    padding:20px 25px;

    background:
        linear-gradient(
            135deg,
            #1d4b73,
            #36a2eb
        );

    color:#fff;
}

.modal-title{

    font-weight:700;
}

.modal-body{

    padding:25px;
}

/* =========================================================
   SMALL BOXES
========================================================= */

.small-box{

    border-radius:22px;

    overflow:hidden;

    color:#fff;

    box-shadow:
        0 10px 22px rgba(0,0,0,0.06);
}

.small-box .icon{

    top:12px;

    font-size:72px;

    opacity:0.10;
}

.small-box h3{

    font-size:30px;

    font-weight:700;
}

/* =========================================================
   SCROLLBAR
========================================================= */

::-webkit-scrollbar{

    width:8px;
}

::-webkit-scrollbar-thumb{

    background:#b7c7d8;

    border-radius:20px;
}

::-webkit-scrollbar-track{

    background:#edf2f7;
}

</style>

</head>