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
                    
                    <!-- Hidden Control Fields -->
                    <input type="hidden" name="patient_id" id="patient_id" value="">
                    <input type="hidden" name="is_new_patient" id="is_new_patient" value="1">
                    <input type="hidden" name="patient_mode" id="patient_mode" value="appointment">
                    
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
                                <label class="control-label"><strong>Appointment Mode:</strong></label>
                                <div>
                                    <label class="radio-inline">
                                        <input type="radio" name="mode_option" value="appointment" checked>
                                        Appointment
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="mode_option" value="walk_in">
                                        Walk-in
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- ========== SECTION 1: EXISTING PATIENT SELECTION ========== -->
                    <div id="existingPatientSection" style="display:block;">
                        <div class="patient-search-info">
                            <i class="fa fa-info-circle"></i> 
                            <strong>Search by:</strong> Patient ID (e.g., PAT2025001), Name, or Mobile Number
                        </div>
                        
                        <div class="form-group">
                            <label for="existing_patient_dropdown" class="control-label">Select Patient *</label>
                            <select id="existing_patient_dropdown" class="form-control selectpicker" data-live-search="true">
                                <option value="">-- Type to search patients --</option>
                            </select>
                        </div>
                        
                        <!-- Mode selection for existing patient -->
                        <div id="existingPatientModeSection" style="display:none;">
                            <div class="form-group">
                                <label class="control-label"><strong>Appointment Mode:</strong></label>
                                <div>
                                    <label class="radio-inline">
                                        <input type="radio" name="existing_mode_option" value="appointment" checked>
                                        Appointment
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="existing_mode_option" value="walk_in">
                                        Walk-in
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- ========== SECTION 2: PATIENT FORM (UNIFIED FOR ALL MODES) ========== -->
                    <div id="patientFormSection" style="display:none;">
                        <div class="alert alert-info" id="formModeInfo">
                            <i class="fa fa-info-circle"></i> <span id="formModeText">Quick registration</span>
                        </div>
                        
                        <!-- Basic Information -->
                        <div class="form-section-title"><i class="fa fa-user"></i> Basic Information</div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Name <span class="text-danger" id="name_required">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Full name">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group" id="gender_group">
                                    <label class="control-label">Gender <span class="text-danger" id="gender_required">*</span></label>
                                    <select name="gender" id="gender" class="form-control">
                                        <option value="">Select</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group" id="age_group">
                                    <label class="control-label">Age</label>
                                    <input type="number" name="age" id="age" class="form-control" min="0" max="150">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row" id="extended_basic_fields">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Date of Birth</label>
                                    <input type="date" name="dob" id="dob" class="form-control">
                                </div>
                            </div>
                            
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="control-label">Patient Type <span class="text-danger" id="patient_type_required">*</span></label>
                                    <select name="patient_type" id="patient_type" class="form-control">
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
                        <div class="form-section-title" id="contact_section"><i class="fa fa-phone"></i> Contact Details</div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Mobile <span class="text-danger" id="mobile_required">*</span></label>
                                    <input type="tel" name="mobile_number" id="mobile_number" class="form-control">
                                </div>
                            </div>
                            
                            <div class="col-md-4" id="phone_group">
                                <div class="form-group">
                                    <label class="control-label">Phone</label>
                                    <input type="tel" name="phone" id="phone" class="form-control">
                                </div>
                            </div>
                            
                            <div class="col-md-4" id="email_group">
                                <div class="form-group">
                                    <label class="control-label">Email</label>
                                    <input type="email" name="email" id="email" class="form-control">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Address Details -->
                        <div id="address_section">
                            <div class="form-section-title"><i class="fa fa-map-marker"></i> Address Details</div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Address</label>
                                        <textarea name="address" id="address" class="form-control" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Landmark</label>
                                        <input type="text" name="address_landmark" id="address_landmark" class="form-control">
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">City</label>
                                        <input type="text" name="city" id="city" class="form-control">
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">State</label>
                                        <input type="text" name="state" id="state" class="form-control">
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label">Pincode</label>
                                        <input type="text" name="pincode" id="pincode" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Other Details -->
                        <div id="other_details_section">
                            <div class="form-section-title"><i class="fa fa-clipboard"></i> Other Details</div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Registered at Other Hospital?</label>
                                        <div>
                                            <label class="radio-inline">
                                                <input type="radio" name="registered_other_hospital" value="1"> Yes
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="registered_other_hospital" value="0" checked> No
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Fee Payment?</label>
                                        <div>
                                            <label class="radio-inline">
                                                <input type="radio" name="fee_payment" value="yes"> Yes
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="fee_payment" value="no"> No
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="fee_payment" value="not_applicable" checked> N/A
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Recommendation Section -->
                        <div id="recommendation_section">
                            <div class="form-section-title"><i class="fa fa-handshake-o"></i> Recommendation Details</div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Have You Been Recommended To This Hospital?</label>
                                        <div>
                                            <label class="radio-inline">
                                                <input type="radio" name="recommended_to_hospital" value="1" class="recommendation_toggle"> Yes
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="recommended_to_hospital" value="0" class="recommendation_toggle" checked> No
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="recommendation_details" style="display:none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Recommended By</label>
                                            <input type="text" name="recommended_by" id="recommended_by" class="form-control">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Recommendation File (Multiple files allowed)</label>
                                            <input type="file" name="recommendation_file[]" id="recommendation_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" multiple>
                                            <small class="text-muted">Upload recommendation letter/document(s)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Membership Section -->
                        <div id="membership_section">
                            <div class="form-section-title"><i class="fa fa-id-card"></i> Membership Details</div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Do You Have Hospital Membership?</label>
                                        <div>
                                            <label class="radio-inline">
                                                <input type="radio" name="has_membership" value="1" class="membership_toggle"> Yes
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="has_membership" value="0" class="membership_toggle" checked> No
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="membership_details" style="display:none;">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Membership Type</label>
                                            <input type="text" name="membership_type" id="membership_type" class="form-control">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Membership Number</label>
                                            <input type="text" name="membership_number" id="membership_number" class="form-control">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Expiry Date</label>
                                            <input type="date" name="membership_expiry_date" id="membership_expiry_date" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">Membership Notes</label>
                                            <textarea name="membership_notes" id="membership_notes" class="form-control" rows="2"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- ========== SECTION 3: APPOINTMENT DETAILS (ALWAYS VISIBLE) ========== -->
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

                    <!-- Time Picker -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Appointment Time *</label>
                                <input type="hidden" id="appointment_time" name="appointment_time" required>
                                
                                <div class="time-picker-container">
                                    <div class="time-section">
                                        <label>Hour</label>
                                        <div class="time-buttons" id="hourButtons">
                                            <?php for ($h = 0; $h < 24; $h++): ?>
                                            <button type="button" class="time-btn" data-hour="<?php echo str_pad($h, 2, '0', STR_PAD_LEFT); ?>">
                                                <?php echo str_pad($h, 2, '0', STR_PAD_LEFT); ?>
                                            </button>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="time-separator">:</div>
                                    
                                    <div class="time-section">
                                        <label>Minute</label>
                                        <div class="time-buttons" id="minuteButtons">
                                            <?php for ($m = 0; $m < 60; $m += 5): ?>
                                            <button type="button" class="time-btn" data-minute="<?php echo str_pad($m, 2, '0', STR_PAD_LEFT); ?>">
                                                <?php echo str_pad($m, 2, '0', STR_PAD_LEFT); ?>
                                            </button>
                                            <?php endfor; ?>
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
                    
                    <!-- Consultant Dropdown -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="consultant_id" class="control-label">
                                    Consultant <span class="text-danger">*</span>
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
// ============================================================================
// CONFIGURATION & GLOBALS
// ============================================================================
let csrfTokenName = '<?php echo $this->security->get_csrf_token_name(); ?>';
let csrfTokenHash = '<?php echo $this->security->get_csrf_hash(); ?>';

// ============================================================================
// DOCUMENT READY
// ============================================================================
$(document).ready(function() {
    // Initialize plugins
    $('.selectpicker').selectpicker();
    
    // Load patients for dropdown
    loadPatients();
    
    // ========== EVENT HANDLERS ==========
    
    // Patient Type Selection (Existing vs New)
    $('input[name="patient_type_option"]').on('change', handlePatientTypeChange);
    
    // Mode Selection for New Patients
    $('input[name="mode_option"]').on('change', handleNewPatientModeChange);
    
    // Existing Patient Selection
    $('#existing_patient_dropdown').on('change', handleExistingPatientSelection);
    
    // Existing Patient Mode Selection
    $('input[name="existing_mode_option"]').on('change', handleExistingPatientModeChange);
    
    // Recommendation Toggle
    $('.recommendation_toggle').on('change', function() {
        if ($('input[name="recommended_to_hospital"]:checked').val() == '1') {
            $('#recommendation_details').slideDown();
        } else {
            $('#recommendation_details').slideUp();
        }
    });
    
    // Membership Toggle
    $('.membership_toggle').on('change', function() {
        if ($('input[name="has_membership"]:checked').val() == '1') {
            $('#membership_details').slideDown();
        } else {
            $('#membership_details').slideUp();
        }
    });
    
    // Time Picker
    initializeTimePicker();
    
    // Save Button
    $('#saveAppointmentBtn').on('click', handleSaveAppointment);
    
    // Modal Reset
    $('#appointmentModal').on('hidden.bs.modal', resetModal);
    
    // Initialize default state
    handlePatientTypeChange();
});

// ============================================================================
// PATIENT TYPE HANDLING
// ============================================================================
function handlePatientTypeChange() {
    const type = $('input[name="patient_type_option"]:checked').val();
    
    if (type === 'existing') {
        // EXISTING PATIENT MODE
        $('#is_new_patient').val('0');
        $('#existingPatientSection').show();
        $('#modeSelection').hide();
        $('#patientFormSection').hide();
        
        // Reset patient selection
        $('#existing_patient_dropdown').val('').selectpicker('refresh');
        $('#patient_id').val('');
        $('#existingPatientModeSection').hide();
        
    } else {
        // NEW PATIENT MODE
        $('#is_new_patient').val('1');
        $('#existingPatientSection').hide();
        $('#modeSelection').show();
        $('#patient_id').val('');
        
        // Trigger mode selection
        handleNewPatientModeChange();
    }
}

function handleNewPatientModeChange() {
    const mode = $('input[name="mode_option"]:checked').val();
    $('#patient_mode').val(mode);
    
    if (mode === 'appointment') {
        // APPOINTMENT MODE - Minimal fields
        showPatientForm('minimal');
    } else {
        // WALK-IN MODE - Full form
        showPatientForm('full');
    }
}

function handleExistingPatientSelection() {
    const patientId = $(this).val();
    
    if (patientId) {
        $('#patient_id').val(patientId);
        $('#existingPatientModeSection').show();
        
        // Load patient data
        loadPatientData(patientId);
    } else {
        $('#patient_id').val('');
        $('#existingPatientModeSection').hide();
        $('#patientFormSection').hide();
    }
}

function handleExistingPatientModeChange() {
    const mode = $('input[name="existing_mode_option"]:checked').val();
    $('#patient_mode').val(mode);
    
    const patientId = $('#patient_id').val();
    
    if (!patientId) {
        alert_float('warning', 'Please select a patient first');
        return;
    }
    
    if (mode === 'appointment') {
        // APPOINTMENT MODE - Hide patient form
        $('#patientFormSection').hide();
    } else {
        // WALK-IN MODE - Show full form with patient data
        loadPatientData(patientId, function() {
            showPatientForm('full');
        });
    }
}

// ============================================================================
// FORM VISIBILITY CONTROL
// ============================================================================
function showPatientForm(mode) {
    $('#patientFormSection').show();
    
    if (mode === 'minimal') {
        // Quick registration - only essential fields
        $('#formModeText').text('Quick registration - Only Name and Mobile required');
        
        // Show minimal fields
        $('#name_required').show();
        $('#mobile_required').show();
        $('#gender_required').hide();
        $('#patient_type_required').hide();
        
        // Hide extended sections
        $('#gender_group').hide();
        $('#age_group').hide();
        $('#extended_basic_fields').hide();
        $('#phone_group').hide();
        $('#email_group').hide();
        $('#address_section').hide();
        $('#other_details_section').hide();
        $('#recommendation_section').hide();
        $('#membership_section').hide();
        
        // Set defaults for hidden fields
        $('#gender').val('other');
        $('#patient_type').val('Regular');
        $('input[name="registered_other_hospital"][value="0"]').prop('checked', true);
        $('input[name="fee_payment"][value="not_applicable"]').prop('checked', true);
        $('input[name="recommended_to_hospital"][value="0"]').prop('checked', true);
        $('input[name="has_membership"][value="0"]').prop('checked', true);
        
    } else {
        // Full registration - all fields
        $('#formModeText').text('Complete patient registration - Fields marked with * are required');
        
        // Show all required markers
        $('#name_required').show();
        $('#mobile_required').show();
        $('#gender_required').show();
        $('#patient_type_required').show();
        
        // Show all sections
        $('#gender_group').show();
        $('#age_group').show();
        $('#extended_basic_fields').show();
        $('#phone_group').show();
        $('#email_group').show();
        $('#address_section').show();
        $('#other_details_section').show();
        $('#recommendation_section').show();
        $('#membership_section').show();
    }
}

// ============================================================================
// DATA LOADING
// ============================================================================
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
                $('#existing_patient_dropdown').html(options).selectpicker('refresh');
            }
        }
    });
}

function loadPatientData(patientId, callback) {
    $.ajax({
        url: admin_url + 'hospital_management/get_patient_details/' + patientId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const p = response.patient;
                
                // Populate all form fields with consistent names
                $('#name').val(p.name || '');
                $('#gender').val(p.gender || '');
                $('#age').val(p.age || '');
                $('#dob').val(p.dob || '');
                $('#patient_type').val(p.patient_type || 'Regular');
                $('#mobile_number').val(p.mobile_number || '');
                $('#phone').val(p.phone || '');
                $('#email').val(p.email || '');
                $('#address').val(p.address || '');
                $('#address_landmark').val(p.address_landmark || '');
                $('#city').val(p.city || '');
                $('#state').val(p.state || '');
                $('#pincode').val(p.pincode || '');
                
                // Radio buttons
                $('input[name="registered_other_hospital"][value="' + (p.registered_other_hospital || '0') + '"]').prop('checked', true);
                $('input[name="fee_payment"][value="' + (p.fee_payment || 'not_applicable') + '"]').prop('checked', true);
                
                // Recommendation
                const recommended = p.recommended_to_hospital == '1' ? '1' : '0';
                $('input[name="recommended_to_hospital"][value="' + recommended + '"]').prop('checked', true);
                if (recommended == '1') {
                    $('#recommendation_details').show();
                    $('#recommended_by').val(p.recommended_by || '');
                }
                
                // Membership
                const hasMembership = p.has_membership == '1' ? '1' : '0';
                $('input[name="has_membership"][value="' + hasMembership + '"]').prop('checked', true);
                if (hasMembership == '1') {
                    $('#membership_details').show();
                    $('#membership_type').val(p.membership_type || '');
                    $('#membership_number').val(p.membership_number || '');
                    $('#membership_expiry_date').val(p.membership_expiry_date || '');
                    $('#membership_notes').val(p.membership_notes || '');
                }
                
                if (callback) callback();
            }
        }
    });
}

// ============================================================================
// TIME PICKER
// ============================================================================
function initializeTimePicker() {
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
    
    function updateTimeDisplay() {
        if (selectedHour !== null && selectedMinute !== null && selectedSecond !== null) {
            const timeString = selectedHour + ':' + selectedMinute + ':' + selectedSecond;
            $('#appointment_time').val(timeString);
            $('#displayTime').text(timeString);
            $('#timeDisplay').fadeIn();
        }
    }
}

// ============================================================================
// SAVE APPOINTMENT
// ============================================================================
function handleSaveAppointment() {
    const $btn = $(this);
    const isNewPatient = $('#is_new_patient').val();
    
    // Basic validation
    if (isNewPatient == '0' && !$('#patient_id').val()) {
        alert_float('warning', 'Please select a patient');
        return;
    }
    
    if (!$('#appointment_date').val() || !$('#appointment_time').val() || 
        !$('#reason_for_appointment').val() || !$('#consultant_id').val()) {
        alert_float('warning', 'Please fill all required appointment fields');
        return;
    }
    
    // Validate patient data for new patients
    if (isNewPatient == '1') {
        if (!$('#name').val() || !$('#mobile_number').val()) {
            alert_float('warning', 'Please fill patient name and mobile number');
            return;
        }
        
        // Validate mobile format
        const mobile = $('#mobile_number').val();
        if (!/^[6-9]\d{9}$/.test(mobile)) {
            alert_float('warning', 'Please enter a valid 10-digit mobile number');
            return;
        }
    }
    
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
    
    // Create FormData
    const formData = new FormData($('#appointmentForm')[0]);
    
    // Add CSRF token
    formData.append(csrfTokenName, csrfTokenHash);
    
    // Submit
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
            console.error('Error:', status, error);
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

// ============================================================================
// MODAL RESET
// ============================================================================
function resetModal() {
    $('#appointmentForm')[0].reset();
    $('#patient_id').val('');
    $('#is_new_patient').val('1');
    $('#patient_mode').val('appointment');
    
    $('input[name="patient_type_option"][value="existing"]').prop('checked', true);
    $('input[name="mode_option"][value="appointment"]').prop('checked', true);
    $('input[name="existing_mode_option"][value="appointment"]').prop('checked', true);
    
    $('#existingPatientSection').show();
    $('#modeSelection').hide();
    $('#patientFormSection').hide();
    $('#existingPatientModeSection').hide();
    $('#recommendation_details').hide();
    $('#membership_details').hide();
    
    $('.selectpicker').selectpicker('refresh');
    
    // Reset time picker
    $('#hourButtons .time-btn').removeClass('selected');
    $('#minuteButtons .time-btn').removeClass('selected');
    $('#appointment_time').val('');
    $('#timeDisplay').hide();
}

// ============================================================================
// APPOINTMENT ACTIONS
// ============================================================================
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
</script>