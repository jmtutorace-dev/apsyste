<!-- Add -->
<div class="modal fade" id="addnew">
    <div class="modal-dialog">
        <div class="modal-content">
          	<div class="modal-header">
            	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
              		<span aria-hidden="true">&times;</span></button>
            	<h4 class="modal-title"><b>Add Employee</b></h4>
          	</div>
          	<div class="modal-body">
            	<form class="form-horizontal" method="POST" action="employee_add.php" enctype="multipart/form-data">
          		  <div class="form-group">
                  	<label for="firstname" class="col-sm-3 control-label">Firstname</label>

                  	<div class="col-sm-9">
                    	<input type="text" class="form-control" id="firstname" name="firstname" required>
                  	</div>
                </div>
                <div class="form-group">
                  	<label for="lastname" class="col-sm-3 control-label">Lastname</label>

                  	<div class="col-sm-9">
                    	<input type="text" class="form-control" id="lastname" name="lastname" required>
                  	</div>
                </div>
                <div class="form-group">
                  	<label for="address" class="col-sm-3 control-label">Address</label>

                  	<div class="col-sm-9">
                      <textarea class="form-control" name="address" id="address"></textarea>
                  	</div>
                </div>
                <div class="form-group">
                  	<label for="datepicker_add" class="col-sm-3 control-label">Birthdate</label>

                  	<div class="col-sm-9"> 
                      <div class="date">
                        <input type="text" class="form-control" id="datepicker_add" name="birthdate">
                      </div>
                  	</div>
                </div>
                <div class="form-group">
                    <label for="contact" class="col-sm-3 control-label">Contact Info</label>

                    <div class="col-sm-9">
                      <input type="text" class="form-control" id="contact" name="contact">
                    </div>
                </div>
                <div class="form-group">
                    <label for="gender" class="col-sm-3 control-label">Gender</label>

                    <div class="col-sm-9"> 
                      <select class="form-control" name="gender" id="gender" required>
                        <option value="" selected>- Select -</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                      </select>
                    </div>
                </div>
                 <div class="form-group">
                    <div class="form-group">
    <label class="col-sm-3 control-label">Position</label>

    <div class="col-sm-9">
        <select class="form-control" id="position_name">

    <option value="">
        - Select Position -
    </option>

    <?php

    $sql = "
        SELECT DISTINCT description
        FROM position
        ORDER BY description ASC
    ";

    $query = $conn->query($sql);

    while($row = $query->fetch_assoc()){

        echo "
            <option value='".$row['description']."'>
                ".$row['description']."
            </option>
        ";
    }

    ?>
    

</select>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">
        Salary Package
    </label>

    <div class="col-sm-9">

        <select class="form-control"
                name="position"
                id="salary_package"
                required>

            <option value="">
                Select Position First
            </option>

        </select>

    </div>
</div>

                <!-- ===================== -->
                <!-- ONLY ADDITION STARTS -->
                <!-- ===================== -->

                <div class="form-group">
                    <label class="col-sm-3 control-label">Deductions</label>

                    <div class="col-sm-9">

                        <?php
                        $dsql = "SELECT * FROM deductions ORDER BY description ASC";
                        $dquery = $conn->query($dsql);

                        while($drow = $dquery->fetch_assoc()){
                            echo "
                                <div class='checkbox'>
                                    <label>
                                        <input type='checkbox' name='deductions[]' value='".$drow['id']."'>
                                        ".$drow['description']." (".$drow['amount']." ".$drow['type'].")
                                    </label>
                                </div>
                            ";
                        }
                        ?>

                    </div>
                </div>

                <!-- ===================== -->
                <!-- ONLY ADDITION ENDS -->
                <!-- ===================== -->
                <div class="form-group">
    <label for="department" class="col-sm-3 control-label">Department</label>

        <div class="col-sm-9">
            <select class="form-control" name="department" id="department" required>
                <option value="" selected>- Select Department -</option>
                <option value="Accounting">Accounting</option>
                <option value="Admitting">Admitting</option>
                <option value="Billing">Billing</option>
                <option value="Biomed">Biomed</option>
                <option value="Cafeteria">Cafeteria</option>
                <option value="Cashiering">Cashiering</option>
                <option value="Cardiovascular">Cardiovascular</option>
                <option value="Central Supply Room">Central Supply Room</option>
                <option value="Credit And Collection">Credit And Collection</option>
                <option value="Customer Service">Customer Service</option>
                <option value="Dietary">Dietary</option>
                <option value="Facilities Management">Facilities Management</option>
                <option value="Finance">Finance</option>
                <option value="HESU">HESU</option>
                <option value="HMO">HMO</option>
                <option value="Housekeeping / Linen And Laundry">Housekeeping / Linen And Laundry</option>
                <option value="Human Resource">Human Resource</option>
                <option value="Imaging">Imaging</option>
                <option value="Information And Communication">Information And Communication</option>
                <option value="Marketing">Marketing</option>
                <option value="Medical Records">Medical Records</option>
                <option value="Neuroscience">Neuroscience</option>
                <option value="Nursing Services">Nursing Services</option>
                <option value="Office Of The Corporate Secretary">Office Of The Corporate Secretary</option>
                <option value="Office Of The Hospital Administrator">Office Of The Hospital Administrator</option>
                <option value="Office Of The Medical Director">Office Of The Medical Director</option>
                <option value="Office Of The President">Office Of The President</option>
                <option value="Orthopedics">Orthopedics</option>
                <option value="Pathology">Pathology</option>
                <option value="Pharmacy">Pharmacy</option>
                <option value="Philhealth">Philhealth</option>
                <option value="Property Management">Property Management</option>
                <option value="Pulmonary">Pulmonary</option>
                <option value="Purchasing">Purchasing</option>
                <option value="Quality Assurance">Quality Assurance</option>
                <option value="Security">Security</option>
                <option value="Sleep Laboratory">Sleep Laboratory</option>
                <option value="Social Services">Social Services</option>
                <option value="Warehousing">Warehousing</option>
                <option value="Woundcare">Woundcare</option>
            </select>
        </div>
    </div>  
                <div class="form-group">
                    <label for="schedule" class="col-sm-3 control-label">Schedule</label>

                    <div class="col-sm-9">
                      <select class="form-control" id="schedule" name="schedule" required>
                        <option value="" selected>- Select -</option>
                        <?php
                          $sql = "SELECT * FROM schedules";
                          $query = $conn->query($sql);
                          while($srow = $query->fetch_assoc()){
                            echo "
                              <option value='".$srow['id']."'>".$srow['time_in'].' - '.$srow['time_out']."</option>
                            ";
                          }
                        ?>
                      </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="photo" class="col-sm-3 control-label">Photo</label>

                    <div class="col-sm-9">
                      <input type="file" name="photo" id="photo">
                    </div>
                </div>
          	</div>
          	<div class="modal-footer">
            	<button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
            	<button type="submit" class="btn btn-primary btn-flat" name="add"><i class="fa fa-save"></i> Save</button>
            	</form>
          	</div>
        </div>
    </div>
</div>

<!-- Edit -->
<div class="modal fade" id="edit">
    <div class="modal-dialog">
        <div class="modal-content">
          	<div class="modal-header">
            	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
              		<span aria-hidden="true">&times;</span></button>
            	<h4 class="modal-title"><b><span class="employee_id"></span></b></h4>
          	</div>
          	<div class="modal-body">
            	<form class="form-horizontal" method="POST" action="employee_edit.php">
            		<input type="hidden" class="empid" name="id">
                <div class="form-group">
                    <label for="edit_firstname" class="col-sm-3 control-label">Firstname</label>

                    <div class="col-sm-9">
                      <input type="text" class="form-control" id="edit_firstname" name="firstname">
                    </div>
                </div>
                <div class="form-group">
                    <label for="edit_lastname" class="col-sm-3 control-label">Lastname</label>

                    <div class="col-sm-9">
                      <input type="text" class="form-control" id="edit_lastname" name="lastname">
                    </div>
                </div>
                <div class="form-group">
                    <label for="edit_address" class="col-sm-3 control-label">Address</label>

                    <div class="col-sm-9">
                      <textarea class="form-control" name="address" id="edit_address"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="datepicker_edit" class="col-sm-3 control-label">Birthdate</label>

                    <div class="col-sm-9"> 
                      <div class="date">
                        <input type="text" class="form-control" id="datepicker_edit" name="birthdate">
                      </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="edit_contact" class="col-sm-3 control-label">Contact Info</label>

                    <div class="col-sm-9">
                      <input type="text" class="form-control" id="edit_contact" name="contact">
                    </div>
                </div>
                <div class="form-group">
                    <label for="edit_gender" class="col-sm-3 control-label">Gender</label>

                    <div class="col-sm-9"> 
                      <select class="form-control" name="gender" id="edit_gender">
                        <option selected id="gender_val"></option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                      </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="edit_position" class="col-sm-3 control-label">Position</label>

                    <div class="col-sm-9">
                      <select class="form-control" name="position" id="edit_position">
                        <option selected id="position_val"></option>
                        <?php
$sql = "SELECT * FROM position ORDER BY description ASC, rate ASC";
$query = $conn->query($sql);

while($prow = $query->fetch_assoc()){

    echo "
      <option value='".$prow['id']."'>
        ".$prow['description']." - ₱".number_format($prow['rate'],2)."
      </option>
    ";
}
?>
                      </select>
                    </div>
                </div>
                <div class="form-group">
    <label class="col-sm-3 control-label">Deductions</label>
    <div class="col-sm-9" id="deduction_container">
        <!-- CHECKBOXES WILL LOAD HERE -->
    </div>
</div>
                <div class="form-group">
    <label for="edit_department" class="col-sm-3 control-label">Department</label>

    <div class="col-sm-9">
        <select class="form-control" name="department" id="edit_department">
            <option selected id="department_val"></option>

            <option value="Accounting">Accounting</option>
            <option value="Admitting">Admitting</option>
            <option value="Billing">Billing</option>
            <option value="Biomed">Biomed</option>
            <option value="Cafeteria">Cafeteria</option>
            <option value="Cashiering">Cashiering</option>
            <option value="Cardiovascular">Cardiovascular</option>
            <option value="Central Supply Room">Central Supply Room</option>
            <option value="Credit And Collection">Credit And Collection</option>
            <option value="Customer Service">Customer Service</option>
            <option value="Dietary">Dietary</option>
            <option value="Facilities Management">Facilities Management</option>
            <option value="Finance">Finance</option>
            <option value="HESU">HESU</option>
            <option value="HMO">HMO</option>
            <option value="Housekeeping / Linen And Laundry">Housekeeping / Linen And Laundry</option>
            <option value="Human Resource">Human Resource</option>
            <option value="Imaging">Imaging</option>
            <option value="Information And Communication">Information And Communication</option>
            <option value="Marketing">Marketing</option>
            <option value="Medical Records">Medical Records</option>
            <option value="Neuroscience">Neuroscience</option>
            <option value="Nursing Services">Nursing Services</option>
            <option value="Office Of The Corporate Secretary">Office Of The Corporate Secretary</option>
            <option value="Office Of The Hospital Administrator">Office Of The Hospital Administrator</option>
            <option value="Office Of The Medical Director">Office Of The Medical Director</option>
            <option value="Office Of The President">Office Of The President</option>
            <option value="Orthopedics">Orthopedics</option>
            <option value="Pathology">Pathology</option>
            <option value="Pharmacy">Pharmacy</option>
            <option value="Philhealth">Philhealth</option>
            <option value="Property Management">Property Management</option>
            <option value="Pulmonary">Pulmonary</option>
            <option value="Purchasing">Purchasing</option>
            <option value="Quality Assurance">Quality Assurance</option>
            <option value="Security">Security</option>
            <option value="Sleep Laboratory">Sleep Laboratory</option>
            <option value="Social Services">Social Services</option>
            <option value="Warehousing">Warehousing</option>
            <option value="Woundcare">Woundcare</option>
        </select>
    </div>
</div>
                <div class="form-group">
                    <label for="edit_schedule" class="col-sm-3 control-label">Schedule</label>

                    <div class="col-sm-9">
                      <select class="form-control" id="edit_schedule" name="schedule">
                        <option selected id="schedule_val"></option>
                        <?php
                          $sql = "SELECT * FROM schedules";
                          $query = $conn->query($sql);
                          while($srow = $query->fetch_assoc()){
                            echo "
                              <option value='".$srow['id']."'>".$srow['time_in'].' - '.$srow['time_out']."</option>
                            ";
                          }
                        ?>
                      </select>
                    </div>
                </div>
          	</div>
          	<div class="modal-footer">
            	<button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
            	<button type="submit" class="btn btn-success btn-flat" name="edit"><i class="fa fa-check-square-o"></i> Update</button>
            	</form>
          	</div>
        </div>
    </div>
</div>

<!-- Delete -->
<div class="modal fade" id="delete">
    <div class="modal-dialog">
        <div class="modal-content">
          	<div class="modal-header">
            	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
              		<span aria-hidden="true">&times;</span></button>
            	<h4 class="modal-title"><b><span class="employee_id"></span></b></h4>
          	</div>
          	<div class="modal-body">
            	<form class="form-horizontal" method="POST" action="employee_delete.php">
            		<input type="hidden" class="empid" name="id">
            		<div class="text-center">
	                	<p>DELETE EMPLOYEE</p>
	                	<h2 class="bold del_employee_name"></h2>
	            	</div>
          	</div>
          	<div class="modal-footer">
            	<button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
            	<button type="submit" class="btn btn-danger btn-flat" name="delete"><i class="fa fa-trash"></i> Delete</button>
            	</form>
          	</div>
        </div>
    </div>
</div>

<!-- VIEW EMPLOYEE -->
<div class="modal fade" id="view_employee">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><b>Employee Details</b></h4>
      </div>

      <div class="modal-body">

        <div class="text-center">
          <img id="view_photo" src="../images/profile.jpg" width="100" height="100" class="img-circle">
          <h3 id="view_name"></h3>
          <p id="view_employee_id"></p>
        </div>

        <hr>

        <p><b>Address:</b> <span id="view_address"></span></p>
        <p><b>Birthdate:</b> <span id="view_birthdate"></span></p>
        <p><b>Contact:</b> <span id="view_contact"></span></p>
        <p><b>Gender:</b> <span id="view_gender"></span></p>
        <p><b>Position:</b> <span id="view_position"></span></p>
        <p><b>Benefits:</b><br><span id="view_benefits"></span></p>
        <p><b>Department:</b> <span id="view_department"></span></p>
        <p><b>Schedule:</b> <span id="view_schedule"></span></p>

      </div>

      <div class="modal-footer">
        <button class="btn btn-default" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

<!-- Update Photo -->
<div class="modal fade" id="edit_photo">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><b><span class="del_employee_name"></span></b></h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" method="POST" action="employee_edit_photo.php" enctype="multipart/form-data">
                <input type="hidden" class="empid" name="id">
                <div class="form-group">
                    <label for="photo" class="col-sm-3 control-label">Photo</label>

                    <div class="col-sm-9">
                      <input type="file" id="photo" name="photo" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
              <button type="submit" class="btn btn-success btn-flat" name="upload"><i class="fa fa-check-square-o"></i> Update</button>
              </form>
            </div>
        </div>
    </div>
</div>    

