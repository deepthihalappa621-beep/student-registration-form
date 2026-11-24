<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Verify CSRF token
$submittedToken = $_POST['csrf_token'] ?? '';
if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], (string)$submittedToken)) {
    $_SESSION['flash_message'] = [
        'type' => 'danger',
        'text' => 'Invalid or expired form session. Please try submitting again.',
    ];
    header('Location: index.php#registration');
    exit;
}

// Collect and trim inputs
$fullName    = trim((string)($_POST['full_name'] ?? ''));
$usn         = strtoupper(trim((string)($_POST['usn'] ?? '')));
$email       = trim((string)($_POST['email'] ?? ''));
$phone       = preg_replace('/\s+/', '', trim((string)($_POST['phone'] ?? '')));
$department  = trim((string)($_POST['department'] ?? ''));
$semester    = trim((string)($_POST['semester'] ?? ''));
$gender      = trim((string)($_POST['gender'] ?? ''));
$dob         = trim((string)($_POST['dob'] ?? ''));
$address     = trim((string)($_POST['address'] ?? ''));
$declaration = isset($_POST['declaration']) && $_POST['declaration'] === '1';

$oldInput = [
    'full_name'   => $fullName,
    'usn'         => $usn,
    'email'       => $email,
    'phone'       => $phone,
    'department'  => $department,
    'semester'    => $semester,
    'gender'      => $gender,
    'dob'         => $dob,
    'address'     => $address,
    'declaration' => $declaration ? '1' : '',
];

$errors = [];

// 1. Full Name validation
if ($fullName === '') {
    $errors['full_name'] = 'Full name is required.';
} elseif (!preg_match("/^[a-zA-Z\s.'-]{2,120}$/", $fullName)) {
    $errors['full_name'] = 'Full name must contain only valid letters, spaces, dots, or hyphens.';
}

// 2. USN validation
if ($usn === '') {
    $errors['usn'] = 'USN is required.';
} elseif (!preg_match(USN_PATTERN, $usn)) {
    $errors['usn'] = 'Invalid USN format. Example: ' . USN_EXAMPLE . '.';
}

// 3. Email validation
if ($email === '') {
    $errors['email'] = 'Email address is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) {
    $errors['email'] = 'Please enter a valid email address.';
}

// 4. Phone validation (exact 10 digits)
if ($phone === '') {
    $errors['phone'] = 'Phone number is required.';
} elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
    $errors['phone'] = 'Phone number must contain exactly 10 digits.';
}

// 5. Department validation
if ($department === '' || !in_array($department, ALLOWED_DEPARTMENTS, true)) {
    $errors['department'] = 'Please select a valid department.';
}

// 6. Semester validation
if ($semester === '' || !in_array($semester, ALLOWED_SEMESTERS, true)) {
    $errors['semester'] = 'Please select a valid semester.';
}

// 7. Gender validation
if ($gender === '' || !in_array($gender, ALLOWED_GENDERS, true)) {
    $errors['gender'] = 'Please select your gender.';
}

// 8. Date of Birth validation
if ($dob === '') {
    $errors['dob'] = 'Date of birth is required.';
} else {
    $dobObj = DateTime::createFromFormat('Y-m-d', $dob);
    $today  = new DateTime('today');
    if (!$dobObj || $dobObj->format('Y-m-d') !== $dob) {
        $errors['dob'] = 'Please enter a valid date of birth.';
    } elseif ($dobObj >= $today) {
        $errors['dob'] = 'Date of birth must be in the past.';
    }
}

// 9. Address validation
if ($address === '') {
    $errors['address'] = 'Address is required.';
} elseif (mb_strlen($address) < 10) {
    $errors['address'] = 'Please provide a complete address (at least 10 characters).';
}

// 10. Declaration validation
if (!$declaration) {
    $errors['declaration'] = 'You must accept the declaration before submitting.';
}

// Redirect back if validation failed
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['old_input']   = $oldInput;
    header('Location: index.php#registration');
    exit;
}

try {
    $pdo = get_db_connection();

    // Prevent duplicate USN registrations
    $checkStmt = $pdo->prepare('SELECT id FROM applications WHERE usn = :usn LIMIT 1');
    $checkStmt->execute([':usn' => $usn]);

    if ($checkStmt->fetch()) {
        $_SESSION['form_errors'] = [
            'usn' => 'An application with USN ' . $usn . ' has already been registered.',
        ];
        $_SESSION['old_input'] = $oldInput;
        header('Location: index.php#registration');
        exit;
    }

    // Insert validated student application using prepared statement
    $insertSql = 'INSERT INTO applications
        (full_name, usn, email, phone, department, semester, gender, dob, address)
        VALUES
        (:full_name, :usn, :email, :phone, :department, :semester, :gender, :dob, :address)';

    $stmt = $pdo->prepare($insertSql);
    $stmt->execute([
        ':full_name'  => $fullName,
        ':usn'        => $usn,
        ':email'      => $email,
        ':phone'      => $phone,
        ':department' => $department,
        ':semester'   => $semester,
        ':gender'     => $gender,
        ':dob'        => $dob,
        ':address'    => $address,
    ]);

    $applicationId = (int)$pdo->lastInsertId();

    // Rotate CSRF token to prevent replay/duplicate submissions
    $_SESSION['csrf_token']          = bin2hex(random_bytes(32));
    $_SESSION['last_application_id'] = $applicationId;
    $_SESSION['just_registered']     = true;

    // Post/Redirect/Get pattern to confirmation page
    header('Location: confirmation.php');
    exit;

} catch (PDOException $e) {
    error_log('Application submission error: ' . $e->getMessage());

    // Handle race-condition duplicate key error (SQLSTATE 23000)
    if ($e->getCode() === '23000') {
        $_SESSION['form_errors'] = [
            'usn' => 'This USN is already registered in the system.',
        ];
    } else {
        $_SESSION['form_errors'] = [
            'general' => 'An unexpected database error occurred while saving your application. Please try again.',
        ];
    }

    $_SESSION['old_input'] = $oldInput;
    header('Location: index.php#registration');
    exit;
}