<?php
require_once "../../lib/db_function.php";

$fines = returnAllData($db, "SELECT a.id, a.fine_amount, a.date, a.status,
                            b.fname, b.lname, c.name AS fine_name, d.name AS user_name
                            FROM special_fines AS a
                            INNER JOIN member AS b ON a.member_id = b.id
                            INNER JOIN fine_types AS c ON a.fine_type_id = c.id
                            INNER JOIN users AS d ON a.user_id = d.id
                            WHERE a.status IN(?,?,?,?)", ["Active", "Pending", "Rejected", "Accepted"]);

if (count($fines) > 0): ?>
    <div class="row">
        <div class="col-sm-12">
            <table class="table table-striped" id="fines_table">
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
                    <?php foreach ($fines as $fine): ?>
                        <tr>
                            <td><?= htmlspecialchars($fine['date']) ?></td>
                            <td><?= htmlspecialchars($fine['fine_name']) ?></td>
                            <td><?= number_format($fine['fine_amount']) ?> Rwf</td>
                            <td><?= htmlspecialchars($fine['fname'] . ' ' . $fine['lname']) ?></td>
                            <td>
                                <span class="label label-<?=
                                    $fine['status'] == 'Active' ? 'primary' :
                                    ($fine['status'] == 'Accepted' ? 'success' :
                                        ($fine['status'] == 'Pending' ? 'warning' :
                                            ($fine['status'] == 'Rejected' ? 'danger' : 'default')))
                                    ?>">
                                    <?= htmlspecialchars($fine['status']) ?>
                                </span>


                            </td>
                            <td>
                                <?php if ($fine['status'] == 'Active'): ?>
                                    <div class="btn-group">
                                        <button class="btn btn-xs btn-primary edit-fine" data-id="<?= $fine['id'] ?>">
                                            <i class="fa fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-xs btn-danger delete-fine" data-id="<?= $fine['id'] ?>">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">No action

                                    </span>
                                <?php endif; ?>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function () {
            // Initialize DataTable
            $('#fines_table').DataTable();

            // Edit Fine Handler
            $(document).on('click', '.edit-fine', function () {
                var fine_id = $(this).data('id');
                var clicked = $(this);
                var old_html = clicked.html();

                clicked.html('<i class="fa fa-spinner fa-spin"></i> Loading...');

                $("#modal_member").find(".modal-content").load("fines/edit.php?id=" + fine_id, function () {
                    clicked.html(old_html);
                    $("#modal_member").modal("show");
                }).fail(function () {
                    clicked.html(old_html);
                    toastr.error("Failed to load edit form");
                });
            });

            // Delete Fine Handler
            $(document).off('click', '.delete-fine').on('click', '.delete-fine', function () {
                var fine_id = $(this).data('id');
                var clicked = $(this);
                var old_html = clicked.html();

                if (confirm("Are you sure you want to delete this fine?")) {
                    clicked.html('<i class="fa fa-spinner fa-spin"></i> Deleting...');

                    $.ajax({
                        url: 'fines/delete-fine.php',
                        type: 'POST',
                        dataType: 'json',
                        data: { id: fine_id },
                        success: function (response) {
                            if (response.status) {
                                toastr.success(response.message);
                                clicked.closest("tr").fadeOut(500, function () {
                                    $(this).remove(); // Smooth row removal
                                });
                            } else {
                                toastr.error(response.message);
                                clicked.html(old_html);
                            }
                        },
                        error: function (xhr) {
                            toastr.error("Error: " + (xhr.responseJSON?.message || "Request failed"));
                            clicked.html(old_html);
                        }
                    });
                }
            });
        });
    </script>

<?php else: ?>
    <div class="alert alert-warning">
        No unpaid fines found
    </div>
<?php endif; ?>