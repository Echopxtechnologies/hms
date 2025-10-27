<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
/* Sleek View Page Styling */
.view-page-header {
    background: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    padding: 20px 30px;
    margin-bottom: 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.view-page-header h4 {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
    color: #2c3e50;
}

.view-page-header h4 i {
    margin-right: 8px;
    color: #7f8c8d;
}

.view-card {
    background: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    margin-bottom: 25px;
    overflow: hidden;
}

.view-card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #e0e0e0;
    padding: 15px 25px;
}

.view-card-title {
    margin: 0;
    font-size: 15px;
    font-weight: 600;
    color: #2c3e50;
}

.view-card-title i {
    margin-right: 8px;
    color: #7f8c8d;
}

.view-card-body {
    padding: 25px;
}

.info-row {
    display: flex;
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    flex: 0 0 35%;
    font-weight: 600;
    color: #495057;
    font-size: 14px;
}

.info-value {
    flex: 1;
    color: #6c757d;
    font-size: 14px;
}

.info-value strong {
    color: #2c3e50;
}

.badge-view {
    display: inline-block;
    padding: 6px 14px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.badge-view-active {
    background: #e8f5e9;
    color: #2e7d32;
}

.badge-view-inactive {
    background: #ffebee;
    color: #c62828;
}

.badge-view-role {
    background: #e3f2fd;
    color: #1565c0;
}

.view-actions {
    background: #f8f9fa;
    border-top: 1px solid #e0e0e0;
    padding: 20px 25px;
    text-align: right;
}

.btn-view {
    padding: 10px 20px;
    font-size: 14px;
    font-weight: 500;
    border-radius: 6px;
    transition: all 0.2s ease;
    margin-left: 10px;
    text-decoration: none;
    display: inline-block;
}

.btn-back {
    background: #ffffff;
    border: 1px solid #dee2e6;
    color: #6c757d;
}

.btn-back:hover {
    background: #f8f9fa;
    border-color: #adb5bd;
    color: #495057;
    text-decoration: none;
}

.btn-edit {
    background: #2c3e50;
    border: 1px solid #2c3e50;
    color: #ffffff;
}

.btn-edit:hover {
    background: #1a252f;
    border-color: #1a252f;
    color: #ffffff;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(44, 62, 80, 0.3);
}

.btn-delete {
    background: #ffffff;
    border: 1px solid #e74c3c;
    color: #e74c3c;
}

.btn-delete:hover {
    background: #e74c3c;
    border-color: #e74c3c;
    color: #ffffff;
    text-decoration: none;
}

.link-clean {
    color: #6c757d;
    text-decoration: none;
    transition: color 0.2s ease;
}

.link-clean:hover {
    color: #2c3e50;
    text-decoration: none;
}

.text-muted-custom {
    color: #bdc3c7;
    font-style: italic;
}

.system-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.system-info-item {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 6px;
    border: 1px solid #e9ecef;
}

.system-info-label {
    font-size: 12px;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 5px;
}

.system-info-value {
    font-size: 15px;
    font-weight: 600;
    color: #2c3e50;
}
</style>

<div id="wrapper">
    <div class="content">
        <!-- Page Header -->
        <div class="row">
            <div class="col-md-12">
                <div class="view-page-header">
                    <h4><i class="fa fa-user"></i>User Details</h4>
                    <a href="<?php echo admin_url('hospital_management/users'); ?>" class="btn-view btn-back">
                        <i class="fa fa-arrow-left"></i> Back to Users
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Basic & Contact Information -->
        <div class="row">
            <!-- Basic Information -->
            <div class="col-md-6">
                <div class="view-card">
                    <div class="view-card-header">
                        <h5 class="view-card-title"><i class="fa fa-info-circle"></i>Basic Information</h5>
                    </div>
                    <div class="view-card-body">
                        <div class="info-row">
                            <div class="info-label">User ID</div>
                            <div class="info-value"><strong>#<?php echo $user->id; ?></strong></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Full Name</div>
                            <div class="info-value">
                                <strong><?php echo $user->first_name . ' ' . $user->last_name; ?></strong>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Email Address</div>
                            <div class="info-value">
                                <a href="mailto:<?php echo $user->email; ?>" class="link-clean">
                                    <i class="fa fa-envelope-o"></i> <?php echo $user->email; ?>
                                </a>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Role</div>
                            <div class="info-value">
                                <span class="badge-view badge-view-role">
                                    <?php echo $user->role_name; ?>
                                </span>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Account Status</div>
                            <div class="info-value">
                                <?php if ($user->active == 1) { ?>
                                    <span class="badge-view badge-view-active">
                                        <i class="fa fa-check-circle"></i> Active
                                    </span>
                                <?php } else { ?>
                                    <span class="badge-view badge-view-inactive">
                                        <i class="fa fa-times-circle"></i> Inactive
                                    </span>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact Information -->
            <div class="col-md-6">
                <div class="view-card">
                    <div class="view-card-header">
                        <h5 class="view-card-title"><i class="fa fa-phone"></i>Contact Information</h5>
                    </div>
                    <div class="view-card-body">
                        <div class="info-row">
                            <div class="info-label">Phone Number</div>
                            <div class="info-value">
                                <?php if (!empty($user->phone_number)) { ?>
                                    <a href="tel:<?php echo $user->phone_number; ?>" class="link-clean">
                                        <i class="fa fa-mobile"></i> <?php echo $user->phone_number; ?>
                                    </a>
                                <?php } else { ?>
                                    <span class="text-muted-custom">Not provided</span>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Landline Number</div>
                            <div class="info-value">
                                <?php if (!empty($user->landline_number)) { ?>
                                    <a href="tel:<?php echo $user->landline_number; ?>" class="link-clean">
                                        <i class="fa fa-phone"></i> <?php echo $user->landline_number; ?>
                                    </a>
                                <?php } else { ?>
                                    <span class="text-muted-custom">Not provided</span>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Address</div>
                            <div class="info-value">
                                <?php if (!empty($user->address)) { ?>
                                    <?php echo nl2br(htmlspecialchars($user->address)); ?>
                                <?php } else { ?>
                                    <span class="text-muted-custom">Not provided</span>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- System Information -->
        <div class="row">
            <div class="col-md-12">
                <div class="view-card">
                    <div class="view-card-header">
                        <h5 class="view-card-title"><i class="fa fa-clock-o"></i>System Information</h5>
                    </div>
                    <div class="view-card-body">
                        <div class="system-info-grid">
                            <div class="system-info-item">
                                <div class="system-info-label">Created Date</div>
                                <div class="system-info-value"><?php echo _dt($user->created_at); ?></div>
                            </div>
                            <div class="system-info-item">
                                <div class="system-info-label">Last Updated</div>
                                <div class="system-info-value"><?php echo _dt($user->updated_at); ?></div>
                            </div>
                            <div class="system-info-item">
                                <div class="system-info-label">Staff ID</div>
                                <div class="system-info-value">
                                    <?php if (!empty($user->staff_id)) { ?>
                                        #<?php echo $user->staff_id; ?>
                                    <?php } else { ?>
                                        <span class="text-muted-custom">Not linked</span>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="system-info-item">
                                <div class="system-info-label">Account Type</div>
                                <div class="system-info-value">
                                    <?php if ($user->active == 1) { ?>
                                        <span style="color: #2e7d32;">Active User</span>
                                    <?php } else { ?>
                                        <span style="color: #c62828;">Inactive User</span>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="row">
            <div class="col-md-12">
                <div class="view-card">
                    <div class="view-actions">
                        <a href="<?php echo admin_url('hospital_management/users'); ?>" class="btn-view btn-back">
                            <i class="fa fa-arrow-left"></i> Back to List
                        </a>
                        
                        <?php if (is_hospital_administrator() || has_permission('hospital_users', '', 'edit')) { ?>
                        <a href="<?php echo admin_url('hospital_management/manage_user/' . $user->id); ?>" class="btn-view btn-edit">
                            <i class="fa fa-pencil"></i> Edit User
                        </a>
                        <?php } ?>
                        
                        <?php if ((is_hospital_administrator() || has_permission('hospital_users', '', 'delete')) && $user->role_id != 6) { ?>
                        <a href="#" onclick="deleteUser(<?php echo $user->id; ?>); return false;" class="btn-view btn-delete">
                            <i class="fa fa-trash-o"></i> Delete User
                        </a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
function deleteUser(id) {
    if (confirm('Are you sure you want to delete this user?\n\nThis action cannot be undone.')) {
        $.ajax({
            url: admin_url + 'hospital_management/delete/' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    setTimeout(function() {
                        window.location.href = admin_url + 'hospital_management/users';
                    }, 1000);
                } else {
                    alert_float('danger', response.message);
                }
            },
            error: function() {
                alert_float('danger', 'An error occurred while deleting');
            }
        });
    }
}
</script>