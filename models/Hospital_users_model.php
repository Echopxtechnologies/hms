<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Hospital_users_model extends App_Model
{
    private $table;
    
    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . 'hospital_users';
    }
    
    /**
     * Get allowed roles (exclude Admin role ID 6)
     */
    public function get_allowed_roles()
    {
        $this->db->where('roleid !=', 6); // Exclude Admin role
        $this->db->order_by('name', 'ASC');
        return $this->db->get(db_prefix() . 'roles')->result_array();
    }
    
    /**
     * Get user by ID
     */
    public function get($id)
    {
        $this->db->select($this->table . '.*, ' . db_prefix() . 'roles.name as role_name');
        $this->db->join(db_prefix() . 'roles', db_prefix() . 'roles.roleid = ' . $this->table . '.role_id', 'left');
        $this->db->where($this->table . '.id', $id);
        return $this->db->get($this->table)->row();
    }
    
    /**
     * Get all users (for client-side DataTables)
     * Simple approach - pulls all data with field names
     */
    public function get_all_users()
    {
        $this->db->select($this->table . '.*, ' . db_prefix() . 'roles.name as role_name');
        $this->db->join(db_prefix() . 'roles', db_prefix() . 'roles.roleid = ' . $this->table . '.role_id', 'left');
        $this->db->where($this->table . '.role_id !=', 6); // Exclude admin role
        $this->db->order_by($this->table . '.created_at', 'DESC');
        return $this->db->get($this->table)->result();
    }
    
    /**
     * Get total count
     */
    public function get_total_count($active_only = false)
    {
        if ($active_only) {
            $this->db->where('active', 1);
        }
        $this->db->where('role_id !=', 6); // Exclude admin role
        return $this->db->count_all_results($this->table);
    }
    
    /**
     * Get count by role
     */
    public function get_count_by_role()
    {
        $this->db->select('role_id, COUNT(*) as count, ' . db_prefix() . 'roles.name as role_name');
        $this->db->join(db_prefix() . 'roles', db_prefix() . 'roles.roleid = ' . $this->table . '.role_id', 'left');
        $this->db->where($this->table . '.role_id !=', 6); // Exclude admin role
        $this->db->group_by('role_id');
        return $this->db->get($this->table)->result();
    }
    
    /**
     * Get recent users
     */
    public function get_recent($limit = 5)
    {
        $this->db->select($this->table . '.*, ' . db_prefix() . 'roles.name as role_name');
        $this->db->join(db_prefix() . 'roles', db_prefix() . 'roles.roleid = ' . $this->table . '.role_id', 'left');
        $this->db->where($this->table . '.role_id !=', 6); // Exclude admin role
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get($this->table)->result();
    }
    
    /**
     * Save (Add or Update)
     */
    public function save($data)
    {
        $id = isset($data['id']) && !empty($data['id']) ? $data['id'] : null;
        
        // Check if trying to create/edit admin role user
        if (isset($data['role_id']) && $data['role_id'] == 6) {
            return ['success' => false, 'message' => 'Cannot create or modify Admin role users'];
        }
        
        // Server-side validation
        $validation_errors = $this->validate_user_data($data, $id);
        if (!empty($validation_errors)) {
            return ['success' => false, 'message' => implode('<br>', $validation_errors), 'errors' => $validation_errors];
        }
        
        // Check email exists
        if ($this->email_exists($data['email'], $id)) {
            return ['success' => false, 'message' => 'Email address already exists', 'errors' => ['email' => 'Email already exists']];
        }
        
        // Prepare data for hospital_users table
        $save_data = [
            'role_id'         => $data['role_id'],
            'first_name'      => trim($data['first_name']),
            'last_name'       => trim($data['last_name']),
            'email'           => trim(strtolower($data['email'])),
            'phone_number'    => !empty($data['phone_number']) ? trim($data['phone_number']) : null,
            'landline_number' => !empty($data['landline_number']) ? trim($data['landline_number']) : null,
            'address'         => !empty($data['address']) ? trim($data['address']) : null,
            'active'          => isset($data['active']) && $data['active'] == 1 ? 1 : 0,
        ];
        
        // Hash password if provided
        if (!empty($data['password'])) {
            $save_data['password'] = app_hash_password($data['password']);
        }
        
        // Update or Insert
        if ($id) {
            $save_data['updated_at'] = date('Y-m-d H:i:s');
            $this->db->where('id', $id);
            $this->db->update($this->table, $save_data);
            
            // Update staff record
            $this->update_staff_record($id, $data);
            
            log_activity('Hospital User Updated [ID: ' . $id . ', Name: ' . $save_data['first_name'] . ' ' . $save_data['last_name'] . ']');
            return ['success' => true, 'message' => 'User updated successfully'];
        } else {
            $save_data['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert($this->table, $save_data);
            $insert_id = $this->db->insert_id();
            
            // Create staff record
            $this->create_staff_record($insert_id, $data);
            
            log_activity('Hospital User Created [ID: ' . $insert_id . ', Name: ' . $save_data['first_name'] . ' ' . $save_data['last_name'] . ']');
            return ['success' => true, 'message' => 'User created successfully', 'id' => $insert_id];
        }
    }
    
    /**
     * Validate user data
     * UPDATED: Removed minimum length validation for names and password
     */
    private function validate_user_data($data, $id = null)
    {
        $errors = [];
        
        // First name - Only check if not empty
        if (empty($data['first_name']) || strlen(trim($data['first_name'])) < 1) {
            $errors['first_name'] = 'First name is required';
        }
        
        // Last name - Only check if not empty
        if (empty($data['last_name']) || strlen(trim($data['last_name'])) < 1) {
            $errors['last_name'] = 'Last name is required';
        }
        
        // Email
        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }
        
        // Role
        if (empty($data['role_id']) || !is_numeric($data['role_id'])) {
            $errors['role_id'] = 'Please select a valid role';
        }
        
        // Password (required for new users only)
        if (!$id && empty($data['password'])) {
            $errors['password'] = 'Password is required';
        }
        
        // Phone validation (if provided)
        if (!empty($data['phone_number']) && !preg_match('/^[0-9+\-\s()]+$/', $data['phone_number'])) {
            $errors['phone_number'] = 'Invalid phone number format';
        }
        
        // Landline validation (if provided)
        if (!empty($data['landline_number']) && !preg_match('/^[0-9+\-\s()]+$/', $data['landline_number'])) {
            $errors['landline_number'] = 'Invalid landline number format';
        }
        
        return $errors;
    }
    
    /**
     * Create staff record
     */
    private function create_staff_record($hospital_user_id, $data)
    {
        $staff_data = [
            'email'       => trim(strtolower($data['email'])),
            'firstname'   => trim($data['first_name']),
            'lastname'    => trim($data['last_name']),
            'phonenumber' => !empty($data['phone_number']) ? trim($data['phone_number']) : '',
            'role'        => $data['role_id'],
            'password'    => app_hash_password($data['password']),
            'datecreated' => date('Y-m-d H:i:s'),
            'active'      => isset($data['active']) && $data['active'] == 1 ? 1 : 0,
        ];
        
        $this->db->insert(db_prefix() . 'staff', $staff_data);
        $staff_id = $this->db->insert_id();
        
        // Link hospital_user with staff
        $this->db->where('id', $hospital_user_id);
        $this->db->update($this->table, ['staff_id' => $staff_id]);
        
        return $staff_id;
    }
    
    /**
     * Update staff record
     */
    private function update_staff_record($hospital_user_id, $data)
    {
        // Get staff_id from hospital_users
        $hospital_user = $this->db->get_where($this->table, ['id' => $hospital_user_id])->row();
        
        if (!$hospital_user || !isset($hospital_user->staff_id)) {
            return;
        }
        
        $staff_data = [
            'email'       => trim(strtolower($data['email'])),
            'firstname'   => trim($data['first_name']),
            'lastname'    => trim($data['last_name']),
            'phonenumber' => !empty($data['phone_number']) ? trim($data['phone_number']) : '',
            'role'        => $data['role_id'],
            'active'      => isset($data['active']) && $data['active'] == 1 ? 1 : 0,
        ];
        
        // Update password if provided
        if (!empty($data['password'])) {
            $staff_data['password'] = app_hash_password($data['password']);
        }
        
        $this->db->where('staffid', $hospital_user->staff_id);
        $this->db->update(db_prefix() . 'staff', $staff_data);
    }
    
    /**
     * Delete user
     */
    public function delete($id)
    {
        $user = $this->get($id);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }
        
        // Prevent deleting admin role users
        if ($user->role_id == 6) {
            return ['success' => false, 'message' => 'Cannot delete Admin role users'];
        }
        
        // Delete staff record if exists
        if (isset($user->staff_id) && $user->staff_id) {
            $this->db->where('staffid', $user->staff_id);
            $this->db->delete(db_prefix() . 'staff');
        }
        
        // Delete hospital user
        $this->db->where('id', $id);
        $this->db->delete($this->table);
        
        if ($this->db->affected_rows() > 0) {
            log_activity('Hospital User Deleted [ID: ' . $id . ', Name: ' . $user->first_name . ' ' . $user->last_name . ']');
            return ['success' => true, 'message' => 'User deleted successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to delete user'];
    }
    
    /**
     * Check if email exists
     */
    public function email_exists($email, $exclude_id = null)
    {
        $this->db->where('email', trim(strtolower($email)));
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        return $this->db->get($this->table)->num_rows() > 0;
    }
    
    /**
     * Create new role - FIXED VERSION
     * Properly initializes permissions field with serialized empty array
     */
    public function create_role($role_name)
    {
        $role_name = trim($role_name);
        
        // Prevent creating "Admin" role
        if (strtolower($role_name) === 'admin') {
            return ['success' => false, 'message' => 'Cannot create Admin role'];
        }
        
        // Validate role name
        if (empty($role_name)) {
            return ['success' => false, 'message' => 'Role name is required'];
        }
        
        if (strlen($role_name) > 150) {
            return ['success' => false, 'message' => 'Role name too long (max 150 characters)'];
        }
        
        // Check if role exists
        $exists = $this->db->get_where(db_prefix() . 'roles', ['name' => $role_name])->num_rows();
        if ($exists > 0) {
            return ['success' => false, 'message' => 'Role already exists'];
        }
        
        // Prepare role data with proper Perfex structure
        $role_data = [
            'name' => $role_name,
            'permissions' => serialize([])  // FIXED: Initialize with empty serialized array
        ];
        
        // Insert into database
        $this->db->insert(db_prefix() . 'roles', $role_data);
        $role_id = $this->db->insert_id();
        
        if ($role_id) {
            log_activity('New Role Created: ' . $role_name . ' [ID: ' . $role_id . ']');
            return [
                'success' => true, 
                'message' => 'Role created successfully', 
                'role_id' => $role_id, 
                'role_name' => $role_name
            ];
        }
        
        return ['success' => false, 'message' => 'Failed to create role'];
    }
    /**
 * Get all roles with user count (excluding admin)
 */
public function get_roles_with_count()
{
    $sql = "SELECT 
                r.roleid, 
                r.name,
                COUNT(hu.id) as user_count
            FROM " . db_prefix() . "roles r
            LEFT JOIN " . $this->table . " hu ON hu.role_id = r.roleid
            WHERE r.roleid != 6
            GROUP BY r.roleid, r.name
            ORDER BY r.name ASC";
    
    return $this->db->query($sql)->result_array();
}
}