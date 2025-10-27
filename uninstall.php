<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

// Drop tables in reverse order (respect foreign keys)
$CI->db->query("DROP TABLE IF EXISTS `" . db_prefix() . "hospital_appointments`");
$CI->db->query("DROP TABLE IF EXISTS `" . db_prefix() . "hospital_patients`");
$CI->db->query("DROP TABLE IF EXISTS `" . db_prefix() . "hospital_patient_types`");
$CI->db->query("DROP TABLE IF EXISTS `" . db_prefix() . "hospital_users`");

log_activity('Hospital Management Module - All tables dropped');