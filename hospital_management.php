<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Hospital Management
Description: Complete hospital user management system with role-based access control
Version: 2.0.0
Author: EchoPx
Features: Inline form, real-time validation, admin protection, user details view
*/

/**
 * Module Activation Hook
 */
register_activation_hook('hospital_management', 'hospital_management_activation_hook');
register_activation_hook('hospital_management', 'hospital_management_activation_hook');
function hospital_management_activation_hook()
{
    $CI = &get_instance();
    
    // Create tables first
    require_once(__DIR__ . '/install.php');
    
    // Create "Administrator" role if doesn't exist
    $admin_role_exists = $CI->db->get_where(db_prefix() . 'roles', ['name' => 'Administrator'])->num_rows();
    
    if ($admin_role_exists == 0) {
        $admin_role_data = [
            'name' => 'Administrator',
            'permissions' => serialize([])
        ];
        
        $CI->db->insert(db_prefix() . 'roles', $admin_role_data);
        log_activity('Hospital Management Module - Administrator Role Created');
    }
    
    // Create "Receptionist" role if doesn't exist
    $receptionist_role_exists = $CI->db->get_where(db_prefix() . 'roles', ['name' => 'Receptionist'])->num_rows();
    
    if ($receptionist_role_exists == 0) {
        $receptionist_role_data = [
            'name' => 'Receptionist',
            'permissions' => serialize([])
        ];
        
        $CI->db->insert(db_prefix() . 'roles', $receptionist_role_data);
        log_activity('Hospital Management Module - Receptionist Role Created');
    }
    
    // Create "Consultant" role if doesn't exist
    $consultant_role_exists = $CI->db->get_where(db_prefix() . 'roles', ['name' => 'Consultant'])->num_rows();
    
    if ($consultant_role_exists == 0) {
        $consultant_role_data = [
            'name' => 'Consultant',
            'permissions' => serialize([])
        ];
        
        $CI->db->insert(db_prefix() . 'roles', $consultant_role_data);
        log_activity('Hospital Management Module - Consultant Role Created');
    }
    // Create "Junior Consultant" role if doesn't exist
    $jc_role_exists = $CI->db->get_where(db_prefix() . 'roles', ['name' => 'Junior Consultant'])->num_rows();

    if ($jc_role_exists == 0) {
        $jc_role_data = [
            'name' => 'Junior Consultant',
            'permissions' => serialize([])
        ];
        
        $CI->db->insert(db_prefix() . 'roles', $jc_role_data);
        log_activity('Hospital Management Module - Junior Consultant Role Created');
    }
}   

/**
 * Module Uninstall Hook
 */
register_uninstall_hook('hospital_management', 'hospital_management_uninstall_hook');
function hospital_management_uninstall_hook()
{
    $CI = &get_instance();
    
    // Drop hospital_users table
    $CI->db->query("DROP TABLE IF EXISTS `" . db_prefix() . "hospital_users`");
    
    log_activity('Hospital Management Module - Table Dropped: hospital_users');
}

/**
 * Helper function - Check if user is Admin by role NAME (portable)
 */
function is_hospital_admin()
{
    $CI = &get_instance();
    $staff_id = get_staff_user_id();
    
    if (!$staff_id) {
        return false;
    }
    
    // Get staff role
    $staff = $CI->db->get_where(db_prefix() . 'staff', ['staffid' => $staff_id])->row();
    if (!$staff) {
        return false;
    }
    
    // Get role name
    $role = $CI->db->get_where(db_prefix() . 'roles', ['roleid' => $staff->role])->row();
    if (!$role) {
        return false;
    }
    
    // Check if role name is "Admin" (case-insensitive)
    return strtolower($role->name) === 'admin';
}

/**
 * Helper function - Check if user has "Administrator" role
 */
function is_hospital_administrator()
{
    $CI = &get_instance();
    $staff_id = get_staff_user_id();
    
    if (!$staff_id) {
        return false;
    }
    
    // Get staff role
    $staff = $CI->db->get_where(db_prefix() . 'staff', ['staffid' => $staff_id])->row();
    if (!$staff) {
        return false;
    }
    
    // Get role name
    $role = $CI->db->get_where(db_prefix() . 'roles', ['roleid' => $staff->role])->row();
    if (!$role) {
        return false;
    }
    
    // Check if role name is "Administrator" (case-insensitive)
    return strtolower($role->name) === 'administrator';
}

/**
 * Helper function - Check if user has "Receptionist" role
 */
function is_receptionist()
{
    $CI = &get_instance();
    $staff_id = get_staff_user_id();
    
    if (!$staff_id) {
        return false;
    }
    
    // Get staff role
    $staff = $CI->db->get_where(db_prefix() . 'staff', ['staffid' => $staff_id])->row();
    if (!$staff) {
        return false;
    }
    
    // Get role name
    $role = $CI->db->get_where(db_prefix() . 'roles', ['roleid' => $staff->role])->row();
    if (!$role) {
        return false;
    }
    
    // Check if role name is "Receptionist" (case-insensitive)
    return strtolower($role->name) === 'receptionist';
}
/**
 * Helper function - Check if user has "Consultant" role
 */
function is_consultant()
{
    $CI = &get_instance();
    $staff_id = get_staff_user_id();
    
    if (!$staff_id) {
        return false;
    }
    
    $staff = $CI->db->get_where(db_prefix() . 'staff', ['staffid' => $staff_id])->row();
    if (!$staff) {
        return false;
    }
    
    $role = $CI->db->get_where(db_prefix() . 'roles', ['roleid' => $staff->role])->row();
    if (!$role) {
        return false;
    }
    
    return strtolower($role->name) === 'consultant';
}

/**
 * Helper function - Check if user has "Junior Consultant" role
 */
function is_junior_consultant()
{
    $CI = &get_instance();
    $staff_id = get_staff_user_id();
    
    if (!$staff_id) {
        return false;
    }
    
    $staff = $CI->db->get_where(db_prefix() . 'staff', ['staffid' => $staff_id])->row();
    if (!$staff) {
        return false;
    }
    
    $role = $CI->db->get_where(db_prefix() . 'roles', ['roleid' => $staff->role])->row();
    if (!$role) {
        return false;
    }
    
    $role_name = strtolower($role->name);
    return $role_name === 'junior consultant' || $role_name === 'jc';
}

/**
 * Helper function - Check if user is Consultant OR Junior Consultant
 */
function is_consultant_or_jc()
{
    return is_consultant() || is_junior_consultant();
}
/**
 * Module Initialization
 * FIXED: Show menu for Administrator role OR users with permissions
 */
hooks()->add_action('admin_init', 'hospital_management_init');
function hospital_management_init()
{
    $CI = &get_instance();
    
    // Register module permissions
    $capabilities = [
        'view'   => 'View',
        'create' => 'Create',
        'edit'   => 'Edit',
        'delete' => 'Delete',
    ];
    
    register_staff_capabilities('hospital_users', $capabilities, 'Hospital Users');
    register_staff_capabilities('reception_management', $capabilities, 'Reception Management');
    register_staff_capabilities('consultant_portal', $capabilities, 'Consultant Portal');
    // ============================================
    // ADMINISTRATOR MENU (Hospital Management)
    // ============================================
    if (is_hospital_administrator() || has_permission('hospital_users', '', 'view')) {
        
        // Main menu item - Hospital Management Dashboard
        $CI->app_menu->add_sidebar_menu_item('hospital-management', [
            'slug'     => 'hospital-management',
            'name'     => 'Hospital Management',
            'icon'     => 'fa fa-hospital-o',
            'href'     => admin_url('hospital_management/index'),
            'position' => 30,
        ]);
        
        // Submenu item - User Management
        $CI->app_menu->add_sidebar_children_item('hospital-management', [
            'slug'     => 'hospital-users-management',
            'name'     => 'User Management',
            'icon'     => 'fa fa-users',
            'href'     => admin_url('hospital_management/users'),
            'position' => 1,
        ]);

        // Submenu item - Role Management
        $CI->app_menu->add_sidebar_children_item('hospital-management', [
            'slug'     => 'hospital-roles-management',
            'name'     => 'Role Management',
            'icon'     => 'fa fa-shield',
            'href'     => admin_url('hospital_management/roles'),
            'position' => 2,
        ]);
    }
    
    // ============================================
    // RECEPTIONIST MENU (Reception Desk)
    // ============================================
    if (is_receptionist() || has_permission('reception_management', '', 'view')) {
        
        // Main menu item - Reception Desk
        $CI->app_menu->add_sidebar_menu_item('reception-desk', [
            'slug'     => 'reception-desk',
            'name'     => 'Reception Desk',
            'icon'     => 'fa fa-desk',
            'href'     => admin_url('hospital_management/reception_dashboard'),
            'position' => 30,
        ]);
        
        // Submenu - Manage Appointments
        $CI->app_menu->add_sidebar_children_item('reception-desk', [
            'slug'     => 'manage-appointments',
            'name'     => 'Manage Appointments',
            'icon'     => 'fa fa-calendar-check-o',
            'href'     => admin_url('hospital_management/appointments'),
            'position' => 1,
        ]);
        
        // Submenu - Staff on Duty
        $CI->app_menu->add_sidebar_children_item('reception-desk', [
            'slug'     => 'staff-on-duty',
            'name'     => 'Staff on Duty',
            'icon'     => 'fa fa-user-md',
            'href'     => admin_url('hospital_management/staff_duty'),
            'position' => 2,
        ]);
        
        // Submenu - Patient Record Management
        $CI->app_menu->add_sidebar_children_item('reception-desk', [
            'slug'     => 'patient-records',
            'name'     => 'Patient Record Management',
            'icon'     => 'fa fa-folder-open',
            'href'     => admin_url('hospital_management/patient_records'),
            'position' => 3,
        ]);
        
        // Submenu - List of Procedures and Lab Records
        $CI->app_menu->add_sidebar_children_item('reception-desk', [
            'slug'     => 'procedures-lab-records',
            'name'     => 'Procedures & Lab Records',
            'icon'     => 'fa fa-flask',
            'href'     => admin_url('hospital_management/lab_records'),
            'position' => 4,
        ]);
        
        // Submenu - List of Patients for Surgery
        $CI->app_menu->add_sidebar_children_item('reception-desk', [
            'slug'     => 'surgery-patients',
            'name'     => 'Patients for Surgery',
            'icon'     => 'fa fa-stethoscope',
            'href'     => admin_url('hospital_management/surgery_patients'),
            'position' => 5,
        ]);
    }


// ============================================
    // CONSULTANT PORTAL MENU (Consultant & JC)
    // ============================================
    if (is_consultant_or_jc() || has_permission('consultant_portal', '', 'view')) {
        
        // Main menu item - Consultant Portal
        $CI->app_menu->add_sidebar_menu_item('consultant-portal', [
            'slug'     => 'consultant-portal',
            'name'     => 'Consultant Portal',
            'icon'     => 'fa fa-user-md',
            'href'     => admin_url('hospital_management/consultant_dashboard'),
            'position' => 31,
        ]);
        
        // Submenu - My Appointments
        $CI->app_menu->add_sidebar_children_item('consultant-portal', [
            'slug'     => 'my-appointments',
            'name'     => 'My Appointments',
            'icon'     => 'fa fa-calendar',
            'href'     => admin_url('hospital_management/consultant_appointments'),
            'position' => 1,
        ]);
    }
}
/**
 * Hide ALL menus except role-specific menus
 * UPDATED: Handles both Administrator and Receptionist roles
 */
hooks()->add_action('app_admin_head', 'hospital_hide_other_menus');
function hospital_hide_other_menus()
{
    if (!is_staff_logged_in()) {
        return;
    }
    
    // Admin sees all menus
    if (is_hospital_admin()) {
        return;
    }
    
    // Determine which menu to show based on role
    $show_hospital_management = is_hospital_administrator() || has_permission('hospital_users', '', 'view');
    $show_reception_desk = is_receptionist() || has_permission('reception_management', '', 'view');
    $show_consultant_portal = is_consultant_or_jc() || has_permission('consultant_portal', '', 'view');
    // CSS FIRST - Hides menus INSTANTLY (no flash on refresh)
    echo '<style>
    /* INSTANT HIDE - Before JavaScript runs */
    #side-menu > li {
        display: none !important;
    }
    ';
    
    // Show Hospital Management menu for Administrator
    if ($show_hospital_management) {
        echo '
        #side-menu > li[class*="hospital"],
        #side-menu > li[class*="hospital-management"] {
            display: block !important;
            visibility: visible !important;
        }
        ';
    }
    
    // Show Reception Desk menu for Receptionist
    if ($show_reception_desk) {
        echo '
        #side-menu > li[class*="reception"],
        #side-menu > li[class*="reception-desk"] {
            display: block !important;
            visibility: visible !important;
        }
        ';
    }
    
    // Show Consultant Portal menu for Consultant/JC
    if ($show_consultant_portal) {
        echo '
        #side-menu > li[class*="consultant"],
        #side-menu > li[class*="consultant-portal"] {
            display: block !important;
            visibility: visible !important;
        }
        ';
    }
    echo '</style>';
    
    // JavaScript cleanup - Removes from DOM after page loads
    echo '<script>
    document.addEventListener("DOMContentLoaded", function() {
        console.log("Hospital Management: Starting menu cleanup");
        
        var showHospitalManagement = ' . ($show_hospital_management ? 'true' : 'false') . ';
        var showReceptionDesk = ' . ($show_reception_desk ? 'true' : 'false') . ';
        var showConsultantPortal = ' . ($show_consultant_portal ? 'true' : 'false') . ';
        function removeOtherMenus() {
            var menuItems = document.querySelectorAll("#side-menu > li");
            
            if (menuItems.length === 0) {
                console.log("Hospital: No menu items found yet");
                return;
            }
            
            console.log("Hospital: Found " + menuItems.length + " menu items");
            
            var kept = 0;
            var removed = 0;
            
            menuItems.forEach(function(item) {
                var classes = item.className || "";
                var link = item.querySelector("a");
                var href = link ? link.getAttribute("href") : "";
                var linkText = link ? link.textContent.trim().toLowerCase() : "";
                
                var shouldKeep = false;
                
                // KEEP Hospital Management menu for Administrator
                if (showHospitalManagement) {
                    var isHospitalMenu = 
                        classes.indexOf("hospital-management") !== -1 || 
                        classes.indexOf("hospital_management") !== -1 ||
                        classes.indexOf("menu-item-hospital-management") !== -1 ||
                        (href && href.indexOf("hospital_management") !== -1) ||
                        linkText.indexOf("hospital management") !== -1;
                    
                    if (isHospitalMenu) {
                        shouldKeep = true;
                    }
                }
                
                // KEEP Reception Desk menu for Receptionist
                if (showReceptionDesk) {
                    var isReceptionMenu = 
                        classes.indexOf("reception-desk") !== -1 || 
                        classes.indexOf("reception_desk") !== -1 ||
                        classes.indexOf("menu-item-reception-desk") !== -1 ||
                        (href && href.indexOf("reception") !== -1) ||
                        linkText.indexOf("reception") !== -1;
                    
                    if (isReceptionMenu) {
                        shouldKeep = true;
                    }
                }
                
                // KEEP Consultant Portal menu for Consultant/JC
                if (showConsultantPortal) {
                    var isConsultantMenu = 
                        classes.indexOf("consultant-portal") !== -1 || 
                        classes.indexOf("consultant_portal") !== -1 ||
                        classes.indexOf("menu-item-consultant-portal") !== -1 ||
                        (href && href.indexOf("consultant") !== -1) ||
                        linkText.indexOf("consultant portal") !== -1;
                    
                    if (isConsultantMenu) {
                        shouldKeep = true;
                    }
                }
                
                if (shouldKeep) {
                    console.log("Hospital: ✓ KEEPING", linkText);
                    item.style.display = "block";
                    item.style.visibility = "visible";
                    kept++;
                    return;
                }
                
                // Remove everything else from DOM
                console.log("Hospital: ✗ REMOVING", linkText);
                setTimeout(function() { 
                    if (item.parentNode) {
                        item.parentNode.removeChild(item); 
                    }
                }, 50);
                removed++;
            });
            
            console.log("Hospital: Cleanup done - Kept: " + kept + ", Removed: " + removed);
        }
        
        // Run cleanup multiple times
        removeOtherMenus();
        setTimeout(removeOtherMenus, 100);
        setTimeout(removeOtherMenus, 300);
        setTimeout(removeOtherMenus, 700);
    });
    </script>';
}
/**
 * Redirect from homepage to appropriate dashboard
 * UPDATED: Handles Receptionist role
 */
hooks()->add_action('admin_init', 'hospital_redirect_from_homepage', 1);
function hospital_redirect_from_homepage()
{
    $CI = &get_instance();
    
    // Admin stays on default dashboard
    if (is_hospital_admin()) {
        return;
    }
    
    $uri_segment_1 = $CI->uri->segment(1);
    $uri_segment_2 = $CI->uri->segment(2);
    
    // Check if on dashboard/homepage
    if ($uri_segment_1 == 'admin' && ($uri_segment_2 == '' || $uri_segment_2 == 'dashboard')) {
        
        // Redirect Receptionist to Reception Dashboard
        if (is_receptionist() || has_permission('reception_management', '', 'view')) {
            redirect(admin_url('hospital_management/reception_dashboard'));
        }
        
        // Redirect Administrator to Hospital Dashboard
        if (is_hospital_administrator() || has_permission('hospital_users', '', 'view')) {
            redirect(admin_url('hospital_management/index'));
        }
    }
}

/**
 * Redirect after login to appropriate dashboard
 * UPDATED: Handles Receptionist role
 */
hooks()->add_action('after_user_login', 'hospital_redirect_after_login');
function hospital_redirect_after_login($staff_id)
{
    // Admin goes to default dashboard
    if (is_hospital_admin()) {
        return;
    }
    
    // Receptionist goes to Reception Dashboard
    if (is_receptionist() || has_permission('reception_management', '', 'view')) {
        redirect(admin_url('hospital_management/reception_dashboard'));
    }
    
    // Administrator goes to Hospital Dashboard
    if (is_hospital_administrator() || has_permission('hospital_users', '', 'view')) {
        redirect(admin_url('hospital_management/index'));
    }
}

/**
 * Module Settings (Optional)
 */
hooks()->add_action('after_custom_fields_select_options', 'hospital_management_settings');
function hospital_management_settings($field_name)
{
    // Add any custom module settings here if needed
}