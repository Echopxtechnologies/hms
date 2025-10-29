<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
/* Action buttons on hover - matching users table style */
.appointment-actions {
    display: flex;
    gap: 8px;
    opacity: 0;
    max-height: 0;
    overflow: hidden;
    transition: all 0.2s ease;
}

.table tbody tr:hover .appointment-actions {
    opacity: 1;
    max-height: 30px;
}

.action-link-appointment {
    font-size: 12px;
    font-weight: 500;
    color: #999;
    text-decoration: none;
    transition: color 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    cursor: pointer;
}

.action-link-appointment:hover {
    color: #333;
}

.action-link-appointment i {
    font-size: 11px;
}

.action-link-confirm:hover {
    color: #2e7d32;
}

.action-link-reject:hover {
    color: #f57c00;
}

.action-link-delete:hover {
    color: #d32f2f;
}

/* Modal styling */
.detail-row {
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 600;
    color: #555;
    margin-bottom: 5px;
}

.detail-value {
    color: #333;
    font-size: 14px;
}

.label-lg {
    font-size: 13px;
    padding: 6px 12px;
}
</style>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                
                <!-- Page Header -->
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <i class="fa fa-calendar"></i> 
                            <?php if ($is_jc): ?>
                                All Appointments (Junior Consultant View)
                            <?php else: ?>
                                My Appointments
                            <?php endif; ?>
                        </h4>
                        <hr class="hr-panel-heading">
                        
                        <!-- Statistics Cards -->
                        <div class="row">
                            <div class="col-md-3 col-sm-6">
                                <div class="tw-mb-2 lg:tw-mb-4">
                                    <div class="panel_s">
                                        <div class="panel-body padding-10">
                                            <h3 class="text-muted tw-mb-0" id="stat-total">
                                                <?php echo isset($statistics['total']) ? $statistics['total'] : 0; ?>
                                            </h3>
                                            <span class="text-dark tw-font-semibold">Total Appointments</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-sm-6">
                                <div class="tw-mb-2 lg:tw-mb-4">
                                    <div class="panel_s">
                                        <div class="panel-body padding-10">
                                            <h3 class="text-warning tw-mb-0" id="stat-pending">
                                                <?php echo isset($statistics['pending']) ? $statistics['pending'] : 0; ?>
                                            </h3>
                                            <span class="text-dark tw-font-semibold">Pending</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-sm-6">
                                <div class="tw-mb-2 lg:tw-mb-4">
                                    <div class="panel_s">
                                        <div class="panel-body padding-10">
                                            <h3 class="text-success tw-mb-0" id="stat-confirmed">
                                                <?php echo isset($statistics['confirmed']) ? $statistics['confirmed'] : 0; ?>
                                            </h3>
                                            <span class="text-dark tw-font-semibold">Confirmed</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-sm-6">
                                <div class="tw-mb-2 lg:tw-mb-4">
                                    <div class="panel_s">
                                        <div class="panel-body padding-10">
                                            <h3 class="text-info tw-mb-0" id="stat-today">
                                                <?php echo isset($statistics['today']) ? $statistics['today'] : 0; ?>
                                            </h3>
                                            <span class="text-dark tw-font-semibold">Today</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Appointments Table -->
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover dt-table" id="appointments-table">
                                <thead>
                                    <tr>
                                        <th>Appointment #</th>
                                        <th>Patient</th>
                                        <th>Mobile</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <?php if ($is_jc): ?>
                                        <th>Consultant</th>
                                        <?php endif; ?>
                                        <th>Status</th>
                                        <th width="15%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($appointments)): ?>
                                        <?php foreach ($appointments as $appt) { ?>
                                        <tr>
                                            <td>
                                                <strong class="text-primary"><?php echo htmlspecialchars($appt['appointment_number'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($appt['patient_name'], ENT_QUOTES, 'UTF-8'); ?><br>
                                                <small class="text-muted"><?php echo htmlspecialchars($appt['patient_number'], ENT_QUOTES, 'UTF-8'); ?></small>
                                            </td>
                                            <td>
                                                <a href="tel:<?php echo htmlspecialchars($appt['patient_mobile'], ENT_QUOTES, 'UTF-8'); ?>">
                                                    <?php echo htmlspecialchars($appt['patient_mobile'], ENT_QUOTES, 'UTF-8'); ?>
                                                </a>
                                            </td>
                                            <td>
                                                <?php echo date('d M Y', strtotime($appt['appointment_date'])); ?>
                                            </td>
                                            <td>
                                                <?php echo date('h:i A', strtotime($appt['appointment_time'])); ?>
                                            </td>
                                            <?php if ($is_jc): ?>
                                            <td>
                                                <?php 
                                                $consultant_name = trim(($appt['consultant_firstname'] ?? '') . ' ' . ($appt['consultant_lastname'] ?? ''));
                                                if (empty($consultant_name)) {
                                                    echo '<span class="label label-default">Not Assigned</span>';
                                                } else {
                                                    echo htmlspecialchars($consultant_name, ENT_QUOTES, 'UTF-8');
                                                }
                                                ?>
                                            </td>
                                            <?php endif; ?>
                                            <td>
                                                <?php
                                                $statusBadges = [
                                                    'pending' => 'warning',
                                                    'confirmed' => 'success',
                                                    'completed' => 'info',
                                                    'cancelled' => 'danger'
                                                ];
                                                $badgeClass = $statusBadges[$appt['status']] ?? 'default';
                                                ?>
                                                <span class="label label-<?php echo $badgeClass; ?>">
                                                    <?php echo ucfirst($appt['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                                                    <!-- View Button -->
                                                    <button class="btn btn-default btn-xs btn-view-appointment" 
                                                            data-id="<?php echo $appt['id']; ?>"
                                                            title="View Details">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                    
                                                    <?php if ($appt['status'] == 'pending'): ?>
                                                        <!-- Confirm Button -->
                                                        <button class="btn btn-success btn-xs btn-confirm-appointment" 
                                                                data-id="<?php echo $appt['id']; ?>"
                                                                data-number="<?php echo htmlspecialchars($appt['appointment_number'], ENT_QUOTES, 'UTF-8'); ?>"
                                                                title="Confirm Appointment">
                                                            <i class="fa fa-check"></i>
                                                        </button>
                                                        
                                                        <!-- Reject Button -->
                                                        <button class="btn btn-warning btn-xs btn-reject-appointment" 
                                                                data-id="<?php echo $appt['id']; ?>"
                                                                data-number="<?php echo htmlspecialchars($appt['appointment_number'], ENT_QUOTES, 'UTF-8'); ?>"
                                                                title="Reject Appointment">
                                                            <i class="fa fa-times"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($is_jc): ?>
                                                        <!-- Delete Button (JC Only) -->
                                                        <button class="btn btn-danger btn-xs btn-delete-appointment" 
                                                                data-id="<?php echo $appt['id']; ?>"
                                                                data-number="<?php echo htmlspecialchars($appt['appointment_number'], ENT_QUOTES, 'UTF-8'); ?>"
                                                                title="Delete Appointment">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="<?php echo $is_jc ? '8' : '7'; ?>" class="text-center text-muted">
                                                <i class="fa fa-info-circle"></i> No appointments found
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

<!-- View Appointment Modal -->
<div class="modal fade" id="appointmentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">
                    <i class="fa fa-calendar-check-o"></i> Appointment Details
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="detail-row">
                            <div class="detail-label">Appointment Number</div>
                            <div class="detail-value" id="m-appt-number">-</div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Patient Name</div>
                            <div class="detail-value" id="m-patient-name">-</div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Patient ID</div>
                            <div class="detail-value" id="m-patient-number">-</div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Mobile Number</div>
                            <div class="detail-value" id="m-patient-mobile">-</div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Email</div>
                            <div class="detail-value" id="m-patient-email">-</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="detail-row">
                            <div class="detail-label">Appointment Date</div>
                            <div class="detail-value" id="m-appt-date">-</div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Appointment Time</div>
                            <div class="detail-value" id="m-appt-time">-</div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Consultant</div>
                            <div class="detail-value" id="m-consultant">-</div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Status</div>
                            <div class="detail-value" id="m-status">-</div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Reason for Appointment</div>
                            <div class="detail-value" id="m-reason">-</div>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="detail-row">
                            <div class="detail-label">Notes</div>
                            <div class="detail-value" id="m-notes">-</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Appointment Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">
                    <i class="fa fa-times-circle"></i> Reject Appointment
                </h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="reject-appointment-id">
                
                <div class="form-group">
                    <label for="reject-reason">Reason for Rejection <span class="text-danger">*</span></label>
                    <textarea class="form-control" 
                              id="reject-reason" 
                              rows="4" 
                              placeholder="Please provide a reason for rejecting this appointment..."
                              required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" onclick="submitReject()">
                    <i class="fa fa-times"></i> Reject Appointment
                </button>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
$(document).ready(function() {
    'use strict';
    
    // Perfex CRM automatically initializes tables with dt-table class
    // No manual DataTable initialization needed to avoid reinitialization error
    
    // View appointment details (Button click)
    $(document).on('click', '.btn-view-appointment', function() {
        viewAppointment($(this).data('id'));
    });
    
    // Confirm appointment (Button click)
    $(document).on('click', '.btn-confirm-appointment', function() {
        confirmAppointment($(this).data('id'), $(this).data('number'));
    });
    
    // Reject appointment (Button click)
    $(document).on('click', '.btn-reject-appointment', function() {
        rejectAppointment($(this).data('id'), $(this).data('number'));
    });
    
    // Delete appointment (Button click)
    $(document).on('click', '.btn-delete-appointment', function() {
        deleteAppointment($(this).data('id'), $(this).data('number'));
    });
});

// View appointment details
function viewAppointment(appointmentId) {
    $.ajax({
        url: admin_url + 'hospital_management/get_appointment_details/' + appointmentId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data) {
                var d = response.data;
                
                // Populate modal fields
                $('#m-appt-number').text(d.appointment_number || '-');
                $('#m-patient-name').text(d.patient_name || '-');
                $('#m-patient-number').text(d.patient_number || '-');
                $('#m-patient-mobile').text(d.patient_mobile || '-');
                $('#m-patient-email').text(d.patient_email || 'N/A');
                
                // Format dates
                if (d.appointment_date) {
                    var apptDate = new Date(d.appointment_date);
                    var months = ['January', 'February', 'March', 'April', 'May', 'June', 
                                 'July', 'August', 'September', 'October', 'November', 'December'];
                    $('#m-appt-date').text(
                        apptDate.getDate() + ' ' + 
                        months[apptDate.getMonth()] + ' ' + 
                        apptDate.getFullYear()
                    );
                } else {
                    $('#m-appt-date').text('-');
                }
                
                if (d.appointment_time) {
                    var timeParts = d.appointment_time.split(':');
                    var hours = parseInt(timeParts[0]);
                    var minutes = timeParts[1];
                    var ampm = hours >= 12 ? 'PM' : 'AM';
                    hours = hours % 12;
                    hours = hours ? hours : 12; // 0 should be 12
                    $('#m-appt-time').text(hours + ':' + minutes + ' ' + ampm);
                } else {
                    $('#m-appt-time').text('-');
                }
                
                // Consultant name
                var consultantName = ((d.consultant_firstname || '') + ' ' + (d.consultant_lastname || '')).trim();
                $('#m-consultant').text(consultantName || 'Not Assigned');
                
                // Clinical info
                $('#m-reason').text(d.reason_for_appointment || '-');
                $('#m-notes').text(d.notes || 'No notes available');
                
                // Status badge
                var statusBadges = {
                    'pending': 'warning',
                    'confirmed': 'success',
                    'completed': 'info',
                    'cancelled': 'danger'
                };
                var status = d.status || 'pending';
                var badgeClass = statusBadges[status] || 'default';
                var statusText = status.charAt(0).toUpperCase() + status.slice(1);
                $('#m-status').html('<span class="label label-' + badgeClass + ' label-lg">' + statusText + '</span>');
                
                // Show modal
                $('#appointmentModal').modal('show');
            } else {
                alert_float('danger', response.message || 'Failed to load appointment details');
            }
        },
        error: function(xhr, error, thrown) {
            console.error('Details AJAX Error:', error, thrown);
            alert_float('danger', 'Error loading appointment details');
        }
    });
}

// Confirm appointment
function confirmAppointment(id, appointmentNumber) {
    if (confirm('Confirm appointment ' + appointmentNumber + '?\n\nThis will mark the appointment as confirmed.')) {
        $.ajax({
            url: admin_url + 'hospital_management/confirm_consultant_appointment/' + id,
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
            error: function(xhr) {
                console.error('Confirm Error:', xhr);
                var errorMsg = 'An error occurred while confirming appointment';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                alert_float('danger', errorMsg);
            }
        });
    }
}

// Reject appointment (show modal)
function rejectAppointment(id, appointmentNumber) {
    $('#reject-appointment-id').val(id);
    $('#reject-reason').val('');
    $('#rejectModal').modal('show');
}

// Submit reject
function submitReject() {
    var id = $('#reject-appointment-id').val();
    var reason = $('#reject-reason').val().trim();
    
    if (!reason) {
        alert_float('warning', 'Please provide a reason for rejection');
        return;
    }
    
    $.ajax({
        url: admin_url + 'hospital_management/reject_consultant_appointment',
        type: 'POST',
        data: {
            id: id,
            reason: reason
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#rejectModal').modal('hide');
                alert_float('success', response.message);
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                alert_float('danger', response.message);
            }
        },
        error: function(xhr) {
            console.error('Reject Error:', xhr);
            var errorMsg = 'An error occurred while rejecting appointment';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            alert_float('danger', errorMsg);
        }
    });
}

// Delete appointment
function deleteAppointment(id, appointmentNumber) {
    if (confirm('⚠️ WARNING: Delete appointment ' + appointmentNumber + '?\n\nThis action cannot be undone.\n\nAre you absolutely sure?')) {
        $.ajax({
            url: admin_url + 'hospital_management/delete_consultant_appointment/' + id,
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
            error: function(xhr) {
                console.error('Delete Error:', xhr);
                var errorMsg = 'An error occurred while deleting appointment';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                alert_float('danger', errorMsg);
            }
        });
    }
}
</script>