<?php include 'includes/session.php'; ?>
<?php
  include 'includes/settings.php';
  $cfg = get_payroll_settings($conn);
  $days = array('0'=>'Sunday','1'=>'Monday','2'=>'Tuesday','3'=>'Wednesday','4'=>'Thursday','5'=>'Friday','6'=>'Saturday');
?>
<?php include 'includes/header.php'; ?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">

    <section class="content-header">
      <h1>Settings</h1>
      <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Settings</li>
      </ol>
    </section>

    <section class="content">

      <?php
        if(isset($_SESSION['success'])){
          echo "<div class='alert alert-success alert-dismissible'><button type='button' class='close' data-dismiss='alert'>&times;</button>".htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8')."</div>";
          unset($_SESSION['success']);
        }
        if(isset($_SESSION['error'])){
          echo "<div class='alert alert-danger alert-dismissible'><button type='button' class='close' data-dismiss='alert'>&times;</button>".htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8')."</div>";
          unset($_SESSION['error']);
        }
      ?>

      <div class="row">
        <div class="col-md-8 col-md-offset-2">
          <div class="box">
            <div class="box-header with-border"><h3 class="box-title">Company &amp; Payroll Settings</h3></div>
            <div class="box-body">

              <form action="settings_update.php" method="POST">

                <h4 class="text-muted">Company</h4>
                <div class="form-group">
                  <label>Company Name <small class="text-muted">(printed on payslips)</small></label>
                  <input type="text" name="company_name" class="form-control" maxlength="150"
                         value="<?php echo htmlspecialchars($cfg['company_name'], ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="form-group">
                  <label>Company Address</label>
                  <input type="text" name="company_address" class="form-control" maxlength="200"
                         value="<?php echo htmlspecialchars($cfg['company_address'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <hr>
                <h4 class="text-muted">Payroll Computation</h4>
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Rest Day <small class="text-muted">(not counted as an absence)</small></label>
                      <select name="rest_day" class="form-control">
                        <?php foreach($days as $val => $name){
                          $sel = ((string)$cfg['rest_day'] === (string)$val) ? 'selected' : '';
                          echo "<option value='".$val."' ".$sel.">".$name."</option>";
                        } ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Working Days / Month <small class="text-muted">(daily-rate basis)</small></label>
                      <input type="number" name="working_days" class="form-control" min="1" max="31"
                             value="<?php echo (int)$cfg['working_days']; ?>" required>
                    </div>
                  </div>
                </div>

                <hr>
                <h4 class="text-muted">13th Month Pay</h4>
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Release Mode</label>
                      <select name="thirteenth_mode" class="form-control">
                        <option value="full"  <?php echo ($cfg['thirteenth_mode']=='full')  ? 'selected' : ''; ?>>Full (Year End only)</option>
                        <option value="split" <?php echo ($cfg['thirteenth_mode']=='split') ? 'selected' : ''; ?>>Split (Midyear + Year End)</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Midyear Percentage <small class="text-muted">(when split)</small></label>
                      <input type="number" name="midyear_percentage" class="form-control" min="0" max="100"
                             value="<?php echo (int)$cfg['midyear_percentage']; ?>">
                    </div>
                  </div>
                </div>

                <button type="submit" name="save" class="btn btn-primary">
                  <i class="fa fa-save"></i> Save Settings
                </button>

              </form>

            </div>
          </div>
        </div>
      </div>

    </section>
  </div>

  <?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>
</body>
</html>