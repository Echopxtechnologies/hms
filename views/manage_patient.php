<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<?php
// Redirect if no ID provided (no creation allowed)
if (!isset($patient)) {
    redirect(admin_url('hospital_management/patient_records'));
}
?>

<style>
/* Same styles as before */
.patient-form-wrapper {
    background: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    padding: 30px;
    margin-bottom: 25px;
}

.form-section {
    margin-bottom: 35px;
    padding-bottom: 25px;
    border-bottom: 2px solid #f0f0f0;
}

.form-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.form-section-title {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #333;
}

.form-section-title i {
    margin-right: 8px;
    color: #666;
}

.radio-inline,
.checkbox-inline {
    margin-right: 20px;
    font-weight: normal;
}

.btn-save-patient {
    background: #333;
    color: #fff;
    padding: 12px 30px;
    font-size: 15px;
    font-weight: 600;
    border: none;
    border-radius: 4px;
    transition: all 0.2s;
}

.btn-save-patient:hover {
    background: #000;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.btn-cancel {
    background: #fff;
    color: #666;
    padding: 12px 30px;
    font-size: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-right: 10px;
}

.btn-cancel:hover {
    background: #f5f5f5;
    color: #333;
}

.required-field::after {
    content: " *";
    color: #d32f2f;
}

.form-actions {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 2px solid #f0f0f0;
    text-align: right;
}

.alert-info {
    background: #e3f2fd;
    border: 1px solid #90caf9;
    color: #1976d2;
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 20px;
}
</style>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h3>
                    <i class="fa fa-edit"></i> Update Patient Information
                </h3>
                <hr>
                
                <div class="alert-info">
                    <i class="fa fa-info-circle"></i> 
                    <strong>Note:</strong> You are updating existing patient information. 
                    Patient ID: <strong><?php echo $patient->patient_number; ?></strong>
                </div>
            </div>
        </div>
        
        <!-- FIXED: Using form_open() for automatic CSRF token -->
        <?php echo form_open('', ['id' => 'patientForm']); ?>
            <input type="hidden" name="id" value="<?php echo $patient->id; ?>">
            
            <div class="row">
                <div class="col-md-12">
                    <div class="patient-form-wrapper">
                        
                        <!-- Patient Details Section -->
                        <div class="form-section">
                            <h4 class="form-section-title">
                                <i class="fa fa-user"></i> Patient Details
                            </h4>
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="name" class="control-label required-field">Name</label>
                                        <input type="text" 
                                               id="name" 
                                               name="name" 
                                               class="form-control" 
                                               value="<?php echo htmlspecialchars($patient->name); ?>"
                                               required>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="gender" class="control-label required-field">Gender</label>
                                        <select id="gender" name="gender" class="form-control selectpicker" required>
                                            <option value="male" <?php echo ($patient->gender == 'male') ? 'selected' : ''; ?>>Male</option>
                                            <option value="female" <?php echo ($patient->gender == 'female') ? 'selected' : ''; ?>>Female</option>
                                            <option value="other" <?php echo ($patient->gender == 'other') ? 'selected' : ''; ?>>Other</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="dob" class="control-label">Date of Birth</label>
                                        <input type="date" 
                                               id="dob" 
                                               name="dob" 
                                               class="form-control" 
                                               value="<?php echo $patient->dob; ?>"
                                               onchange="calculateAge()">
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="age" class="control-label">Age</label>
                                        <input type="number" 
                                               id="age" 
                                               name="age" 
                                               class="form-control" 
                                               value="<?php echo $patient->age; ?>"
                                               min="0" 
                                               max="150">
                                        <div class="age-display" id="ageDisplay"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Address Section -->
                        <div class="form-section">
                            <h4 class="form-section-title">
                                <i class="fa fa-map-marker"></i> Address Details
                            </h4>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="address" class="control-label">Address</label>
                                        <textarea id="address" 
                                                  name="address" 
                                                  class="form-control" 
                                                  rows="2"><?php echo htmlspecialchars($patient->address); ?></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="address_landmark" class="control-label">Landmark</label>
                                        <input type="text" 
                                               id="address_landmark" 
                                               name="address_landmark" 
                                               class="form-control" 
                                               value="<?php echo htmlspecialchars($patient->address_landmark); ?>">
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="city" class="control-label">City</label>
                                        <input type="text" 
                                               id="city" 
                                               name="city" 
                                               class="form-control" 
                                               value="<?php echo htmlspecialchars($patient->city); ?>">
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="state" class="control-label">State</label>
                                        <input type="text" 
                                               id="state" 
                                               name="state" 
                                               class="form-control" 
                                               value="<?php echo htmlspecialchars($patient->state); ?>">
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="pincode" class="control-label">Pincode</label>
                                        <input type="text" 
                                               id="pincode" 
                                               name="pincode" 
                                               class="form-control" 
                                               value="<?php echo htmlspecialchars($patient->pincode); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Contact Details Section -->
                        <div class="form-section">
                            <h4 class="form-section-title">
                                <i class="fa fa-phone"></i> Contact Details
                            </h4>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="phone" class="control-label">Phone</label>
                                        <input type="tel" 
                                               id="phone" 
                                               name="phone" 
                                               class="form-control" 
                                               value="<?php echo htmlspecialchars($patient->phone); ?>">
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="mobile_number" class="control-label required-field">Mobile Number</label>
                                        <input type="tel" 
                                               id="mobile_number" 
                                               name="mobile_number" 
                                               class="form-control" 
                                               value="<?php echo htmlspecialchars($patient->mobile_number); ?>"
                                               required>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="email" class="control-label">Email</label>
                                        <input type="email" 
                                               id="email" 
                                               name="email" 
                                               class="form-control" 
                                               value="<?php echo htmlspecialchars($patient->email); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Other Details Section -->
                        <div class="form-section">
                            <h4 class="form-section-title">
                                <i class="fa fa-clipboard"></i> Other Details
                            </h4>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="reason_for_appointment" class="control-label required-field">Reason for Appointment</label>
                                        <select id="reason_for_appointment" name="reason_for_appointment" class="form-control selectpicker" required>
                                            <option value="consultation" <?php echo ($patient->reason_for_appointment == 'consultation') ? 'selected' : ''; ?>>Consultation</option>
                                            <option value="procedure" <?php echo ($patient->reason_for_appointment == 'procedure') ? 'selected' : ''; ?>>Procedure</option>
                                            <option value="surgery" <?php echo ($patient->reason_for_appointment == 'surgery') ? 'selected' : ''; ?>>Surgery</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="patient_type" class="control-label required-field">Patient Type</label>
                                        <select id="patient_type" name="patient_type" class="form-control selectpicker" data-live-search="true" required>
                                            <?php foreach ($patient_types as $type) { ?>
                                            <option value="<?php echo $type['type_name']; ?>" 
                                                <?php echo ($patient->patient_type == $type['type_name']) ? 'selected' : ''; ?>>
                                                <?php echo $type['type_name']; ?>
                                            </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="form-actions">
                            <a href="<?php echo admin_url('hospital_management/patient_records'); ?>" class="btn btn-cancel">
                                <i class="fa fa-times"></i> Cancel
                            </a>
                            <button type="submit" id="savePatientBtn" class="btn btn-save-patient">
                                <i class="fa fa-check"></i> Update Patient
                            </button>
                        </div>
                        
                    </div>
                </div>
            </div>
        <?php echo form_close(); ?>
    </div>
</div>

<?php init_tail(); ?>

<script>
$(document).ready(function() {
    $('.selectpicker').selectpicker();
    calculateAge();
    
    // FIXED: Include CSRF token in Ajax request
    $('#patientForm').on('submit', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $btn = $('#savePatientBtn');
        const formData = $form.serialize(); // This now includes CSRF token from form_open()
        
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');
        
        $.ajax({
            url: admin_url + 'hospital_management/save_patient',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    setTimeout(function() {
                        window.location.href = admin_url + 'hospital_management/patient_records';
                    }, 1500);
                } else {
                    alert_float('danger', response.message);
                    $btn.prop('disabled', false).html('<i class="fa fa-check"></i> Update Patient');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert_float('danger', 'An error occurred while updating patient');
                $btn.prop('disabled', false).html('<i class="fa fa-check"></i> Update Patient');
            }
        });
    });
});

function calculateAge() {
    const dobInput = document.getElementById('dob');
    const ageInput = document.getElementById('age');
    const ageDisplay = document.getElementById('ageDisplay');
    
    if (!dobInput.value) {
        ageDisplay.textContent = '';
        return;
    }
    
    const dob = new Date(dobInput.value);
    const today = new Date();
    
    let age = today.getFullYear() - dob.getFullYear();
    const monthDiff = today.getMonth() - dob.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
        age--;
    }
    
    if (age >= 0 && age <= 150) {
        ageInput.value = age;
        ageDisplay.textContent = 'Calculated age: ' + age + ' years';
    }
}
</script>