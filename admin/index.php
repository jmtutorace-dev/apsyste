<?php
  session_start();

  if(isset($_SESSION['admin'])){
    header('location:home.php');
  }
?>

<?php include 'includes/header.php'; ?>

<body class="hold-transition login-page">

<style>

  body.login-page{
    margin: 0;
    padding: 0;
    overflow: hidden;
    font-family: 'Source Sans Pro', sans-serif;

    background:
      linear-gradient(
        135deg,
        #0f172a 0%,
        #172554 45%,
        #1e3a8a 100%
      );
  }

  /* =========================================
     BACKGROUND ANIMATION
  ========================================= */

  .bg-animation{
    position: fixed;
    width: 100%;
    height: 100%;
    overflow: hidden;
    top: 0;
    left: 0;
    z-index: 1;
  }

  .pulse{
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,0.04);
    animation: pulseAnim 10s infinite ease-in-out;
  }

  .pulse:nth-child(1){
    width: 300px;
    height: 300px;
    top: -80px;
    left: -80px;
  }

  .pulse:nth-child(2){
    width: 250px;
    height: 250px;
    bottom: -50px;
    right: -50px;
    animation-delay: 2s;
  }

  .pulse:nth-child(3){
    width: 180px;
    height: 180px;
    top: 40%;
    left: 70%;
    animation-delay: 4s;
  }

  @keyframes pulseAnim{

    0%{
      transform: scale(1);
      opacity: 0.3;
    }

    50%{
      transform: scale(1.2);
      opacity: 0.08;
    }

    100%{
      transform: scale(1);
      opacity: 0.3;
    }
  }

  /* =========================================
     HEARTBEAT LINE
  ========================================= */

  .heartbeat-line{
    position: absolute;
    bottom: 80px;
    width: 200%;
    height: 120px;
    opacity: 0.12;
    animation: moveLine 12s linear infinite;
  }

  @keyframes moveLine{

    0%{
      transform: translateX(0);
    }

    100%{
      transform: translateX(-50%);
    }
  }

  /* =========================================
     LOGIN BOX
  ========================================= */

  .login-box{
    width: 430px;
    position: relative;
    z-index: 5;
    margin: 4% auto;
  }

  .login-logo{
    text-align: center;
    margin-bottom: 20px;
    animation: fadeDown 1s ease;
  }

  @keyframes fadeDown{

    from{
      opacity: 0;
      transform: translateY(-30px);
    }

    to{
      opacity: 1;
      transform: translateY(0);
    }
  }

  .hospital-icon{
    width: 90px;
    height: 90px;
    border-radius: 50%;
    margin: 0 auto 18px auto;

    background:
      linear-gradient(
        135deg,
        #2563eb,
        #1d4ed8
      );

    display: flex;
    align-items: center;
    justify-content: center;

    color: white;
    font-size: 38px;

    box-shadow:
      0 12px 35px rgba(37,99,235,0.35);

    animation: floatIcon 4s ease-in-out infinite;
  }

  @keyframes floatIcon{

    0%{
      transform: translateY(0px);
    }

    50%{
      transform: translateY(-10px);
    }

    100%{
      transform: translateY(0px);
    }
  }

  .brand-title{
    color: white;
    font-size: 34px;
    font-weight: 700;
    letter-spacing: 1px;
  }

  .brand-subtitle{
    color: rgba(255,255,255,0.75);
    font-size: 15px;
    letter-spacing: 3px;
    margin-top: 6px;
  }

  /* =========================================
     CARD
  ========================================= */

  .login-box-body{

    background: rgba(255,255,255,0.97);

    border-radius: 22px;

    padding: 38px;

    box-shadow:
      0 15px 45px rgba(0,0,0,0.25);

    backdrop-filter: blur(10px);

    border-top: 6px solid #2563eb;

    animation: fadeUp 1s ease;
  }

  @keyframes fadeUp{

    from{
      opacity: 0;
      transform: translateY(30px);
    }

    to{
      opacity: 1;
      transform: translateY(0);
    }
  }

  .login-box-msg{
    font-size: 17px;
    color: #475569;
    margin-bottom: 28px;
    text-align: center;
    font-weight: 600;
  }

  /* =========================================
     INPUTS
  ========================================= */

  .form-control{
    height: 54px;
    border-radius: 14px;
    border: 1px solid #dbe2ea;
    font-size: 15px;
    padding-left: 18px;
    transition: 0.3s;
    box-shadow: none;
  }

  .form-control:focus{

    border-color: #2563eb;

    box-shadow:
      0 0 0 5px rgba(37,99,235,0.12);
  }

  .form-group{
    margin-bottom: 18px;
  }

  .form-control-feedback{
    top: 7px;
    right: 12px;
    color: #64748b;
  }

  /* =========================================
     BUTTON
  ========================================= */

  .btn-login{

    height: 54px;

    border: none;

    border-radius: 14px;

    font-size: 16px;

    font-weight: 700;

    color: white;

    background:
      linear-gradient(
        135deg,
        #2563eb,
        #1d4ed8
      );

    transition: 0.3s;
  }

  .btn-login:hover{

    transform: translateY(-2px);

    box-shadow:
      0 12px 25px rgba(37,99,235,0.35);

    background:
      linear-gradient(
        135deg,
        #1d4ed8,
        #1e40af
      );

    color: white;
  }

  .btn-login.loading{
    pointer-events: none;
    opacity: 0.9;
  }

  /* =========================================
     LOADING SCREEN
  ========================================= */

  .loading-screen{

    position: fixed;
    top: 0;
    left: 0;

    width: 100%;
    height: 100%;

    background:
      linear-gradient(
        135deg,
        #0f172a,
        #172554,
        #1e3a8a
      );

    z-index: 9999;

    display: none;

    align-items: center;
    justify-content: center;
    flex-direction: column;
  }

  .loading-heart{
    font-size: 70px;
    color: #ffffff;
    animation: heartbeat 1s infinite;
  }

  @keyframes heartbeat{

    0%{
      transform: scale(1);
    }

    25%{
      transform: scale(1.2);
    }

    40%{
      transform: scale(1);
    }

    60%{
      transform: scale(1.2);
    }

    100%{
      transform: scale(1);
    }
  }

  .loading-text{
    margin-top: 20px;
    color: white;
    font-size: 22px;
    font-weight: 700;
    letter-spacing: 2px;
  }

  .loading-sub{
    margin-top: 8px;
    color: rgba(255,255,255,0.7);
    font-size: 14px;
    letter-spacing: 1px;
  }

  /* =========================================
     FOOTER
  ========================================= */

  .login-footer{
    text-align: center;
    margin-top: 22px;
    color: rgba(255,255,255,0.7);
    font-size: 13px;
    letter-spacing: 1px;
  }

  /* =========================================
     ERROR
  ========================================= */

  .callout{
    border-radius: 12px;
    border: none;
    margin-top: 18px;
    font-weight: 600;
  }

  .callout-danger{
    background: #fee2e2 !important;
    color: #b91c1c !important;
  }

</style>

<!-- LOADING SCREEN -->
<div class="loading-screen" id="loadingScreen">

  <div class="loading-heart">
    <i class="fa fa-heartbeat"></i>
  </div>

  <div class="loading-text">
    LOADING...
  </div>

  <div class="loading-sub">
    Accessing ACE Medical Center System
  </div>

</div>

<!-- BACKGROUND -->
<div class="bg-animation">

  <div class="pulse"></div>
  <div class="pulse"></div>
  <div class="pulse"></div>

  <svg class="heartbeat-line" viewBox="0 0 1200 200">

    <polyline
      fill="none"
      stroke="white"
      stroke-width="4"

      points="
      0,100
      100,100
      150,100
      180,40
      210,160
      240,100
      350,100
      400,100
      430,50
      460,150
      490,100
      1200,100
      "
    />

  </svg>

</div>

<!-- LOGIN -->
<div class="login-box">

  <div class="login-logo">

    <div class="hospital-icon">
      <i class="fa fa-heartbeat"></i>
    </div>

    <div class="brand-title">
      ACE MEDICAL CENTER
    </div>

    <div class="brand-subtitle">
      HR & PAYROLL SYSTEM
    </div>

  </div>

  <div class="login-box-body">

    <p class="login-box-msg">
      Secure Administrator Login
    </p>

    <form
      action="login.php"
      method="POST"
      id="loginForm"
    >

      <div class="form-group has-feedback">

        <input
          type="text"
          class="form-control"
          name="username"
          placeholder="Enter Username"
          required
          autofocus
        >

        <span class="glyphicon glyphicon-user form-control-feedback"></span>

      </div>

      <div class="form-group has-feedback">

        <input
          type="password"
          class="form-control"
          name="password"
          placeholder="Enter Password"
          required
        >

        <span class="glyphicon glyphicon-lock form-control-feedback"></span>

      </div>

      <div class="row">

        <div class="col-xs-12">

          <button
            type="submit"
            class="btn btn-login btn-block"
            name="login"
            id="loginBtn"
          >

            <i class="fa fa-sign-in"></i>
            LOGIN TO SYSTEM

          </button>

        </div>

      </div>

    </form>

    <?php

      if(isset($_SESSION['error'])){

        echo "
          <div class='callout callout-danger text-center'>
            ".$_SESSION['error']."
          </div>
        ";

        unset($_SESSION['error']);
      }

    ?>

  </div>

  <div class="login-footer">

    ACE Medical Center © 2026

  </div>

</div>

<script>

  document
    .getElementById('loginForm')
    .addEventListener('submit', function(){

      document
        .getElementById('loadingScreen')
        .style.display = 'flex';

      document
        .getElementById('loginBtn')
        .classList.add('loading');

      document
        .getElementById('loginBtn')
        .innerHTML =
        '<i class="fa fa-spinner fa-spin"></i> Logging In...';

    });

</script>

<?php include 'includes/scripts.php' ?>

</body>
</html>