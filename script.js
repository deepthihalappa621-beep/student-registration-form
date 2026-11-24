$(document).ready(function () {
    const $form = $('#registrationForm');
    if ($form.length === 0) return;

    // Regex patterns matching PHP server-side validation
    const patterns = {
        name: /^[a-zA-Z\s.'-]{2,120}$/,
        usn: /^4RA[0-9]{2}[A-Z]{2}[0-9]{3}$/i,
        email: /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/,
        phone: /^[0-9]{10}$/
    };

    // Helper: set error or valid state
    function setFieldState(fieldId, isValid, message) {
        const $field = $('#' + fieldId);
        const $error = $('#error_' + fieldId);

        if (isValid) {
            if ($field.length) {
                $field.removeClass('is-invalid').addClass('is-valid');
            }
            $error.text('');
        } else {
            if ($field.length) {
                $field.removeClass('is-valid').addClass('is-invalid');
            }
            $error.text(message);
        }
        return isValid;
    }

    // Individual field validators
    function validateFullName() {
        const val = $('#full_name').val().trim();
        if (!val) return setFieldState('full_name', false, 'Full name is required.');
        if (!patterns.name.test(val)) {
            return setFieldState('full_name', false, 'Full name must contain only valid letters, spaces, dots, or hyphens.');
        }
        return setFieldState('full_name', true, '');
    }

    function validateUSN() {
        const $usn = $('#usn');
        const val = $usn.val().trim().toUpperCase();
        $usn.val(val);
        if (!val) return setFieldState('usn', false, 'USN is required.');
        if (!patterns.usn.test(val)) {
            return setFieldState('usn', false, 'Invalid USN format. Example: 4RA23CS001.');
        }
        return setFieldState('usn', true, '');
    }

    function validateEmail() {
        const val = $('#email').val().trim();
        if (!val) return setFieldState('email', false, 'Email address is required.');
        if (!patterns.email.test(val)) {
            return setFieldState('email', false, 'Please enter a valid email address.');
        }
        return setFieldState('email', true, '');
    }

    function validatePhone() {
        const $phone = $('#phone');
        // Strip non-digits as user types
        const digits = $phone.val().replace(/\D/g, '').slice(0, 10);
        $phone.val(digits);
        if (!digits) return setFieldState('phone', false, 'Phone number is required.');
        if (!patterns.phone.test(digits)) {
            return setFieldState('phone', false, 'Phone number must contain exactly 10 digits.');
        }
        return setFieldState('phone', true, '');
    }

    function validateDepartment() {
        const val = $('#department').val();
        if (!val) return setFieldState('department', false, 'Please select a department.');
        return setFieldState('department', true, '');
    }

    function validateSemester() {
        const val = $('#semester').val();
        if (!val) return setFieldState('semester', false, 'Please select a semester.');
        return setFieldState('semester', true, '');
    }

    function validateGender() {
        const selected = $('input[name="gender"]:checked').val();
        if (!selected) {
            $('#error_gender').text('Please select your gender.');
            return false;
        }
        $('#error_gender').text('');
        return true;
    }

    function validateDob() {
        const val = $('#dob').val();
        if (!val) return setFieldState('dob', false, 'Date of birth is required.');
        const dobDate = new Date(val + 'T00:00:00');
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        if (isNaN(dobDate.getTime()) || dobDate >= today) {
            return setFieldState('dob', false, 'Date of birth must be in the past.');
        }
        return setFieldState('dob', true, '');
    }

    function validateAddress() {
        const val = $('#address').val().trim();
        if (!val) return setFieldState('address', false, 'Address is required.');
        if (val.length < 10) {
            return setFieldState('address', false, 'Please enter a complete address (at least 10 characters).');
        }
        return setFieldState('address', true, '');
    }

    function validateDeclaration() {
        const checked = $('#declaration').is(':checked');
        if (!checked) {
            return setFieldState('declaration', false, 'You must confirm the declaration before submitting.');
        }
        return setFieldState('declaration', true, '');
    }

    // Dynamic Progress Bar Calculation (10 required fields)
    function updateProgress() {
        let completed = 0;
        const total = 10;

        if (patterns.name.test($('#full_name').val().trim())) completed++;
        if (patterns.usn.test($('#usn').val().trim())) completed++;
        if (patterns.email.test($('#email').val().trim())) completed++;
        if (patterns.phone.test($('#phone').val().trim())) completed++;
        if ($('#department').val()) completed++;
        if ($('#semester').val()) completed++;
        if ($('input[name="gender"]:checked').length > 0) completed++;
        if ($('#dob').val()) completed++;
        if ($('#address').val().trim().length >= 10) completed++;
        if ($('#declaration').is(':checked')) completed++;

        const percent = Math.round((completed / total) * 100);
        $('#progressBar')
            .css('width', percent + '%')
            .parent().attr('aria-valuenow', percent);
        $('#progressText').text(percent + '% Completed');
    }

    // Bind real-time validation & progress updates
    $('#full_name').on('input blur', function () { validateFullName(); updateProgress(); });
    $('#usn').on('input blur', function () { validateUSN(); updateProgress(); });
    $('#email').on('input blur', function () { validateEmail(); updateProgress(); });
    $('#phone').on('input blur', function () { validatePhone(); updateProgress(); });
    $('#department').on('change blur', function () { validateDepartment(); updateProgress(); });
    $('#semester').on('change blur', function () { validateSemester(); updateProgress(); });
    $('input[name="gender"]').on('change', function () { validateGender(); updateProgress(); });
    $('#dob').on('change blur', function () { validateDob(); updateProgress(); });
    $('#address').on('input blur', function () { validateAddress(); updateProgress(); });
    $('#declaration').on('change', function () { validateDeclaration(); updateProgress(); });

    // Clear / Reset Form button
    $('#resetBtn').on('click', function () {
        $form[0].reset();
        $form.find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
        $form.find('.field-error').text('');
        updateProgress();
    });

    // Form Submit Handler
    $form.on('submit', function (e) {
        const checks = [
            validateFullName(),
            validateUSN(),
            validateEmail(),
            validatePhone(),
            validateDepartment(),
            validateSemester(),
            validateGender(),
            validateDob(),
            validateAddress(),
            validateDeclaration()
        ];

        const isAllValid = checks.every(Boolean);

        if (!isAllValid) {
            e.preventDefault();
            const $firstError = $form.find('.is-invalid').first();
            if ($firstError.length) {
                $('html, body').animate({ scrollTop: $firstError.offset().top - 120 }, 300);
                $firstError.focus();
            }
            return;
        }

        // Prevent double-submission and show loading spinner
        const $submitBtn = $('#submitBtn');
        $submitBtn.prop('disabled', true);
        $submitBtn.find('.btn-text').addClass('d-none');
        $submitBtn.find('.btn-loader').removeClass('d-none');
    });

    // Initialize progress on page load (handles repopulated fields)
    updateProgress();
});