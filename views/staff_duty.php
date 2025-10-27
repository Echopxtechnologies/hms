<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <i class="fa fa-user-md"></i> Staff on Duty
                        </h4>
                        <hr>
                        <p>Staff duty schedule will be displayed here.</p>
                        <a href="<?php echo admin_url('hospital_management/reception_dashboard'); ?>" class="btn btn-default">
                            <i class="fa fa-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>