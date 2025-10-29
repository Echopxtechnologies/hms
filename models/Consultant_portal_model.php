<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Consultant_portal_model extends App_Model
{
    private $appointments_table;
    private $patients_table;
    private $staff_table;
    
    public function __construct()
    {
        parent::__construct();
        $this->appointments_table = db_prefix() . 'hospital_appointments';
        $this->patients_table = db_prefix() . 'hospital_patients';
        $this->staff_table = db_prefix() . 'staff';
    }
    
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
        
        // FIXED: Direct join to staff table
        $this->db->join($this->staff_table, $this->staff_table . '.staffid = ' . $this->appointments_table . '.consultant_id', 'left');
        
        if (!$is_jc) {
            $this->db->where($this->appointments_table . '.consultant_id', $staff_id);
        }
        
        $this->db->order_by($this->appointments_table . '.appointment_date', 'DESC');
        $this->db->order_by($this->appointments_table . '.appointment_time', 'DESC');
        
        return $this->db->get()->result_array();
    }
    
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
        $this->db->join($this->staff_table, $this->staff_table . '.staffid = ' . $this->appointments_table . '.consultant_id', 'left');
        $this->db->where($this->appointments_table . '.id', $appointment_id);
        
        return $this->db->get()->row_array();
    }
    
    public function can_access($appointment_id, $staff_id)
    {
        $this->db->select($this->appointments_table . '.id');
        $this->db->from($this->appointments_table);
        $this->db->where($this->appointments_table . '.id', $appointment_id);
        $this->db->where($this->appointments_table . '.consultant_id', $staff_id);
        
        return $this->db->count_all_results() > 0;
    }
    
    public function get_statistics($staff_id, $is_jc = false)
    {
        $stats = [];
        
        $apply_filter = function() use ($is_jc, $staff_id) {
            if (!$is_jc) {
                $this->db->where('consultant_id', $staff_id);
            }
        };
        
        $this->db->from($this->appointments_table);
        $apply_filter();
        $stats['total'] = $this->db->count_all_results();
        
        $this->db->from($this->appointments_table);
        $this->db->where('status', 'pending');
        $apply_filter();
        $stats['pending'] = $this->db->count_all_results();
        
        $this->db->from($this->appointments_table);
        $this->db->where('status', 'confirmed');
        $apply_filter();
        $stats['confirmed'] = $this->db->count_all_results();
        
        $this->db->from($this->appointments_table);
        $this->db->where('appointment_date', date('Y-m-d'));
        $apply_filter();
        $stats['today'] = $this->db->count_all_results();
        
        return $stats;
    }
}