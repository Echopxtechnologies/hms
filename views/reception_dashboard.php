<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
.reception-dashboard {
    padding: 25px;
}

.dashboard-card {
    background: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 30px;
    margin-bottom: 25px;
    text-align: center;
    transition: all 0.3s ease;
}

.dashboard-card:hover {
    border-color: #333;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-4px);
}

.dashboard-card i {
    font-size: 48px;
    color: #333;
    margin-bottom: 15px;
}

.dashboard-card h4 {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin-bottom: 10px;
}

.dashboard-card p {
    color: #666;
    font-size: 14px;
    margin-bottom: 20px;
}

.dashboard-card .btn {
    background: #333;
    color: #fff;
    padding: 10px 25px;
    border-radius: 4px;
    text-decoration: none;
    transition: background 0.2s;
}

.dashboard-card .btn:hover {
    background: #000;
    color: #fff;
    text-decoration: none;
}
</style>

<div id="wrapper">
    <div class="content reception-dashboard">
        <div class="row">
            <div class="col-md-12">
                <h3><i class="fa fa-desk"></i> Welcome to Reception Desk</h3>
                <hr>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="dashboard-card">
                    <i class="fa fa-calendar-check-o"></i>
                    <h4>Manage Appointments</h4>
                    <p>Schedule and manage patient appointments</p>
                    <a href="<?php echo admin_url('hospital_management/appointments'); ?>" class="btn">
                        Open <i class="fa fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="dashboard-card">
                    <i class="fa fa-user-md"></i>
                    <h4>Staff on Duty</h4>
                    <p>View current staff on duty schedule</p>
                    <a href="<?php echo admin_url('hospital_management/staff_duty'); ?>" class="btn">
                        Open <i class="fa fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="dashboard-card">
                    <i class="fa fa-folder-open"></i>
                    <h4>Patient Records</h4>
                    <p>Manage patient medical records</p>
                    <a href="<?php echo admin_url('hospital_management/patient_records'); ?>" class="btn">
                        Open <i class="fa fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="dashboard-card">
                    <i class="fa fa-flask"></i>
                    <h4>Procedures & Lab Records</h4>
                    <p>View laboratory tests and procedures</p>
                    <a href="<?php echo admin_url('hospital_management/lab_records'); ?>" class="btn">
                        Open <i class="fa fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="dashboard-card">
                    <i class="fa fa-stethoscope"></i>
                    <h4>Patients for Surgery</h4>
                    <p>List of scheduled surgery patients</p>
                    <a href="<?php echo admin_url('hospital_management/surgery_patients'); ?>" class="btn">
                        Open <i class="fa fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>