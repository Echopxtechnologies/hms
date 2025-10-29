<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 25px;
}

.stat-card {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    padding: 20px;
    text-align: center;
}

.stat-card h3 {
    font-size: 32px;
    font-weight: 700;
    margin: 10px 0;
}

.stat-card.pending h3 { color: #ff9800; }
.stat-card.confirmed h3 { color: #4caf50; }
.stat-card.today h3 { color: #2196f3; }

.appointment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.btn-new-appointment {
    background: #333;
    color: #fff;
    padding: 10px 20px;
    border-radius: 4px;
    text-decoration: none;
}

.btn-new-appointment:hover {
    background: #000;
    color: #fff;
    text-decoration: none;
}

.modal-lg {
    max-width: 900px;
}

.patient-type-section {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
}

.form-section-divider {
    border-top: 2px solid #e0e0e0;
    margin: 25px 0;
    padding-top: 20px;
}

#existingPatientFields,
#existingModeSelection,
#existingPatientFullForm,
#newPatientFields,
#walkInFields {
    display: none;
}

.patient-search-info {
    background: #e3f2fd;
    padding: 10px;
    border-radius: 4px;
    margin-bottom: 15px;
    font-size: 13px;
    color: #1976d2;
}

.form-section-title {
    font-size: 15px;
    font-weight: 600;
    color: #333;
    margin-bottom: 15px;
    padding-bottom: 8px;
    border-bottom: 2px solid #e0e0e0;
}

/* ============ TIME PICKER STYLES ============ */
.time-picker-container {
    display: flex;
    gap: 10px;
    align-items: center;
}

.time-section {
    flex: 1;
}

.time-section label {
    display: block;
    margin-bottom: 5px;
    font-size: 12px;
    color: #666;
}

.time-buttons {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 5px;
}

.time-btn {
    padding: 8px 5px;
    border: 1px solid #ddd;
    background: #fff;
    border-radius: 4px;
    cursor: pointer;
    font-size: 13px;
    transition: all 0.2s;
}

.time-btn:hover {
    background: #f5f5f5;
    border-color: #999;
}

.time-btn.selected {
    background: #333;
    color: #fff;
    border-color: #333;
}

.time-separator {
    font-size: 24px;
    font-weight: bold;
    padding-top: 20px;
}

.selected-time-display {
    background: #e8f5e9;
    padding: 10px;
    border-radius: 4px;
    margin-top: 10px;
    text-align: center;
    font-weight: 600;
    color: #2e7d32;
}
</style>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="appointment-header">
                    <h3><i class="fa fa-calendar-check-o"></i> Manage Appointments</h3>
                    <button class="btn-new-appointment" data-toggle="modal" data-target="#appointmentModal">
                        <i class="fa fa-plus"></i> Create Appointment
                    </button>
                </div>
                
                <!-- Statistics -->
                <div class="stats-grid">
                    <div class="stat-card today">
                        <i class="fa fa-calendar"></i>
                        <h3><?php echo $statistics['today']; ?></h3>
                        <p>Today's Appointments</p>
                    </div>
                    
                    <div class="stat-card pending">
                        <i class="fa fa-clock-o"></i>
                        <h3><?php echo $statistics['pending']; ?></h3>
                        <p>Pending</p>
                    </div>
                    
                    <div class="stat-card confirmed">
                        <i class="fa fa-check-circle"></i>
                        <h3><?php echo $statistics['confirmed']; ?></h3>
                        <p>Confirmed</p>
                    </div>
                    
                    <div class="stat-card">
                        <i class="fa fa-list"></i>
                        <h3><?php echo $statistics['total']; ?></h3>
                        <p>Total Appointments</p>
                    </div>
                </div>
                
                <!-- Appointments Table -->
                <div class="panel_s">
                    <div class="panel-body">
                        <table class="table dt-table" id="appointments_table">
                            <thead>
                                <tr>
                                    <th>Appointment #</th>
                                    <th>Patient ID</th>
                                    <th>Patient Name</th>
                                    <th>Mobile</th>
                                    <th>Date</th>
                                    <th>Consultant</th>
                                    <th>Reason</th>
                                    <th>Mode</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($appointments as $apt) { ?>
                                <tr>
                                    <td><strong><?php echo $apt->appointment_number; ?></strong></td>
                                    <td><span class="label label-primary"><?php echo $apt->patient_number; ?></span></td>
                                    <td><?php echo $apt->patient_name; ?></td>
                                    <td>
                                        <a href="tel:<?php echo $apt->patient_mobile; ?>">
                                            <?php echo $apt->patient_mobile; ?>
                                        </a>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($apt->appointment_date)); ?></td>
                                    <td><?php echo $apt->consultant_firstname . ' ' . $apt->consultant_lastname; ?></td>
                                    <td><?php echo ucfirst($apt->reason_for_appointment); ?></td>
                                    <td>
                                        <span class="label label-default">
                                            <?php echo $apt->patient_mode == 'appointment' ? 'Appointment' : 'Walk-in'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $status_class = '';
                                        switch($apt->status) {
                                            case 'pending': $status_class = 'label-warning'; break;
                                            case 'confirmed': $status_class = 'label-success'; break;
                                            case 'cancelled': $status_class = 'label-danger'; break;
                                            case 'completed': $status_class = 'label-info'; break;
                                        }
                                        ?>
                                        <span class="label <?php echo $status_class; ?>">
                                            <?php echo ucfirst($apt->status); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($apt->status == 'pending') { ?>
                                        <button class="btn btn-success btn-sm" onclick="confirmAppointment(<?php echo $apt->id; ?>)">
                                            <i class="fa fa-check"></i> Confirm
                                        </button>
                                        <?php } ?>
                                        
                                        <?php if ($apt->status != 'cancelled') { ?>
                                        <button class="btn btn-warning btn-sm" onclick="cancelAppointment(<?php echo $apt->id; ?>)">
                                            <i class="fa fa-ban"></i> Cancel
                                        </button>
                                        <?php } ?>
                                        
                                        <button class="btn btn-danger btn-sm" onclick="deleteAppointment(<?php echo $apt->id; ?>)">
                                            <i class="fa fa-trash"></i>
                                        </button>
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
</div>

<!-- Appointment Modal -->
<div class="modal fade" id="appointmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-calendar-plus-o"></i> Create New Appointment</h4>
            </div>
            <div class="modal-body">
                <?php echo form_open_multipart('', ['id' => 'appointmentForm']); ?>
                    <!-- Hidden field to indicate full form shown -->
                    <input type="hidden" name="show_full_patient_form" id="show_full_patient_form" value="0">
                    
                    <!-- Patient Type Selection -->
                    <div class="patient-type-section">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="control-label"><strong>Patient Type:</strong></label>
                                <div>
                                    <label class="radio-inline">
                                        <input type="radio" name="patient_type_option" value="existing" checked>
                                        Existing Patient
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="patient_type_option" value="new">
                                        New Patient
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-6" id="modeSelection" style="display:none;">
                                <label class="control-label"><strong>Mode:</strong></label>
                                <div>
                                    <label class="radio-inline">
                                        <input type="radio" name="patient_mode" value="appointment" checked>
                                        Appointment
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="patient_mode" value="walk_in">
                                        Walk-in
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Existing Patient Fields -->
                    <div id="existingPatientFields">
                        <div class="patient-search-info">
                            <i class="fa fa-info-circle"></i> 
                            <strong>Search by:</strong> Patient ID (e.g., PAT2025001), Name, or Mobile Number
                        </div>
                        
                        <div class="form-group">
                            <label for="existing_patient_id" class="control-label">Select Patient *</label>
                            <select id="existing_patient_id" name="existing_patient_id" class="form-control selectpicker" data-live-search="true">
                                <option value="">-- Type to search patients --</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Existing Patient Mode Selection (FIXED: Added for existing patients) -->
                    <div id="existingModeSelection" style="display:none;">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label"><strong>Appointment Mode:</strong></label>
                                    <div>
                                        <label class="radio-inline">
                                            <input type="radio" name="existing_patient_mode" value="appointment" checked>
                                            Appointment
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="existing_patient_mode" value="walk_in">
                                            Walk-in
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Existing Patient Full Form (shown for walk-in or when needed) -->
                    <div id="existingPatientFullForm">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> You can update patient information below while creating the appointment.
                        </div>
                        
                        <!-- Basic Information -->
                        <div class="form-section-title"><i class="fa fa-user"></i> Basic Information</div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Name</label>
                                    <input type="text" name="patient_name" id="existing_patient_name" class="form-control">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Gender</label>
                                    <select name="gender" id="existing_gender" class="form-control">
                                        <option value="">Select</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Age</label>
                                    <input type="number" name="age" id="existing_age" class="form-control" min="0" max="150">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Date of Birth</label>
                                    <input type="date" name="dob" id="existing_dob" class="form-control">
                                </div>
                            </div>
                            
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="control-label">Patient Type</label>
                                    <select name="patient_type" id="existing_patient_type" class="form-control">
                                        <option value="Regular">Regular</option>
                                        <?php 
                                        $this->load->model('hospital_patients_model');
                                        $patient_types = $this->hospital_patients_model->get_patient_types();
                                        foreach ($patient_types as $type) {
                                            echo '<option value="' . $type['type_name'] . '">' . $type['type_name'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Contact Details -->
                        <div class="form-section-title"><i class="fa fa-phone"></i> Contact Details</div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Mobile</label>
                                    <input type="tel" name="mobile_number" id="existing_mobile" class="form-control">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Phone</label>
                                    <input type="tel" name="phone" id="existing_phone" class="form-control">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Email</label>
                                    <input type="email" name="email" id="existing_email" class="form-control">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Address Details -->
                        <div class="form-section-title"><i class="fa fa-map-marker"></i> Address Details</div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Address</label>
                                    <textarea name="address" id="existing_address" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Landmark</label>
                                    <input type="text" name="address_landmark" id="existing_landmark" class="form-control">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">City</label>
                                    <input type="text" name="city" id="existing_city" class="form-control">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">State</label>
                                    <input type="text" name="state" id="existing_state" class="form-control">
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="control-label">Pincode</label>
                                    <input type="text" name="pincode" id="existing_pincode" class="form-control">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Other Details -->
                        <div class="form-section-title"><i class="fa fa-clipboard"></i> Other Details</div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Registered at Other Hospital?</label>
                                    <div>
                                        <label class="radio-inline">
                                            <input type="radio" name="registered_other_hospital" id="existing_registered_yes" value="1"> Yes
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="registered_other_hospital" id="existing_registered_no" value="0" checked> No
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Fee Payment?</label>
                                    <div>
                                        <label class="radio-inline">
                                            <input type="radio" name="fee_payment" id="existing_fee_yes" value="yes"> Yes
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="fee_payment" id="existing_fee_no" value="no"> No
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="fee_payment" id="existing_fee_na" value="not_applicable" checked> N/A
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Recommendation Section -->
                        <div class="form-section-title"><i class="fa fa-handshake-o"></i> Recommendation Details</div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Have You Been Recommended To This Hospital?</label>
                                    <div>
                                        <label class="radio-inline">
                                            <input type="radio" name="recommended_to_hospital" value="1" id="existing_recommended_yes"> Yes
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="recommended_to_hospital" value="0" id="existing_recommended_no" checked> No
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="existingRecommendationDetails" style="display:none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Recommended By</label>
                                        <input type="text" name="recommended_by" id="existing_recommended_by" class="form-control" placeholder="Name of person/organization">
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Recommendation File (Multiple files allowed)</label>
                                        <input type="file" name="recommendation_file[]" id="existing_recommendation_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" multiple>
                                        <small class="text-muted">Upload recommendation letter/document(s)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Membership Section (FIXED: Removed document field) -->
                        <div class="form-section-title"><i class="fa fa-id-card"></i> Membership Details</div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Do You Have Hospital Membership?</label>
                                    <div>
                                        <label class="radio-inline">
                                            <input type="radio" name="has_membership" value="1" id="existing_membership_yes"> Yes
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="has_membership" value="0" id="existing_membership_no" checked> No
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="existingMembershipDetails" style="display:none;">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Membership Type</label>
                                        <input type="text" name="membership_type" id="existing_membership_type" class="form-control" placeholder="e.g., Gold, Silver">
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Membership Number</label>
                                        <input type="text" name="membership_number" id="existing_membership_number" class="form-control" placeholder="Membership ID">
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Expiry Date</label>
                                        <input type="date" name="membership_expiry_date" id="existing_membership_expiry" class="form-control">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Membership Notes</label>
                                        <textarea name="membership_notes" id="existing_membership_notes" class="form-control" rows="2" placeholder="Any additional membership details"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- New Patient - Appointment Mode (Minimal Fields) -->
                    <div id="newPatientFields">
                        <h5 style="margin-bottom: 15px; color: #333;"><i class="fa fa-user-plus"></i> Quick Patient Registration</h5>
                        <p style="color: #666; font-size: 13px; margin-bottom: 15px;">
                            <em>Quick registration for appointment booking. Only Name and Mobile required.</em>
                        </p>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="new_name" class="control-label">Name *</label>
                                    <input type="text" id="new_name" name="new_name" class="form-control" placeholder="Full name">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="new_mobile" class="control-label">Mobile Number *</label>
                                    <input type="tel" id="new_mobile" name="new_mobile" class="form-control" placeholder="Mobile">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Walk-in Mode (Full Form) - Same as before with FIXED membership section -->
                    <div id="walkInFields">
                        <h5 style="margin-bottom: 15px; color: #333;"><i class="fa fa-user-plus"></i> Walk-in Patient Registration</h5>
                        <p style="color: #666; font-size: 13px; margin-bottom: 20px;">
                            <em>Complete patient registration for walk-in. Fields marked with * are required.</em>
                        </p>
                        
                        <!-- Basic Information -->
                        <div class="form-section-title"><i class="fa fa-user"></i> Basic Information</div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Name *</label>
                                    <input type="text" name="walkin_name" class="form-control" placeholder="Full name">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Gender *</label>
                                    <select name="walkin_gender" class="form-control">
                                        <option value="">Select</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Age</label>
                                    <input type="number" name="walkin_age" class="form-control" placeholder="Age" min="0" max="150">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Date of Birth</label>
                                    <input type="date" name="walkin_dob" class="form-control">
                                </div>
                            </div>
                            
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="control-label">Patient Type</label>
                                    <select name="walkin_patient_type" class="form-control">
                                        <option value="Regular">Regular</option>
                                        <?php 
                                        foreach ($patient_types as $type) {
                                            echo '<option value="' . $type['type_name'] . '">' . $type['type_name'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Contact Details -->
                        <div class="form-section-title"><i class="fa fa-phone"></i> Contact Details</div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Mobile *</label>
                                    <input type="tel" name="walkin_mobile" class="form-control" placeholder="Mobile number">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Phone</label>
                                    <input type="tel" name="walkin_phone" class="form-control" placeholder="Landline">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Email</label>
                                    <input type="email" name="walkin_email" class="form-control" placeholder="email@example.com">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Address Details -->
                        <div class="form-section-title"><i class="fa fa-map-marker"></i> Address Details</div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Address</label>
                                    <textarea name="walkin_address" class="form-control" rows="2" placeholder="Full address"></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Landmark</label>
                                    <input type="text" name="walkin_landmark" class="form-control" placeholder="Nearby landmark">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">City</label>
                                    <input type="text" name="walkin_city" class="form-control" placeholder="City">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">State</label>
                                    <input type="text" name="walkin_state" class="form-control" placeholder="State">
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="control-label">Pincode</label>
                                    <input type="text" name="walkin_pincode" class="form-control" placeholder="Pincode">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Other Details -->
                        <div class="form-section-title"><i class="fa fa-clipboard"></i> Other Details</div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Registered at Other Hospital?</label>
                                    <div>
                                        <label class="radio-inline">
                                            <input type="radio" name="walkin_registered_other" value="1"> Yes
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="walkin_registered_other" value="0" checked> No
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Fee Payment?</label>
                                    <div>
                                        <label class="radio-inline">
                                            <input type="radio" name="walkin_fee_payment" value="yes"> Yes
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="walkin_fee_payment" value="no"> No
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="walkin_fee_payment" value="not_applicable" checked> N/A
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Recommendation Section for Walk-in -->
                        <div class="form-section-title"><i class="fa fa-handshake-o"></i> Recommendation Details</div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Have You Been Recommended To This Hospital?</label>
                                    <div>
                                        <label class="radio-inline">
                                            <input type="radio" name="walkin_recommended_to_hospital" value="1" id="walkin_recommended_yes"> Yes
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="walkin_recommended_to_hospital" value="0" id="walkin_recommended_no" checked> No
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="walkinRecommendationDetails" style="display:none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Recommended By</label>
                                        <input type="text" name="walkin_recommended_by" class="form-control" placeholder="Name of person/organization">
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Recommendation File (Multiple files allowed)</label>
                                        <input type="file" name="recommendation_file[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" multiple>
                                        <small class="text-muted">Upload recommendation letter/document(s)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Membership Section for Walk-in (FIXED: Removed document field) -->
                        <div class="form-section-title"><i class="fa fa-id-card"></i> Membership Details</div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Do You Have Hospital Membership?</label>
                                    <div>
                                        <label class="radio-inline">
                                            <input type="radio" name="walkin_has_membership" value="1" id="walkin_membership_yes"> Yes
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="walkin_has_membership" value="0" id="walkin_membership_no" checked> No
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="walkinMembershipDetails" style="display:none;">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Membership Type</label>
                                        <input type="text" name="walkin_membership_type" class="form-control" placeholder="e.g., Gold, Silver">
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Membership Number</label>
                                        <input type="text" name="walkin_membership_number" class="form-control" placeholder="Membership ID">
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Expiry Date</label>
                                        <input type="date" name="walkin_membership_expiry_date" class="form-control">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Membership Notes</label>
                                        <textarea name="walkin_membership_notes" class="form-control" rows="2" placeholder="Any additional membership details"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Appointment Details (FIXED: Common for all - Always visible) -->
                    <div class="form-section-divider"></div>
                    <h5 style="margin-bottom: 15px; color: #333;"><i class="fa fa-calendar"></i> Appointment Details</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="reason_for_appointment" class="control-label">Reason for Appointment *</label>
                                <select id="reason_for_appointment" name="reason_for_appointment" class="form-control" required>
                                    <option value="">-- Select --</option>
                                    <option value="consultation">Consultation</option>
                                    <option value="procedure">Procedure</option>
                                    <option value="surgery">Surgery</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="appointment_date" class="control-label">Appointment Date *</label>
                                <select id="appointment_date" name="appointment_date" class="form-control" required>
                                    <option value="">-- Select Appointment Date --</option>
                                    <?php
                                    for ($i = 0; $i < 15; $i++) {
                                        $date = date('Y-m-d', strtotime("+$i days"));
                                        $display = date('d-M-Y', strtotime("+$i days"));
                                        echo '<option value="' . $date . '">' . $display . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- ============ APPOINTMENT TIME FIELD ============ -->
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label class="control-label">Appointment Time *</label>
            <input type="hidden" id="appointment_time" name="appointment_time" required>
            
            <div class="time-picker-container">
                <div class="time-section">
                    <label>Hour</label>
                    <div class="time-buttons" id="hourButtons">
                        <button type="button" class="time-btn" data-hour="00">00</button>
                        <button type="button" class="time-btn" data-hour="01">01</button>
                        <button type="button" class="time-btn" data-hour="02">02</button>
                        <button type="button" class="time-btn" data-hour="03">03</button>
                        <button type="button" class="time-btn" data-hour="04">04</button>
                        <button type="button" class="time-btn" data-hour="05">05</button>
                        <button type="button" class="time-btn" data-hour="06">06</button>
                        <button type="button" class="time-btn" data-hour="07">07</button>
                        <button type="button" class="time-btn" data-hour="08">08</button>
                        <button type="button" class="time-btn" data-hour="09">09</button>
                        <button type="button" class="time-btn" data-hour="10">10</button>
                        <button type="button" class="time-btn" data-hour="11">11</button>
                        <button type="button" class="time-btn" data-hour="12">12</button>
                        <button type="button" class="time-btn" data-hour="13">13</button>
                        <button type="button" class="time-btn" data-hour="14">14</button>
                        <button type="button" class="time-btn" data-hour="15">15</button>
                        <button type="button" class="time-btn" data-hour="16">16</button>
                        <button type="button" class="time-btn" data-hour="17">17</button>
                        <button type="button" class="time-btn" data-hour="18">18</button>
                        <button type="button" class="time-btn" data-hour="19">19</button>
                        <button type="button" class="time-btn" data-hour="20">20</button>
                        <button type="button" class="time-btn" data-hour="21">21</button>
                        <button type="button" class="time-btn" data-hour="22">22</button>
                        <button type="button" class="time-btn" data-hour="23">23</button>
                    </div>
                </div>
                
                <div class="time-separator">:</div>
                
                <div class="time-section">
                    <label>Minute</label>
                    <div class="time-buttons" id="minuteButtons">
                        <button type="button" class="time-btn" data-minute="00">00</button>
                        <button type="button" class="time-btn" data-minute="05">05</button>
                        <button type="button" class="time-btn" data-minute="10">10</button>
                        <button type="button" class="time-btn" data-minute="15">15</button>
                        <button type="button" class="time-btn" data-minute="20">20</button>
                        <button type="button" class="time-btn" data-minute="25">25</button>
                        <button type="button" class="time-btn" data-minute="30">30</button>
                        <button type="button" class="time-btn" data-minute="35">35</button>
                        <button type="button" class="time-btn" data-minute="40">40</button>
                        <button type="button" class="time-btn" data-minute="45">45</button>
                        <button type="button" class="time-btn" data-minute="50">50</button>
                        <button type="button" class="time-btn" data-minute="55">55</button>
                    </div>
                </div>
                
                <div class="time-separator">:</div>
                
                <div class="time-section">
                    <label>Second</label>
                    <div class="time-buttons" id="secondButtons">
                        <button type="button" class="time-btn selected" data-second="00">00</button>
                    </div>
                </div>
            </div>
            
            <div class="selected-time-display" id="timeDisplay" style="display:none;">
                Selected Time: <span id="displayTime">--:--:--</span>
            </div>
        </div>
    </div>
</div>
<!-- ============ END TIME FIELD ============ -->
                    
                    <!-- FIXED: Consultant dropdown always visible -->
               <!-- Consultant Dropdown - Fetches Active Consultants Only -->
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label for="consultant_id" class="control-label">
                <?php echo _l('consultant'); ?> 
                <span class="text-danger">*</span>
            </label>
           <select id="consultant_id" name="consultant_id" class="form-control selectpicker" data-live-search="true" data-width="100%" required>
    <option value="">-- Select Consultant --</option>
    <?php if (!empty($consultants)) { ?>
        <?php foreach ($consultants as $consultant) { ?>
            <option value="<?php echo $consultant['id']; ?>">
                <?php echo $consultant['first_name'] . ' ' . $consultant['last_name']; ?>
                <?php if (!empty($consultant['email'])) { ?>
                    (<?php echo $consultant['email']; ?>)
                <?php } ?>
            </option>
        <?php } ?>
    <?php } else { ?>
        <option value="" disabled>No consultants found</option>
    <?php } ?>
</select>
            <?php echo form_error('consultant_id', '<small class="text-danger">', '</small>'); ?>
        </div>
    </div>
</div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="notes" class="control-label">Notes (Optional)</label>
                                <textarea id="notes" name="notes" class="form-control" rows="2" placeholder="Additional notes"></textarea>
                            </div>
                        </div>
                    </div>
                    
                <?php echo form_close(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancel
                </button>
                <button type="button" id="saveAppointmentBtn" class="btn btn-primary">
                    <i class="fa fa-check"></i> Create Appointment
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Cancel Appointment</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="cancel_appointment_id">
                <div class="form-group">
                    <label>Cancellation Reason:</label>
                    <textarea id="cancellation_reason" class="form-control" rows="3" placeholder="Enter reason for cancellation"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" onclick="submitCancellation()">
                    <i class="fa fa-ban"></i> Cancel Appointment
                </button>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
let csrfTokenName = '<?php echo $this->security->get_csrf_token_name(); ?>';
let csrfTokenHash = '<?php echo $this->security->get_csrf_hash(); ?>';
$(document).ready(function() {
    // Initialize selectpicker
    $('.selectpicker').selectpicker();
    
    // Load patients for dropdown
    loadPatients();
    
    // Patient type selection
    $('input[name="patient_type_option"]').on('change', function() {
        const type = $(this).val();
        
        $('#existingPatientFields').hide();
        $('#existingModeSelection').hide();
        $('#existingPatientFullForm').hide();
        $('#newPatientFields').hide();
        $('#walkInFields').hide();
        $('#modeSelection').hide();
        $('#show_full_patient_form').val('0');
        
        if (type === 'existing') {
            $('#existingPatientFields').show();
        } else {
            $('#modeSelection').show();
            checkMode();
        }
    });
    
    // Mode selection for new patients
    $('input[name="patient_mode"]').on('change', function() {
        checkMode();
    });
    
    function checkMode() {
        const mode = $('input[name="patient_mode"]:checked').val();
        
        $('#newPatientFields').hide();
        $('#walkInFields').hide();
        $('#show_full_patient_form').val('0');
        
        if (mode === 'appointment') {
            $('#newPatientFields').show();
        } else {
            $('#walkInFields').show();
            $('#show_full_patient_form').val('1'); // Show full form for walk-in
        }
    }
    
    // FIXED: Existing patient selection - Show mode selection
    $('#existing_patient_id').on('change', function() {
        const patientId = $(this).val();
        
        if (patientId) {
            // Show mode selection for existing patient
            $('#existingModeSelection').show();
            
            // Load patient details
            $.ajax({
                url: admin_url + 'hospital_management/get_patient_details/' + patientId,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const p = response.patient;
                        
                        // Populate form fields
                        $('#existing_patient_name').val(p.name);
                        $('#existing_gender').val(p.gender);
                        $('#existing_age').val(p.age);
                        $('#existing_dob').val(p.dob);
                        $('#existing_patient_type').val(p.patient_type);
                        $('#existing_mobile').val(p.mobile_number);
                        $('#existing_phone').val(p.phone);
                        $('#existing_email').val(p.email);
                        $('#existing_address').val(p.address);
                        $('#existing_landmark').val(p.address_landmark);
                        $('#existing_city').val(p.city);
                        $('#existing_state').val(p.state);
                        $('#existing_pincode').val(p.pincode);
                        
                        // Radio buttons
                        if (p.registered_other_hospital == 1) {
                            $('#existing_registered_yes').prop('checked', true);
                        } else {
                            $('#existing_registered_no').prop('checked', true);
                        }
                        
                        $('input[name="fee_payment"][value="' + p.fee_payment + '"]').prop('checked', true);
                        
                        // Recommendation
                        if (p.recommended_to_hospital == 1) {
                            $('#existing_recommended_yes').prop('checked', true);
                            $('#existingRecommendationDetails').show();
                            $('#existing_recommended_by').val(p.recommended_by);
                        } else {
                            $('#existing_recommended_no').prop('checked', true);
                            $('#existingRecommendationDetails').hide();
                        }
                        
                        // Membership
                        if (p.has_membership == 1) {
                            $('#existing_membership_yes').prop('checked', true);
                            $('#existingMembershipDetails').show();
                            $('#existing_membership_type').val(p.membership_type);
                            $('#existing_membership_number').val(p.membership_number);
                            $('#existing_membership_expiry').val(p.membership_expiry_date);
                            $('#existing_membership_notes').val(p.membership_notes);
                        } else {
                            $('#existing_membership_no').prop('checked', true);
                            $('#existingMembershipDetails').hide();
                        }
                    }
                }
            });
            
            // Check existing patient mode to show/hide full form
            checkExistingPatientMode();
        } else {
            $('#existingModeSelection').hide();
            $('#existingPatientFullForm').hide();
            $('#show_full_patient_form').val('0');
        }
    });
    
    // FIXED: Mode selection for existing patients
    $('input[name="existing_patient_mode"]').on('change', function() {
        checkExistingPatientMode();
    });
    
    function checkExistingPatientMode() {
        const mode = $('input[name="existing_patient_mode"]:checked').val();
        
        if (mode === 'walk_in') {
            $('#existingPatientFullForm').show();
            $('#show_full_patient_form').val('1');
        } else {
            $('#existingPatientFullForm').hide();
            $('#show_full_patient_form').val('0');
        }
    }
    
    // Existing patient - Recommendation toggle
    $('input[name="recommended_to_hospital"]').on('change', function() {
        if ($(this).val() == '1') {
            $('#existingRecommendationDetails').show();
        } else {
            $('#existingRecommendationDetails').hide();
        }
    });
    
    // Existing patient - Membership toggle
    $('input[name="has_membership"]').on('change', function() {
        if ($(this).val() == '1') {
            $('#existingMembershipDetails').show();
        } else {
            $('#existingMembershipDetails').hide();
        }
    });
    
    // Walk-in - Recommendation toggle
    $('#walkin_recommended_yes, #walkin_recommended_no').on('change', function() {
        if ($('#walkin_recommended_yes').is(':checked')) {
            $('#walkinRecommendationDetails').show();
        } else {
            $('#walkinRecommendationDetails').hide();
        }
    });
    
    // Walk-in - Membership toggle
    $('#walkin_membership_yes, #walkin_membership_no').on('change', function() {
        if ($('#walkin_membership_yes').is(':checked')) {
            $('#walkinMembershipDetails').show();
        } else {
            $('#walkinMembershipDetails').hide();
        }
    });
    
    // Force show form fields when modal opens
$('#appointmentModal').on('shown.bs.modal', function() {
    console.log('Modal opened');
    // Make sure sections are properly initialized
    const patientType = $('input[name="patient_type_option"]:checked').val();
    if (patientType === 'new') {
        const mode = $('input[name="patient_mode"]:checked').val();
        if (mode === 'appointment') {
            $('#newPatientFields').show();
            $('#walkInFields').hide();
        } else if (mode === 'walkin') {
            $('#newPatientFields').hide();
            $('#walkInFields').show();
        }
    }
});

// Also trigger when patient type changes
$('input[name="patient_type_option"]').on('change', function() {
    const type = $(this).val();
    console.log('Patient type changed to:', type);
    
    if (type === 'new') {
        $('#modeSelection').show();
        // Trigger mode check
        const mode = $('input[name="patient_mode"]:checked').val();
        if (mode) {
            if (mode === 'appointment') {
                $('#newPatientFields').show();
                $('#walkInFields').hide();
            } else if (mode === 'walkin') {
                $('#newPatientFields').hide();
                $('#walkInFields').show();
            }
        }
    }
});

// Ensure mode selection works
$('input[name="patient_mode"]').on('change', function() {
    const mode = $(this).val();
    console.log('Patient mode changed to:', mode);
    
    if (mode === 'appointment') {
        $('#newPatientFields').show();
        $('#walkInFields').hide();
    } else if (mode === 'walkin') {
        $('#newPatientFields').hide();
        $('#walkInFields').show();
    }
});
    // Reset modal on close
    $('#appointmentModal').on('hidden.bs.modal', function() {
        $('#appointmentForm')[0].reset();
        $('input[name="patient_type_option"][value="existing"]').prop('checked', true).trigger('change');
        $('#existingModeSelection').hide();
        $('#existingPatientFullForm').hide();
        $('#show_full_patient_form').val('0');
        $('.selectpicker').selectpicker('refresh');
    });
    
    // Save appointment
    $('#saveAppointmentBtn').on('click', function() {
        const patientType = $('input[name="patient_type_option"]:checked').val();
        
        if (patientType === 'existing') {
            saveExistingPatientAppointment();
        } else {
            saveNewPatientAppointment();
        }
    });
});

function loadPatients() {
    $.ajax({
        url: admin_url + 'hospital_management/get_patients_dropdown',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                let options = '<option value="">-- Type to search patients --</option>';
                response.patients.forEach(function(patient) {
                    options += `<option value="${patient.id}">${patient.patient_number} - ${patient.name} - ${patient.mobile_number}</option>`;
                });
                $('#existing_patient_id').html(options).selectpicker('refresh');
            }
        }
    });
}
function saveExistingPatientAppointment() {
    const patientId = $('#existing_patient_id').val();
    const patientMode = $('input[name="existing_patient_mode"]:checked').val();
    
    // ========== VALIDATION ==========
    if (!patientId) {
        alert_float('warning', 'Please select a patient');
        return;
    }
    
    if (!patientMode) {
        alert_float('warning', 'Please select appointment mode (Appointment or Walk-in)');
        return;
    }
    
    // Validate appointment fields
    if (!$('#appointment_date').val()) {
        alert_float('warning', 'Please select appointment date');
        return;
    }
    
    if (!$('#appointment_time').val()) {
        alert_float('warning', 'Please select appointment time');
        return;
    }
    
    if (!$('#reason_for_appointment').val()) {
        alert_float('warning', 'Please select reason for appointment');
        return;
    }
    
    if (!$('#consultant_id').val()) {
        alert_float('warning', 'Please select consultant');
        return;
    }
    
    const $btn = $('#saveAppointmentBtn');
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Creating...');
    
    // Create FormData
    const formData = new FormData();
    
    // CSRF Token
    formData.append(csrfTokenName, csrfTokenHash);
    
    // Appointment data
    formData.append('patient_id', patientId);
    formData.append('patient_mode', patientMode);
    formData.append('is_new_patient', '0');
    formData.append('reason_for_appointment', $('#reason_for_appointment').val());
    formData.append('appointment_date', $('#appointment_date').val());
    formData.append('appointment_time', $('#appointment_time').val());
    formData.append('consultant_id', $('#consultant_id').val());
    formData.append('notes', $('#notes').val() || '');
    
    // Check if updating patient info (for walk-in with full form)
    const showFullForm = $('#show_full_patient_form').val();
    formData.append('show_full_patient_form', showFullForm || '0');
    
    // Patient update data (if full form shown - walk-in mode)
    if (showFullForm == '1') {
        formData.append('patient_name', $('#existing_patient_name').val() || '');
        formData.append('gender', $('#existing_gender').val() || '');
        formData.append('age', $('#existing_age').val() || '');
        formData.append('dob', $('#existing_dob').val() || '');
        formData.append('patient_type', $('#existing_patient_type').val() || '');
        formData.append('mobile_number', $('#existing_mobile').val() || '');
        formData.append('phone', $('#existing_phone').val() || '');
        formData.append('email', $('#existing_email').val() || '');
        formData.append('address', $('#existing_address').val() || '');
        formData.append('address_landmark', $('#existing_landmark').val() || '');
        formData.append('city', $('#existing_city').val() || '');
        formData.append('state', $('#existing_state').val() || '');
        formData.append('pincode', $('#existing_pincode').val() || '');
        formData.append('registered_other_hospital', $('input[name="registered_other_hospital"]:checked').val() || '0');
        formData.append('other_hospital_patient_id', $('#existing_other_hospital_id').val() || '');
        formData.append('fee_payment', $('input[name="fee_payment"]:checked').val() || '0');
        
        // Recommendation
        formData.append('recommended_to_hospital', $('input[name="recommended_to_hospital"]:checked').val() || '0');
        formData.append('recommended_by', $('#existing_recommended_by').val() || '');
        
        // Recommendation file(s)
        const recFiles = $('#existing_recommendation_file')[0]?.files;
        if (recFiles && recFiles.length > 0) {
            for (let i = 0; i < recFiles.length; i++) {
                formData.append('recommendation_file[]', recFiles[i]);
            }
        }
        
        // Membership
        formData.append('has_membership', $('input[name="has_membership"]:checked').val() || '0');
        formData.append('membership_type', $('#existing_membership_type').val() || '');
        formData.append('membership_number', $('#existing_membership_number').val() || '');
        formData.append('membership_expiry_date', $('#existing_membership_expiry').val() || '');
        formData.append('membership_notes', $('#existing_membership_notes').val() || '');
        
        // Membership file(s)
        if ($('input[name="has_membership"]:checked').val() == '1') {
            const memFiles = $('#existing_membership_file')[0]?.files;
            if (memFiles && memFiles.length > 0) {
                for (let i = 0; i < memFiles.length; i++) {
                    formData.append('membership_file[]', memFiles[i]);
                }
            }
        }
        
        // Other documents
        const otherFiles = $('#existing_other_documents')[0]?.files;
        if (otherFiles && otherFiles.length > 0) {
            for (let i = 0; i < otherFiles.length; i++) {
                formData.append('other_documents[]', otherFiles[i]);
            }
        }
    }
    
    // Submit via AJAX
    $.ajax({
        url: admin_url + 'hospital_management/save_appointment',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Update CSRF token
                if (response.csrf_token_name && response.csrf_token_hash) {
                    csrfTokenName = response.csrf_token_name;
                    csrfTokenHash = response.csrf_token_hash;
                }
                alert_float('success', response.message);
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                alert_float('danger', response.message);
                $btn.prop('disabled', false).html('<i class="fa fa-check"></i> Create Appointment');
            }
        },
        error: function(xhr, status, error) {
            console.error('Appointment save error:', status, error);
            let errorMsg = 'Error creating appointment';
            if (xhr.status === 419) {
                errorMsg = 'Session expired. Please refresh the page and try again.';
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            alert_float('danger', errorMsg);
            $btn.prop('disabled', false).html('<i class="fa fa-check"></i> Create Appointment');
        }
    });
}

function saveNewPatientAppointment() {
    const mode = $('input[name="patient_mode"]:checked').val();
    const $btn = $('#saveAppointmentBtn');
    
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
    
    // Create FormData to handle file uploads
    const formData = new FormData();
    
    // CRITICAL: Add CSRF token
    formData.append(csrfTokenName, csrfTokenHash);
    
    // ========== COMMON FIELDS (Both Modes) ==========
    formData.append('mode', mode);
    formData.append('reason_for_appointment', $('#reason_for_appointment').val());
    
    if (mode === 'appointment') {
        // ========== QUICK APPOINTMENT MODE - Minimal Fields ==========
        formData.append('name', $('#new_name').val());
        formData.append('mobile_number', $('#new_mobile').val());
        formData.append('patient_type', 'Regular');
        formData.append('gender', 'other');
        formData.append('registered_other_hospital', '0');
        formData.append('fee_payment', '0');
        formData.append('recommended_to_hospital', '0');
        formData.append('has_membership', '0');
        
    } else if (mode === 'walkin') {
        // ========== WALK-IN MODE - ALL FIELDS ==========
        
        // Basic information
        formData.append('name', $('input[name="walkin_name"]').val());
        formData.append('gender', $('select[name="walkin_gender"]').val());
        formData.append('age', $('input[name="walkin_age"]').val());
        formData.append('dob', $('input[name="walkin_dob"]').val());
        formData.append('patient_type', $('select[name="walkin_patient_type"]').val());
        
        // Contact information
        formData.append('mobile_number', $('input[name="walkin_mobile"]').val());
        formData.append('phone', $('input[name="walkin_phone"]').val());
        formData.append('email', $('input[name="walkin_email"]').val());
        
        // Address information
        formData.append('address', $('textarea[name="walkin_address"]').val());
        formData.append('address_landmark', $('input[name="walkin_landmark"]').val());
        formData.append('city', $('input[name="walkin_city"]').val());
        formData.append('state', $('input[name="walkin_state"]').val());
        formData.append('pincode', $('input[name="walkin_pincode"]').val());
        
        // Other hospital registration
        formData.append('registered_other_hospital', $('input[name="walkin_registered_other"]:checked').val() || '0');
        formData.append('other_hospital_patient_id', $('input[name="walkin_other_hospital_id"]').val() || '');
        
        // Fee payment
        formData.append('fee_payment', $('input[name="walkin_fee_payment"]:checked').val() || '0');
        
        // Recommendation
        formData.append('recommended_to_hospital', $('input[name="walkin_recommended_to_hospital"]:checked').val() || '0');
        formData.append('recommended_by', $('input[name="walkin_recommended_by"]').val() || '');
        
        // Recommendation file(s) - multiple files
        const recFiles = $('input[name="recommendation_file[]"]')[0]?.files;
        if (recFiles && recFiles.length > 0) {
            for (let i = 0; i < recFiles.length; i++) {
                formData.append('recommendation_file[]', recFiles[i]);
            }
        }
        
        // Membership
        formData.append('has_membership', $('input[name="walkin_has_membership"]:checked').val() || '0');
        formData.append('membership_type', $('input[name="walkin_membership_type"]').val() || '');
        formData.append('membership_number', $('input[name="walkin_membership_number"]').val() || '');
        formData.append('membership_expiry_date', $('input[name="walkin_membership_expiry_date"]').val() || '');
        formData.append('membership_notes', $('textarea[name="walkin_membership_notes"]').val() || '');
        
        // Membership file(s) - only if has membership
        if ($('input[name="walkin_has_membership"]:checked').val() == '1') {
            const memFiles = $('input[name="membership_file[]"]')[0]?.files;
            if (memFiles && memFiles.length > 0) {
                for (let i = 0; i < memFiles.length; i++) {
                    formData.append('membership_file[]', memFiles[i]);
                }
            }
        }
        
        // Other documents (optional)
        const otherFiles = $('input[name="other_documents[]"]')[0]?.files;
        if (otherFiles && otherFiles.length > 0) {
            for (let i = 0; i < otherFiles.length; i++) {
                formData.append('other_documents[]', otherFiles[i]);
            }
        }
    }
    
    // ========== VALIDATION ==========
    if (!formData.get('name') || !formData.get('mobile_number')) {
        alert_float('warning', 'Please fill name and mobile number');
        $btn.prop('disabled', false).html('<i class="fa fa-check"></i> Create Appointment');
        return;
    }
    
    // Validate mobile number format
    const mobileNumber = formData.get('mobile_number');
    if (!/^[6-9]\d{9}$/.test(mobileNumber)) {
        alert_float('warning', 'Please enter a valid 10-digit mobile number');
        $btn.prop('disabled', false).html('<i class="fa fa-check"></i> Create Appointment');
        return;
    }
    
    // ========== CREATE PATIENT FIRST ==========
    $.ajax({
        url: admin_url + 'hospital_management/save_quick_patient',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // ✅ UPDATE CSRF TOKEN FIRST
                if (response.csrf_token_name && response.csrf_token_hash) {
                    csrfTokenName = response.csrf_token_name;
                    csrfTokenHash = response.csrf_token_hash;
                }
                // ========== THEN CREATE APPOINTMENT ==========
                const appointmentData = {
                    [csrfTokenName]: csrfTokenHash,
                    patient_id: response.id,
                    patient_mode: mode,
                    is_new_patient: 1,
                    reason_for_appointment: $('#reason_for_appointment').val(),
                    appointment_date: $('#appointment_date').val(),
                    appointment_time: $('#appointment_time').val(),
                    consultant_id: $('#consultant_id').val(),
                    notes: $('#notes').val()
                };
                
                submitAppointment(appointmentData);
            } else {
                alert_float('danger', response.message);
                $btn.prop('disabled', false).html('<i class="fa fa-check"></i> Create Appointment');
            }
        },
        error: function(xhr) {
            let errorMsg = 'Error creating patient';
            if (xhr.status === 419) {
                errorMsg = 'Session expired. Please refresh the page and try again.';
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            alert_float('danger', errorMsg);
            $btn.prop('disabled', false).html('<i class="fa fa-check"></i> Create Appointment');
        }
    });
}

function submitAppointment(data) {
    const $btn = $('#saveAppointmentBtn');
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Creating...');
    
    $.ajax({
        url: admin_url + 'hospital_management/save_appointment',
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert_float('success', response.message);
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                alert_float('danger', response.message);
                $btn.prop('disabled', false).html('<i class="fa fa-check"></i> Create Appointment');
            }
        },
        error: function() {
            alert_float('danger', 'Error creating appointment');
            $btn.prop('disabled', false).html('<i class="fa fa-check"></i> Create Appointment');
        }
    });
}

function confirmAppointment(id) {
    if (confirm('Confirm this appointment?')) {
        $.ajax({
            url: admin_url + 'hospital_management/confirm_appointment/' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    location.reload();
                } else {
                    alert_float('danger', response.message);
                }
            }
        });
    }
}

function cancelAppointment(id) {
    $('#cancel_appointment_id').val(id);
    $('#cancelModal').modal('show');
}

function submitCancellation() {
    const id = $('#cancel_appointment_id').val();
    const reason = $('#cancellation_reason').val();
    
    $.ajax({
        url: admin_url + 'hospital_management/cancel_appointment',
        type: 'POST',
        data: { id: id, reason: reason },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert_float('success', response.message);
                $('#cancelModal').modal('hide');
                location.reload();
            } else {
                alert_float('danger', response.message);
            }
        }
    });
}

function deleteAppointment(id) {
    if (confirm('Are you sure you want to delete this appointment?')) {
        $.ajax({
            url: admin_url + 'hospital_management/delete_appointment/' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    location.reload();
                } else {
                    alert_float('danger', response.message);
                }
            }
        });
    }
}


// ============ TIME PICKER JAVASCRIPT ============
let selectedHour = null;
let selectedMinute = null;
let selectedSecond = '00';

$('#hourButtons .time-btn').click(function() {
    $('#hourButtons .time-btn').removeClass('selected');
    $(this).addClass('selected');
    selectedHour = $(this).data('hour');
    updateTimeDisplay();
});

$('#minuteButtons .time-btn').click(function() {
    $('#minuteButtons .time-btn').removeClass('selected');
    $(this).addClass('selected');
    selectedMinute = $(this).data('minute');
    updateTimeDisplay();
});

$('#secondButtons .time-btn').click(function() {
    $('#secondButtons .time-btn').removeClass('selected');
    $(this).addClass('selected');
    selectedSecond = $(this).data('second');
    updateTimeDisplay();
});

function updateTimeDisplay() {
    if (selectedHour !== null && selectedMinute !== null && selectedSecond !== null) {
        const timeString = selectedHour + ':' + selectedMinute + ':' + selectedSecond;
        $('#appointment_time').val(timeString);
        $('#displayTime').text(timeString);
        $('#timeDisplay').fadeIn();
    }
}

// ============ END TIME PICKER ============
</script>