<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<?php
// Get patient documents and appointments
$this->load->model('hospital_patients_model');
$this->load->model('hospital_appointments_model');

$documents = $this->hospital_patients_model->get_patient_documents($patient->id);

// Get appointments for this patient
$this->db->select(
    db_prefix() . 'hospital_appointments.*, ' .
    'COALESCE(' . db_prefix() . 'staff.firstname, "Not Assigned") as consultant_firstname, ' .
    'COALESCE(' . db_prefix() . 'staff.lastname, "") as consultant_lastname'
);
$this->db->join(
    db_prefix() . 'staff',
    db_prefix() . 'staff.staffid = ' . db_prefix() . 'hospital_appointments.consultant_id',
    'left'
);
$this->db->where(db_prefix() . 'hospital_appointments.patient_id', $patient->id);
$this->db->order_by(db_prefix() . 'hospital_appointments.appointment_date', 'DESC');
$this->db->order_by(db_prefix() . 'hospital_appointments.appointment_time', 'DESC');
$appointments = $this->db->get(db_prefix() . 'hospital_appointments')->result();
?>

<style>
/* Patient View Header */
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

.badge-danger {
    background: #ffebee;
    color: #c62828;
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

/* Documents Section */
.documents-wrapper {
    background: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 25px;
    margin-bottom: 25px;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}

.section-header-title {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin: 0;
}

.documents-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 15px;
}

.document-card {
    background: #f8f9fa;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    padding: 15px;
    transition: all 0.2s;
}

.document-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.document-icon {
    font-size: 35px;
    color: #666;
    margin-bottom: 10px;
    text-align: center;
}

.document-name {
    font-size: 13px;
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
    word-break: break-word;
}

.document-type {
    font-size: 11px;
    color: #666;
    margin-bottom: 5px;
}

.document-date {
    font-size: 10px;
    color: #999;
    margin-bottom: 10px;
}

.document-actions {
    display: flex;
    gap: 8px;
}

.btn-doc-action {
    flex: 1;
    padding: 5px 10px;
    font-size: 11px;
    border-radius: 4px;
    text-align: center;
    text-decoration: none;
    transition: all 0.2s;
}

.btn-view {
    background: #2196F3;
    color: #fff;
    border: 1px solid #2196F3;
}

.btn-view:hover {
    background: #1976D2;
    color: #fff;
    text-decoration: none;
}

.no-documents {
    text-align: center;
    padding: 30px 20px;
    color: #999;
}

.no-documents i {
    font-size: 50px;
    margin-bottom: 10px;
    display: block;
}

/* Appointments Section */
.appointments-wrapper {
    background: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 25px;
    margin-bottom: 25px;
}

.appointments-table {
    width: 100%;
    margin-top: 15px;
}

.appointments-table th {
    background: #f8f9fa;
    padding: 12px;
    font-weight: 600;
    color: #333;
    border-bottom: 2px solid #e0e0e0;
    font-size: 13px;
}

.appointments-table td {
    padding: 12px;
    border-bottom: 1px solid #f0f0f0;
    font-size: 13px;
}

.appointments-table tr:last-child td {
    border-bottom: none;
}

.appointments-table tr:hover {
    background: #f8f9fa;
}

.badge-status {
    padding: 4px 10px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-pending {
    background: #fff3e0;
    color: #f57c00;
}

.badge-confirmed {
    background: #e8f5e9;
    color: #388e3c;
}

.badge-cancelled {
    background: #ffebee;
    color: #d32f2f;
}

.badge-completed {
    background: #e3f2fd;
    color: #1976d2;
}

.no-appointments {
    text-align: center;
    padding: 40px 20px;
    color: #999;
}

.no-appointments i {
    font-size: 60px;
    margin-bottom: 15px;
    display: block;
}
</style>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- PATIENT DETAILS PANEL -->
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
                                    <?php echo ucfirst(str_replace('_', ' ', $patient->reason_for_appointment)); ?>
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
                                    if ($patient->other_hospital_patient_id) {
                                        echo ' (ID: ' . htmlspecialchars($patient->other_hospital_patient_id) . ')';
                                    }
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
                                    if ($patient->recommended_by) {
                                        echo ' (By: ' . htmlspecialchars($patient->recommended_by) . ')';
                                    }
                                } elseif ($patient->recommended_to_hospital === 0) {
                                    echo 'No';
                                } else {
                                    echo '<span style="color: #999;">Not specified</span>';
                                }
                                ?>
                            </div>
                        </div>
                        
                        <?php if ($patient->has_membership) { ?>
                        <div class="detail-row">
                            <div class="detail-label">Membership</div>
                            <div class="detail-value">
                                <?php echo htmlspecialchars($patient->membership_type); ?>
                                (<?php echo htmlspecialchars($patient->membership_number); ?>)
                                <?php if ($patient->membership_expiry_date) { ?>
                                    <br><small>Expires: <?php echo date('d M Y', strtotime($patient->membership_expiry_date)); ?></small>
                                <?php } ?>
                            </div>
                        </div>
                        <?php } ?>
                        
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
        
        <!-- DOCUMENTS SECTION -->
        <div class="row">
            <div class="col-md-12">
                <div class="documents-wrapper">
                    <div class="section-header">
                        <h4 class="section-header-title">
                            <i class="fa fa-file-text-o"></i> Patient Documents
                        </h4>
                    </div>
                    
                    <?php if (empty($documents)) { ?>
                        <div class="no-documents">
                            <i class="fa fa-folder-open-o"></i>
                            <p>No documents uploaded yet</p>
                        </div>
                    <?php } else { ?>
                        <div class="documents-grid">
                            <?php foreach ($documents as $doc) { 
                                // Get file icon based on type
                                $icon = 'fa-file-o';
                                if (strpos($doc->file_type, 'image') !== false) {
                                    $icon = 'fa-file-image-o';
                                } elseif (strpos($doc->file_type, 'pdf') !== false) {
                                    $icon = 'fa-file-pdf-o';
                                } elseif (strpos($doc->file_type, 'word') !== false || strpos($doc->file_type, 'document') !== false) {
                                    $icon = 'fa-file-word-o';
                                }
                            ?>
                                <div class="document-card">
                                    <div class="document-icon">
                                        <i class="fa <?php echo $icon; ?>"></i>
                                    </div>
                                    <div class="document-name"><?php echo htmlspecialchars($doc->original_filename); ?></div>
                                    <div class="document-type">
                                        <i class="fa fa-tag"></i> <?php echo htmlspecialchars($doc->document_type); ?>
                                    </div>
                                    <div class="document-date">
                                        <i class="fa fa-calendar"></i> <?php echo date('d M Y', strtotime($doc->created_at)); ?>
                                    </div>
                                    <div class="document-actions">
                                        <a href="<?php echo admin_url('hospital_management/download_document/' . $doc['id']); ?>" 
                                           class="btn-doc-action btn-view"
                                           target="_blank">
                                            <i class="fa fa-download"></i> Download
                                        </a>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        
        <!-- APPOINTMENTS HISTORY SECTION -->
        <div class="row">
            <div class="col-md-12">
                <div class="appointments-wrapper">
                    <div class="section-header">
                        <h4 class="section-header-title">
                            <i class="fa fa-calendar-check-o"></i> Appointment History
                        </h4>
                    </div>
                    
                    <?php if (empty($appointments)) { ?>
                        <div class="no-appointments">
                            <i class="fa fa-calendar-times-o"></i>
                            <p>No appointments found for this patient</p>
                        </div>
                    <?php } else { ?>
                        <div class="table-responsive">
                            <table class="table appointments-table">
                                <thead>
                                    <tr>
                                        <th>Appointment #</th>
                                        <th>Date & Time</th>
                                        <th>Consultant</th>
                                        <th>Reason</th>
                                        <th>Mode</th>
                                        <th>Status</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($appointments as $apt) { 
                                        // Determine status badge class
                                        $status_class = 'badge-pending';
                                        if ($apt->status == 'confirmed') {
                                            $status_class = 'badge-confirmed';
                                        } elseif ($apt->status == 'cancelled') {
                                            $status_class = 'badge-cancelled';
                                        } elseif ($apt->status == 'completed') {
                                            $status_class = 'badge-completed';
                                        }
                                    ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo $apt->appointment_number; ?></strong>
                                            </td>
                                            <td>
                                                <i class="fa fa-calendar"></i> <?php echo date('d M Y', strtotime($apt->appointment_date)); ?>
                                                <br>
                                                <i class="fa fa-clock-o"></i> <?php echo date('h:i A', strtotime($apt->appointment_time)); ?>
                                            </td>
                                            <td>
                                                <?php 
                                                $consultant_name = trim($apt->consultant_firstname . ' ' . $apt->consultant_lastname);
                                                echo htmlspecialchars($consultant_name);
                                                ?>
                                            </td>
                                            <td><?php echo ucfirst(str_replace('_', ' ', $apt->reason_for_appointment)); ?></td>
                                            <td>
                                                <span class="badge-custom badge-info">
                                                    <?php echo $apt->patient_mode == 'appointment' ? 'Appointment' : 'Walk In'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge-status <?php echo $status_class; ?>">
                                                    <?php echo $apt->status; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php 
                                                if ($apt->notes) {
                                                    echo nl2br(htmlspecialchars($apt->notes));
                                                } else {
                                                    echo '<span style="color: #999;">-</span>';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #f0f0f0;">
                            <strong>Total Appointments:</strong> <?php echo count($appointments); ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
function deletePatient(id) {
    if (confirm('Are you sure you want to delete this patient record?\n\nThis action cannot be undone and will delete all associated appointments and documents.')) {
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