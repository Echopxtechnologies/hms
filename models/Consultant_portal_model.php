<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Consultant_portal_model extends App_Model
{
    private $appointments_table;
    private $patients_table;
    private $staff_table;
    private $hospital_users_table;
    
    public function __construct()
    {
        parent::__construct();
        $this->appointments_table = db_prefix() . 'hospital_appointments';
        $this->patients_table = db_prefix() . 'hospital_patients';
        $this->staff_table = db_prefix() . 'staff';
        $this->hospital_users_table = db_prefix() . 'hospital_users';
    }
    
    /**
     * Get appointments based on role
     * @param int $staff_id - Current logged in staff ID
     * @param bool $is_jc - Is Junior Consultant?
     * @return array
     */
    public function get_appointments($staff_id, $is_jc = false)
    {
        $this->db->select(
            $this->appointments_table . '.*,' .
            $this->patients_table . '.name as patient_name,' .
            $this->patients_table . '.mobile_number as patient_mobile,' .
            $this->patients_table . '.patient_number,' .
            $this->patients_table . '.email as patient_email,' .
            $this->patients_table . '.age as patient_age,' .
            $this->patients_table . '.gender as patient_gender,' .
            $this->staff_table . '.firstname as consultant_firstname,' .
            $this->staff_table . '.lastname as consultant_lastname'
        );
        
        $this->db->from($this->appointments_table);
        $this->db->join($this->patients_table, $this->patients_table . '.id = ' . $this->appointments_table . '.patient_id', 'left');
        
        // CRITICAL FIX: Join through hospital_users to match consultant_id properly
        $this->db->join(
            $this->hospital_users_table, 
            $this->hospital_users_table . '.id = ' . $this->appointments_table . '.consultant_id', 
            'left'
        );
        $this->db->join(
            $this->staff_table, 
            $this->staff_table . '.staffid = ' . $this->hospital_users_table . '.staff_id', 
            'left'
        );
        
        // JC sees all, Consultant sees only their own
        if (!$is_jc) {
            // Filter by staff_id in hospital_users table
            $this->db->where($this->hospital_users_table . '.staff_id', $staff_id);
        }
        
        $this->db->order_by($this->appointments_table . '.appointment_date', 'DESC');
        $this->db->order_by($this->appointments_table . '.appointment_time', 'DESC');
        
        return $this->db->get()->result_array();
    }
    
    /**
     * Get single appointment with full details
     * @param int $appointment_id
     * @return object|null
     */
    public function get($appointment_id)
    {
        $this->db->select(
            $this->appointments_table . '.*,' .
            $this->patients_table . '.name as patient_name,' .
            $this->patients_table . '.mobile_number as patient_mobile,' .
            $this->patients_table . '.patient_number,' .
            $this->patients_table . '.email as patient_email,' .
            $this->patients_table . '.age as patient_age,' .
            $this->patients_table . '.gender as patient_gender,' .
            $this->patients_table . '.address,' .
            $this->patients_table . '.city,' .
            $this->patients_table . '.state,' .
            $this->staff_table . '.firstname as consultant_firstname,' .
            $this->staff_table . '.lastname as consultant_lastname,' .
            $this->staff_table . '.email as consultant_email'
        );
        
        $this->db->from($this->appointments_table);
        $this->db->join($this->patients_table, $this->patients_table . '.id = ' . $this->appointments_table . '.patient_id', 'left');
        
        // CRITICAL FIX: Join through hospital_users
        $this->db->join(
            $this->hospital_users_table, 
            $this->hospital_users_table . '.id = ' . $this->appointments_table . '.consultant_id', 
            'left'
        );
        $this->db->join(
            $this->staff_table, 
            $this->staff_table . '.staffid = ' . $this->hospital_users_table . '.staff_id', 
            'left'
        );
        
        $this->db->where($this->appointments_table . '.id', $appointment_id);
        
        return $this->db->get()->row_array();
    }
    
    /**
     * Check if consultant has access to this appointment
     * @param int $appointment_id
     * @param int $staff_id
     * @return bool
     */
    public function can_access($appointment_id, $staff_id)
    {
        // Join through hospital_users to check access
        $this->db->select($this->appointments_table . '.id');
        $this->db->from($this->appointments_table);
        $this->db->join(
            $this->hospital_users_table, 
            $this->hospital_users_table . '.id = ' . $this->appointments_table . '.consultant_id', 
            'inner'
        );
        $this->db->where($this->appointments_table . '.id', $appointment_id);
        $this->db->where($this->hospital_users_table . '.staff_id', $staff_id);
        
        return $this->db->count_all_results() > 0;
    }
    
    /**
     * Get statistics
     * @param int $staff_id
     * @param bool $is_jc
     * @return array
     */
    public function get_statistics($staff_id, $is_jc = false)
    {
        $stats = [];
        
        // Build base query with hospital_users join
        $base_query = function($is_jc, $staff_id) {
            if (!$is_jc) {
                $this->db->join(
                    $this->hospital_users_table, 
                    $this->hospital_users_table . '.id = ' . $this->appointments_table . '.consultant_id', 
                    'inner'
                );
                $this->db->where($this->hospital_users_table . '.staff_id', $staff_id);
            }
        };
        
        // Total
        $base_query($is_jc, $staff_id);
        $stats['total'] = $this->db->count_all_results($this->appointments_table);
        
        // Pending
        $this->db->where('status', 'pending');
        $base_query($is_jc, $staff_id);
        $stats['pending'] = $this->db->count_all_results($this->appointments_table);
        
        // Confirmed
        $this->db->where('status', 'confirmed');
        $base_query($is_jc, $staff_id);
        $stats['confirmed'] = $this->db->count_all_results($this->appointments_table);
        
        // Today
        $this->db->where('appointment_date', date('Y-m-d'));
        $base_query($is_jc, $staff_id);
        $stats['today'] = $this->db->count_all_results($this->appointments_table);
        
        return $stats;
    }
}