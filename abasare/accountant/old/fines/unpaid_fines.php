<?php
require_once "../../lib/db_function.php";

$fines = returnAllData($db, "SELECT   a.id,
                                      a.fine_amount,
                                      a.date,
                                      a.status,
                                      b.fname,
                                      b.lname,
                                      c.name AS fine_name
                                      FROM special_fines AS a
                                      INNER JOIN member AS b
                                      ON a.member_id = b.id
                                      INNER JOIN fine_types AS c
                                      ON a.fine_type_id = c.id
                                      WHERE a.status IN(?,?)
                                      ORDER BY a.date DESC
                                      ", ["Active", "Pending"]);

if (count($fines) > 0) {
?>
  <div class="row">
    <div class="col-sm-12">
      <table class="table table-striped table-bordered" id="fines_list_table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Member</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          foreach ($fines as $fine) {
            ?>
            <tr id="fine-row-<?= htmlspecialchars($fine['id']) ?>">
              <td><?= htmlspecialchars($fine['date']) ?></td>
              <td><?= htmlspecialchars($fine['fine_name']) ?></td>
              <td><?= number_format(htmlspecialchars($fine['fine_amount'])) ?></td>
              <td><?= htmlspecialchars($fine['fname'] . ' ' . $fine['lname']) ?></td>
              <td><?= htmlspecialchars($fine['status']) ?></td>
              <td>
                <div class="btn-group">
                  <!-- MODIFIED: Added text next to the icon -->
               <a href="fines/edit.php?id=<?= htmlspecialchars($fine['id']) ?>" class="btn btn-xs btn-warning open_box" title="Edit Fine">
                    <i class="fa fa-edit"></i> Edit
                  </a>  

                  <!-- MODIFIED: Added text next to the icon -->
                  <a href="fines/delete.php?id=<?= htmlspecialchars($fine['id']) ?>" onclick="return confirm('Are you sure you want to delete this fine? This action cannot be undone.');" class="btn btn-xs btn-danger" title="Delete Fine">
                    <i class="fa fa-trash"></i> Delete
                  </a>
                </div>
              </td>
            </tr>
          <?php
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
  <script type="text/javascript">
    $(document).ready(function() {
        $('#fines_list_table').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
        });
    });
  </script>
<?php
} else {
?>
  <div class="alert alert-warning text-center">
    <strong>No Unpaid Fines Found</strong>
  </div>
<?php
}
?>