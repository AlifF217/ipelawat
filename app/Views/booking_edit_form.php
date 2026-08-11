<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isPentadbir ? 'Kemaskini Pendaftaran Pentadbir' : 'Kemaskini Pendaftaran Pelawat' ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="<?= base_url('css/menustyle.css') ?>">
    
    <style>
        :root {
            --main-color: #6fdce0;
            --main-dark: #3bbcc3;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f8f9fa;
        }
        
        .list-group {
            position: absolute;
            z-index: 1000;
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
            display: none;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
        }
        
        .list-group-item {
            cursor: pointer;
            padding: 8px 12px;
        }
        
        .list-group-item:hover {
            background-color: #f8f9fa;
        }
        
        .qr-box {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        
        .qr-box img {
            max-width: 200px;
            height: auto;
        }
        
        .autofilled {
            background-color: #e8f4fd;
        }
        
        .countdown {
            font-weight: bold;
            color: #dc3545;
        }
        
        .time-info {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 5px;
        }
        
        /* Style for datetime-local input */
        input[type="datetime-local"] {
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-family: 'Montserrat', sans-serif;
        }
        
        input[type="datetime-local"]:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
            outline: 0;
        }
        
        /* Style for time input */
        input[type="time"] {
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-family: 'Montserrat', sans-serif;
        }
        
        input[type="time"]:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
            outline: 0;
        }
        
        .date-display {
            font-weight: bold;
            color: #495057;
            margin-bottom: 5px;
        }
        
        .current-date {
            background-color: #e7f3ff;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .hidden-date-fields {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-header <?= $isPentadbir ? 'bg-primary' : 'bg-success' ?> text-white">
                <h4 class="mb-0">
                    <i class="fas <?= $isPentadbir ? 'fa-user-tie' : 'fa-user' ?>"></i>
                    <?= $isPentadbir ? 'Kemaskini Pendaftaran Pentadbir' : 'Kemaskini Pendaftaran Pelawat' ?>
                </h4>
            </div>

            <div class="card-body">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (!$allowEdit): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <b>Pengemaskinian hanya dibenarkan antara jam 8:00 pagi hingga 6:00 petang.</b>
                    </div>
                <?php endif; ?>

                <div class="current-date">
                    <i class="fas fa-calendar-day"></i>
                    Tarikh hari ini: <strong><?= date('d/m/Y') ?></strong>
                </div>

                <form id="editForm" action="<?= base_url('updateRegManual/' . $booking['booking_id']) ?>" method="post">
                    <!-- Hidden field to store pelawat type for JavaScript -->
                    <input type="hidden" id="pelawat_type" value="<?= $isPentadbir ? 'pentadbir' : 'pelawat' ?>">
                    <!-- Hidden field for today's date -->
                    <input type="hidden" id="today_date" value="<?= date('Y-m-d') ?>">

                    <div class="row">
                        <div class="col-md-6">
                            <!-- Nama User Live Search -->
                            <div class="mb-3 position-relative">
                                <label class="form-label">
                                    <i class="fas fa-user"></i> 
                                    <?= $isPentadbir ? 'Nama Pengguna' : 'Nama Pelawat' ?>
                                </label>
                                <input type="text" id="search_user" class="form-control" 
                                       placeholder="<?= $isPentadbir ? 'Taip nama pengguna...' : 'Taip nama pelawat...' ?>" 
                                       autocomplete="off" <?= !$allowEdit ? 'readonly' : '' ?> required
                                       value="<?= esc($selectedUser['Name'] ?? '') ?>">
                                <ul id="user_results" class="list-group"></ul>
                                <!-- Store NAME not ID -->
                                <input type="hidden" name="name" id="user_selected" 
                                       value="<?= esc($selectedUser['Name'] ?? '') ?>" required>
                                <div id="user_error" class="text-danger mt-1" style="display:none;">
                                    Nama tidak wujud. Sila pilih daripada senarai.
                                </div>
                            </div>

                            <!-- Phone -->
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-phone"></i> No. Telefon</label>
                                <!-- FIXED: For pentadbir, ALWAYS get phone from booking table -->
                                <?php if($isPentadbir): ?>
                                    <!-- For pentadbir: Always use phone_no from booking table -->
                                    <input type="text" name="phone_no" id="phone_no" class="form-control" maxlength="10" 
                                           value="<?= esc($booking['phone_no']) ?>" 
                                           <?= !$allowEdit ? 'readonly' : '' ?> required>
                                <?php else: ?>
                                    <!-- For pelawat: Use the existing logic (from selectedUser or booking) -->
                                    <input type="text" name="phone_no" id="phone_no" class="form-control" maxlength="10" 
                                           value="<?= esc($selectedUser['Phone'] ?? $booking['phone_no']) ?>" 
                                           <?= !$allowEdit ? 'readonly' : '' ?> required>
                                <?php endif; ?>
                                <div id="phone_error" class="text-danger mt-1"></div>
                            </div>

                            <!-- Pegawai Ditemui -->
                            <div class="mb-3 position-relative">
                                <label class="form-label"><i class="fas fa-user-tie"></i> Pegawai Ditemui</label>
                                <input type="text" id="search_officer" class="form-control" 
                                       placeholder="Taip nama pegawai..." autocomplete="off" 
                                       <?= !$allowEdit ? 'readonly' : '' ?> required
                                       value="<?= esc($selectedOfficer['Name'] ?? $booking['officer']) ?>">
                                <ul id="officer_results" class="list-group"></ul>
                                <!-- Store NAME not ID -->
                                <input type="hidden" name="officer" id="officer_selected" 
                                       value="<?= esc($selectedOfficer['Name'] ?? $booking['officer']) ?>" required>
                                <div id="officer_error" class="text-danger mt-1" style="display:none;">
                                    Pegawai tidak wujud atau sama dengan nama <?= $isPentadbir ? 'pengguna' : 'pelawat' ?>.
                                </div>
                            </div>

                            <!-- Reason -->
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-file-alt"></i> Sebab Lawatan</label>
                                <textarea name="reason" id="reason" class="form-control" rows="3" 
                                          <?= !$allowEdit ? 'readonly' : '' ?> required><?= esc($booking['reason']) ?></textarea>
                                <div id="reason_error" class="text-danger mt-1" style="display:none;"></div>
                                <small class="text-muted">Minima 10 aksara, maksima 50 aksara</small>
                            </div>
                        </div>
                        
                        <!-- QR COLUMN -->
                        <div class="col-md-6">
                            <div class="qr-box mb-3">
                                <h5><i class="fas fa-qrcode"></i> Kod QR Akses</h5>
                                <div><img src="<?= $qr ?>" alt="QR Code" /></div>

                                <a href="<?= $qr ?>" class="btn btn-success btn-sm mt-3"
                                   download="qr_<?= $isPentadbir ? 'pentadbir' : 'pelawat' ?>_<?= $booking['booking_id'] ?>.png">
                                    <i class="fas fa-download"></i> Muat Turun QR (PNG)
                                </a>

                                <br>

                                <?php
                                $waMessage = "Sila buka link untuk kemaskini lawatan anda: $editURL , Sila kemaskini sebelum pukul 6 PM. Terima kasih.";
                                $waLink = "https://wa.me/?text=" . urlencode($waMessage);
                                ?>

                                <a href="<?= $waLink ?>" target="_blank" class="btn btn-success mt-3">
                                    <i class="fab fa-whatsapp"></i> Hantar ke WhatsApp
                                </a>

                                <div class="mt-3">
                                    <small>Imbas untuk akses semula ke halaman ini.</small>
                                </div>
                            </div>

                            <!-- Countdown Timer -->
                            <?php if ($allowEdit): ?>
                            <div class="alert alert-info text-center">
                                <i class="fas fa-clock"></i>
                                Anda boleh ubah sehingga edit <strong>6:00 PM</strong> hari ini.
                                <br>Masa yang tinggal : <span id="countdown" class="countdown"></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <hr>

                    <!-- TIME FIELDS -->
                    <div class="row mt-3">
                        <?php
                        // Parse existing timestamps
                        $timeInVal = $booking['time_in'] ?? '';
                        $timeOutExpVal = $booking['time_out_exp'] ?? '';
                        $timeOutRealVal = $booking['time_out_real'] ?? '';
                        
                        // For existing records, extract dates and times
                        $timeInDate = '';
                        $timeOutExpDate = '';
                        $timeOutRealDate = '';
                        
                        $timeInTime = '';
                        $timeOutExpTime = '';
                        $timeOutRealTime = '';
                        
                        // Today's date in Y-m-d format
                        $todayDate = date('Y-m-d');
                        
                        // Parse time_in
                        if ($timeInVal && $timeInVal !== '0000-00-00 00:00:00' && $timeInVal !== '00:00:00') {
                            $timeInDate = date('Y-m-d', strtotime($timeInVal));
                            $timeInTime = date('H:i', strtotime($timeInVal));
                        } else {
                            // For new registration, use today's date
                            $timeInDate = $todayDate;
                            $timeInTime = '08:00'; // Default time
                        }
                        
                        // Parse time_out_exp
                        if ($timeOutExpVal && $timeOutExpVal !== '0000-00-00 00:00:00' && $timeOutExpVal !== '00:00:00') {
                            $timeOutExpDate = date('Y-m-d', strtotime($timeOutExpVal));
                            $timeOutExpTime = date('H:i', strtotime($timeOutExpVal));
                        } else {
                            // For new registration, use today's date
                            $timeOutExpDate = $todayDate;
                            $timeOutExpTime = '17:00'; // Default time
                        }
                        
                        // Parse time_out_real
                        if ($timeOutRealVal && $timeOutRealVal !== '0000-00-00 00:00:00' && $timeOutRealVal !== '00:00:00') {
                            $timeOutRealDate = date('Y-m-d', strtotime($timeOutRealVal));
                            $timeOutRealTime = date('H:i', strtotime($timeOutRealVal));
                        } else {
                            // For new registration, use today's date
                            $timeOutRealDate = $todayDate;
                            $timeOutRealTime = $timeOutExpTime; // Match expected time
                        }
                        ?>

                        <div class="col-md-4 mb-3">
                            <div class="date-display">Tarikh: <?= !empty($timeInDate) ? date('d/m/Y', strtotime($timeInDate)) : date('d/m/Y') ?></div>
                            <label class="form-label"><i class="fas fa-sign-in-alt"></i> Masa Masuk</label>
                            <input type="datetime-local" id="time_in" name="time_in" class="form-control"
                                   value="<?= esc($timeInTime) ?>"
                                   <?= !$allowEdit ? 'readonly' : '' ?> required
                                   min="08:00" max="18:00">
                            <!-- Hidden field for date - MUST BE SENT -->
                            <input type="hidden" id="time_in_date" name="time_in_date" value="<?= esc($timeInDate) ?>">
                            <div id="time_error" class="text-danger mt-1"></div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="date-display">Tarikh: <?= !empty($timeOutExpDate) ? date('d/m/Y', strtotime($timeOutExpDate)) : date('d/m/Y') ?></div>
                            <label class="form-label"><i class="fas fa-sign-out-alt"></i> Jangkaan Masa Keluar</label>
                            <input type="datetime-local" id="time_out_exp" name="time_out_exp" class="form-control"
                                   value="<?= esc($timeOutExpTime) ?>"
                                   <?= !$allowEdit ? 'readonly' : '' ?> required
                                   min="08:00" max="18:00">
                            <!-- Hidden field for date - MUST BE SENT -->
                            <input type="hidden" id="time_out_exp_date" name="time_out_exp_date" value="<?= esc($timeOutExpDate) ?>">
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="date-display">Tarikh: <?= !empty($timeOutRealDate) ? date('d/m/Y', strtotime($timeOutRealDate)) : date('d/m/Y') ?></div>
                            <label class="form-label"><i class="fas fa-door-closed"></i> Masa Keluar Sebenar</label>
                            <input type="datetime-local" id="time_out_real" name="time_out_real" class="form-control"
                                   value="<?= esc($timeOutRealTime) ?>" readonly
                                   min="08:00" max="18:00">
                            <!-- Hidden field for date - MUST BE SENT -->
                            <input type="hidden" id="time_out_real_date" name="time_out_real_date" value="<?= esc($timeOutRealDate) ?>">
                        </div>
                    </div>

                    <?php if ($allowEdit): ?>
                        <button id="submitBtn" type="submit" class="btn <?= $isPentadbir ? 'btn-primary' : 'btn-success' ?>">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    <?php endif; ?>
                </form>

                <hr>
                <a href="javascript:history.back()" class="btn btn-secondary w-100 mt-2">
                    <i class="fas fa-arrow-left"></i> Kembali ke Kalendar
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
document.addEventListener("DOMContentLoaded", function(){
    const submitBtn = document.getElementById('submitBtn');
    const phoneInput = document.getElementById('phone_no');
    const timeIn = document.getElementById('time_in');
    const timeOutExp = document.getElementById('time_out_exp');
    const timeOutReal = document.getElementById('time_out_real');
    const timeInDate = document.getElementById('time_in_date');
    const timeOutExpDate = document.getElementById('time_out_exp_date');
    const timeOutRealDate = document.getElementById('time_out_real_date');
    const timeError = document.getElementById('time_error');
    const phoneError = document.getElementById('phone_error');
    const userHidden = document.getElementById('user_selected');
    const officerHidden = document.getElementById('officer_selected');
    const searchUser = document.getElementById('search_user');
    const searchOfficer = document.getElementById('search_officer');
    const userError = document.getElementById('user_error');
    const officerError = document.getElementById('officer_error');
    const reasonInput = document.getElementById('reason');
    const reasonError = document.getElementById('reason_error');
    const pelawatType = document.getElementById('pelawat_type').value;
    const todayDateInput = document.getElementById('today_date');
    const todayDate = todayDateInput.value;
    
    // Store original phone number for pentadbir (from booking table)
    const originalPhoneNumber = phoneInput.value;

    // ==== Initialize date fields if empty ====
    function initializeDateFields() {
        // Ensure date fields have values
        if (!timeInDate.value || timeInDate.value === '') {
            timeInDate.value = todayDate;
        }
        
        if (!timeOutExpDate.value || timeOutExpDate.value === '') {
            timeOutExpDate.value = todayDate;
        }
        
        if (!timeOutRealDate.value || timeOutRealDate.value === '') {
            timeOutRealDate.value = todayDate;
        }
        
        // Set default times if empty
        if (!timeIn.value || timeIn.value === '') {
            // Create full datetime-local value with today's date
            timeIn.value = todayDate + 'T08:00';
        } else if (!timeIn.value.includes('T')) {
            // If it's just a time, add today's date
            timeIn.value = todayDate + 'T' + timeIn.value;
        }
        
        if (!timeOutExp.value || timeOutExp.value === '') {
            // Create full datetime-local value with today's date
            timeOutExp.value = todayDate + 'T17:00';
        } else if (!timeOutExp.value.includes('T')) {
            // If it's just a time, add today's date
            timeOutExp.value = todayDate + 'T' + timeOutExp.value;
        }
        
        // Auto-set time_out_real to match time_out_exp
        if ((!timeOutReal.value || timeOutReal.value === '') && timeOutExp.value) {
            timeOutReal.value = timeOutExp.value;
        } else if (timeOutReal.value && !timeOutReal.value.includes('T')) {
            // If it's just a time, add today's date
            timeOutReal.value = todayDate + 'T' + timeOutReal.value;
        }
    }
    
    // Initialize on page load
    initializeDateFields();
    
    // Log current values for debugging
    console.log('Initial Date Values:');
    console.log('time_in_date:', timeInDate.value);
    console.log('time_out_exp_date:', timeOutExpDate.value);
    console.log('time_out_real_date:', timeOutRealDate.value);
    console.log('time_in:', timeIn.value);
    console.log('time_out_exp:', timeOutExp.value);
    console.log('time_out_real:', timeOutReal.value);
    console.log('Pelawat Type:', pelawatType);
    console.log('Phone input value:', phoneInput.value);

    // ==== Reason Validation ====
    reasonInput.addEventListener('input', function() {
        const val = reasonInput.value.trim();
        if (val.length === 0) {
            reasonError.style.display = 'none';
        } else if (val.length < 10) {
            reasonError.textContent = "Sebab lawatan mesti sekurang-kurangnya 10 aksara.";
            reasonError.style.display = 'block';
        } else if (val.length > 50) {
            reasonError.textContent = "Sebab lawatan tidak boleh melebihi 50 aksara.";
            reasonError.style.display = 'block';
        } else {
            reasonError.style.display = 'none';
        }
    });

    // ==== Prepare Data Sources ====
    const officerDataSource = [
        <?php foreach($users as $u): ?>
        {
            id: "<?= esc($u['Name']) ?>", 
            name: "<?= esc($u['Name']) ?>", 
            phone: "<?= esc($u['Phone'] ?? '') ?>"
        },
        <?php endforeach; ?>
    ];

    // User data source depends on pelawat type
    let userDataSource;
    
    <?php if($isPentadbir): ?>
        userDataSource = [
            <?php foreach($users as $u): ?>
            {
                id: "<?= esc($u['Name']) ?>", 
                name: "<?= esc($u['Name']) ?>", 
                phone: "<?= esc($u['Phone'] ?? '') ?>"
            },
            <?php endforeach; ?>
        ];
    <?php else: ?>
        userDataSource = [
            <?php if(isset($guests) && is_array($guests)): ?>
                <?php foreach($guests as $g): ?>
                {
                    id: "<?= esc($g['name'] ?? '') ?>", 
                    name: "<?= esc($g['name'] ?? '') ?>", 
                    phone: "<?= esc($g['tel'] ?? '') ?>"
                },
                <?php endforeach; ?>
            <?php endif; ?>
        ];
    <?php endif; ?>

    // ==== Live Search Functions ====
    function attachLiveSearch(input, results, list, hiddenInput, otherInput = null, errorDiv = null) {
        input.addEventListener("input", function() {
            const keyword = this.value.trim().toLowerCase();
            results.innerHTML = "";
            hiddenInput.value = "";

            if (!keyword) {
                results.style.display = "none";
                if (errorDiv) errorDiv.style.display = "block";
                return;
            }

            let matches = list.filter(u => u.name && u.name.toLowerCase().includes(keyword));

            if (otherInput) {
                matches = matches.filter(u => u.name !== otherInput.value.trim());
            }

            if (matches.length === 0) {
                results.style.display = "none";
                if (errorDiv) errorDiv.style.display = "block";
                return;
            }

            matches.forEach(u => {
                const li = document.createElement("li");
                li.className = "list-group-item list-group-item-action";
                li.textContent = u.name;

                li.onclick = () => {
                    input.value = u.name;
                    hiddenInput.value = u.name; // Store NAME not ID

                    // FIXED: For pentadbir, don't auto-fill phone from users table
                    // Keep the phone from booking table
                    if (input === searchUser && u.phone && pelawatType !== 'pentadbir') {
                        phoneInput.value = u.phone;
                        validatePhone();
                    }

                    if (errorDiv) errorDiv.style.display = "none";
                    results.style.display = "none";
                    checkExactValidation();
                };

                results.appendChild(li);
            });

            results.style.display = "block";
            if (errorDiv) errorDiv.style.display = "none";
        });

        input.addEventListener("blur", function() {
            setTimeout(() => {
                results.style.display = "none";
                checkExactValidation();
            }, 150);
        });
    }

    // Attach live searches
    attachLiveSearch(searchUser, document.getElementById('user_results'), userDataSource, userHidden, searchOfficer, userError);
    attachLiveSearch(searchOfficer, document.getElementById('officer_results'), officerDataSource, officerHidden, searchUser, officerError);

    // ==== Exact Match Validation ====
    function isExactNameMatch(inputValue, list) {
        const cleaned = inputValue.trim().toLowerCase();
        return list.some(u => u.name && u.name.trim().toLowerCase() === cleaned);
    }

    function checkExactValidation() {
        // USER EXACT MATCH CHECK
        const userValid = isExactNameMatch(searchUser.value, userDataSource);
        if (!userValid) {
            userError.style.display = "block";
            userHidden.value = "";
        } else {
            userError.style.display = "none";
        }

        // OFFICER EXACT MATCH CHECK
        const officerValid = isExactNameMatch(searchOfficer.value, officerDataSource);
        if (!officerValid || searchOfficer.value.trim() === searchUser.value.trim()) {
            officerError.style.display = "block";
            officerHidden.value = "";
        } else {
            officerError.style.display = "none";
        }
    }

    // ==== Phone Validation ====
    function validatePhone() {
        // For pentadbir: phone number should come from booking table
        // User can modify it, but we validate the format
        phoneInput.value = phoneInput.value.replace(/\D/g,'');
        if (phoneInput.value.length > 10) {
            phoneInput.value = phoneInput.value.slice(0,10);
        }

        if (phoneInput.value.length === 0) {
            phoneError.style.display = "none";
            return true;
        } else if (!phoneInput.value.startsWith('0')) {
            phoneError.textContent = "Nombor telefon mesti bermula dengan 0.";
            phoneError.style.display = "block";
            return false;
        } else if (phoneInput.value.length < 10) {
            phoneError.textContent = "Nombor telefon mesti tepat 10 digit.";
            phoneError.style.display = "block";
            return false;
        } else {
            phoneError.style.display = "none";
            return true;
        }
    }

    phoneInput.addEventListener('input', function() {
        validatePhone();
    });

    // ==== Time Validation for datetime-local inputs ====
    function validateTimes() {
        timeError.textContent = '';
        timeError.style.display = 'none';
        
        const inVal = timeIn.value; // Format: YYYY-MM-DDTHH:mm
        const outVal = timeOutExp.value; // Format: YYYY-MM-DDTHH:mm
        
        if (!inVal || !outVal) {
            timeError.textContent = "Sila isi kedua-dua masa masuk dan keluar dijangka.";
            timeError.style.display = 'block';
            return false;
        }
        
        // Parse the datetime-local values
        const inDateTime = new Date(inVal);
        const outDateTime = new Date(outVal);
        
        // Check if dates are valid
        if (isNaN(inDateTime.getTime()) || isNaN(outDateTime.getTime())) {
            timeError.textContent = "Format masa tidak sah.";
            timeError.style.display = 'block';
            return false;
        }
        
        // Extract time components (HH:mm)
        const inTimeStr = inVal.split('T')[1] || '';
        const outTimeStr = outVal.split('T')[1] || '';
        
        const inHour = inDateTime.getHours();
        const inMinute = inDateTime.getMinutes();
        const inTimeInMinutes = inHour * 60 + inMinute;
        
        const outHour = outDateTime.getHours();
        const outMinute = outDateTime.getMinutes();
        const outTimeInMinutes = outHour * 60 + outMinute;
        
        // Check if times are within allowed range (8 AM - 6 PM)
        const minAllowed = 8 * 60; // 8:00 AM in minutes
        const maxAllowed = 18 * 60; // 6:00 PM in minutes
        
        if (inTimeInMinutes < minAllowed || inTimeInMinutes > maxAllowed) {
            timeError.textContent = "Masa masuk mesti antara 8:00 pagi dan 6:00 petang.";
            timeError.style.display = 'block';
            return false;
        }
        
        if (outTimeInMinutes < minAllowed || outTimeInMinutes > maxAllowed) {
            timeError.textContent = "Masa keluar dijangka mesti antara 8:00 pagi dan 6:00 petang.";
            timeError.style.display = 'block';
            return false;
        }
        
        // Check if out time is after in time
        if (outDateTime <= inDateTime) {
            timeError.textContent = "Masa keluar dijangka mesti selepas masa masuk.";
            timeError.style.display = 'block';
            return false;
        }
        
        // Calculate time difference in minutes
        const timeDiffMinutes = (outDateTime - inDateTime) / (1000 * 60);
        
        // Check minimum 30 minutes difference
        if (timeDiffMinutes <= 30) {
            timeError.textContent = "Masa keluar dijangka mesti lebih daripada 30 minit selepas masa masuk.";
            timeError.style.display = 'block';
            return false;
        }
        
        // Check maximum 10 hours difference (optional)
        if (timeDiffMinutes > 600) { // 10 hours = 600 minutes
            timeError.textContent = "Masa keluar dijangka tidak boleh melebihi 10 jam dari masa masuk.";
            timeError.style.display = 'block';
            return false;
        }
        
        return true;
    }

    // Auto-set time_out_real when time_out_exp changes
    timeOutExp.addEventListener('input', function() {
        if (timeOutExp.value) {
            timeOutReal.value = timeOutExp.value;
        }
        validateTimes();
    });

    timeIn.addEventListener('input', function() {
        validateTimes();
    });

    timeOutExp.addEventListener('change', function() {
        validateTimes();
    });

    // Ensure date fields are populated when times change
    timeIn.addEventListener('change', function() {
        if (!timeInDate.value || timeInDate.value === '') {
            timeInDate.value = todayDate;
        }
        
        // Extract date from datetime-local value and update hidden field
        if (timeIn.value && timeIn.value.includes('T')) {
            const datePart = timeIn.value.split('T')[0];
            timeInDate.value = datePart;
        }
        
        console.log('time_in changed, date value:', timeInDate.value);
        validateTimes();
    });

    timeOutExp.addEventListener('change', function() {
        if (!timeOutExpDate.value || timeOutExpDate.value === '') {
            timeOutExpDate.value = todayDate;
        }
        
        // Extract date from datetime-local value and update hidden field
        if (timeOutExp.value && timeOutExp.value.includes('T')) {
            const datePart = timeOutExp.value.split('T')[0];
            timeOutExpDate.value = datePart;
            
            // Also update time_out_real
            if (timeOutReal) {
                timeOutReal.value = timeOutExp.value;
                const realDatePart = timeOutReal.value.split('T')[0];
                timeOutRealDate.value = realDatePart;
            }
        }
        
        console.log('time_out_exp changed, date value:', timeOutExpDate.value);
        validateTimes();
    });

    // ==== Countdown Timer ====
    function updateCountdown() {
        let now = new Date();
        let deadline = new Date();
        deadline.setHours(18, 0, 0, 0);
        let diff = deadline - now;

        let countdown = document.getElementById("countdown");
        if (!countdown) return;

        if (diff <= 0) {
            countdown.innerHTML = "EXPIRED";
            location.reload();
            return;
        }

        let h = Math.floor(diff / 1000 / 60 / 60);
        let m = Math.floor(diff / 1000 / 60) % 60;
        let s = Math.floor(diff / 1000) % 60;

        countdown.innerHTML = `${h}h ${m}m ${s}s`;
    }

    <?php if($allowEdit): ?>
    setInterval(updateCountdown, 1000);
    updateCountdown();
    <?php endif; ?>

    // ==== Form Validation ====
    function validateForm() {
        checkExactValidation();
        
        const phoneValid = validatePhone();
        const timeValid = validateTimes();
        
        const reasonVal = reasonInput.value.trim();
        const reasonValid = reasonError.style.display !== 'block' && 
                            reasonVal.length >= 10 && 
                            reasonVal.length <= 50;
        
        // Check for exact name matches
        const userExactMatch = isExactNameMatch(searchUser.value, userDataSource);
        const officerExactMatch = isExactNameMatch(searchOfficer.value, officerDataSource);
        const namesDifferent = searchUser.value.trim().toLowerCase() !== searchOfficer.value.trim().toLowerCase();
        
        const allValid = 
            phoneValid &&
            timeValid &&
            userExactMatch &&
            officerExactMatch &&
            namesDifferent &&
            userHidden.value.trim() !== "" &&
            officerHidden.value.trim() !== "" &&
            searchUser.value.trim() !== "" &&
            searchOfficer.value.trim() !== "" &&
            reasonValid
        ;

        return allValid;
    }

    // Form submission handler - ENHANCED DEBUGGING
    document.getElementById('editForm').addEventListener('submit', function(e) {
        // Log all values before submission
        console.log('Form Submission Values:');
        console.log('Pelawat Type:', pelawatType);
        console.log('Phone number:', phoneInput.value);
        console.log('time_in_date:', timeInDate.value);
        console.log('time_out_exp_date:', timeOutExpDate.value);
        console.log('time_out_real_date:', timeOutRealDate.value);
        console.log('time_in:', timeIn.value);
        console.log('time_out_exp:', timeOutExp.value);
        console.log('time_out_real:', timeOutReal.value);
        console.log('today_date:', todayDate);
        
        // Ensure all date fields are populated
        if (!timeInDate.value || timeInDate.value === '') {
            console.log('Warning: time_in_date was empty, setting to:', todayDate);
            timeInDate.value = todayDate;
        }
        if (!timeOutExpDate.value || timeOutExpDate.value === '') {
            console.log('Warning: time_out_exp_date was empty, setting to:', todayDate);
            timeOutExpDate.value = todayDate;
        }
        if (!timeOutRealDate.value || timeOutRealDate.value === '') {
            console.log('Warning: time_out_real_date was empty, setting to:', todayDate);
            timeOutRealDate.value = todayDate;
        }
        
        // Validate form
        if (!validateForm()) {
            e.preventDefault();
            alert('Sila pastikan semua medan diisi dengan betul sebelum menghantar.');
            return false;
        }
        
        console.log('Form validation passed, submitting...');
        return true;
    });

    // Hide dropdown when clicking outside
    document.addEventListener("click", function(e) {
        if (!searchUser.contains(e.target)) {
            document.getElementById('user_results').style.display = "none";
        }
        if (!searchOfficer.contains(e.target)) {
            document.getElementById('officer_results').style.display = "none";
        }
    });

    // Initial validation
    checkExactValidation();
    validatePhone();
    validateTimes();
    
    // Log final state
    console.log('Page loaded successfully');
});
</script>
</body>
</html>