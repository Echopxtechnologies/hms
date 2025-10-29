<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<?php
// Redirect if no ID provided (no creation allowed)
if (!isset($patient)) {
    redirect(admin_url('hospital_management/patient_records'));
}

// Get patient documents
$this->load->model('hospital_patients_model');
$documents = $this->hospital_patients_model->get_patient_documents($patient->id);
?>

<style>
/* Patient Form Styles */
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

/* Document Management Styles */
.documents-section {
    background: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    padding: 30px;
    margin-bottom: 25px;
}

.documents-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}

.documents-title {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin: 0;
}

.btn-upload-document {
    background: #333;
    color: #fff;
    padding: 8px 20px;
    border: none;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-upload-document:hover {
    background: #000;
    color: #fff;
    transform: translateY(-1px);
}

.documents-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
    margin-top: 20px;
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
    font-size: 40px;
    color: #666;
    margin-bottom: 10px;
    text-align: center;
}

.document-name {
    font-size: 14px;
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
    word-break: break-word;
}

.document-type {
    font-size: 12px;
    color: #666;
    margin-bottom: 5px;
}

.document-date {
    font-size: 11px;
    color: #999;
    margin-bottom: 10px;
}

.document-actions {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.btn-doc-action {
    flex: 1;
    padding: 6px 12px;
    font-size: 12px;
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

.btn-delete-doc {
    background: #fff;
    color: #f44336;
    border: 1px solid #f44336;
}

.btn-delete-doc:hover {
    background: #f44336;
    color: #fff;
    text-decoration: none;
}

.no-documents {
    text-align: center;
    padding: 40px 20px;
    color: #999;
}

.no-documents i {
    font-size: 60px;
    margin-bottom: 15px;
    display: block;
}

/* Upload Modal */
.modal-header {
    background: #333;
    color: #fff;
}

.modal-header .close {
    color: #fff;
    opacity: 0.8;
}

.modal-header .close:hover {
    opacity: 1;
}

.file-upload-area {
    border: 2px dashed #ddd;
    border-radius: 6px;
    padding: 30px;
    text-align: center;
    background: #f8f9fa;
    transition: all 0.2s;
}

.file-upload-area:hover {
    border-color: #333;
    background: #f0f0f0;
}

.file-upload-area i {
    font-size: 50px;
    color: #999;
    margin-bottom: 15px;
}

.file-info {
    margin-top: 15px;
    padding: 10px;
    background: #e3f2fd;
    border-radius: 4px;
    display: none;
}

.age-display {
    font-size: 12px;
    color: #666;
    margin-top: 5px;
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
        
        <!-- PATIENT FORM -->
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
                                
                                <div class="col-md-4">
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
                        
                        <!-- Address Section -->
                        <div class="form-section">
                            <h4 class="form-section-title">
                                <i class="fa fa-map-marker"></i> Address Information
                            </h4>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="address" class="control-label">Address</label>
                                        <textarea id="address" 
                                                  name="address" 
                                                  class="form-control" 
                                                  rows="3"><?php echo htmlspecialchars($patient->address); ?></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="address_landmark" class="control-label">Landmark</label>
                                        <input type="text" 
                                               id="address_landmark" 
                                               name="address_landmark" 
                                               class="form-control" 
                                               value="<?php echo htmlspecialchars($patient->address_landmark); ?>">
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
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
                                            <option value="follow_up" <?php echo ($patient->reason_for_appointment == 'follow_up') ? 'selected' : ''; ?>>Follow Up</option>
                                            <option value="emergency" <?php echo ($patient->reason_for_appointment == 'emergency') ? 'selected' : ''; ?>>Emergency</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fee_payment" class="control-label">Fee Payment Status</label>
                                        <select id="fee_payment" name="fee_payment" class="form-control selectpicker">
                                            <option value="not_applicable" <?php echo ($patient->fee_payment == 'not_applicable') ? 'selected' : ''; ?>>Not Applicable</option>
                                            <option value="yes" <?php echo ($patient->fee_payment == 'yes') ? 'selected' : ''; ?>>Paid</option>
                                            <option value="no" <?php echo ($patient->fee_payment == 'no') ? 'selected' : ''; ?>>Not Paid</option>
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
        
        <!-- DOCUMENTS SECTION -->
        <div class="row">
            <div class="col-md-12">
                <div class="documents-section">
                    <div class="documents-header">
                        <h4 class="documents-title">
                            <i class="fa fa-file-text-o"></i> Patient Documents
                        </h4>
                        <button type="button" class="btn btn-upload-document" data-toggle="modal" data-target="#uploadDocumentModal">
                            <i class="fa fa-upload"></i> Upload Document
                        </button>
                    </div>
                    
                    <div id="documentsContainer">
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
                                    <div class="document-card" id="doc-<?php echo $doc['id'] ; ?>">
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
                                            <a href="<?php echo admin_url('hospital_management/download_document/' . $doc['id'] ); ?>" 
                                               class="btn-doc-action btn-view"
                                               target="_blank">
                                                <i class="fa fa-download"></i> Download
                                            </a>
                                            <a href="javascript:void(0);" 
                                               onclick="deleteDocument(<?php echo $doc['id'] ; ?>)"
                                               class="btn-doc-action btn-delete-doc">
                                                <i class="fa fa-trash"></i> Delete
                                            </a>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Document Modal -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-upload"></i> Upload Document</h4>
            </div>
            <form id="uploadDocumentForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="patient_id" value="<?php echo $patient->id; ?>">
                    
                    <div class="form-group">
                        <label for="document_type">Document Type <span class="text-danger">*</span></label>
                        <select name="document_type" id="document_type" class="form-control" required>
                            <option value="">Select Type</option>
                            <option value="recommendation">Recommendation Letter</option>
                            <option value="membership">Membership Card</option>
                            <option value="medical_report">Medical Report</option>
                            <option value="prescription">Prescription</option>
                            <option value="lab_report">Lab Report</option>
                            <option value="insurance">Insurance Document</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="document_file">Select File <span class="text-danger">*</span></label>
                        <div class="file-upload-area" onclick="$('#document_file').click()">
                            <i class="fa fa-cloud-upload"></i>
                            <p>Click to select file or drag and drop</p>
                            <small class="text-muted">Supported: PDF, DOC, DOCX, JPG, PNG (Max: 5MB)</small>
                        </div>
                        <input type="file" 
                               name="document_file" 
                               id="document_file" 
                               style="display: none;" 
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                               required>
                        <div class="file-info" id="fileInfo"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="uploadBtn">
                        <i class="fa fa-upload"></i> Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
$(document).ready(function() {
    $('.selectpicker').selectpicker();
    calculateAge();
    
    // ========== PATIENT FORM SUBMIT ==========
    $('#patientForm').on('submit', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $btn = $('#savePatientBtn');
        const formData = $form.serialize();
        
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');
        
        $.ajax({
            url: '<?php echo admin_url("hospital_management/save_patient"); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    setTimeout(function() {
                        window.location.reload();
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
    
    // ========== FILE UPLOAD HANDLING ==========
    $('#document_file').on('change', function() {
        const file = this.files[0];
        const fileInfo = $('#fileInfo');
        
        if (file) {
            const size = (file.size / 1024 / 1024).toFixed(2); // Convert to MB
            fileInfo.html(`
                <strong>Selected:</strong> ${file.name}<br>
                <strong>Size:</strong> ${size} MB
            `).show();
        } else {
            fileInfo.hide();
        }
    });
    
    // ========== UPLOAD DOCUMENT FORM SUBMIT ==========
    $('#uploadDocumentForm').on('submit', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $btn = $('#uploadBtn');
        const formData = new FormData(this);
        
        // Validate file size
        const file = $('#document_file')[0].files[0];
        if (file && file.size > 5 * 1024 * 1024) { // 5MB
            alert_float('danger', 'File size must be less than 5MB');
            return;
        }
        
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Uploading...');
        
        $.ajax({
            url: admin_url + 'hospital_management/upload_patient_document',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    $('#uploadDocumentModal').modal('hide');
                    $form[0].reset();
                    $('#fileInfo').hide();
                    
                    // Reload page to show new document
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    alert_float('danger', response.message);
                }
                $btn.prop('disabled', false).html('<i class="fa fa-upload"></i> Upload');
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert_float('danger', 'An error occurred while uploading document');
                $btn.prop('disabled', false).html('<i class="fa fa-upload"></i> Upload');
            }
        });
    });
    
    // Reset form when modal is closed
    $('#uploadDocumentModal').on('hidden.bs.modal', function() {
        $('#uploadDocumentForm')[0].reset();
        $('#fileInfo').hide();
    });
});

// ========== DELETE DOCUMENT ==========
function deleteDocument(documentId) {
    if (!confirm('Are you sure you want to delete this document?')) {
        return;
    }
    
    $.ajax({
        url: admin_url + 'hospital_management/delete_document/' + documentId,
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert_float('success', response.message);
                $('#doc-' + documentId).fadeOut(300, function() {
                    $(this).remove();
                    
                    // Check if no documents left
                    if ($('.documents-grid .document-card').length === 0) {
                        $('#documentsContainer').html(`
                            <div class="no-documents">
                                <i class="fa fa-folder-open-o"></i>
                                <p>No documents uploaded yet</p>
                            </div>
                        `);
                    }
                });
            } else {
                alert_float('danger', response.message);
            }
        },
        error: function() {
            alert_float('danger', 'An error occurred while deleting document');
        }
    });
}

// ========== AGE CALCULATION ==========
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