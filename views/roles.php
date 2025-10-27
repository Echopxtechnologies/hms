<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
/* Minimal Role Management */
.role-header {
    background: #f9fafb;
    border: 1px solid #d5dce2;
    border-radius: 4px;
    padding: 20px 25px;
    margin-bottom: 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.role-header h4 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #333333;
}

.role-card {
    background: #ffffff;
    border: 1px solid #d5dce2;
    border-radius: 4px;
    padding: 25px;
    margin-bottom: 25px;
    transition: all 0.2s ease;
}

.role-card:hover {
    border-color: #333333;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.role-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #f0f0f0;
}

.role-name {
    font-size: 18px;
    font-weight: 600;
    color: #333333;
    margin: 0;
}

.role-count {
    background: #e9ecef;
    color: #333333;
    padding: 6px 14px;
    border-radius: 3px;
    font-size: 13px;
    font-weight: 600;
}

.role-info {
    display: flex;
    gap: 30px;
    margin-bottom: 20px;
}

.role-stat {
    flex: 1;
}

.role-stat-label {
    font-size: 12px;
    color: #6c757d;
    margin-bottom: 5px;
}

.role-stat-value {
    font-size: 20px;
    font-weight: 600;
    color: #333333;
}

.role-actions {
    padding-top: 15px;
    border-top: 1px solid #f0f0f0;
}
</style>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="role-header">
                    <h4><i class="fa fa-shield"></i> Role Management</h4>
                    <button class="btn btn-dark" data-toggle="modal" data-target="#createRoleModal">
                        <i class="fa fa-plus"></i> Create New Role
                    </button>
                </div>
            </div>
        </div>
        
        <div class="row" id="rolesContainer">
            <!-- Roles will be loaded here -->
        </div>
    </div>
</div>

<!-- Create Role Modal -->
<div class="modal fade" id="createRoleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
                <h4 class="modal-title"><i class="fa fa-plus"></i> Create New Role</h4>
            </div>
            <div class="modal-body">
                <form id="createRoleForm">
                    <div class="form-group">
                        <label for="role_name" class="control-label">
                            Role Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               id="role_name" 
                               name="role_name" 
                               class="form-control" 
                               placeholder="e.g., Doctor, Nurse, Receptionist"
                               required>
                        <span class="help-block">Enter a unique name for this role</span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-clean" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancel
                </button>
                <button type="button" id="saveRoleBtn" class="btn btn-dark">
                    <i class="fa fa-check"></i> Create Role
                </button>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
$(document).ready(function() {
    loadRoles();
    
    function loadRoles() {
        $.ajax({
            url: admin_url + 'hospital_management/get_roles',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    displayRoles(response.roles);
                } else {
                    $('#rolesContainer').html('<div class="col-md-12"><p style="text-align: center; color: #999;">No roles found</p></div>');
                }
            }
        });
    }
    
    function displayRoles(roles) {
        let html = '';
        
        $.each(roles, function(index, role) {
            if (role.roleid == 6) return true;
            
            html += `
                <div class="col-md-4">
                    <div class="role-card">
                        <div class="role-card-header">
                            <h5 class="role-name">${role.name}</h5>
                            <span class="role-count">${role.user_count || 0} Users</span>
                        </div>
                        
                        <div class="role-info">
                            <div class="role-stat">
                                <div class="role-stat-label">Role ID</div>
                                <div class="role-stat-value">#${role.roleid}</div>
                            </div>
                            <div class="role-stat">
                                <div class="role-stat-label">Status</div>
                                <div class="role-stat-value" style="color: #28a745;">
                                    <i class="fa fa-check"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="role-actions">
                            <a href="${admin_url}hospital_management/users?role=${role.roleid}" 
                               class="btn btn-outline-dark btn-block">
                                <i class="fa fa-users"></i> View Users
                            </a>
                        </div>
                    </div>
                </div>
            `;
        });
        
        $('#rolesContainer').html(html);
    }
    
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
        $btn.addClass('btn-loading').prop('disabled', true);
        
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
                    loadRoles();
                } else {
                    alert_float('danger', response.message);
                }
                $btn.removeClass('btn-loading').prop('disabled', false);
            },
            error: function() {
                alert_float('danger', 'An error occurred');
                $btn.removeClass('btn-loading').prop('disabled', false);
            }
        });
    });
});
</script>