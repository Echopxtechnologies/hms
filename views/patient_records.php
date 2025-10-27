<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    padding: 20px;
    text-align: center;
}

.stat-card i {
    font-size: 36px;
    color: #333;
    margin-bottom: 10px;
}

.stat-card h3 {
    font-size: 32px;
    font-weight: 700;
    color: #333;
    margin: 10px 0;
}

.stat-card p {
    color: #666;
    font-size: 14px;
    margin: 0;
}

.page-header-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.btn-register {
    background: #333;
    color: #fff;
    padding: 10px 20px;
    border-radius: 4px;
    text-decoration: none;
    transition: background 0.2s;
}

.btn-register:hover {
    background: #000;
    color: #fff;
    text-decoration: none;
}
</style>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="page-header-actions">
                    <h3><i class="fa fa-folder-open"></i> Patient Record Management</h3>
                </div>
                
                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <i class="fa fa-users"></i>
                        <h3><?php echo $statistics['total_patients']; ?></h3>
                        <p>Total Patients</p>
                    </div>
                    
                    <div class="stat-card">
                        <i class="fa fa-user-plus"></i>
                        <h3><?php echo $statistics['active_patients']; ?></h3>
                        <p>Active Patients</p>
                    </div>
                    
                    <div class="stat-card">
                        <i class="fa fa-calendar-check-o"></i>
                        <h3><?php echo $statistics['today_registrations']; ?></h3>
                        <p>Today's Registrations</p>
                    </div>
                </div>
                
                <!-- Patients Table -->
                <div class="panel_s">
                    <div class="panel-body">
                        <table class="table dt-table" id="patients_table">
                            <thead>
                                <tr>
                                    <th>Patient #</th>
                                    <th>Name</th>
                                    <th>Gender</th>
                                    <th>Mobile</th>
                                    <th>Type</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th>Registered</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($patients as $patient) { ?>
                                <tr>
                                    <td><strong><?php echo $patient->patient_number; ?></strong></td>
                                    <td><?php echo htmlspecialchars($patient->name); ?></td>
                                    <td><?php echo ucfirst($patient->gender); ?></td>
                                    <td>
                                        <a href="tel:<?php echo $patient->mobile_number; ?>">
                                            <?php echo $patient->mobile_number; ?>
                                        </a>
                                    </td>
                                    <td><span class="label label-default"><?php echo $patient->patient_type; ?></span></td>
                                    <td><?php echo ucfirst($patient->reason_for_appointment); ?></td>
                                    <td>
                                        <?php if ($patient->status == 'active') { ?>
                                            <span class="label label-success">Active</span>
                                        <?php } else { ?>
                                            <span class="label label-default"><?php echo ucfirst($patient->status); ?></span>
                                        <?php } ?>
                                    </td>
                                    <td><?php echo _dt($patient->created_at); ?></td>
                                    <td>
                                        <a href="<?php echo admin_url('hospital_management/view_patient/' . $patient->id); ?>" 
                                           class="btn btn-default btn-icon btn-sm"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           title="View Details">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="<?php echo admin_url('hospital_management/manage_patient/' . $patient->id); ?>" 
                                           class="btn btn-default btn-icon btn-sm"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           title="Edit Patient">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        <a href="#" 
                                           onclick="deletePatient(<?php echo $patient->id; ?>); return false;" 
                                           class="btn btn-danger btn-icon btn-sm"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           title="Delete Patient">
                                            <i class="fa fa-trash"></i>
                                        </a>
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

<?php init_tail(); ?>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#patients_table').DataTable({
        "pageLength": 25,
        "order": [[7, "desc"]], // Sort by registered date
        "columnDefs": [
            { "orderable": false, "targets": 8 } // Disable sorting on Actions column
        ]
    });
    
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
});

// FIXED: Include CSRF token in delete request
function deletePatient(id) {
    if (confirm('Are you sure you want to delete this patient record?\n\nThis action cannot be undone and will permanently remove:\n- Patient information\n- All associated records\n\nClick OK to proceed or Cancel to abort.')) {
        
        // Get CSRF token from Perfex's global variable
        var csrfData = {};
        csrfData[csrfData.csrf_token_name] = csrfData.csrf_hash;
        
        $.ajax({
            url: admin_url + 'hospital_management/delete_patient/' + id,
            type: 'POST',
            data: csrfData, // Include CSRF token
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
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert_float('danger', 'An error occurred while deleting the patient');
            }
        });
    }
}
</script>