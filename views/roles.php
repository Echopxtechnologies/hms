<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
.page-header {
    background: #f9fafb;
    border: 1px solid #d5dce2;
    border-radius: 4px;
    padding: 20px 25px;
    margin-bottom: 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.page-header h4 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #333333;
}

.role-users {
    max-width: 300px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.role-users .badge {
    margin-right: 5px;
    margin-bottom: 3px;
}

.action-btn {
    padding: 4px 8px;
    font-size: 12px;
}
</style>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="page-header">
                    <h4><i class="fa fa-shield"></i> Role Management</h4>
                    <button class="btn btn-dark" data-toggle="modal" data-target="#createRoleModal">
                        <i class="fa fa-plus"></i> Create New Role
                    </button>
                </div>

                <div class="panel_s">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover dt-table">
                                <thead>
                                    <tr>
                                        <th width="10%">Role ID</th>
                                        <th width="20%">Role Name</th>
                                        <th width="10%">Total Users</th>
                                        <th width="45%">Assigned Users</th>
                                        <th width="15%" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($roles)): ?>
                                        <?php foreach ($roles as $role): ?>
                                            <?php 
                                            // Skip Admin role
                                            if ($role['roleid'] == 1 || strtolower($role['name']) == 'admin') {
                                                continue;
                                            }
                                            ?>
                                            <tr>
                                                <td>
                                                    <strong class="text-primary">#<?php echo $role['roleid']; ?></strong>
                                                </td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($role['name']); ?></strong>
                                                </td>
                                                <td>
                                                    <span class="label label-default">
                                                        <?php echo isset($role['total_users']) ? $role['total_users'] : 0; ?> Users
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="role-users">
                                                        <?php if (!empty($role['users'])): ?>
                                                            <?php foreach ($role['users'] as $user): ?>
                                                                <span class="badge badge-info">
                                                                    <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                                                </span>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <span class="text-muted">No users assigned</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <a href="<?php echo admin_url('hospital_management/users?role=' . $role['roleid']); ?>" 
                                                       class="btn btn-default btn-xs action-btn" 
                                                       title="View Users">
                                                        <i class="fa fa-users"></i> View
                                                    </a>
                                                    
                                                    <?php if ($role['total_users'] == 0): ?>
                                                        <button class="btn btn-danger btn-xs action-btn btn-delete-role" 
                                                                data-id="<?php echo $role['roleid']; ?>"
                                                                data-name="<?php echo htmlspecialchars($role['name']); ?>"
                                                                title="Delete Role">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </button>
                                                    <?php else: ?>
                                                        <button class="btn btn-default btn-xs action-btn" 
                                                                disabled
                                                                title="Cannot delete role with assigned users">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">
                                                <i class="fa fa-info-circle"></i> No roles found
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Create Role Modal -->
<div class="modal fade" id="createRoleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title"><i class="fa fa-plus"></i> Create New Role</h4>
            </div>
            <div class="modal-body">
                <form id="createRoleForm">
                    <div class="form-group">
                        <label for="role_name" class="control-label">
                            Role Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="role_name" name="role_name" class="form-control" 
                               placeholder="e.g., Doctor, Nurse, Receptionist" required>
                        <span class="help-block">Enter a unique name for this role</span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancel
                </button>
                <button type="button" id="saveRoleBtn" class="btn btn-primary">
                    <i class="fa fa-check"></i> Create Role
                </button>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
$(document).ready(function() {
    
    // Create role
    $('#saveRoleBtn').on('click', function() {
        const roleName = $('#role_name').val().trim();
        
        if (!roleName) {
            alert_float('warning', 'Please enter a role name');
            return;
        }
        
        if (roleName.toLowerCase() === 'admin') {
            alert_float('danger', 'Cannot create Admin role');
            return;
        }
        
        const $btn = $(this);
        $btn.addClass('disabled').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Creating...');
        
        $.ajax({
            url: admin_url + 'hospital_management/create_role',
            type: 'POST',
            dataType: 'json',
            data: { role_name: roleName },
            success: function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    $('#createRoleModal').modal('hide');
                    $('#createRoleForm')[0].reset();
                    setTimeout(function() { location.reload(); }, 1000);
                } else {
                    alert_float('danger', response.message);
                    $btn.removeClass('disabled').prop('disabled', false).html('<i class="fa fa-check"></i> Create Role');
                }
            },
            error: function(xhr) {
                console.error('Create Role Error:', xhr);
                alert_float('danger', 'An error occurred while creating role');
                $btn.removeClass('disabled').prop('disabled', false).html('<i class="fa fa-check"></i> Create Role');
            }
        });
    });
    
    // Delete role
    $(document).on('click', '.btn-delete-role', function() {
        const roleId = $(this).data('id');
        const roleName = $(this).data('name');
        
        if (confirm('⚠️ WARNING: Delete role "' + roleName + '"?\n\nThis action cannot be undone.\n\nAre you sure?')) {
            $.ajax({
                url: admin_url + 'hospital_management/delete_role/' + roleId,
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert_float('success', response.message);
                        setTimeout(function() { location.reload(); }, 1000);
                    } else {
                        alert_float('danger', response.message);
                    }
                },
                error: function(xhr) {
                    console.error('Delete Role Error:', xhr);
                    alert_float('danger', 'An error occurred while deleting role');
                }
            });
        }
    });
});
</script>