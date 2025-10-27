<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
/* Clean Minimal Design */
.content {
    background: #fafafa;
    padding: 20px;
}

.page-header {
    background: #ffffff;
    border-bottom: 2px solid #e0e0e0;
    padding: 20px 25px;
    margin: -20px -20px 25px -20px;
}

.page-header h4 {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
    color: #333;
    display: inline-block;
}

.page-header h4 i {
    color: #666;
    margin-right: 8px;
}

.btn-create-user {
    background: #333;
    color: #fff;
    border: none;
    padding: 8px 18px;
    border-radius: 3px;
    font-weight: 500;
    font-size: 14px;
    float: right;
    transition: background 0.2s;
}

.btn-create-user:hover {
    background: #000;
    color: #fff;
}

.table-wrapper {
    background: #ffffff;
    border: 1px solid #e0e0e0;
    margin-top: 20px;
}

/* DataTables Controls Spacing - FIXED */
.dataTables_wrapper {
    padding: 20px;
}

.dataTables_wrapper .dataTables_length {
    float: left;
    margin-bottom: 15px;
}

.dataTables_wrapper .dataTables_filter {
    float: right;
    margin-bottom: 15px;
    margin-right: 0;
}

.dataTables_wrapper .dataTables_filter input {
    margin-left: 10px;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    width: 300px;
    font-size: 14px;
}

.dataTables_wrapper .dataTables_filter input:focus {
    border-color: #333;
    outline: none;
}

.dataTables_wrapper .dataTables_length select {
    padding: 6px 30px 6px 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin: 0 10px;
    font-size: 14px;
}

.dataTables_wrapper .dataTables_info {
    float: left;
    padding-top: 15px;
    font-size: 13px;
    color: #666;
}

.dataTables_wrapper .dataTables_paginate {
    float: right;
    padding-top: 15px;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 6px 12px;
    margin-left: 4px;
    border: 1px solid #ddd;
    border-radius: 3px;
    background: #fff;
    color: #333;
    cursor: pointer;
    transition: all 0.2s;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #f5f5f5;
    border-color: #999;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #333;
    color: #fff;
    border-color: #333;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Clear floats */
.dataTables_wrapper::after {
    content: "";
    display: table;
    clear: both;
}

.clean-table {
    margin: 0;
    width: 100%;
}

.clean-table thead th {
    background: #fafafa;
    color: #666;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: none;
    padding: 15px;
    border-bottom: 1px solid #e0e0e0;
}

.clean-table tbody td {
    padding: 15px;
    font-size: 14px;
    color: #444;
    border: none;
    border-bottom: 1px solid #f5f5f5;
}

.clean-table tbody tr {
    transition: background 0.15s;
}

.clean-table tbody tr:hover {
    background: #fafafa;
}

.user-name-cell {
    position: relative;
}

.user-name-link {
    display: block;
    font-weight: 500;
    font-size: 14px;
    color: #333;
    text-decoration: none;
}

.user-name-link:hover {
    color: #000;
}

.user-actions {
    display: flex;
    gap: 12px;
    margin-top: 5px;
    opacity: 0;
    max-height: 0;
    overflow: hidden;
    transition: all 0.2s ease;
}

.clean-table tbody tr:hover .user-actions {
    opacity: 1;
    max-height: 30px;
}

.action-link {
    font-size: 12px;
    font-weight: 500;
    color: #999;
    text-decoration: none;
    transition: color 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.action-link:hover {
    color: #333;
}

.action-link i {
    font-size: 11px;
}

.action-link-delete:hover {
    color: #d32f2f;
}

.simple-badge {
    display: inline-block;
    padding: 3px 8px;
    font-size: 11px;
    font-weight: 500;
    border-radius: 2px;
}

.badge-active {
    background: #e8f5e9;
    color: #2e7d32;
}

.badge-inactive {
    background: #ffebee;
    color: #c62828;
}

.badge-role {
    background: #f5f5f5;
    color: #666;
}

.email-link,
.phone-link {
    color: #666;
    text-decoration: none;
}

.email-link:hover,
.phone-link:hover {
    color: #333;
    text-decoration: underline;
}

.id-text {
    font-weight: 600;
    color: #999;
    font-size: 13px;
}

.date-text {
    color: #999;
    font-size: 13px;
}

.header-flex {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

@media (max-width: 768px) {
    .header-flex {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .btn-create-user {
        float: none;
    }
    
    .dataTables_wrapper .dataTables_filter input {
        width: 100%;
        max-width: 100%;
    }
    
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        float: none;
        text-align: left;
        margin-bottom: 10px;
    }
}
</style>

<div id="wrapper">
    <div class="content">
        <div class="page-header">
            <div class="header-flex">
                <h4>
                    <i class="fa fa-users"></i> Hospital Users Management
                </h4>
                <?php if (is_hospital_administrator() || has_permission('hospital_users', '', 'create')) { ?>
                <a href="<?php echo admin_url('hospital_management/manage_user'); ?>" class="btn btn-create-user">
                    <i class="fa fa-plus"></i> Create New User
                </a>
                <?php } ?>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="table-wrapper">
                    <table class="table clean-table dt-table" id="users_table">
                        <thead>
                            <tr>
                                <th width="5%">ID</th>
                                <th width="25%">Name</th>
                                <th width="22%">Email</th>
                                <th width="12%">Role</th>
                                <th width="13%">Phone</th>
                                <th width="10%">Status</th>
                                <th width="13%">Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user) { ?>
                            <tr>
                                <td class="id-text"><?php echo htmlspecialchars($user->id, ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="user-name-cell">
                                    <a href="<?php echo admin_url('hospital_management/view/' . $user->id); ?>" class="user-name-link">
                                        <?php echo htmlspecialchars($user->first_name . ' ' . $user->last_name, ENT_QUOTES, 'UTF-8'); ?>
                                    </a>
                                    
                                    <div class="user-actions">
                                        <?php if (is_hospital_administrator() || has_permission('hospital_users', '', 'view')) { ?>
                                        <a href="<?php echo admin_url('hospital_management/view/' . $user->id); ?>" 
                                           class="action-link">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                        <?php } ?>
                                        
                                        <?php if (is_hospital_administrator() || has_permission('hospital_users', '', 'edit')) { ?>
                                        <a href="<?php echo admin_url('hospital_management/manage_user/' . $user->id); ?>" 
                                           class="action-link">
                                            <i class="fa fa-pencil"></i> Edit
                                        </a>
                                        <?php } ?>
                                        
                                        <?php if ((is_hospital_administrator() || has_permission('hospital_users', '', 'delete')) && $user->role_id != 6) { ?>
                                        <a href="#" 
                                           onclick="deleteUser(<?php echo $user->id; ?>); return false;" 
                                           class="action-link action-link-delete">
                                            <i class="fa fa-trash"></i> Delete
                                        </a>
                                        <?php } ?>
                                    </div>
                                </td>
                                <td>
                                    <a href="mailto:<?php echo htmlspecialchars($user->email, ENT_QUOTES, 'UTF-8'); ?>" class="email-link">
                                        <?php echo htmlspecialchars($user->email, ENT_QUOTES, 'UTF-8'); ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="simple-badge badge-role">
                                        <?php echo htmlspecialchars($user->role_name, ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($user->phone_number)) { ?>
                                        <a href="tel:<?php echo htmlspecialchars($user->phone_number, ENT_QUOTES, 'UTF-8'); ?>" class="phone-link">
                                            <?php echo htmlspecialchars($user->phone_number, ENT_QUOTES, 'UTF-8'); ?>
                                        </a>
                                    <?php } else { ?>
                                        <span style="color: #ccc;">—</span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <?php if ($user->active == 1) { ?>
                                        <span class="simple-badge badge-active">Active</span>
                                    <?php } else { ?>
                                        <span class="simple-badge badge-inactive">Inactive</span>
                                    <?php } ?>
                                </td>
                                <td class="date-text">
                                    <?php echo _dt($user->created_at); ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
$(document).ready(function() {
    // Initialize DataTable with proper styling
    $('#users_table').DataTable({
        "pageLength": 25,
        "order": [[0, "desc"]],
        "language": {
            "search": "Search:",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ users",
            "infoEmpty": "No users found",
            "infoFiltered": "(filtered from _MAX_ total users)",
            "zeroRecords": "No matching users found",
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            }
        },
        "dom": '<"top"lf>rt<"bottom"ip><"clear">'
    });
});

function deleteUser(id) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        $.ajax({
            url: admin_url + 'hospital_management/delete/' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    alert_float('danger', response.message);
                }
            },
            error: function() {
                alert_float('danger', 'An error occurred');
            }
        });
    }
}
</script>