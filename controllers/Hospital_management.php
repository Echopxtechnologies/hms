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
     * Dashboard - FIXED: Administrator role bypasses permission check
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
     * Users listing - FIXED: Administrator role bypasses permission check
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
     * Manage User - FIXED: Administrator role bypasses permission check
     */
    public function manage_user($id = null)
    {
        // Administrator role has full access
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
     * View user - FIXED: Administrator role bypasses permission check
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
     * Save user - FIXED: Administrator role bypasses permission check
     */
    public function save()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        $id = $this->input->post('id');
        
        // Administrator role has full access
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
     * Delete user - FIXED: Administrator role bypasses permission check
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
     * Roles Management - FIXED: Administrator role bypasses permission check
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
     * Create role - FIXED: Administrator role bypasses permission check
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
     * Get roles - FIXED: Administrator role bypasses permission check
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
        echo json_encode(['success' => true, 'roles' => $roles]);
    }

    /**
     * Appointment Management
     */
    public function appointments()
    {
        if (!is_receptionist() && !has_permission('reception_management', '', 'view')) {
            access_denied('Appointments');
        }
        
        $data['title'] = 'Appointment Management';
        $this->load->view('appointments', $data);
    }
    
    /**
     * Patient Management
     */
    public function patients()
    {
        if (!is_receptionist() && !has_permission('reception_management', '', 'view')) {
            access_denied('Patient Records');
        }
        
        $data['title'] = 'Patient Management';
        $this->load->view('patients', $data);
    }
    
    /**
     * Manage Patient
     */
    public function manage_patient($id = null)
    {
        if (!is_receptionist() && !has_permission('reception_management', '', 'view')) {
            access_denied('Patient Records');
        }
        
        $this->load->model('hospital_patients_model');
        $data['patient_types'] = $this->hospital_patients_model->get_patient_types();
        
        if ($id) {
            $data['patient'] = $this->hospital_patients_model->get($id);
            if (!$data['patient']) {
                show_404();
            }
            $data['title'] = 'Edit Patient';
        } else {
            $data['title'] = 'Register New Patient';
        }
        
        $this->load->view('manage_patient', $data);
    }
    
    /**
     * Save patient (AJAX) - UPDATED to handle file uploads
     */
    public function save_patient()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        if (!is_receptionist() && !has_permission('reception_management', '', 'create')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No permission']);
            return;
        }
        
        $this->load->model('hospital_patients_model');
        
        // Handle file uploads
        $files = [];
        
        // Recommendation file(s)
        if (!empty($_FILES['recommendation_file']['name'])) {
            $files['recommendation'] = $_FILES['recommendation_file'];
        }
        
        // Membership file(s)
        if ($this->input->post('has_membership') == '1' && !empty($_FILES['membership_file']['name'])) {
            $files['membership'] = $_FILES['membership_file'];
        }
        
        // Other documents
        if (!empty($_FILES['other_documents']['name'][0])) {
            $files['other'] = $_FILES['other_documents'];
        }
        
        $result = $this->hospital_patients_model->save($this->input->post(), $files);
        
        header('Content-Type: application/json');
        echo json_encode($result);
    }
    
    /**
     * View patient details
     */
    public function view_patient($id)
    {
        if (!is_receptionist() && !has_permission('reception_management', '', 'view')) {
            access_denied('Patient Records');
        }
        
        $this->load->model('hospital_patients_model');
        
        $data['patient'] = $this->hospital_patients_model->get($id);
        if (!$data['patient']) {
            show_404();
        }
        
        // Get patient documents
        $data['documents'] = $this->hospital_patients_model->get_patient_documents($id);
        
        $data['title'] = 'Patient Details';
        $this->load->view('view_patient', $data);
    }
    
  
    /**
     * Procedures and Lab Records
     */
    public function lab_records()
    {
        if (!is_receptionist() && !has_permission('reception_management', '', 'view')) {
            access_denied('Lab Records');
        }
        
        $data['title'] = 'Procedures & Lab Records';
        $this->load->view('lab_records', $data);
    }
    
    /**
     * Patients for Surgery
     */
    public function surgery_patients()
    {
        if (!is_receptionist() && !has_permission('reception_management', '', 'view')) {
            access_denied('Surgery Patients');
        }
        
        $data['title'] = 'List of Patients for Surgery';
        $this->load->view('surgery_patients', $data);
    }

    /**
     * Get patients for dropdown (AJAX) - UPDATED with search
     */
    public function get_patients_dropdown()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        $this->load->model('hospital_appointments_model');
        $search = $this->input->get('search');
        
        $patients = $this->hospital_appointments_model->get_patients_for_dropdown($search);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'patients' => $patients]);
    }
    
    /**
     * Save quick patient (for appointments - minimal or full info)
     */
    public function save_quick_patient()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        $this->load->model('hospital_patients_model');
        
        // Collect all patient data (for walk-in full form)
        $patient_data = [
            'is_new_patient'            => 1,
            'mode'                      => $this->input->post('mode'),
            'name'                      => $this->input->post('name'),
            'gender'                    => $this->input->post('gender') ?: 'other',
            'mobile_number'             => $this->input->post('mobile_number'),
            'reason_for_appointment'    => $this->input->post('reason_for_appointment'),
            'patient_type'              => $this->input->post('patient_type') ?: 'Regular',
            'status'                    => 'active'
        ];
        
        // Add optional fields if provided
        if ($this->input->post('age')) {
            $patient_data['age'] = $this->input->post('age');
        }
        if ($this->input->post('dob')) {
            $patient_data['dob'] = $this->input->post('dob');
        }
        if ($this->input->post('phone')) {
            $patient_data['phone'] = $this->input->post('phone');
        }
        if ($this->input->post('email')) {
            $patient_data['email'] = $this->input->post('email');
        }
        if ($this->input->post('address')) {
            $patient_data['address'] = $this->input->post('address');
        }
        if ($this->input->post('address_landmark')) {
            $patient_data['address_landmark'] = $this->input->post('address_landmark');
        }
        if ($this->input->post('city')) {
            $patient_data['city'] = $this->input->post('city');
        }
        if ($this->input->post('state')) {
            $patient_data['state'] = $this->input->post('state');
        }
        if ($this->input->post('pincode')) {
            $patient_data['pincode'] = $this->input->post('pincode');
        }
        if ($this->input->post('registered_other_hospital') !== null) {
            $patient_data['registered_other_hospital'] = $this->input->post('registered_other_hospital');
        }
        if ($this->input->post('fee_payment')) {
            $patient_data['fee_payment'] = $this->input->post('fee_payment');
        }
        
        // Recommendation fields
        if ($this->input->post('recommended_to_hospital') !== null) {
            $patient_data['recommended_to_hospital'] = $this->input->post('recommended_to_hospital');
        }
        if ($this->input->post('recommended_by')) {
            $patient_data['recommended_by'] = $this->input->post('recommended_by');
        }
        
        // Membership fields
        if ($this->input->post('has_membership') !== null) {
            $patient_data['has_membership'] = $this->input->post('has_membership');
        }
        if ($this->input->post('membership_type')) {
            $patient_data['membership_type'] = $this->input->post('membership_type');
        }
        if ($this->input->post('membership_number')) {
            $patient_data['membership_number'] = $this->input->post('membership_number');
        }
        if ($this->input->post('membership_expiry_date')) {
            $patient_data['membership_expiry_date'] = $this->input->post('membership_expiry_date');
        }
        if ($this->input->post('membership_notes')) {
            $patient_data['membership_notes'] = $this->input->post('membership_notes');
        }
        
        // Handle file uploads
        $files = [];
        
        if (!empty($_FILES['recommendation_file']['name'])) {
            $files['recommendation'] = $_FILES['recommendation_file'];
        }
        
        if ($this->input->post('has_membership') == '1' && !empty($_FILES['membership_file']['name'])) {
            $files['membership'] = $_FILES['membership_file'];
        }
        
        if (!empty($_FILES['other_documents']['name'][0])) {
            $files['other'] = $_FILES['other_documents'];
        }
        
        $result = $this->hospital_patients_model->save($patient_data, $files);
        
        header('Content-Type: application/json');
        echo json_encode($result);
    }
    
    /**
     * Save appointment (AJAX) - UPDATED to handle patient data and file uploads
     */
    public function save_appointment()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        $this->load->model('hospital_appointments_model');
        $this->load->model('hospital_patients_model');
        
        // Get appointment data
        $appointment_data = [
            'id'                     => $this->input->post('appointment_id'),
            'patient_id'             => $this->input->post('patient_id'),
            'patient_mode'           => $this->input->post('patient_mode'),
            'is_new_patient'         => $this->input->post('is_new_patient'),
            'appointment_date'       => $this->input->post('appointment_date'),
            'appointment_time'       => $this->input->post('appointment_time'),
            'reason_for_appointment' => $this->input->post('reason_for_appointment'),
            'consultant_id'          => $this->input->post('consultant_id'),
            'status'                 => $this->input->post('status'),
            'notes'                  => $this->input->post('notes'),
        ];
        
        // Check if we need to update patient data (for existing/walk-in)
        $patient_data = [];
        $show_full_form = $this->input->post('show_full_patient_form'); // Hidden field to indicate full form shown
        
        if ($show_full_form == '1' && !empty($appointment_data['patient_id'])) {
            // Collect all patient data from form
            $patient_data = [
                'mode'                      => $this->input->post('patient_mode'),
                'registered_other_hospital' => $this->input->post('registered_other_hospital'),
                'name'                      => $this->input->post('patient_name'),
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
                
                // Recommendation fields
                'recommended_to_hospital'   => $this->input->post('recommended_to_hospital'),
                'recommended_by'            => $this->input->post('recommended_by'),
                
                // Membership fields
                'has_membership'            => $this->input->post('has_membership'),
                'membership_type'           => $this->input->post('membership_type'),
                'membership_number'         => $this->input->post('membership_number'),
                'membership_expiry_date'    => $this->input->post('membership_expiry_date'),
                'membership_notes'          => $this->input->post('membership_notes'),
            ];
        }
        
        // Handle file uploads
        $files = [];
        
        // Recommendation file(s)
        if (!empty($_FILES['recommendation_file']['name'])) {
            $files['recommendation'] = $_FILES['recommendation_file'];
        }
        
        // Membership file(s) - only if has_membership = 1
        if ($this->input->post('has_membership') == '1' && !empty($_FILES['membership_file']['name'])) {
            $files['membership'] = $_FILES['membership_file'];
        }
        
        // Other documents (optional)
        if (!empty($_FILES['other_documents']['name'][0])) {
            $files['other'] = $_FILES['other_documents'];
        }
        
        // Save appointment (with patient data and files if applicable)
        $result = $this->hospital_appointments_model->save($appointment_data, $patient_data, $files);
        
        header('Content-Type: application/json');
        echo json_encode($result);
    }
    
    /**
     * Get patient details via AJAX (NEW - for populating form when existing patient selected)
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
        
        $this->load->model('hospital_patients_model');
        
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
     * Download patient document (NEW)
     */
    public function download_document($document_id)
    {
        if (!is_receptionist() && !has_permission('reception_management', '', 'view')) {
            access_denied('Patient Records');
        }
        
        $this->load->model('hospital_patients_model');
        
        $document = $this->hospital_patients_model->get_document_file($document_id);
        
        if (!$document) {
            set_alert('danger', 'Document not found');
            redirect($_SERVER['HTTP_REFERER']);
        }
        
        // Set headers for file download
        header('Content-Type: ' . $document->file_type);
        header('Content-Disposition: attachment; filename="' . $document->original_filename . '"');
        header('Content-Length: ' . strlen($document->file_data));
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        // Output file data
        echo $document->file_data;
        exit;
    }
    
    /**
     * Delete patient document (AJAX) (NEW)
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
        
        $this->load->model('hospital_patients_model');
        $result = $this->hospital_patients_model->delete_document($document_id);
        
        header('Content-Type: application/json');
        echo json_encode($result);
    }
    
    /**
     * Confirm appointment (AJAX)
     */
    public function confirm_appointment($id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        $this->load->model('hospital_appointments_model');
        $result = $this->hospital_appointments_model->confirm($id);
        
        header('Content-Type: application/json');
        echo json_encode($result);
    }
    
    /**
     * Cancel appointment (AJAX)
     */
    public function cancel_appointment()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        $id = $this->input->post('id');
        $reason = $this->input->post('reason');
        
        $this->load->model('hospital_appointments_model');
        $result = $this->hospital_appointments_model->cancel($id, $reason);
        
        header('Content-Type: application/json');
        echo json_encode($result);
    }
    
    /**
     * Delete appointment (AJAX)
     */
    public function delete_appointment($id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        $this->load->model('hospital_appointments_model');
        $result = $this->hospital_appointments_model->delete($id);
        
        header('Content-Type: application/json');
        echo json_encode($result);
    }

    /**
 * Patient Records Management
 */
public function patient_records()
{
    // Check permissions
    if (!is_hospital_staff()) {
        access_denied('Hospital Management');
    }
    
    // Load model
    $this->load->model('hospital_management/hospital_patients_model');
    
    // Get all patients
    $data['patients'] = $this->hospital_patients_model->get_all();
    
    // Get statistics
    $data['statistics'] = [
        'total_patients' => $this->hospital_patients_model->get_total_count(),
        'active_patients' => $this->hospital_patients_model->get_active_count(),
        'today_registrations' => $this->hospital_patients_model->get_today_registrations_count()
    ];
    
    // Set page title
    $data['title'] = 'Patient Records';
    
    // Load view
    $this->load->view('hospital_management/patient_records', $data);
}

/**
 * Delete patient (FIXED with proper response)
 */
public function delete_patient($id)
{
    // Must be POST request
    if (!$this->input->is_ajax_request() || $this->input->method() !== 'post') {
        show_404();
    }
    
    // Check permissions
    if (!is_hospital_administrator() && !has_permission('hospital_patients', '', 'delete')) {
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        return;
    }
    
    // Load model
    $this->load->model('hospital_management/hospital_patients_model');
    
    // Delete patient
    $result = $this->hospital_patients_model->delete($id);
    
    // Return JSON response
    echo json_encode($result);
}
}