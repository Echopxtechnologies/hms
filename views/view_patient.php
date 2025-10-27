<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
.patient-view-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #ffffff;
    padding: 30px;
    border-radius: 8px 8px 0 0;
    margin-bottom: 0;
}

.patient-view-header h2 {
    margin: 0 0 10px 0;
    font-size: 28px;
    font-weight: 700;
}

.patient-number {
    font-size: 16px;
    opacity: 0.9;
}

.detail-section {
    background: #ffffff;
    border: 1px solid #e0e0e0;
    border-top: none;
    padding: 25px;
    margin-bottom: 0;
}

.detail-section:last-child {
    border-radius: 0 0 8px 8px;
    margin-bottom: 25px;
}

.section-title {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #f0f0f0;
}

.detail-row {
    display: flex;
    padding: 12px 0;
    border-bottom: 1px solid #f8f8f8;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    flex: 0 0 35%;
    font-weight: 600;
    color: #555;
}

.detail-value {
    flex: 1;
    color: #333;
}

.badge-custom {
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

.badge-success {
    background: #e8f5e9;
    color: #2e7d32;
}

.badge-warning {
    background: #fff3e0;
    color: #ef6c00;
}

.badge-info {
    background: #e3f2fd;
    color: #1976d2;
}

.action-buttons {
    padding: 20px 25px;
    background: #f8f9fa;
    border-radius: 0 0 8px 8px;
    text-align: right;
}

.btn-action {
    padding: 10px 20px;
    border-radius: 4px;
    text-decoration: none;
    transition: all 0.2s;
    margin-left: 10px;
}

.btn-edit {
    background: #333;
    color: #fff;
    border: 1px solid #333;
}

.btn-edit:hover {
    background: #000;
    color: #fff;
    text-decoration: none;
}

.btn-delete {
    background: #fff;
    color: #d32f2f;
    border: 1px solid #d32f2f;
}

.btn-delete:hover {
    background: #d32f2f;
    color: #fff;
    text-decoration: none;
}

.btn-back {
    background: #fff;
    color: #666;
    border: 1px solid #ddd;
}

.btn-back:hover {
    background: #f5f5f5;
    color: #333;
    text-decoration: none;
}
</style>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s" style="margin-bottom: 0;">
                    <!-- Patient Header -->
                    <div class="patient-view-header">
                        <h2><?php echo htmlspecialchars($patient->name); ?></h2>
                        <div class="patient-number">
                            <i class="fa fa-id-card"></i> Patient Number: <strong><?php echo $patient->patient_number; ?></strong>
                        </div>
                    </div>
                    
                    <!-- Basic Information -->
                    <div class="detail-section">
                        <h4 class="section-title"><i class="fa fa-user"></i> Basic Information</h4>
                        
                        <div class="detail-row">
                            <div class="detail-label">Gender</div>
                            <div class="detail-value"><?php echo ucfirst($patient->gender); ?></div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Date of Birth</div>
                            <div class="detail-value">
                                <?php echo $patient->dob ? date('d M Y', strtotime($patient->dob)) : 'Not provided'; ?>
                            </div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Age</div>
                            <div class="detail-value">
                                <?php echo $patient->age ? $patient->age . ' years' : 'Not provided'; ?>
                            </div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Patient Type</div>
                            <div class="detail-value">
                                <span class="badge-custom badge-info"><?php echo $patient->patient_type; ?></span>
                            </div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Mode</div>
                            <div class="detail-value">
                                <span class="badge-custom badge-info">
                                    <?php echo $patient->mode == 'appointment' ? 'Appointment' : 'Walk In'; ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">New/Existing</div>
                            <div class="detail-value">
                                <?php echo $patient->is_new_patient ? 'New Patient' : 'Existing Patient'; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contact Information -->
                    <div class="detail-section">
                        <h4 class="section-title"><i class="fa fa-phone"></i> Contact Information</h4>
                        
                        <div class="detail-row">
                            <div class="detail-label">Mobile Number</div>
                            <div class="detail-value">
                                <a href="tel:<?php echo $patient->mobile_number; ?>">
                                    <i class="fa fa-mobile"></i> <?php echo $patient->mobile_number; ?>
                                </a>
                            </div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Phone</div>
                            <div class="detail-value">
                                <?php if ($patient->phone) { ?>
                                    <a href="tel:<?php echo $patient->phone; ?>">
                                        <i class="fa fa-phone"></i> <?php echo $patient->phone; ?>
                                    </a>
                                <?php } else { ?>
                                    <span style="color: #999;">Not provided</span>
                                <?php } ?>
                            </div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Email</div>
                            <div class="detail-value">
                                <?php if ($patient->email) { ?>
                                    <a href="mailto:<?php echo $patient->email; ?>">
                                        <i class="fa fa-envelope"></i> <?php echo $patient->email; ?>
                                    </a>
                                <?php } else { ?>
                                    <span style="color: #999;">Not provided</span>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Address Information -->
                    <div class="detail-section">
                        <h4 class="section-title"><i class="fa fa-map-marker"></i> Address Information</h4>
                        
                        <div class="detail-row">
                            <div class="detail-label">Address</div>
                            <div class="detail-value">
                                <?php echo $patient->address ? nl2br(htmlspecialchars($patient->address)) : '<span style="color: #999;">Not provided</span>'; ?>
                            </div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Landmark</div>
                            <div class="detail-value">
                                <?php echo $patient->address_landmark ? htmlspecialchars($patient->address_landmark) : '<span style="color: #999;">Not provided</span>'; ?>
                            </div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">City</div>
                            <div class="detail-value">
                                <?php echo $patient->city ? htmlspecialchars($patient->city) : '<span style="color: #999;">Not provided</span>'; ?>
                            </div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">State</div>
                            <div class="detail-value">
                                <?php echo $patient->state ? htmlspecialchars($patient->state) : '<span style="color: #999;">Not provided</span>'; ?>
                            </div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Pincode</div>
                            <div class="detail-value">
                                <?php echo $patient->pincode ? htmlspecialchars($patient->pincode) : '<span style="color: #999;">Not provided</span>'; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Medical Information -->
                    <div class="detail-section">
                        <h4 class="section-title"><i class="fa fa-stethoscope"></i> Medical Information</h4>
                        
                        <div class="detail-row">
                            <div class="detail-label">Reason for Appointment</div>
                            <div class="detail-value">
                                <span class="badge-custom badge-warning">
                                    <?php echo ucfirst($patient->reason_for_appointment); ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Fee Payment</div>
                            <div class="detail-value">
                                <?php 
                                if ($patient->fee_payment == 'yes') {
                                    echo '<span class="badge-custom badge-success">Paid</span>';
                                } elseif ($patient->fee_payment == 'no') {
                                    echo '<span class="badge-custom badge-warning">Not Paid</span>';
                                } else {
                                    echo '<span class="badge-custom badge-info">Not Applicable</span>';
                                }
                                ?>
                            </div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Registered at Other Hospital</div>
                            <div class="detail-value">
                                <?php 
                                if ($patient->registered_other_hospital === 1) {
                                    echo 'Yes';
                                } elseif ($patient->registered_other_hospital === 0) {
                                    echo 'No';
                                } else {
                                    echo '<span style="color: #999;">Not specified</span>';
                                }
                                ?>
                            </div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Recommended to Hospital</div>
                            <div class="detail-value">
                                <?php 
                                if ($patient->recommended_to_hospital === 1) {
                                    echo 'Yes';
                                } elseif ($patient->recommended_to_hospital === 0) {
                                    echo 'No';
                                } else {
                                    echo '<span style="color: #999;">Not specified</span>';
                                }
                                ?>
                            </div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Status</div>
                            <div class="detail-value">
                                <?php 
                                if ($patient->status == 'active') {
                                    echo '<span class="badge-custom badge-success">Active</span>';
                                } else {
                                    echo '<span class="badge-custom">' . ucfirst($patient->status) . '</span>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- System Information -->
                    <div class="detail-section">
                        <h4 class="section-title"><i class="fa fa-clock-o"></i> System Information</h4>
                        
                        <div class="detail-row">
                            <div class="detail-label">Registered On</div>
                            <div class="detail-value"><?php echo _dt($patient->created_at); ?></div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Last Updated</div>
                            <div class="detail-value"><?php echo _dt($patient->updated_at); ?></div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <a href="<?php echo admin_url('hospital_management/patient_records'); ?>" class="btn-action btn-back">
                            <i class="fa fa-arrow-left"></i> Back to List
                        </a>
                        
                        <a href="<?php echo admin_url('hospital_management/manage_patient/' . $patient->id); ?>" class="btn-action btn-edit">
                            <i class="fa fa-pencil"></i> Edit Patient
                        </a>
                        
                        <a href="#" onclick="deletePatient(<?php echo $patient->id; ?>); return false;" class="btn-action btn-delete">
                            <i class="fa fa-trash"></i> Delete Patient
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
function deletePatient(id) {
    if (confirm('Are you sure you want to delete this patient record?\n\nThis action cannot be undone.')) {
        $.ajax({
            url: admin_url + 'hospital_management/delete_patient/' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    setTimeout(function() {
                        window.location.href = admin_url + 'hospital_management/patient_records';
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