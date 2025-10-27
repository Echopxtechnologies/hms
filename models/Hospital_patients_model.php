<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Hospital_patients_model extends App_Model
{
    private $table;
    private $types_table;
    
    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . 'hospital_patients';
        $this->types_table = db_prefix() . 'hospital_patient_types';
    }
    
    /**
     * Generate unique patient number
     */
    private function generate_patient_number()
    {
        $prefix = 'PAT';
        $year = date('Y');
        
        // Get last patient number for this year
        $this->db->select('patient_number');
        $this->db->like('patient_number', $prefix . $year, 'after');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $last = $this->db->get($this->table)->row();
        
        if ($last) {
            // Extract number and increment
            $last_number = (int) substr($last->patient_number, -4);
            $new_number = $last_number + 1;
        } else {
            $new_number = 1;
        }
        
        return $prefix . $year . str_pad($new_number, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Get all patient types
     */
    public function get_patient_types()
    {
        $this->db->where('is_active', 1);
        $this->db->order_by('display_order', 'ASC');
        return $this->db->get($this->types_table)->result_array();
    }
    
    /**
     * Get patient by ID
     */
    public function get($id)
    {
        $this->db->where('id', $id);
        return $this->db->get($this->table)->row();
    }
    
    /**
     * Get patient by patient number
     */
    public function get_by_patient_number($patient_number)
    {
        $this->db->where('patient_number', $patient_number);
        return $this->db->get($this->table)->row();
    }
    
    /**
     * Get all patients
     */
    public function get_all()
    {
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get($this->table)->result();
    }
    
    /**
     * Search patients by name, mobile, or patient number
     */
    public function search($keyword)
    {
        $this->db->group_start();
        $this->db->like('name', $keyword);
        $this->db->or_like('mobile_number', $keyword);
        $this->db->or_like('patient_number', $keyword);
        $this->db->or_like('email', $keyword);
        $this->db->group_end();
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get($this->table)->result();
    }
    
    /**
 * Save patient (Add or Update) - UPDATED VERSION
 */
public function save($data, $files = [])
{
    $id = isset($data['id']) && !empty($data['id']) ? $data['id'] : null;
    
    // Validate required fields
    $validation_errors = $this->validate_patient_data($data, $id);
    if (!empty($validation_errors)) {
        return [
            'success' => false, 
            'message' => implode('<br>', $validation_errors), 
            'errors' => $validation_errors
        ];
    }
    
    // Prepare save data
    $save_data = [
        'is_new_patient'            => isset($data['is_new_patient']) ? (int)$data['is_new_patient'] : 1,
        'mode'                      => isset($data['mode']) ? $data['mode'] : 'appointment',
        'registered_other_hospital' => isset($data['registered_other_hospital']) ? (int)$data['registered_other_hospital'] : null,
        'name'                      => trim($data['name']),
        'gender'                    => $data['gender'],
        'dob'                       => !empty($data['dob']) ? $data['dob'] : null,
        'age'                       => !empty($data['age']) ? (int)$data['age'] : null,
        'address'                   => !empty($data['address']) ? trim($data['address']) : null,
        'address_landmark'          => !empty($data['address_landmark']) ? trim($data['address_landmark']) : null,
        'city'                      => !empty($data['city']) ? trim($data['city']) : null,
        'state'                     => !empty($data['state']) ? trim($data['state']) : null,
        'pincode'                   => !empty($data['pincode']) ? trim($data['pincode']) : null,
        'phone'                     => !empty($data['phone']) ? trim($data['phone']) : null,
        'mobile_number'             => trim($data['mobile_number']),
        'email'                     => !empty($data['email']) ? trim(strtolower($data['email'])) : null,
        'fee_payment'               => isset($data['fee_payment']) ? $data['fee_payment'] : 'not_applicable',
        'reason_for_appointment'    => $data['reason_for_appointment'],
        'patient_type'              => $data['patient_type'],
        
        // Recommendation fields (NEW)
        'recommended_to_hospital'   => isset($data['recommended_to_hospital']) ? (int)$data['recommended_to_hospital'] : null,
        'recommended_by'            => !empty($data['recommended_by']) ? trim($data['recommended_by']) : null,
        
        // Membership fields (NEW)
        'has_membership'            => isset($data['has_membership']) ? (int)$data['has_membership'] : 0,
        'membership_type'           => !empty($data['membership_type']) ? trim($data['membership_type']) : null,
        'membership_number'         => !empty($data['membership_number']) ? trim($data['membership_number']) : null,
        'membership_expiry_date'    => !empty($data['membership_expiry_date']) ? $data['membership_expiry_date'] : null,
        'membership_notes'          => !empty($data['membership_notes']) ? trim($data['membership_notes']) : null,
        
        'status'                    => isset($data['status']) ? $data['status'] : 'active',
    ];
    
    // Update or Insert
    if ($id) {
        // Update
        $save_data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        $this->db->update($this->table, $save_data);
        
        $patient_id = $id;
        log_activity('Hospital Patient Updated [ID: ' . $id . ', Name: ' . $save_data['name'] . ']');
        $message = 'Patient updated successfully';
    } else {
        // Insert - Generate patient number
        $save_data['patient_number'] = $this->generate_patient_number();
        $save_data['created_by'] = get_staff_user_id();
        $save_data['created_at'] = date('Y-m-d H:i:s');
        
        $this->db->insert($this->table, $save_data);
        $patient_id = $this->db->insert_id();
        
        log_activity('Hospital Patient Created [ID: ' . $patient_id . ', Number: ' . $save_data['patient_number'] . ', Name: ' . $save_data['name'] . ']');
        $message = 'Patient registered successfully';
    }
    
    // Handle document uploads (store in database as LONGBLOB)
    if (!empty($files)) {
        $this->save_patient_documents($patient_id, $files);
    }
    
    return [
        'success' => true, 
        'message' => $message, 
        'id' => $patient_id,
        'patient_number' => isset($save_data['patient_number']) ? $save_data['patient_number'] : null
    ];
}

/**
 * Save patient documents in database (LONGBLOB) - NEW METHOD
 */
private function save_patient_documents($patient_id, $files)
{
    $this->load->helper('file');
    
    foreach ($files as $document_type => $file_data) {
        // $file_data can be array if multiple files, or single file array
        $files_to_process = isset($file_data['name']) && is_array($file_data['name']) 
            ? $this->rearray_files($file_data) 
            : [$file_data];
        
        foreach ($files_to_process as $file) {
            if (empty($file['name']) || $file['error'] !== UPLOAD_ERR_OK) {
                continue; // Skip invalid files
            }
            
            // Read file content
            $file_content = file_get_contents($file['tmp_name']);
            
            if ($file_content === false) {
                log_activity('Failed to read file: ' . $file['name']);
                continue;
            }
            
            // Prepare document data
            $doc_data = [
                'patient_id'        => $patient_id,
                'document_type'     => $document_type, // 'recommendation', 'membership', etc.
                'document_name'     => pathinfo($file['name'], PATHINFO_FILENAME),
                'original_filename' => $file['name'],
                'file_type'         => $file['type'],
                'file_size'         => $file['size'],
                'file_data'         => $file_content, // LONGBLOB
                'uploaded_by'       => get_staff_user_id(),
                'uploaded_at'       => date('Y-m-d H:i:s')
            ];
            
            // Insert into database
            $this->db->insert(db_prefix() . 'hospital_patient_documents', $doc_data);
            
            log_activity('Document uploaded for patient [Patient ID: ' . $patient_id . ', File: ' . $file['name'] . ']');
        }
    }
}

/**
 * Helper to rearrange multiple file upload array - NEW METHOD
 */
private function rearray_files($file_post)
{
    $file_array = [];
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i = 0; $i < $file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_array[$i][$key] = $file_post[$key][$i];
        }
    }

    return $file_array;
}

/**
 * Get patient documents - NEW METHOD
 */
public function get_patient_documents($patient_id, $document_type = null)
{
    $this->db->select('id, patient_id, document_type, document_name, original_filename, file_type, file_size, uploaded_by, uploaded_at, notes');
    $this->db->where('patient_id', $patient_id);
    
    if ($document_type) {
        $this->db->where('document_type', $document_type);
    }
    
    $this->db->order_by('uploaded_at', 'DESC');
    return $this->db->get(db_prefix() . 'hospital_patient_documents')->result_array();
}

/**
 * Get document file data (binary) - NEW METHOD
 */
public function get_document_file($document_id)
{
    $this->db->select('file_data, original_filename, file_type');
    $this->db->where('id', $document_id);
    return $this->db->get(db_prefix() . 'hospital_patient_documents')->row();
}

/**
 * Delete patient document - NEW METHOD
 */
public function delete_document($document_id)
{
    $this->db->where('id', $document_id);
    $this->db->delete(db_prefix() . 'hospital_patient_documents');
    
    if ($this->db->affected_rows() > 0) {
        log_activity('Patient document deleted [Document ID: ' . $document_id . ']');
        return ['success' => true, 'message' => 'Document deleted successfully'];
    }
    
    return ['success' => false, 'message' => 'Failed to delete document'];
}
    /**
     * Get statistics
     */
    public function get_statistics()
    {
        $stats = [];
        
        // Total patients
        $stats['total_patients'] = $this->db->count_all_results($this->table);
        
        // Active patients
        $this->db->where('status', 'active');
        $stats['active_patients'] = $this->db->count_all_results($this->table);
        
        // New patients (created today)
        $this->db->where('DATE(created_at)', date('Y-m-d'));
        $stats['today_registrations'] = $this->db->count_all_results($this->table);
        
        // By appointment reason
        $this->db->select('reason_for_appointment, COUNT(*) as count');
        $this->db->group_by('reason_for_appointment');
        $stats['by_reason'] = $this->db->get($this->table)->result_array();
        
        return $stats;
    }

    /**
 * Update patient information (used during appointment creation for existing/walk-in)
 */
public function update_patient_info($patient_id, $data, $files = [])
{
    // Get existing patient
    $existing_patient = $this->get($patient_id);
    if (!$existing_patient) {
        return ['success' => false, 'message' => 'Patient not found'];
    }
    
    // Prepare update data (only update fields that are provided)
    $update_data = [];
    
    // Patient mode and type
    if (isset($data['mode'])) {
        $update_data['mode'] = $data['mode'];
    }
    
    if (isset($data['registered_other_hospital'])) {
        $update_data['registered_other_hospital'] = (int)$data['registered_other_hospital'];
    }
    
    // Personal details
    if (!empty($data['name'])) {
        $update_data['name'] = trim($data['name']);
    }
    
    if (!empty($data['gender'])) {
        $update_data['gender'] = $data['gender'];
    }
    
    if (isset($data['dob'])) {
        $update_data['dob'] = !empty($data['dob']) ? $data['dob'] : null;
    }
    
    if (isset($data['age'])) {
        $update_data['age'] = !empty($data['age']) ? (int)$data['age'] : null;
    }
    
    // Address fields
    if (isset($data['address'])) {
        $update_data['address'] = !empty($data['address']) ? trim($data['address']) : null;
    }
    
    if (isset($data['address_landmark'])) {
        $update_data['address_landmark'] = !empty($data['address_landmark']) ? trim($data['address_landmark']) : null;
    }
    
    if (isset($data['city'])) {
        $update_data['city'] = !empty($data['city']) ? trim($data['city']) : null;
    }
    
    if (isset($data['state'])) {
        $update_data['state'] = !empty($data['state']) ? trim($data['state']) : null;
    }
    
    if (isset($data['pincode'])) {
        $update_data['pincode'] = !empty($data['pincode']) ? trim($data['pincode']) : null;
    }
    
    // Contact details
    if (isset($data['phone'])) {
        $update_data['phone'] = !empty($data['phone']) ? trim($data['phone']) : null;
    }
    
    if (!empty($data['mobile_number'])) {
        $update_data['mobile_number'] = trim($data['mobile_number']);
    }
    
    if (isset($data['email'])) {
        $update_data['email'] = !empty($data['email']) ? trim(strtolower($data['email'])) : null;
    }
    
    // Other details
    if (isset($data['fee_payment'])) {
        $update_data['fee_payment'] = $data['fee_payment'];
    }
    
    if (!empty($data['reason_for_appointment'])) {
        $update_data['reason_for_appointment'] = $data['reason_for_appointment'];
    }
    
    if (!empty($data['patient_type'])) {
        $update_data['patient_type'] = $data['patient_type'];
    }
    
    // Recommendation fields
    if (isset($data['recommended_to_hospital'])) {
        $update_data['recommended_to_hospital'] = (int)$data['recommended_to_hospital'];
    }
    
    if (isset($data['recommended_by'])) {
        $update_data['recommended_by'] = !empty($data['recommended_by']) ? trim($data['recommended_by']) : null;
    }
    
    // Membership fields
    if (isset($data['has_membership'])) {
        $update_data['has_membership'] = (int)$data['has_membership'];
    }
    
    if (isset($data['membership_type'])) {
        $update_data['membership_type'] = !empty($data['membership_type']) ? trim($data['membership_type']) : null;
    }
    
    if (isset($data['membership_number'])) {
        $update_data['membership_number'] = !empty($data['membership_number']) ? trim($data['membership_number']) : null;
    }
    
    if (isset($data['membership_expiry_date'])) {
        $update_data['membership_expiry_date'] = !empty($data['membership_expiry_date']) ? $data['membership_expiry_date'] : null;
    }
    
    if (isset($data['membership_notes'])) {
        $update_data['membership_notes'] = !empty($data['membership_notes']) ? trim($data['membership_notes']) : null;
    }
    
    // Update if there's data to update
    if (!empty($update_data)) {
        $update_data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $patient_id);
        $this->db->update($this->table, $update_data);
    }
    
    // Handle document uploads
    if (!empty($files)) {
        $this->save_patient_documents($patient_id, $files);
    }
    
    log_activity('Patient information updated during appointment [Patient ID: ' . $patient_id . ']');
    return ['success' => true, 'message' => 'Patient information updated successfully'];
}

/**
 * Get total count of patients
 */
public function get_total_count()
{
    return $this->db->count_all_results($this->table);
}

/**
 * Get active patients count
 */
public function get_active_count()
{
    $this->db->where('status', 'active');
    return $this->db->count_all_results($this->table);
}

/**
 * Get today's registrations count
 */
public function get_today_registrations_count()
{
    $this->db->where('DATE(created_at)', date('Y-m-d'));
    return $this->db->count_all_results($this->table);
}



/**
 * Delete patient
 */
public function delete($id)
{
    $patient = $this->get($id);
    
    if (!$patient) {
        return ['success' => false, 'message' => 'Patient not found'];
    }
    
    // Delete patient (will cascade delete appointments and documents)
    $this->db->where('id', $id);
    $this->db->delete($this->table);
    
    if ($this->db->affected_rows() > 0) {
        log_activity('Hospital Patient Deleted [ID: ' . $id . ', Name: ' . $patient->name . ']');
        return ['success' => true, 'message' => 'Patient deleted successfully'];
    }
    
    return ['success' => false, 'message' => 'Failed to delete patient'];
}
}