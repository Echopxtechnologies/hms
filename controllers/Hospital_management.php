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
     * Roles Management
     */
    public function roles()
    {
        if (!is_hospital_administrator() && !has_permission('hospital_users', '', 'view')) {
            access_denied('Hospital Management');
        }
        
        $data['title'] = 'Role Management';
        $this->load->view('roles', $data);
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
     * Delete role
     */
    public function delete_role($role_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        if (!is_hospital_administrator() && !has_permission('hospital_users', '', 'delete')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No permission']);
            return;
        }
        
        if ($role_id == 6) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Cannot delete Admin role']);
            return;
        }
        
        $result = $this->hospital_users_model->delete_role($role_id);
        
        header('Content-Type: application/json');
        echo json_encode($result);
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
        
        // Appointment data
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
        
        // Patient data (if provided - for new patients or existing patient updates)
        $patient_data = [];
        
        // Collect patient data for: 1) New patients, OR 2) Walk-in mode (existing patient update)
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
        
        // ========== SAVE APPOINTMENT (MODEL HANDLES EVERYTHING) ==========
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
     * Download patient document
     */
    public function download_document($document_id)
    {
        if (!is_receptionist() && !has_permission('reception_management', '', 'view')) {
            access_denied('Patient Records');
        }
        
        $document = $this->hospital_patients_model->get_document_file($document_id);
        
        if (!$document) {
            set_alert('danger', 'Document not found');
            redirect($_SERVER['HTTP_REFERER']);
        }
        
        header('Content-Type: ' . $document->file_type);
        header('Content-Disposition: attachment; filename="' . $document->original_filename . '"');
        header('Content-Length: ' . strlen($document->file_data));
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        echo $document->file_data;
        exit;
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
}