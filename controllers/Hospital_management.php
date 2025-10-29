<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Hospital_management extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('hospital_users_model');
        $this->load->model('hospital_patients_model');
        $this->load->model('hospital_appointments_model'); 
        
    }
    
    /**
     * Dashboard
     */
    public function index()
    {
        if (!is_hospital_administrator() && !has_permission('hospital_users', '', 'view')) {
            access_denied('Hospital Management');
        }
        
        $data['title'] = 'Hospital Management Dashboard';
        $this->load->view('dashboard', $data);
    }
    
    /**
     * Users listing
     */
    public function users()
    {
        if (!is_hospital_administrator() && !has_permission('hospital_users', '', 'view')) {
            access_denied('Hospital Users');
        }
        
        $data['title'] = 'Hospital Users Management';
        $data['users'] = $this->hospital_users_model->get_all_users();
        
        $this->load->view('users', $data);
    }
    
    /**
     * Manage User
     */
    public function manage_user($id = null)
    {
        if ($id && !is_hospital_administrator() && !has_permission('hospital_users', '', 'edit')) {
            access_denied('Hospital Users');
        }
        
        if (!$id && !is_hospital_administrator() && !has_permission('hospital_users', '', 'create')) {
            access_denied('Hospital Users');
        }
        
        $data['roles'] = $this->hospital_users_model->get_allowed_roles();
        
        if ($id) {
            $data['user'] = $this->hospital_users_model->get($id);
            
            if (!$data['user']) {
                show_404();
            }
            
            $data['title'] = 'Edit User';
        } else {
            $data['title'] = 'Create New User';
        }
        
        $this->load->view('manage_user', $data);
    }
    
    /**
     * View user
     */
    public function view($id)
    {
        if (!is_hospital_administrator() && !has_permission('hospital_users', '', 'view')) {
            access_denied('Hospital Users');
        }
        
        $data['user'] = $this->hospital_users_model->get($id);
        
        if (!$data['user']) {
            show_404();
        }
        
        $data['title'] = 'User Details';
        $this->load->view('view_user', $data);
    }
    
    /**
     * Check email availability (AJAX)
     */
    public function check_email()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        $email = $this->input->post('email');
        $user_id = $this->input->post('user_id');
        
        $exists = $this->hospital_users_model->email_exists($email, $user_id);
        
        header('Content-Type: application/json');
        echo json_encode(['available' => !$exists]);
    }
    
    /**
     * Save user
     */
    public function save()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        $id = $this->input->post('id');
        
        if ($id && !is_hospital_administrator() && !has_permission('hospital_users', '', 'edit')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'You do not have permission to edit users']);
            return;
        }
        
        if (!$id && !is_hospital_administrator() && !has_permission('hospital_users', '', 'create')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'You do not have permission to create users']);
            return;
        }
        
        $role_id = $this->input->post('role_id');
        if ($role_id == 6) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Cannot create or modify Admin role users']);
            return;
        }
        
        $result = $this->hospital_users_model->save($this->input->post());
        
        header('Content-Type: application/json');
        echo json_encode($result);
    }
    
    /**
     * Delete user
     */
    public function delete($id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        if (!is_hospital_administrator() && !has_permission('hospital_users', '', 'delete')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'You do not have permission to delete users']);
            return;
        }
        
        $user = $this->hospital_users_model->get($id);
        if ($user && $user->role_id == 6) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Cannot delete Admin role users']);
            return;
        }
        
        $result = $this->hospital_users_model->delete($id);
        
        header('Content-Type: application/json');
        echo json_encode($result);
    }
    
  
    
    /**
     * Create role
     */
    public function create_role()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        if (!is_hospital_administrator() && !has_permission('hospital_users', '', 'create')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'You do not have permission to create roles']);
            return;
        }
        
        $role_name = trim($this->input->post('role_name'));
        
        if (strtolower($role_name) === 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Cannot create Admin role']);
            return;
        }
        
        if (empty($role_name)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Role name is required']);
            return;
        }
        
        $result = $this->hospital_users_model->create_role($role_name);
        
        header('Content-Type: application/json');
        echo json_encode($result);
    }

    /**
     * Get roles
     */
    public function get_roles()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        if (!is_hospital_administrator() && !has_permission('hospital_users', '', 'view')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No permission']);
            return;
        }
        
        $roles = $this->hospital_users_model->get_roles_with_count();
        
        header('Content-Type: application/json');
        echo json_encode($roles);
    }
    
 
    
    /**
     * Get permissions for a role
     */
    public function get_role_permissions($role_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        if (!is_hospital_administrator() && !has_permission('hospital_users', '', 'view')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No permission']);
            return;
        }
        
        $permissions = $this->hospital_users_model->get_role_permissions($role_id);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'permissions' => $permissions]);
    }
    
    /**
     * Update role permissions
     */
    public function update_role_permissions()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        if (!is_hospital_administrator() && !has_permission('hospital_users', '', 'edit')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No permission']);
            return;
        }
        
        $role_id = $this->input->post('role_id');
        $permissions = $this->input->post('permissions');
        
        if ($role_id == 6) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Cannot modify Admin role permissions']);
            return;
        }
        
        $result = $this->hospital_users_model->update_role_permissions($role_id, $permissions);
        
        header('Content-Type: application/json');
        echo json_encode($result);
    }

    // ==========================================
    // APPOINTMENTS MANAGEMENT - SIMPLIFIED
    // ==========================================
    
    /**
     * GET: View appointments page with data
     */
    public function appointments()
    {
        if (!is_receptionist() && !has_permission('reception_management', '', 'view')) {
            access_denied('Appointments Management');
        }
        
        $data['title'] = 'Appointments Management';
        $data['appointments'] = $this->hospital_appointments_model->get_all();
        $data['statistics'] = $this->hospital_appointments_model->get_statistics();
        $data['consultants'] = $this->hospital_appointments_model->get_consultants();
        $data['patient_types'] = $this->hospital_patients_model->get_patient_types();
        $data['patients'] = $this->hospital_appointments_model->get_patients_for_dropdown();
        
        $this->load->view('appointments', $data);
    }
    
    /**
     * SET: Save appointment with patient data (handles all scenarios)
     * This single function handles:
     * 1. Existing patient appointment
     * 2. Existing patient walk-in
     * 3. New patient appointment
     * 4. New patient walk-in
     */
   public function save_appointment()
{
    if (!$this->input->is_ajax_request()) {
        show_404();
    }
    
    if (!is_receptionist() && !has_permission('reception_management', '', 'create')) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'No permission']);
        return;
    }
    
    // ========== COLLECT ALL DATA ==========
    $appointment_data = [
        'id'                        => $this->input->post('appointment_id'),
        'patient_id'                => $this->input->post('patient_id'),
        'patient_mode'              => $this->input->post('patient_mode'),
        'is_new_patient'            => $this->input->post('is_new_patient'),
        'appointment_date'          => $this->input->post('appointment_date'),
        'appointment_time'          => $this->input->post('appointment_time'),
        'reason_for_appointment'    => $this->input->post('reason_for_appointment'),
        'consultant_id'             => $this->input->post('consultant_id'),
        'notes'                     => $this->input->post('notes'),
    ];
    
    // ========== CRITICAL: VALIDATE CONSULTANT BEFORE SAVING ==========
    $consultant_id = $appointment_data['consultant_id'];
    
    if (empty($consultant_id)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Please select a consultant']);
        return;
    }
    
    // Check if consultant exists in tblstaff
    $this->db->where('staffid', $consultant_id);
    $this->db->where('active', 1);
    $staff_exists = $this->db->count_all_results(db_prefix() . 'staff');
    
    if ($staff_exists == 0) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false, 
            'message' => 'Invalid consultant selected. Consultant does not exist in staff records. Please refresh and try again.'
        ]);
        log_message('error', 'Invalid consultant_id: ' . $consultant_id . ' attempted by user: ' . get_staff_user_id());
        return;
    }
    
    // Patient data collection...
    $patient_data = [];
    
    if ($this->input->post('is_new_patient') == '1' || $this->input->post('patient_mode') == 'walk_in') {
        $patient_data = [
            'is_new_patient'            => $this->input->post('is_new_patient'),
            'mode'                      => $this->input->post('patient_mode'),
            'registered_other_hospital' => $this->input->post('registered_other_hospital'),
            'other_hospital_patient_id' => $this->input->post('other_hospital_patient_id'),
            'name'                      => $this->input->post('name'),
            'gender'                    => $this->input->post('gender'),
            'dob'                       => $this->input->post('dob'),
            'age'                       => $this->input->post('age'),
            'address'                   => $this->input->post('address'),
            'address_landmark'          => $this->input->post('address_landmark'),
            'city'                      => $this->input->post('city'),
            'state'                     => $this->input->post('state'),
            'pincode'                   => $this->input->post('pincode'),
            'phone'                     => $this->input->post('phone'),
            'mobile_number'             => $this->input->post('mobile_number'),
            'email'                     => $this->input->post('email'),
            'fee_payment'               => $this->input->post('fee_payment'),
            'reason_for_appointment'    => $this->input->post('reason_for_appointment'),
            'patient_type'              => $this->input->post('patient_type'),
            'recommended_to_hospital'   => $this->input->post('recommended_to_hospital'),
            'recommended_by'            => $this->input->post('recommended_by'),
            'has_membership'            => $this->input->post('has_membership'),
            'membership_type'           => $this->input->post('membership_type'),
            'membership_number'         => $this->input->post('membership_number'),
            'membership_expiry_date'    => $this->input->post('membership_expiry_date'),
            'membership_notes'          => $this->input->post('membership_notes'),
        ];
    }
    
    // Handle file uploads
    $files = [];
    
    if (!empty($_FILES['recommendation_file']['name'][0])) {
        $files['recommendation'] = $_FILES['recommendation_file'];
    }
    
    if ($this->input->post('has_membership') == '1' && !empty($_FILES['membership_file']['name'][0])) {
        $files['membership'] = $_FILES['membership_file'];
    }
    
    if (!empty($_FILES['other_documents']['name'][0])) {
        $files['other'] = $_FILES['other_documents'];
    }
    
    // ========== SAVE APPOINTMENT ==========
    $result = $this->hospital_appointments_model->save($appointment_data, $patient_data, $files);
    
    // Add CSRF token to response
    if ($result['success']) {
        $result['csrf_token_name'] = $this->security->get_csrf_token_name();
        $result['csrf_token_hash'] = $this->security->get_csrf_hash();
    }
    
    header('Content-Type: application/json');
    echo json_encode($result);
}
     /**
     * Get patients for dropdown (AJAX)
     */
    public function get_patients_dropdown()
    {
        if (!has_permission('hospital_management', '', 'view')) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }
        
        $search = $this->input->get('search', true);
        
        $this->load->model('hospital_appointments_model');
        $patients = $this->hospital_appointments_model->get_patients_for_dropdown($search);
        
        echo json_encode([
            'success' => true,
            'patients' => $patients
        ]);
    }
    
  
    /**
     * Get patient details via AJAX
     */
    public function get_patient_details($patient_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        if (!is_receptionist() && !has_permission('reception_management', '', 'view')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No permission']);
            return;
        }
        
        $patient = $this->hospital_patients_model->get($patient_id);
        $documents = $this->hospital_patients_model->get_patient_documents($patient_id);
        
        if ($patient) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'patient' => $patient,
                'documents' => $documents
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Patient not found'
            ]);
        }
    }
    
/**
 * Save/Update patient information
 */
public function save_patient()
{
    if (!$this->input->is_ajax_request()) {
        show_404();
    }
    
    if (!is_receptionist() && !has_permission('reception_management', '', 'edit')) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'No permission']);
        return;
    }
    
    $patient_id = $this->input->post('id');
    
    $data = [
        'name'                      => $this->input->post('name'),
        'gender'                    => $this->input->post('gender'),
        'dob'                       => $this->input->post('dob'),
        'age'                       => $this->input->post('age'),
        'address'                   => $this->input->post('address'),
        'address_landmark'          => $this->input->post('address_landmark'),
        'city'                      => $this->input->post('city'),
        'state'                     => $this->input->post('state'),
        'pincode'                   => $this->input->post('pincode'),
        'phone'                     => $this->input->post('phone'),
        'mobile_number'             => $this->input->post('mobile_number'),
        'email'                     => $this->input->post('email'),
        'reason_for_appointment'    => $this->input->post('reason_for_appointment'),
        'patient_type'              => $this->input->post('patient_type'),
        'fee_payment'               => $this->input->post('fee_payment'),
    ];
    
    $result = $this->hospital_patients_model->update_patient_info($patient_id, $data);
    
    header('Content-Type: application/json');
    echo json_encode($result);
}
  /**
 * Download patient document (QUICK FIX)
 */
public function download_document($document_id = null)
{
    $document_id = intval($document_id);
    
    if ($document_id <= 0) {
        show_404();
    }
    
    $this->load->model('hospital_patients_model');
    $document = $this->hospital_patients_model->get_document_file($document_id);
    
    if (!$document) {
        show_404();
    }
    
    $this->load->helper('download');
    force_download($document->original_filename, $document->file_data);
}
    
    /**
     * Delete patient document
     */
    public function delete_document($document_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        if (!is_receptionist() && !has_permission('reception_management', '', 'delete')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No permission']);
            return;
        }
        
        $result = $this->hospital_patients_model->delete_document($document_id);
        
        header('Content-Type: application/json');
        echo json_encode($result);
    }

    
/**
 * Manage patient (edit patient form)
 * Shows the patient edit form with document management
 */
public function manage_patient($id = null)
{
    if (!$id) {
        redirect(admin_url('hospital_management/patient_records'));
    }
    
    if (!is_receptionist() && !has_permission('reception_management', '', 'view')) {
        access_denied('Patient Records');
    }
    
    $data['patient'] = $this->hospital_patients_model->get($id);
    
    if (!$data['patient']) {
        show_404();
    }
    
    $data['patient_types'] = $this->hospital_patients_model->get_patient_types();
    $data['title'] = 'Update Patient Information';
    
    $this->load->view('manage_patient', $data);
}

/**
 * View patient details
 * Shows complete patient information with documents and appointment history
 */
public function view_patient($id)
{
    if (!is_receptionist() && !has_permission('reception_management', '', 'view')) {
        access_denied('Patient Records');
    }
    
    $data['patient'] = $this->hospital_patients_model->get($id);
    
    if (!$data['patient']) {
        show_404();
    }
    
    $data['title'] = 'Patient Details - ' . $data['patient']->name;
    
    $this->load->view('view_patient', $data);
}
    
    /**
     * Confirm appointment
     */
    public function confirm_appointment($id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        $result = $this->hospital_appointments_model->confirm($id);
        
        header('Content-Type: application/json');
        echo json_encode($result);
    }
    
    /**
     * Cancel appointment
     */
    public function cancel_appointment()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        $id = $this->input->post('id');
        $reason = $this->input->post('reason');
        
        $result = $this->hospital_appointments_model->cancel($id, $reason);
        
        header('Content-Type: application/json');
        echo json_encode($result);
    }
    
    /**
     * Delete appointment
     */
    public function delete_appointment($id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        $result = $this->hospital_appointments_model->delete($id);
        
        header('Content-Type: application/json');
        echo json_encode($result);
    }

    /**
     * Patient Records Management
     */
    public function patient_records()
    {
        if (!is_receptionist() && !has_permission('reception_management', '', 'view')) {
            access_denied('Patient Records');
        }
        
        $data['patients'] = $this->hospital_patients_model->get_all();
        $data['statistics'] = [
            'total_patients' => $this->hospital_patients_model->get_total_count(),
            'active_patients' => $this->hospital_patients_model->get_active_count(),
            'today_registrations' => $this->hospital_patients_model->get_today_registrations_count()
        ];
        
        $data['title'] = 'Patient Records';
        $this->load->view('patient_records', $data);
    }

    /**
     * Delete patient
     */
    public function delete_patient($id)
    {
        if (!$this->input->is_ajax_request() || $this->input->method() !== 'post') {
            show_404();
        }
        
        if (!is_hospital_administrator() && !has_permission('hospital_patients', '', 'delete')) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }
        
        $result = $this->hospital_patients_model->delete($id);
        
        echo json_encode($result);
    }
// consultant
// ============================================
  
 public function consultant_appointments()
{
    // Admin can access via permissions, Consultant/JC via role
    if (!is_consultant_or_jc() && !has_permission('consultant_portal', '', 'view')) {
        access_denied('Consultant Portal');
    }
    
    $this->load->model('consultant_portal_model');
    
    $staff_id = get_staff_user_id();
    $is_jc = is_junior_consultant();
    
    // If admin (not consultant/jc), treat as JC (see all)
    if (!is_consultant_or_jc() && has_permission('consultant_portal', '', 'view')) {
        $is_jc = true;
    }
    
    // Fetch appointments and statistics
    $data['appointments'] = $this->consultant_portal_model->get_appointments($staff_id, $is_jc);
    $data['statistics'] = $this->consultant_portal_model->get_statistics($staff_id, $is_jc);
    $data['title'] = 'My Appointments';
    $data['is_jc'] = $is_jc;
    
    $this->load->view('consultant_appointments', $data);
}
    
    /**
     * Get appointments (AJAX)
     */
    public function get_consultant_appointments()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        // Admin can access via permissions, Consultant/JC via role
        if (!is_consultant_or_jc() && !has_permission('consultant_portal', '', 'view')) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }
        
        $this->load->model('consultant_portal_model');
        
        $staff_id = get_staff_user_id();
        $is_jc = is_junior_consultant();
        
        // If admin (not consultant/jc), treat as JC (see all)
        if (!is_consultant_or_jc() && has_permission('consultant_portal', '', 'view')) {
            $is_jc = true; // Admin sees all
        }
        
        $appointments = $this->consultant_portal_model->get_appointments($staff_id, $is_jc);
        
        echo json_encode(['success' => true, 'data' => $appointments]);
    }
    
   /**
 * Get single appointment (AJAX)
 */
public function get_appointment_details($id)
{
    if (!$this->input->is_ajax_request()) {
        show_404();
    }
    
    // Admin can access via permissions, Consultant/JC via role
    if (!is_consultant_or_jc() && !has_permission('consultant_portal', '', 'view')) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        return;
    }
    
    $this->load->model('consultant_portal_model');
    
    $appointment = $this->consultant_portal_model->get($id);
    
    if (!$appointment) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Appointment not found']);
        return;
    }
    
    // Consultant can only see their own (JC and Admin see all)
    if (is_consultant() && !is_junior_consultant() && !has_permission('consultant_portal', '', 'view')) {
        if (!$this->consultant_portal_model->can_access($id, get_staff_user_id())) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Access denied to this appointment']);
            return;
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'data' => $appointment]);
}
    /**
 * Confirm appointment (Consultant Portal)
 */
public function confirm_consultant_appointment($id)
{
    if (!$this->input->is_ajax_request()) {
        show_404();
    }
    
    // Check access - Admin OR Consultant/JC
    if (!is_consultant_or_jc() && !has_permission('consultant_portal', '', 'view')) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        return;
    }
    
    $this->load->model('consultant_portal_model');
    
    // If consultant (not JC), verify they own this appointment
    if (is_consultant() && !is_junior_consultant() && !has_permission('consultant_portal', '', 'view')) {
        if (!$this->consultant_portal_model->can_access($id, get_staff_user_id())) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'You do not have access to this appointment']);
            return;
        }
    }
    
    // Use the appointments model to confirm
    $result = $this->hospital_appointments_model->confirm($id);
    
    header('Content-Type: application/json');
    echo json_encode($result);
}

/**
 * Reject appointment (Consultant Portal)
 */
public function reject_consultant_appointment()
{
    if (!$this->input->is_ajax_request()) {
        show_404();
    }
    
    // Check access - Admin OR Consultant/JC
    if (!is_consultant_or_jc() && !has_permission('consultant_portal', '', 'view')) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        return;
    }
    
    $id = $this->input->post('id');
    $reason = $this->input->post('reason');
    
    if (empty($id)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Appointment ID is required']);
        return;
    }
    
    $this->load->model('consultant_portal_model');
    
    // If consultant (not JC), verify they own this appointment
    if (is_consultant() && !is_junior_consultant() && !has_permission('consultant_portal', '', 'view')) {
        if (!$this->consultant_portal_model->can_access($id, get_staff_user_id())) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'You do not have access to this appointment']);
            return;
        }
    }
    
    // Use cancel method with reason
    $result = $this->hospital_appointments_model->cancel($id, $reason);
    
    header('Content-Type: application/json');
    echo json_encode($result);
}

/**
 * Delete appointment (Consultant Portal)
 */
public function delete_consultant_appointment($id)
{
    if (!$this->input->is_ajax_request()) {
        show_404();
    }
    
    // Check access - Admin OR JC only (regular consultants cannot delete)
    if (!is_junior_consultant() && !has_permission('consultant_portal', '', 'delete')) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Only Junior Consultants and Admins can delete appointments']);
        return;
    }
    
    // Perform deletion
    $result = $this->hospital_appointments_model->delete($id);
    
    header('Content-Type: application/json');
    echo json_encode($result);
}

/**
 * Get consultant statistics (AJAX)
 */
public function get_consultant_statistics()
{
    if (!$this->input->is_ajax_request()) {
        show_404();
    }
    
    // Check access
    if (!is_consultant_or_jc() && !has_permission('consultant_portal', '', 'view')) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        return;
    }
    
    $this->load->model('consultant_portal_model');
    
    $staff_id = get_staff_user_id();
    $is_jc = is_junior_consultant();
    
    // If admin (not consultant/jc), treat as JC (see all)
    if (!is_consultant_or_jc() && has_permission('consultant_portal', '', 'view')) {
        $is_jc = true;
    }
    
    $stats = $this->consultant_portal_model->get_statistics($staff_id, $is_jc);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'data' => $stats]);
}


// helpers
public function roles()
{
    if (!is_hospital_administrator() && !has_permission('hospital_users', '', 'view')) {
        access_denied('Hospital Management');
    }
    
    $this->load->model('roles_model');
    $all_roles = $this->roles_model->get();
    
    $roles_with_count = [];
    foreach ($all_roles as $role) {
        $this->db->where('role_id', $role['roleid']);
        $this->db->where('active', 1);
        $count = $this->db->count_all_results(db_prefix() . 'hospital_users');
        
        $role['total_users'] = $count;
        $roles_with_count[] = $role;
    }
    
    $data['title'] = 'Role Management';
    $data['roles'] = $roles_with_count;
    $this->load->view('roles', $data);
}
public function delete_role($role_id)
{
    if (!$this->input->is_ajax_request()) {
        show_404();
    }
    
    if (!is_hospital_administrator() && !has_permission('hospital_users', '', 'delete')) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'No permission to delete roles']);
        return;
    }
    
    // Cannot delete Admin role
    if ($role_id == 1) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Cannot delete Admin role']);
        return;
    }
    
    // Check if role has users
    $this->db->where('role_id', $role_id);
    $this->db->where('active', 1);
    $user_count = $this->db->count_all_results(db_prefix() . 'hospital_users');
    
    if ($user_count > 0) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Cannot delete role with assigned users. Please reassign users first.']);
        return;
    }
    
    // Delete role
    $this->load->model('roles_model');
    $deleted = $this->roles_model->delete($role_id);
    
    if ($deleted) {
        log_activity('Hospital Role Deleted [ID: ' . $role_id . ']');
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Role deleted successfully']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Failed to delete role']);
    }
}
}