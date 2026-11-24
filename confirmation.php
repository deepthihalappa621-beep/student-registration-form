<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

$applicationId = isset($_SESSION['last_application_id']) ? (int)$_SESSION['last_application_id'] : 0;

if ($applicationId <= 0) {
    $_SESSION['flash_message'] = [
        'type' => 'warning',
        'text' => 'No recent registration found. Please complete the registration form below.',
    ];
    header('Location: index.php#registration');
    exit;
}

try {
    $pdo  = get_db_connection();
    $stmt = $pdo->prepare('SELECT * FROM applications WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $applicationId]);
    $application = $stmt->fetch();
} catch (PDOException $e) {
    error_log('Confirmation fetch error: ' . $e->getMessage());
    $application = false;
}

if (!$application) {
    header('Location: index.php');
    exit;
}

$justRegistered = !empty($_SESSION['just_registered']);
unset($_SESSION['just_registered']);

$refNumber     = format_application_ref((int)$application['id'], $application['created_at']);
$submittedTime = date('d M Y, h:i A', strtotime($application['created_at']));
$formattedDob  = date('d M Y', strtotime($application['dob']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Confirmation | <?= e(COLLEGE_NAME) ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Registration Success Toast Notification -->
    <?php if ($justRegistered): ?>
        <div class="toast-container position-fixed top-0 end-0 p-3 no-print" style="z-index: 1080;">
            <div id="successToast" class="toast align-items-center text-bg-success border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body fw-medium">
                        <i class="fa-solid fa-circle-check me-2"></i>
                        Application saved! Ref: <?= e($refNumber) ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">

                <!-- Action Bar (Hidden when printing) -->
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4 no-print">
                    <a href="index.php#home" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-house me-1"></i> Back to Home
                    </a>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" onclick="window.print()" class="btn btn-primary">
                            <i class="fa-solid fa-print me-1"></i> Print Application
                        </button>
                        <button type="button" id="downloadPdfBtn" class="btn btn-success">
                            <i class="fa-solid fa-file-pdf me-1"></i> Download / Save as PDF
                        </button>
                    </div>
                </div>

                <!-- Official College Application Receipt -->
                <div id="receiptContainer" class="receipt-card card border-0 shadow-lg overflow-hidden">

                    <!-- Receipt Header -->
                    <div class="receipt-header text-white p-4">
                        <div class="d-flex flex-column flex-sm-row align-items-center gap-3 text-center text-sm-start">
                            <div class="logo-wrapper bg-white">
                                <img src="assets/images/college-logo.png"
                                     alt="RIT Hassan Logo"
                                     class="college-logo"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="logo-fallback" style="display: none;">
                                    <i class="fa-solid fa-building-columns"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h4 class="fw-bold mb-1"><?= e(COLLEGE_NAME) ?></h4>
                                <div class="small opacity-90"><?= e(PORTAL_TITLE) ?> &mdash; OFFICIAL APPLICATION RECEIPT</div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4 p-md-5 bg-white">
                        <!-- Success Banner -->
                        <div class="text-center mb-4 pb-3 border-bottom">
                            <div class="success-icon-circle mx-auto mb-3">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <h3 class="fw-bold text-success mb-1">Registration Submitted Successfully!</h3>
                            <p class="text-muted small mb-0">
                                Your registration details have been securely recorded in the college database.
                            </p>
                        </div>

                        <!-- Reference Metadata Strip -->
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <div class="meta-box p-3 rounded-3">
                                    <div class="small text-muted text-uppercase fw-semibold">Application Reference Number</div>
                                    <div class="fs-5 fw-bold text-primary mt-1"><?= e($refNumber) ?></div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="meta-box p-3 rounded-3">
                                    <div class="small text-muted text-uppercase fw-semibold">Submission Date &amp; Time</div>
                                    <div class="fs-6 fw-bold text-dark mt-1"><?= e($submittedTime) ?></div>
                                </div>
                            </div>
                        </div>

                        <!-- Student Details Table -->
                        <h6 class="fw-bold text-uppercase text-primary mb-3">
                            <i class="fa-solid fa-id-badge me-2"></i>Submitted Application Details
                        </h6>

                        <div class="table-responsive">
                            <table class="table table-bordered receipt-table align-middle mb-4">
                                <tbody>
                                    <tr>
                                        <th scope="row" style="width: 35%;">Full Name</th>
                                        <td class="fw-semibold"><?= e($application['full_name']) ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">University Seat Number (USN)</th>
                                        <td class="fw-bold text-primary"><?= e($application['usn']) ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Email Address</th>
                                        <td><?= e($application['email']) ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Phone Number</th>
                                        <td><?= e($application['phone']) ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Department</th>
                                        <td><?= e($application['department']) ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Semester</th>
                                        <td><?= e($application['semester']) ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Gender</th>
                                        <td><?= e($application['gender']) ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Date of Birth</th>
                                        <td><?= e($formattedDob) ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Address</th>
                                        <td><?= nl2br(e($application['address'])) ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Official Footer Note -->
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-end pt-3 border-top small text-muted gap-3">
                            <div>
                                <div class="fw-semibold text-dark">Verification Status: <span class="text-success">Recorded</span></div>
                                <div>This is a computer-generated receipt and does not require a physical signature.</div>
                            </div>
                            <div class="text-sm-end">
                                <div class="fw-bold text-dark">Academic Section</div>
                                <div><?= e(COLLEGE_NAME) ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Navigation Buttons (Hidden when printing) -->
                <div class="d-flex flex-wrap justify-content-center gap-3 mt-4 no-print">
                    <a href="index.php#home" class="btn btn-outline-primary px-4">
                        <i class="fa-solid fa-house me-2"></i>Back to Home
                    </a>
                    <a href="index.php#registration" class="btn btn-primary px-4">
                        <i class="fa-solid fa-user-plus me-2"></i>Submit Another Application
                    </a>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- html2pdf.js for Direct PDF Download -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        // Show registration success toast on load
        document.addEventListener('DOMContentLoaded', function () {
            const toastEl = document.getElementById('successToast');
            if (toastEl) {
                const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
                toast.show();
            }

            // Download as PDF button handler
            const pdfBtn = document.getElementById('downloadPdfBtn');
            if (pdfBtn) {
                pdfBtn.addEventListener('click', function () {
                    const element = document.getElementById('receiptContainer');
                    if (typeof html2pdf !== 'undefined') {
                        const opt = {
                            margin:       0.4,
                            filename:     'RIT_Application_<?= e($application['usn']) ?>.pdf',
                            image:        { type: 'jpeg', quality: 0.98 },
                            html2canvas:  { scale: 2, useCORS: true },
                            jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
                        };
                        html2pdf().set(opt).from(element).save();
                    } else {
                        // Fallback to browser Save as PDF print dialog
                        window.print();
                    }
                });
            }
        });
    </script>
</body>
</html>