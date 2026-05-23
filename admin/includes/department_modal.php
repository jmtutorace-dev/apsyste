<!-- ADD MODAL -->
<div class="modal fade" id="addnew" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <form action="department_add.php" method="POST">

        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Add Department</h4>
        </div>

        <div class="modal-body">
          <input type="text" name="name" class="form-control" placeholder="Department Name" required>
        </div>

        <div class="modal-footer">
          <button type="submit" name="add" class="btn btn-primary">
            Save
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="edit" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <form action="department_edit.php" method="POST">

        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Edit Department</h4>
        </div>

        <div class="modal-body">
          <input type="hidden" class="deptid" name="id">
          <input type="text" id="edit_name" name="name" class="form-control" required>
        </div>

        <div class="modal-footer">
          <button type="submit" name="edit" class="btn btn-success">
            Update
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

<!-- DELETE MODAL -->
<div class="modal fade" id="delete" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <form action="department_delete.php" method="POST">

        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Delete Department</h4>
        </div>

        <div class="modal-body">
          <input type="hidden" class="deptid" name="id">
          <p>Delete <b id="del_name"></b> ?</p>
        </div>

        <div class="modal-footer">
          <button type="submit" name="delete" class="btn btn-danger">
            Delete
          </button>
        </div>

      </form>

    </div>
  </div>
</div>