<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

// ==========================================
// TABLE 1: hospital_users
// ==========================================
if (!$CI->db->table_exists(db_prefix() . 'hospital_users')) {
    
    $CI->db->query("CREATE TABLE `" . db_prefix() . "hospital_users` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `staff_id` INT(11) DEFAULT NULL,
        `role_id` INT(11) NOT NULL,
        `first_name` VARCHAR(100) NOT NULL,
        `last_name` VARCHAR(100) NOT NULL,
        `email` VARCHAR(150) NOT NULL,
        `password` VARCHAR(255) NOT NULL,
        `phone_number` VARCHAR(30) DEFAULT NULL,
        `landline_number` VARCHAR(30) DEFAULT NULL,
        `address` TEXT DEFAULT NULL,
        `active` TINYINT(1) NOT NULL DEFAULT 1,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `email` (`email`),
        KEY `role_id` (`role_id`),
        KEY `staff_id` (`staff_id`),
        CONSTRAINT `fk_hospital_users_role` FOREIGN KEY (`role_id`) 
            REFERENCES `" . db_prefix() . "roles` (`roleid`) 
            ON DELETE RESTRICT ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";");
    
    log_activity('Hospital Management Module - Table Created: hospital_users');
}

// ==========================================
// TABLE 2: hospital_patient_types
// ==========================================
if (!$CI->db->table_exists(db_prefix() . 'hospital_patient_types')) {
    
    $CI->db->query("CREATE TABLE `" . db_prefix() . "hospital_patient_types` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `type_name` VARCHAR(100) NOT NULL,
        `type_code` VARCHAR(50) DEFAULT NULL,
        `description` TEXT DEFAULT NULL,
        `is_active` TINYINT(1) NOT NULL DEFAULT 1,
        `display_order` INT(11) DEFAULT 0,
        PRIMARY KEY (`id`),
        UNIQUE KEY `type_name` (`type_name`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";");
    
    // Insert default patient types from the screenshots
    $patient_types = [
        'Biohazard', 'Concession Cases', 'Contact Lens', 'Donor', 'DRPROJECT',
        'Emergency', 'HELPAGE', 'IGICH', 'IGICH-UVEA', 'KTM', 'Low Vision',
        'Post Operative', 'Regular', 'ROP Cognizant', 'SLS', 'SSVCB', 'SSVCJ',
        'SSVCK', 'SSVCM', 'SSVCN', 'SSVCP', 'SSVCT', 'VCA', 'VCB', 'VCJ',
        'VCK', 'VCKP', 'VCM', 'VCN', 'VCP', 'VCR', 'VCSM MADDUR', 'VCT',
        'VIIO Staff', 'VIP', 'Vision Therapy', 'Vision Therapy Cognizant',
        'Visual Rehabilitation'
    ];
    
    $display_order = 1;
    foreach ($patient_types as $type) {
        $CI->db->insert(db_prefix() . 'hospital_patient_types', [
            'type_name' => $type,
            'type_code' => strtoupper(str_replace(' ', '_', $type)),
            'is_active' => 1,
            'display_order' => $display_order++
        ]);
    }
    
    log_activity('Hospital Management Module - Table Created: hospital_patient_types');
}

// ==========================================
// TABLE 3: hospital_patients
// ==========================================
if (!$CI->db->table_exists(db_prefix() . 'hospital_patients')) {
    
    $CI->db->query("CREATE TABLE `" . db_prefix() . "hospital_patients` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `patient_number` VARCHAR(50) NOT NULL,
        
        -- Patient/Appointment Type
        `is_new_patient` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=New, 0=Existing',
        `mode` ENUM('appointment', 'walk_in') NOT NULL DEFAULT 'appointment',
        
        -- Patient Details
        `registered_other_hospital` TINYINT(1) DEFAULT NULL COMMENT '1=Yes, 0=No',
        `other_hospital_patient_id` VARCHAR(100) DEFAULT NULL COMMENT 'Patient ID from other hospital', -- ← NEW FIELD
        `name` VARCHAR(200) NOT NULL,
        `gender` ENUM('male', 'female', 'other') NOT NULL,
        `dob` DATE DEFAULT NULL,
        `age` INT(3) DEFAULT NULL,
        `address` TEXT DEFAULT NULL,
        `address_landmark` VARCHAR(200) DEFAULT NULL,
        `city` VARCHAR(100) DEFAULT NULL,
        `state` VARCHAR(100) DEFAULT NULL,
        `pincode` VARCHAR(20) DEFAULT NULL,
        
        -- Contact Details
        `phone` VARCHAR(30) DEFAULT NULL,
        `mobile_number` VARCHAR(30) NOT NULL,
        `email` VARCHAR(150) DEFAULT NULL,
        
        -- Other Details
        `fee_payment` ENUM('yes', 'no', 'not_applicable') DEFAULT 'not_applicable',
        `reason_for_appointment` ENUM('consultation', 'procedure', 'surgery') NOT NULL,
        `patient_type` VARCHAR(100) NOT NULL,
        
        -- Membership Details
        `recommended_to_hospital` TINYINT(1) DEFAULT NULL COMMENT '1=Yes, 0=No',
        `recommended_by` VARCHAR(200) DEFAULT NULL COMMENT 'Name of person who recommended',
        `has_membership` TINYINT(1) DEFAULT 0 COMMENT '1=Yes, 0=No',
        `membership_type` VARCHAR(100) DEFAULT NULL,
        `membership_number` VARCHAR(100) DEFAULT NULL,
        `membership_expiry_date` DATE DEFAULT NULL,
        `membership_notes` TEXT DEFAULT NULL,
                
        -- System Fields
        `status` ENUM('active', 'inactive', 'discharged') NOT NULL DEFAULT 'active',
        `created_by` INT(11) DEFAULT NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        
        PRIMARY KEY (`id`),
        UNIQUE KEY `patient_number` (`patient_number`),
        KEY `mobile_number` (`mobile_number`),
        KEY `email` (`email`),
        KEY `patient_type` (`patient_type`),
        KEY `created_by` (`created_by`),
        KEY `idx_other_hospital_patient_id` (`other_hospital_patient_id`) -- ← NEW INDEX
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";");
    
    log_activity('Hospital Management Module - Table Created: hospital_patients');
}

// ==========================================
// TABLE 4: hospital_appointments
// ==========================================
if (!$CI->db->table_exists(db_prefix() . 'hospital_appointments')) {
    
    $CI->db->query("CREATE TABLE `" . db_prefix() . "hospital_appointments` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `appointment_number` VARCHAR(50) NOT NULL,
        `patient_id` INT(11) NOT NULL,
        `patient_mode` ENUM('appointment', 'walk_in') NOT NULL DEFAULT 'appointment',
        `is_new_patient` TINYINT(1) NOT NULL DEFAULT 1,
        
        -- Appointment Details
        `appointment_date` DATE NOT NULL,
        `appointment_time` TIME DEFAULT NULL,
        `reason_for_appointment` ENUM('consultation', 'procedure', 'surgery') NOT NULL,
        `consultant_id` INT(11) NOT NULL COMMENT 'Staff ID from staff table',
        
        -- Status
        `status` ENUM('pending', 'confirmed', 'cancelled', 'completed') NOT NULL DEFAULT 'pending',
        `notes` TEXT DEFAULT NULL,
        `cancellation_reason` TEXT DEFAULT NULL,
        
        -- System Fields
        `created_by` INT(11) DEFAULT NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        
        PRIMARY KEY (`id`),
        UNIQUE KEY `appointment_number` (`appointment_number`),
        KEY `patient_id` (`patient_id`),
        KEY `consultant_id` (`consultant_id`),
        KEY `appointment_date` (`appointment_date`),
        KEY `status` (`status`),
        CONSTRAINT `fk_appointment_patient` FOREIGN KEY (`patient_id`) 
            REFERENCES `" . db_prefix() . "hospital_patients` (`id`) 
            ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT `fk_appointment_consultant` FOREIGN KEY (`consultant_id`) 
            REFERENCES `" . db_prefix() . "staff` (`staffid`) 
            ON DELETE RESTRICT ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";");
    
    log_activity('Hospital Management Module - Table Created: hospital_appointments');
}


// ==========================================
// TABLE 5: hospital_patient_documents (NEW)
// ==========================================
if (!$CI->db->table_exists(db_prefix() . 'hospital_patient_documents')) {
    
    $CI->db->query("CREATE TABLE `" . db_prefix() . "hospital_patient_documents` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `patient_id` INT(11) NOT NULL,
        `document_type` ENUM('recommendation', 'membership', 'medical_report', 'prescription', 'lab_report', 'other') NOT NULL,
        `document_name` VARCHAR(255) NOT NULL COMMENT 'Display name for document',
        `original_filename` VARCHAR(255) NOT NULL COMMENT 'Original uploaded filename',
        `file_type` VARCHAR(100) NOT NULL COMMENT 'MIME type (e.g., application/pdf, image/jpeg)',
        `file_size` INT(11) NOT NULL COMMENT 'File size in bytes',
        `file_data` LONGBLOB NOT NULL COMMENT 'Binary file data stored in database',
        `uploaded_by` INT(11) DEFAULT NULL COMMENT 'Staff ID who uploaded',
        `uploaded_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `notes` TEXT DEFAULT NULL COMMENT 'Additional notes about document',
        
        PRIMARY KEY (`id`),
        KEY `patient_id` (`patient_id`),
        KEY `document_type` (`document_type`),
        KEY `uploaded_by` (`uploaded_by`),
        CONSTRAINT `fk_patient_documents` FOREIGN KEY (`patient_id`) 
            REFERENCES `" . db_prefix() . "hospital_patients` (`id`) 
            ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";");
    
    log_activity('Hospital Management Module - Table Created: hospital_patient_documents');
}