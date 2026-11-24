<?php
declare(strict_types=1);

// Start session securely if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Application timezone (Indian Standard Time)
date_default_timezone_set(getenv('APP_TIMEZONE') ?: 'Asia/Kolkata');

// Database configuration via environment variables (with local fallbacks)
define('DB_HOST', getenv('DB_HOST') ?: getenv('MYSQLHOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: getenv('MYSQLPORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: getenv('MYSQLDATABASE') ?: 'student_registration');
define('DB_USER', getenv('DB_USER') ?: getenv('MYSQLUSER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : (getenv('MYSQLPASSWORD') !== false ? getenv('MYSQLPASSWORD') : ''));

// College and Portal Constants
define('COLLEGE_NAME', 'Rajeev Institute of Technology, Hassan');
define('PORTAL_TITLE', 'STUDENT ONLINE REGISTRATION PORTAL');

// RIT Hassan VTU USN format (e.g., 4RA23CS001).
// To allow any VTU college code, replace 4RA with [1-4][A-Z]{2}
define('USN_PATTERN', '/^4RA[0-9]{2}[A-Z]{2}[0-9]{3}$/i');
define('USN_EXAMPLE', '4RA23CS001');

// Allowed Dropdown Options (shared between frontend and backend validation)
define('ALLOWED_DEPARTMENTS', [
    'Computer Science and Engineering',
    'Artificial Intelligence and Machine Learning',
    'Information Science and Engineering',
    'Electronics and Communication Engineering',
    'Electrical and Electronics Engineering',
    'Civil Engineering',
    'Mechanical Engineering',
]);

define('ALLOWED_SEMESTERS', [
    '1st Semester',
    '2nd Semester',
    '3rd Semester',
    '4th Semester',
    '5th Semester',
    '6th Semester',
    '7th Semester',
    '8th Semester',
]);

define('ALLOWED_GENDERS', ['Male', 'Female', 'Other']);

/**
 * Safely escape output for HTML rendering.
 */
function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Generate or retrieve a session CSRF token.
 */
function get_csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Format an official Application Reference Number from record ID and timestamp.
 */
function format_application_ref(int $id, string$createdAt): string
{
    $year = date('Y', strtotime($createdAt) ?: time());
    return sprintf('RIT-%s-%05d', $year,$id);
}