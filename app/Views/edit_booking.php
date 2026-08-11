<!DOCTYPE html>
<html lang="en">
<head>
    <?= view('security_prompt') ?>
    <meta charset="UTF-8">
    <title>Edit Data Pelawat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.bootcss.com/font-awesome/5.7.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/menustyle.css') ?>">
    <style>
        /* Remove old field-error styles and replace with error-message */
        .field-error { 
            display: none !important; /* Hide old error messages */
        }
        .autofilled { background: #e6ffef; }
        #officer_results { 
            position: absolute; 
            width: 100%; 
            background: white; 
            border: 1px solid #ccc; 
            border-radius: 4px; 
            display: none; 
            max-height: 200px; 
            overflow-y: auto; 
            z-index: 2000; 
            margin-top: 2px;
            padding: 0;
            list-style: none;
        }
        #officer_results li { 
            padding: 8px 12px; 
            border-bottom: 1px solid #eee; 
            cursor: pointer; 
        }
        #officer_results li:last-child { 
            border-bottom: none; 
        }
        #officer_results li:hover { 
            background: #f0f0f0; 
            cursor: pointer; 
        }
        .is-invalid { 
            border-color: #dc3545 !important; 
        }
        .is-valid { 
            border-color: #28a745 !important; 
        }
        .time-input-group {
            display: flex;
            gap: 15px;
        }
        .time-input-wrapper {
            flex: 1;
        }
        .time-display {
            font-size: 0.85rem;
            color: #6c757d;
            min-height: 20px;
        }
        @media (max-width: 768px) {
            .time-input-group {
                flex-direction: column;
                gap: 10px;
            }
        }
        .error-message {
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 0.25rem;
            display: none; /* Hidden by default */
        }
        .form-label {
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        /* Remove Bootstrap validation icons since they conflict with our system */
        .form-control.is-invalid {
            background-image: none !important;
            padding-right: 0.75rem !important;
        }
        .form-control.is-valid {
            background-image: none !important;
            padding-right: 0.75rem !important;
        }
    </style>
</head>
<body class="bg-light">
<div class="container p-4 mt-5 bg-white shadow rounded">
    <h2 class="text-center mb-3">Kemaskini Maklumat Lawatan</h2>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="text-center mb-4">
        <p><strong>Kod QR untuk membuka halaman ini:</strong></p>
        <img src="<?= $qr ?>" style="width:150px; height:150px;">
        <br><br>
        <a href="<?= $qrPNG ?>" download="qr_booking_<?= $booking['booking_id'] ?>.png" class="btn btn-info mb-2">
            <i class="fas fa-download"></i> Muat Turun QR
        </a>
        <br>
        <?php
        $waMessage = "Sila buka link untuk kemaskini lawatan anda: $editURL , Sila kemaskini sebelum pukul 6 PM. Terima kasih.";
        $waLink = "https://wa.me/?text=" . urlencode($waMessage);
        ?>
        <a href="<?= $waLink ?>" target="_blank" class="btn btn-success mt-3">
            <i class="fab fa-whatsapp"></i> Hantar ke WhatsApp
        </a>
    </div>

    <?php if (!$allowEdit): ?>
        <div class="alert alert-danger text-center">
            <h4>Akses Disekat</h4>
            Masa untuk mengubah data pendaftaran telah tamat.<br>
            Sila hubungi admin untuk maklumat lanjut.
        </div>
    <?php else: ?>
        <!-- Countdown Timer -->
        <div class="alert alert-info text-center">
            Anda boleh ubah sehingga edit <strong>6:00 PM</strong> hari ini. <br>
            Masa yang tinggal : <span id="countdown"></span>
        </div>
        <script>
            function updateCountdown() {
                let now = new Date();
                let deadline = new Date();
                deadline.setHours(18, 0, 0, 0);
                let diff = deadline - now;
                if (diff <= 0) {
                    document.getElementById("countdown").innerHTML = "EXPIRED";
                    location.reload();
                    return;
                }
                let h = Math.floor(diff / 1000 / 60 / 60);
                let m = Math.floor(diff / 1000 / 60) % 60;
                let s = Math.floor(diff / 1000) % 60;
                document.getElementById("countdown").innerHTML = `${h}h ${m}m ${s}s`;
            }
            setInterval(updateCountdown, 1000);
            updateCountdown();
        </script>

        <form id="editBookingForm" action="<?= base_url('pelawat/update/' . $booking['booking_id']) ?>" method="post">
            <?= csrf_field() ?>
            
            <!-- NAME -->
            <div class="mb-3">
                <label class="form-label">Nama <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name_pelawat" class="form-control" value="<?= esc($booking['name']) ?>" maxlength="40" required>
                <div id="name_error_message" class="error-message"></div>
            </div>

            <!-- PHONE NUMBER VALIDATION -->
            <div class="mb-3">
                <label class="form-label">No. Telefon <span class="text-danger">*</span></label>
                <input type="text" id="phone_pelawat" name="phone_no" class="form-control" value="<?= esc($booking['phone_no']) ?>" maxlength="10" autocomplete="off" required>
                <div id="phone_error_message" class="error-message"></div>
            </div>

            <!-- OFFICER LIVE SEARCH -->
            <div class="mb-3 position-relative">
                <label class="form-label">Pegawai <span class="text-danger">*</span></label>
                <input type="text" id="search_officer" class="form-control" placeholder="Taip nama pegawai..." value="<?= esc($booking['officer']) ?>" autocomplete="off" maxlength="40" required>
                <div id="officer_error_message" class="error-message"></div>
                <ul id="officer_results" class="list-group shadow"></ul>
                <input type="hidden" name="officer" id="officer_selected" required>
            </div>

            <!-- REASON -->
            <div class="mb-3">
                <label class="form-label">Sebab <span class="text-danger">*</span></label>
                <textarea name="reason" id="reason_pelawat" class="form-control" maxlength="50" required><?= esc($booking['reason']) ?></textarea>
                <div id="reason_error_message" class="error-message"></div>
            </div>

            <!-- TIME SECTION -->
            <div class="mb-3">
                <label class="form-label">Masa Lawatan <span class="text-danger">*</span></label>
                <small class="text-muted d-block mb-2">(Hari ini sahaja: <?= date('d/m/Y') ?> | Waktu pejabat: 8:00 pagi - 6:00 petang)</small>
                
                <!-- Hidden fields for storing full timestamps in MySQL format -->
                <input type="hidden" name="time_in" id="time_in_full">
                <input type="hidden" name="time_out_exp" id="time_out_exp_full">
                <input type="hidden" name="time_out_real" id="time_out_real_full">
                
                <div class="time-input-group">
                    <div class="time-input-wrapper">
                        <label class="form-label small">Masa Masuk</label>
                        <input type="time" id="time_in_input" class="form-control" min="08:00" max="18:00" step="300" required>
                        <div class="time-display small text-muted mt-1" id="time_in_display"></div>
                        <div id="time_in_error_message" class="error-message"></div>
                    </div>
                    
                    <div class="time-input-wrapper">
                        <label class="form-label small">Masa Keluar (Dijangka)</label>
                        <input type="time" id="time_out_exp_input" class="form-control" min="08:00" max="18:00" step="300" required>
                        <div class="time-display small text-muted mt-1" id="time_out_exp_display"></div>
                        <div id="time_out_error_message" class="error-message"></div>
                    </div>
                </div>
                
                <div id="time_error_message" class="error-message mt-2"></div>
            </div>

            <div class="mb-3">
                <button class="btn btn-primary w-100" id="submitBtn" disabled>Kemaskini</button>
            </div>
            <div class="mb-3 text-center">
                <a href="<?= base_url('pelawat') ?>" class="btn btn-primary px-4 py-2">← Kembali ke Halaman utama</a>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
    /* ========================= TIME HELPER FUNCTIONS ========================= */
    function parseTimeString(timeStr) {
        if (!timeStr) return 0;
        const [hours, minutes] = timeStr.split(':').map(Number);
        return hours * 60 + minutes;
    }

    function formatTimeString(minutes) {
        const hours = Math.floor(minutes / 60);
        const mins = minutes % 60;
        return `${hours.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}`;
    }

    function isWithinOfficeHours(timeStr) {
        if (!timeStr) return false;
        const minutes = parseTimeString(timeStr);
        return minutes >= 480 && minutes <= 1080; // 8:00 AM = 480, 6:00 PM = 1080
    }

    function createTimestamp(timeStr) {
        // Combine today's date with time to create MySQL TIMESTAMP format
        // Format: YYYY-MM-DD HH:MM:SS
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        
        return `${year}-${month}-${day} ${timeStr}:00`;
    }

    function createTimestampForDisplay(timeStr) {
        // Format for display: DD/MM/YYYY HH:MM
        const today = new Date();
        const day = String(today.getDate()).padStart(2, '0');
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const year = today.getFullYear();
        
        return `${day}/${month}/${year} ${timeStr}`;
    }

    function updateTimestampFields() {
        if (timeInInput.value) {
            const timestamp = createTimestamp(timeInInput.value);
            timeInFull.value = timestamp;
            document.getElementById('time_in_display').textContent = createTimestampForDisplay(timeInInput.value);
        }
        if (timeOutExpInput.value) {
            const timestamp = createTimestamp(timeOutExpInput.value);
            timeOutExpFull.value = timestamp;
            timeOutRealFull.value = timestamp; // Set real time same as expected time
            document.getElementById('time_out_exp_display').textContent = createTimestampForDisplay(timeOutExpInput.value);
        }
    }

    function validateTimeInputs() {
        const timeInValue = timeInInput.value;
        const timeOutValue = timeOutExpInput.value;
        const timeInErrorMessage = document.getElementById("time_in_error_message");
        const timeOutErrorMessage = document.getElementById("time_out_error_message");
        const timeErrorMessage = document.getElementById("time_error_message");
        
        // Reset errors
        timeInErrorMessage.textContent = '';
        timeInErrorMessage.style.display = 'none';
        timeOutErrorMessage.textContent = '';
        timeOutErrorMessage.style.display = 'none';
        timeErrorMessage.textContent = '';
        timeErrorMessage.style.display = 'none';
        timeInInput.classList.remove('is-invalid', 'is-valid');
        timeOutExpInput.classList.remove('is-invalid', 'is-valid');
        
        // Check if both times are filled
        if (!timeInValue || !timeOutValue) {
            timeErrorMessage.textContent = "Sila isi kedua-dua masa masuk dan keluar";
            timeErrorMessage.style.display = 'block';
            timeInInput.classList.add('is-invalid');
            timeOutExpInput.classList.add('is-invalid');
            return false;
        }
        
        // Check if times are within office hours
        if (!isWithinOfficeHours(timeInValue)) {
            timeInErrorMessage.textContent = "Masa masuk mesti antara 8:00 pagi hingga 6:00 petang";
            timeInErrorMessage.style.display = 'block';
            timeInInput.classList.add('is-invalid');
            return false;
        }
        
        if (!isWithinOfficeHours(timeOutValue)) {
            timeOutErrorMessage.textContent = "Masa keluar mesti antara 8:00 pagi hingga 6:00 petang";
            timeOutErrorMessage.style.display = 'block';
            timeOutExpInput.classList.add('is-invalid');
            return false;
        }
        
        const timeInMinutes = parseTimeString(timeInValue);
        const timeOutMinutes = parseTimeString(timeOutValue);
        
        // Check if times are the same
        if (timeOutMinutes === timeInMinutes) {
            timeErrorMessage.textContent = "Masa masuk dan keluar tidak boleh sama";
            timeErrorMessage.style.display = 'block';
            timeInInput.classList.add('is-invalid');
            timeOutExpInput.classList.add('is-invalid');
            return false;
        }
        
        // Check if time_out is after time_in
        if (timeOutMinutes <= timeInMinutes) {
            timeErrorMessage.textContent = "Masa keluar mesti selepas masa masuk";
            timeErrorMessage.style.display = 'block';
            timeInInput.classList.add('is-invalid');
            timeOutExpInput.classList.add('is-invalid');
            return false;
        }
        
        // Check if duration is at least 30 minutes
        const duration = timeOutMinutes - timeInMinutes;
        if (duration < 30) {
            timeErrorMessage.textContent = "Masa keluar mesti sekurang-kurangnya 30 minit selepas masa masuk";
            timeErrorMessage.style.display = 'block';
            timeOutExpInput.classList.add('is-invalid');
            return false;
        }
        
        // Check maximum 10 hours difference (optional)
        if (duration > 600) { // 10 hours = 600 minutes
            timeErrorMessage.textContent = "Masa keluar tidak boleh melebihi 10 jam dari masa masuk";
            timeErrorMessage.style.display = 'block';
            timeOutExpInput.classList.add('is-invalid');
            return false;
        }
        
        // All validations passed
        timeInInput.classList.add('is-valid');
        timeOutExpInput.classList.add('is-valid');
        
        // Update timestamp fields for SQL
        updateTimestampFields();
        
        return true;
    }

    function autoAdjustTimeOut() {
        if (!timeInInput.value) return;
        
        const timeInMinutes = parseTimeString(timeInInput.value);
        const currentOutMinutes = timeOutExpInput.value ? parseTimeString(timeOutExpInput.value) : null;
        
        // If time_out is not set or is before/equal to time_in, set it to 30 minutes later
        if (!currentOutMinutes || currentOutMinutes <= timeInMinutes) {
            const newOutMinutes = Math.min(timeInMinutes + 30, 1080); // 30 mins later, max 6:00 PM
            timeOutExpInput.value = formatTimeString(newOutMinutes);
        } else if (currentOutMinutes - timeInMinutes < 30) {
            // If duration is less than 30 minutes, adjust to minimum 30 minutes
            const newOutMinutes = Math.min(timeInMinutes + 30, 1080);
            timeOutExpInput.value = formatTimeString(newOutMinutes);
        }
        
        updateTimestampFields();
    }

    function autoAdjustTimeIn() {
        if (!timeOutExpInput.value) return;
        
        const timeOutMinutes = parseTimeString(timeOutExpInput.value);
        const currentInMinutes = timeInInput.value ? parseTimeString(timeInInput.value) : null;
        
        // If time_in is not set or is after/equal to time_out, set it to 30 minutes before
        if (!currentInMinutes || currentInMinutes >= timeOutMinutes) {
            const newInMinutes = Math.max(timeOutMinutes - 30, 480); // 30 mins before, min 8:00 AM
            timeInInput.value = formatTimeString(newInMinutes);
        } else if (timeOutMinutes - currentInMinutes < 30) {
            // If duration is less than 30 minutes, adjust to minimum 30 minutes
            const newInMinutes = Math.max(timeOutMinutes - 30, 480);
            timeInInput.value = formatTimeString(newInMinutes);
        }
        
        updateTimestampFields();
    }

    /* ========================= GLOBAL VARIABLES ========================= */
    const submitBtn = document.getElementById("submitBtn");
    const officerList = <?= json_encode(array_column($users ?? [], 'Name')) ?>;
    
    /* ========================= ELEMENTS ========================= */
    const nameInput = document.getElementById("name_pelawat");
    const phoneInput = document.getElementById("phone_pelawat");
    const reasonInput = document.getElementById("reason_pelawat");
    const searchOfficer = document.getElementById("search_officer");
    const officerResults = document.getElementById("officer_results");
    const officerHidden = document.getElementById("officer_selected");
    const timeInInput = document.getElementById("time_in_input");
    const timeOutExpInput = document.getElementById("time_out_exp_input");
    const timeInFull = document.getElementById("time_in_full");
    const timeOutExpFull = document.getElementById("time_out_exp_full");
    const timeOutRealFull = document.getElementById("time_out_real_full");

    /* ========================= Officer Validation & Live Search ========================= */
    const officerErrorMessage = document.getElementById("officer_error_message");

    // Set initial value
    officerHidden.value = searchOfficer.value || "";

    function validateOfficer() {
        const name = searchOfficer.value.trim();
        
        // Clear previous error message
        officerErrorMessage.textContent = '';
        officerErrorMessage.style.display = 'none';
        
        if (!name) {
            searchOfficer.classList.remove('is-valid', 'is-invalid');
            officerHidden.value = "";
            return false;
        }
        
        const exists = officerList.includes(name);
        
        if (!exists) {
            searchOfficer.classList.remove('is-valid');
            searchOfficer.classList.add('is-invalid');
            officerErrorMessage.textContent = 'Pegawai tidak wujud. Sila pilih nama pegawai daripada senarai.';
            officerErrorMessage.style.display = 'block';
            officerHidden.value = "";
            return false;
        }
        
        searchOfficer.classList.remove('is-invalid');
        searchOfficer.classList.add('is-valid');
        officerHidden.value = name;
        return true;
    }

    // Officer autocomplete
    searchOfficer.addEventListener("input", function(){
        const keyword = this.value.toLowerCase();
        officerResults.innerHTML = "";
        
        validateOfficer();
        updateSubmitState();
        
        if(!keyword){ 
            officerResults.style.display = "none"; 
            return; 
        }

        const filteredOfficers = officerList.filter(officer => 
            officer.toLowerCase().includes(keyword)
        );

        if(filteredOfficers.length === 0){
            officerResults.style.display = "none";
            return;
        }

        filteredOfficers.forEach(officer => {
            const li = document.createElement("li");
            li.className = "list-group-item";
            li.textContent = officer;
            li.style.cursor = "pointer";
            
            li.addEventListener('click', () => {
                searchOfficer.value = officer;
                officerHidden.value = officer;
                officerResults.style.display = "none";
                validateOfficer();
                updateSubmitState();
            });
            
            officerResults.appendChild(li);
        });
        
        officerResults.style.display = "block";
    });

    searchOfficer.addEventListener("blur", function() {
        setTimeout(() => {
            officerResults.style.display = "none";
        }, 200);
        validateOfficer();
        updateSubmitState();
    });

    document.addEventListener("click", function(e){
        if (!searchOfficer.contains(e.target) && !officerResults.contains(e.target)) {
            officerResults.style.display = "none";
        }
    });

    /* ========================= Name Validation ========================= */
    const nameErrorMessage = document.getElementById("name_error_message");
    
    function validateName() {
        const name = nameInput.value.trim();
        const isValid = /^[A-Za-z\s]{10,40}$/.test(name);
        
        // Clear previous error message
        nameErrorMessage.textContent = '';
        nameErrorMessage.style.display = 'none';
        
        if (!name) {
            nameInput.classList.remove('is-valid', 'is-invalid');
            return false;
        }
        
        if (isValid) {
            nameInput.classList.remove('is-invalid');
            nameInput.classList.add('is-valid');
            return true;
        } else {
            nameInput.classList.remove('is-valid');
            nameInput.classList.add('is-invalid');
            
            // Set specific error message based on validation failure
            if (name.length < 10) {
                nameErrorMessage.textContent = 'Nama mesti sekurang-kurangnya 10 aksara.';
            } else if (name.length > 40) {
                nameErrorMessage.textContent = 'Nama tidak boleh melebihi 40 aksara.';
            } else if (!/^[A-Za-z\s]+$/.test(name)) {
                nameErrorMessage.textContent = 'Nama hanya boleh mengandungi huruf dan ruang.';
            } else {
                nameErrorMessage.textContent = 'Nama tidak sah.';
            }
            
            nameErrorMessage.style.display = 'block';
            return false;
        }
    }

    nameInput.addEventListener("input", function() {
        validateName();
        updateSubmitState();
    });

    nameInput.addEventListener("blur", function() {
        validateName();
        updateSubmitState();
    });

    /* ========================= Reason Validation ========================= */
    const reasonErrorMessage = document.getElementById("reason_error_message");
    
    function validateReason() {
        const r = reasonInput.value.trim();
        const isValid = r.length >= 10 && r.length <= 50;
        
        // Clear previous error message
        reasonErrorMessage.textContent = '';
        reasonErrorMessage.style.display = 'none';
        
        if (!r) {
            reasonInput.classList.remove('is-valid', 'is-invalid');
            return false;
        }
        
        if (isValid) {
            reasonInput.classList.remove('is-invalid');
            reasonInput.classList.add('is-valid');
            return true;
        } else {
            reasonInput.classList.remove('is-valid');
            reasonInput.classList.add('is-invalid');
            
            // Set specific error message
            if (r.length < 10) {
                reasonErrorMessage.textContent = 'Sebab lawatan mesti sekurang-kurangnya 10 aksara.';
            } else if (r.length > 50) {
                reasonErrorMessage.textContent = 'Sebab lawatan tidak boleh melebihi 50 aksara.';
            }
            
            reasonErrorMessage.style.display = 'block';
            return false;
        }
    }

    reasonInput.addEventListener("input", function() {
        validateReason();
        updateSubmitState();
    });

    reasonInput.addEventListener("blur", function() {
        validateReason();
        updateSubmitState();
    });

    /* ========================= Phone Validation ========================= */
    const phoneErrorMessage = document.getElementById("phone_error_message");
    
    function validatePhone() {
        let value = phoneInput.value.replace(/\D/g,'');
        value = value.slice(0, 10);
        phoneInput.value = value;
        
        const isValid = value.startsWith("0") && value.length === 10;
        
        // Clear previous error message
        phoneErrorMessage.textContent = '';
        phoneErrorMessage.style.display = 'none';
        
        if (!value) {
            phoneInput.classList.remove('is-valid', 'is-invalid');
            return false;
        }
        
        if (isValid) {
            phoneInput.classList.remove('is-invalid');
            phoneInput.classList.add('is-valid');
            return true;
        } else {
            phoneInput.classList.remove('is-valid');
            phoneInput.classList.add('is-invalid');
            
            // Set specific error message
            if (!value.startsWith("0")) {
                phoneErrorMessage.textContent = 'Nombor telefon mesti bermula dengan 0.';
            } else if (value.length !== 10) {
                phoneErrorMessage.textContent = 'Nombor telefon mesti tepat 10 digit.';
            } else {
                phoneErrorMessage.textContent = 'Nombor telefon tidak sah.';
            }
            
            phoneErrorMessage.style.display = 'block';
            return false;
        }
    }

    phoneInput.addEventListener("input", function() {
        validatePhone();
        updateSubmitState();
    });

    phoneInput.addEventListener("blur", function() {
        validatePhone();
        updateSubmitState();
    });

    /* ========================= Time Validation ========================= */
    function validateTime() {
        return validateTimeInputs();
    }

    function getDefaultTimes() {
        const now = new Date();
        const currentHour = now.getHours();
        const currentMinutes = now.getMinutes();
        const currentTimeInMinutes = currentHour * 60 + currentMinutes;
        
        let defaultTimeInMinutes;
        
        if (currentTimeInMinutes < 480) {
            // Before 8 AM, use 8:00 AM
            defaultTimeInMinutes = 480;
        } else if (currentTimeInMinutes >= 1080) {
            // After 6 PM, use 5:30 PM (30 mins before closing)
            defaultTimeInMinutes = 1050; // 17:30
        } else {
            // Within office hours, use current time rounded to next 5 minutes
            defaultTimeInMinutes = Math.ceil(currentTimeInMinutes / 5) * 5;
        }
        
        // Set time in (rounded to nearest 5 minutes, within limits)
        defaultTimeInMinutes = Math.min(Math.max(defaultTimeInMinutes, 480), 1080);
        const defaultTimeIn = formatTimeString(defaultTimeInMinutes);
        
        // Set time out to 30 minutes later (max 6:00 PM)
        const defaultTimeOutMinutes = Math.min(defaultTimeInMinutes + 30, 1080);
        const defaultTimeOut = formatTimeString(defaultTimeOutMinutes);
        
        return { defaultTimeIn, defaultTimeOut };
    }

    // Extract time from existing datetime values
    function extractTimeFromDateTime(datetimeStr) {
        if (!datetimeStr || datetimeStr === '0000-00-00 00:00:00' || datetimeStr === '00:00:00') return '';
        try {
            // Handle both formats: 'YYYY-MM-DD HH:MM:SS' and just 'HH:MM:SS'
            let timePart = datetimeStr;
            if (datetimeStr.includes(' ')) {
                const parts = datetimeStr.split(' ');
                timePart = parts.length > 1 ? parts[1] : datetimeStr;
            }
            
            const [hours, minutes] = timePart.split(':');
            if (hours && minutes) {
                return `${hours.padStart(2, '0')}:${minutes.padStart(2, '0')}`;
            }
            return '';
        } catch (e) {
            console.error('Error parsing time:', e);
            return '';
        }
    }

    // Set initial time values from booking data or use defaults
    function initializeTimeValues() {
        const existingTimeIn = extractTimeFromDateTime("<?= esc($booking['time_in']) ?>");
        const existingTimeOut = extractTimeFromDateTime("<?= esc($booking['time_out_exp']) ?>");
        
        console.log('Existing times:', {existingTimeIn, existingTimeOut});
        
        if (existingTimeIn && existingTimeOut && isWithinOfficeHours(existingTimeIn) && isWithinOfficeHours(existingTimeOut)) {
            // Use existing booking times if valid
            timeInInput.value = existingTimeIn;
            timeOutExpInput.value = existingTimeOut;
        } else {
            // Use default times
            const defaultTimes = getDefaultTimes();
            timeInInput.value = defaultTimes.defaultTimeIn;
            timeOutExpInput.value = defaultTimes.defaultTimeOut;
        }
        
        // Update timestamp fields
        updateTimestampFields();
    }

    // Time input event listeners
    timeInInput.addEventListener("change", function() {
        autoAdjustTimeOut();
        validateTime();
        updateSubmitState();
    });

    timeInInput.addEventListener("input", function() {
        updateTimestampFields();
        autoAdjustTimeOut();
        validateTime();
        updateSubmitState();
    });

    timeOutExpInput.addEventListener("change", function() {
        autoAdjustTimeIn();
        validateTime();
        updateSubmitState();
    });

    timeOutExpInput.addEventListener("input", function() {
        updateTimestampFields();
        autoAdjustTimeIn();
        validateTime();
        updateSubmitState();
    });

    /* ========================= Submit Logic ========================= */
    function updateSubmitState() {
        const officerValid = validateOfficer();
        const nameValid = validateName();
        const reasonValid = validateReason();
        const phoneValid = validatePhone();
        const timeValid = validateTime();
        
        const allValid = officerValid && nameValid && reasonValid && phoneValid && timeValid;
        submitBtn.disabled = !allValid;
        
        // Visual feedback for button state
        if (allValid) {
            submitBtn.classList.remove('btn-secondary');
            submitBtn.classList.add('btn-primary');
        } else {
            submitBtn.classList.remove('btn-primary');
            submitBtn.classList.add('btn-secondary');
        }
    }

    // Initialize form on load
    document.addEventListener('DOMContentLoaded', function() {
        initializeTimeValues();
        // Trigger validation for all fields
        validateOfficer();
        validateName();
        validateReason();
        validatePhone();
        validateTime();
        updateSubmitState();
        
        // Debug log
        console.log('Form initialized with timestamps:', {
            time_in: timeInFull.value,
            time_out_exp: timeOutExpFull.value,
            time_out_real: timeOutRealFull.value
        });
    });

    /* ========================= Prevent submit if invalid ========================= */
    document.getElementById("editBookingForm").addEventListener("submit", function(e){
        // Final validation check
        const officerValid = validateOfficer();
        const nameValid = validateName();
        const reasonValid = validateReason();
        const phoneValid = validatePhone();
        const timeValid = validateTime();
        
        if (!(officerValid && nameValid && reasonValid && phoneValid && timeValid)) {
            e.preventDefault();
            
            // Show an alert with summary of errors
            let errorMessages = [];
            
            if (!nameValid) errorMessages.push("• Nama tidak sah");
            if (!phoneValid) errorMessages.push("• Nombor telefon tidak sah");
            if (!officerValid) errorMessages.push("• Pegawai tidak sah");
            if (!reasonValid) errorMessages.push("• Sebab lawatan tidak sah");
            if (!timeValid) errorMessages.push("• Masa lawatan tidak sah");
            
            alert("Sila betulkan kesilapan berikut sebelum menghantar:\n\n" + errorMessages.join('\n'));
            
            // Scroll to first error
            const firstError = document.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
            
            return false;
        }
        
        // Ensure all timestamp fields are up to date
        updateTimestampFields();
        
        // Debug logging (remove in production)
        console.log("=== Data to be submitted ===");
        console.log("name:", nameInput.value);
        console.log("phone_no:", phoneInput.value);
        console.log("reason:", reasonInput.value);
        console.log("officer:", officerHidden.value);
        console.log("time_in (TIMESTAMP):", timeInFull.value);
        console.log("time_out_exp (TIMESTAMP):", timeOutExpFull.value);
        console.log("time_out_real (TIMESTAMP):", timeOutRealFull.value);
        
        return true;
    });
</script>
</body>
</html>