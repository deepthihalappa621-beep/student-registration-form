<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

$csrfToken = get_csrf_token();
$errors    = $_SESSION['form_errors'] ?? [];
$old       = $_SESSION['old_input'] ?? [];
$flashMsg  = $_SESSION['flash_message'] ?? null;

// Clear flash session data after reading
unset($_SESSION['form_errors'], $_SESSION['old_input'], $_SESSION['flash_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(PORTAL_TITLE) ?> | <?= e(COLLEGE_NAME) ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="style.css" rel="stylesheet">
</head>
<body>

    <!-- Top Institutional Bar -->
    <div class="top-bar py-2">
        <div class="container d-flex flex-wrap justify-content-between align-items-center small">
            <div>
                <i class="fa-solid fa-graduation-cap me-2"></i>
                Affiliated to Visvesvaraya Technological University (VTU), Belagavi &bull; Approved by AICTE
            </div>
            <div class="d-none d-md-block">
                <i class="fa-solid fa-phone me-1"></i> +91-8172-243180
                <span class="mx-2">|</span>
                <i class="fa-solid fa-envelope me-1"></i> admissions@rithassan.ac.in
            </div>
        </div>
    </div>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-3" href="#home">
                <div class="logo-wrapper">
                    <img src="college-logo.png"
                         alt="RIT Hassan Logo"
                         class="college-logo"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="logo-fallback" style="display: none;">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>
                </div>
                <div>
                    <div class="brand-title"><?= e(COLLEGE_NAME) ?></div>
                    <div class="brand-subtitle"><?= e(PORTAL_TITLE) ?></div>
                </div>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                    aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                    <li class="nav-item">
                        <a class="nav-link active" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#registration">Registration</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                        <a class="btn btn-primary btn-sm px-3 rounded-pill" href="#registration">
                            <i class="fa-solid fa-file-signature me-1"></i> Apply Now
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header id="home" class="hero-section text-white">
        <div class="container py-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <span class="badge hero-badge mb-3">
                        <i class="fa-solid fa-circle-check me-1"></i> Academic Session 2026–2027 Open
                    </span>
                    <h1 class="display-5 fw-bold mb-3"><?= e(PORTAL_TITLE) ?></h1>
                    <p class="lead mb-4 opacity-90">
                        Welcome to the official digital registration portal of <strong><?= e(COLLEGE_NAME) ?></strong>.
                        Complete your semester registration online in minutes and download your verified application receipt immediately.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#registration" class="btn btn-light btn-lg text-primary fw-semibold px-4 shadow-sm">
                            <i class="fa-solid fa-pen-to-square me-2"></i> Start Registration
                        </a>
                        <a href="#about" class="btn btn-outline-light btn-lg px-4">
                            <i class="fa-solid fa-circle-info me-2"></i> Portal Guidelines
                        </a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="hero-info-card p-4">
                        <h5 class="fw-bold mb-3"><i class="fa-solid fa-list-check me-2"></i>Quick Checklist</h5>
                        <ul class="list-unstyled mb-0 small d-grid gap-2">
                            <li><i class="fa-solid fa-check text-info me-2"></i>Valid RIT Hassan USN (e.g., <?= e(USN_EXAMPLE) ?>)</li>
                            <li><i class="fa-solid fa-check text-info me-2"></i>Active Email Address &amp; 10-digit Mobile</li>
                            <li><i class="fa-solid fa-check text-info me-2"></i>Department &amp; Current Semester Details</li>
                            <li><i class="fa-solid fa-check text-info me-2"></i>Instant Printable / PDF Receipt</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Registration Form Section -->
    <section id="registration" class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-9">

                    <div class="text-center mb-4">
                        <span class="section-tag">Online Application Form</span>
                        <h2 class="fw-bold text-dark mt-2">Student Registration Details</h2>
                        <p class="text-muted">Fields marked with <span class="text-danger fw-bold">*</span> are mandatory.</p>
                    </div>

                    <?php if (!empty($flashMsg)): ?>
                        <div class="alert alert-<?= e($flashMsg['type'] ?? 'danger') ?> alert-dismissible fade show shadow-sm" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-2"></i>
                            <?= e($flashMsg['text'] ?? '') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($errors['general'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i>
                            <?= e($errors['general']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Registration Card -->
                    <div class="card registration-card border-0 shadow-lg">
                        <div class="card-header bg-white border-bottom p-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-semibold text-primary small text-uppercase">
                                    <i class="fa-solid fa-chart-line me-1"></i> Registration Progress
                                </span>
                                <span id="progressText" class="badge bg-primary rounded-pill">0% Completed</span>
                            </div>
                            <div class="progress" style="height: 10px;" role="progressbar" aria-label="Registration Progress" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width: 0%"></div>
                            </div>
                        </div>

                        <div class="card-body p-4 p-md-5">
                            <form id="registrationForm" action="submit.php" method="POST" novalidate>
                                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

                                <!-- Section 1: Personal & Academic Identity -->
                                <h5 class="form-section-title mb-4">
                                    <i class="fa-solid fa-user-graduate me-2"></i>Student Information
                                </h5>

                                <div class="row g-4">
                                    <!-- 1. Full Name -->
                                    <div class="col-md-6">
                                        <label for="full_name" class="form-label fw-semibold">
                                            Full Name <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                                            <input type="text"
                                                   class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>"
                                                   id="full_name"
                                                   name="full_name"
                                                   placeholder="Enter your full name"
                                                   value="<?= e($old['full_name'] ?? '') ?>"
                                                   maxlength="120"
                                                   required>
                                        </div>
                                        <div class="field-error" id="error_full_name"><?= e($errors['full_name'] ?? '') ?></div>
                                    </div>

                                    <!-- 2. USN -->
                                    <div class="col-md-6">
                                        <label for="usn" class="form-label fw-semibold">
                                            University Seat Number (USN) <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa-solid fa-id-card"></i></span>
                                            <input type="text"
                                                   class="form-control text-uppercase <?= isset($errors['usn']) ? 'is-invalid' : '' ?>"
                                                   id="usn"
                                                   name="usn"
                                                   placeholder="e.g., <?= e(USN_EXAMPLE) ?>"
                                                   value="<?= e($old['usn'] ?? '') ?>"
                                                   maxlength="10"
                                                   required>
                                        </div>
                                        <small class="text-muted">Format: <code><?= e(USN_EXAMPLE) ?></code> (RIT Hassan VTU USN)</small>
                                        <div class="field-error" id="error_usn"><?= e($errors['usn'] ?? '') ?></div>
                                    </div>

                                    <!-- 3. Email Address -->
                                    <div class="col-md-6">
                                        <label for="email" class="form-label fw-semibold">
                                            Email Address <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                                            <input type="email"
                                                   class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                                   id="email"
                                                   name="email"
                                                   placeholder="student@example.com"
                                                   value="<?= e($old['email'] ?? '') ?>"
                                                   maxlength="150"
                                                   required>
                                        </div>
                                        <div class="field-error" id="error_email"><?= e($errors['email'] ?? '') ?></div>
                                    </div>

                                    <!-- 4. Phone Number -->
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label fw-semibold">
                                            Phone Number <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
                                            <input type="tel"
                                                   class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>"
                                                   id="phone"
                                                   name="phone"
                                                   placeholder="10-digit mobile number"
                                                   value="<?= e($old['phone'] ?? '') ?>"
                                                   maxlength="10"
                                                   inputmode="numeric"
                                                   required>
                                        </div>
                                        <div class="field-error" id="error_phone"><?= e($errors['phone'] ?? '') ?></div>
                                    </div>

                                    <!-- 5. Department -->
                                    <div class="col-md-6">
                                        <label for="department" class="form-label fw-semibold">
                                            Department <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa-solid fa-building"></i></span>
                                            <select class="form-select <?= isset($errors['department']) ? 'is-invalid' : '' ?>"
                                                    id="department"
                                                    name="department"
                                                    required>
                                                <option value="">-- Select Department --</option>
                                                <?php foreach (ALLOWED_DEPARTMENTS as $dept): ?>
                                                    <option value="<?= e($dept) ?>" <?= (($old['department'] ?? '') ===$dept) ? 'selected' : '' ?>>
                                                        <?= e($dept) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="field-error" id="error_department"><?= e($errors['department'] ?? '') ?></div>
                                    </div>

                                    <!-- 6. Semester -->
                                    <div class="col-md-6">
                                        <label for="semester" class="form-label fw-semibold">
                                            Semester <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa-solid fa-layer-group"></i></span>
                                            <select class="form-select <?= isset($errors['semester']) ? 'is-invalid' : '' ?>"
                                                    id="semester"
                                                    name="semester"
                                                    required>
                                                <option value="">-- Select Semester --</option>
                                                <?php foreach (ALLOWED_SEMESTERS as $sem): ?>
                                                    <option value="<?= e($sem) ?>" <?= (($old['semester'] ?? '') ===$sem) ? 'selected' : '' ?>>
                                                        <?= e($sem) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="field-error" id="error_semester"><?= e($errors['semester'] ?? '') ?></div>
                                    </div>

                                    <!-- 7. Gender -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold d-block">
                                            Gender <span class="text-danger">*</span>
                                        </label>
                                        <div class="gender-group d-flex flex-wrap gap-3 pt-1">
                                            <?php foreach (ALLOWED_GENDERS as $genderOption): ?>
                                                <?php $gid = 'gender_' . strtolower($genderOption); ?>
                                                <div class="form-check form-check-inline custom-radio">
                                                    <input class="form-check-input"
                                                           type="radio"
                                                           name="gender"
                                                           id="<?= e($gid) ?>"
                                                           value="<?= e($genderOption) ?>"
                                                           <?= (($old['gender'] ?? '') ===$genderOption) ? 'checked' : '' ?>
                                                           required>
                                                    <label class="form-check-label" for="<?= e($gid) ?>">
                                                        <?= e($genderOption) ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="field-error" id="error_gender"><?= e($errors['gender'] ?? '') ?></div>
                                    </div>

                                    <!-- 8. Date of Birth -->
                                    <div class="col-md-6">
                                        <label for="dob" class="form-label fw-semibold">
                                            Date of Birth <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa-solid fa-calendar-days"></i></span>
                                            <input type="date"
                                                   class="form-control <?= isset($errors['dob']) ? 'is-invalid' : '' ?>"
                                                   id="dob"
                                                   name="dob"
                                                   value="<?= e($old['dob'] ?? '') ?>"
                                                   max="<?= date('Y-m-d') ?>"
                                                   required>
                                        </div>
                                        <div class="field-error" id="error_dob"><?= e($errors['dob'] ?? '') ?></div>
                                    </div>

                                    <!-- 9. Address -->
                                    <div class="col-12">
                                        <label for="address" class="form-label fw-semibold">
                                            Permanent / Correspondence Address <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control <?= isset($errors['address']) ? 'is-invalid' : '' ?>"
                                                  id="address"
                                                  name="address"
                                                  rows="3"
                                                  placeholder="Enter door number, street, city, district, state, and PIN code"
                                                  required><?= e($old['address'] ?? '') ?></textarea>
                                        <div class="field-error" id="error_address"><?= e($errors['address'] ?? '') ?></div>
                                    </div>

                                    <!-- 10. Declaration -->
                                    <div class="col-12">
                                        <div class="declaration-box p-3 rounded-3">
                                            <div class="form-check">
                                                <input class="form-check-input <?= isset($errors['declaration']) ? 'is-invalid' : '' ?>"
                                                       type="checkbox"
                                                       id="declaration"
                                                       name="declaration"
                                                       value="1"
                                                       <?= !empty($old['declaration']) ? 'checked' : '' ?>
                                                       required>
                                                <label class="form-check-label small" for="declaration">
                                                    <strong>Declaration <span class="text-danger">*</span>:</strong>
                                                    I hereby declare that all the information furnished above is true, complete, and correct to the best of my knowledge and belief. I understand that any discrepancy may lead to cancellation of my registration.
                                                </label>
                                            </div>
                                            <div class="field-error mt-1" id="error_declaration"><?= e($errors['declaration'] ?? '') ?></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Actions -->
                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mt-5 pt-3 border-top">
                                    <button type="button" id="resetBtn" class="btn btn-outline-secondary px-4 w-100 w-sm-auto">
                                        <i class="fa-solid fa-rotate-left me-2"></i>Clear / Reset Form
                                    </button>
                                    <button type="submit" id="submitBtn" class="btn btn-primary btn-submit px-5 py-2 fw-bold w-100 w-sm-auto">
                                        <span class="btn-text">
                                            <i class="fa-solid fa-paper-plane me-2"></i>SUBMIT APPLICATION
                                        </span>
                                        <span class="btn-loader d-none">
                                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                            Submitting...
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-5 bg-white border-top">
        <div class="container">
            <div class="text-center mb-5">
                <span class="section-tag">About the Portal</span>
                <h2 class="fw-bold text-dark mt-2">Streamlined Digital Registration</h2>
                <p class="text-muted mx-auto" style="max-width: 680px;">
                    Designed for students of <?= e(COLLEGE_NAME) ?>, this portal ensures accurate data capture,
                    duplicate USN prevention, and instant generation of official registration receipts.
                </p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card p-4 h-100">
                        <div class="feature-icon mb-3"><i class="fa-solid fa-shield-halved"></i></div>
                        <h5 class="fw-bold">Dual-Layer Validation</h5>
                        <p class="text-muted small mb-0">
                            Real-time JavaScript/jQuery feedback combined with strict PHP server-side validation and PDO prepared statements.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card p-4 h-100">
                        <div class="feature-icon mb-3"><i class="fa-solid fa-database"></i></div>
                        <h5 class="fw-bold">Unique USN Protection</h5>
                        <p class="text-muted small mb-0">
                            Each USN is verified against the MySQL database to prevent duplicate entries and maintain clean academic records.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card p-4 h-100">
                        <div class="feature-icon mb-3"><i class="fa-solid fa-file-pdf"></i></div>
                        <h5 class="fw-bold">Instant Official Receipt</h5>
                        <p class="text-muted small mb-0">
                            Receive a formatted confirmation receipt with a unique reference number, ready to print or download as a PDF.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-5">
        <div class="container">
            <div class="card border-0 shadow-sm p-4 p-md-5">
                <div class="row align-items-center g-4">
                    <div class="col-lg-5">
                        <span class="section-tag">Help Desk</span>
                        <h3 class="fw-bold mt-2 mb-3">Need Assistance?</h3>
                        <p class="text-muted mb-0">
                            If you face any issues with your USN or registration details, contact the Academic Administration Office during working hours (Mon–Sat, 9:00 AM – 4:30 PM).
                        </p>
                    </div>
                    <div class="col-lg-7">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="contact-item p-3 rounded-3">
                                    <i class="fa-solid fa-location-dot text-primary mb-2 fs-5"></i>
                                    <h6 class="fw-bold mb-1">Campus Address</h6>
                                    <p class="small text-muted mb-0">Plot 1-D, Growth Center, Industrial Area, B.M. Bypass Road, Hassan, Karnataka – 573201</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="contact-item p-3 rounded-3">
                                    <i class="fa-solid fa-headset text-primary mb-2 fs-5"></i>
                                    <h6 class="fw-bold mb-1">Student Support</h6>
                                    <p class="small text-muted mb-0">Phone: +91-8172-243180<br>Email: registrar@rithassan.ac.in</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="site-footer text-white pt-5 pb-4">
        <div class="container">
            <div class="row g-4 pb-4 border-bottom border-light border-opacity-10">
                <div class="col-lg-5">
                    <h5 class="fw-bold mb-2"><?= e(COLLEGE_NAME) ?></h5>
                    <p class="small text-white-50 mb-0">
                        Official Student Online Registration Portal. Empowering engineering education through digital academic services.
                    </p>
                </div>
                <div class="col-6 col-lg-3">
                    <h6 class="fw-semibold mb-3">Quick Links</h6>
                    <ul class="list-unstyled small d-grid gap-2">
                        <li><a href="#home" class="footer-link">Home</a></li>
                        <li><a href="#registration" class="footer-link">Registration Form</a></li>
                        <li><a href="#about" class="footer-link">About Portal</a></li>
                        <li><a href="#contact" class="footer-link">Contact Help Desk</a></li>
                    </ul>
                </div>
                <div class="col-6 col-lg-4">
                    <h6 class="fw-semibold mb-3">Contact</h6>
                    <ul class="list-unstyled small text-white-50 d-grid gap-2">
                        <li><i class="fa-solid fa-location-dot me-2"></i>Hassan, Karnataka – 573201</li>
                        <li><i class="fa-solid fa-phone me-2"></i>+91-8172-243180</li>
                        <li><i class="fa-solid fa-envelope me-2"></i>admissions@rithassan.ac.in</li>
                    </ul>
                </div>
            </div>
            <div class="pt-3 d-flex flex-column flex-sm-row justify-content-between align-items-center small text-white-50">
                <div>&copy; <?= date('Y') ?> <?= e(COLLEGE_NAME) ?>. All Rights Reserved.</div>
                <div><?= e(PORTAL_TITLE) ?></div>
            </div>
        </div>
    </footer>

    <!-- jQuery 3.7 -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom Validation & Interactivity -->
    <script src="assets/js/script.js"></script>
</body>
</html>
