<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= strtolower($booking['pelawat']) === 'pentadbir' ? 'Kemaskini Pendaftaran Pentadbir' : 'Kemaskini Pendaftaran Pelawat' ?></title>
    
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
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-header <?= strtolower($booking['pelawat']) === 'pentadbir' ? 'bg-primary' : 'bg-success' ?> text-white">
                <h4 class="mb-0">
                    <i class="fas <?= strtolower($booking['pelawat']) === 'pentadbir' ? 'fa-user-tie' : 'fa-user' ?>"></i>
                    <?= strtolower($booking['pelawat']) === 'pentadbir' ? 'Kemaskini Pendaftaran Pentadbir' : 'Kemaskini Pendaftaran Pelawat' ?>
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

                <form id="editForm" action="<?= base_url('updateRegManual/' . $booking['booking_id']) ?>" method="post">
                    <!-- Hidden field to store pelawat type for JavaScript -->
                    <input type="hidden" id="pelawat_type" value="<?= strtolower($booking['pelawat']) ?>">
                    <!-- Hidden field for today's date -->
                    <input type="hidden" id="today_date" value="<?= date('Y-m-d') ?>">

                    <div class="row">
                        <div class="col-md-6">
                            <!-- Nama User Live Search -->
                            <div class="mb-3 position-relative">
                                <label class="form-label">
                                    <i class="fas fa-user"></i> 
                                    <?= strtolower($booking['pelawat']) === 'pentadbir' ? 'Nama Pengguna' : 'Nama Pelawat' ?>
                                </label>
                                <!-- FIXED: ALWAYS use booking['name'] for BOTH pentadbir and pelawat -->
                                <input type="text" id="search_user" class="form-control" 
                                       placeholder="<?= strtolower($booking['pelawat']) === 'pentadbir' ? 'Taip nama pengguna...' : 'Taip nama pelawat...' ?>" 
                                       autocomplete="off" <?= !$allowEdit ? 'readonly' : '' ?> required
                                       value="<?= esc($booking['name'] ?? '') ?>">
                                <ul id="user_results" class="list-group"></ul>
                                <!-- Store NAME not ID -->
                                <!-- FIXED: ALWAYS use booking['name'] for BOTH pentadbir and pelawat -->
                                <input type="hidden" name="name" id="user_selected" 
                                       value="<?= esc($booking['name'] ?? '') ?>" required>
                                <div id="user_error" class="text-danger mt-1" style="display:none;">
                                    Nama tidak wujud. Sila pilih daripada senarai.
                                </div>
                            </div>

                            <!-- Phone -->
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-phone"></i> No. Telefon</label>
                                <!-- FIXED: ALWAYS use booking['phone_no'] for BOTH pentadbir and pelawat -->
                                <input type="text" name="phone_no" id="phone_no" class="form-control" maxlength="10" 
                                       value="<?= esc($booking['phone_no'] ?? '') ?>" 
                                       <?= !$allowEdit ? 'readonly' : '' ?> required>
                                <div id="phone_error" class="text-danger mt-1"></div>
                            </div>

                            <!-- Pegawai Ditemui -->
                            <div class="mb-3 position-relative">
                                <label class="form-label"><i class="fas fa-user-tie"></i> Pegawai Ditemui</label>
                                <!-- FIXED: Use booking['officer'] directly -->
                                <input type="text" id="search_officer" class="form-control" 
                                       placeholder="Taip nama pegawai..." autocomplete="off" 
                                       <?= !$allowEdit ? 'readonly' : '' ?> required
                                       value="<?= esc($booking['officer'] ?? '') ?>">
                                <ul id="officer_results" class="list-group"></ul>
                                <!-- Store NAME not ID -->
                                <!-- FIXED: Use booking['officer'] directly -->
                                <input type="hidden" name="officer" id="officer_selected" 
                                       value="<?= esc($booking['officer'] ?? '') ?>" required>
                                <div id="officer_error" class="text-danger mt-1" style="display:none;">
                                    Pegawai tidak wujud atau sama dengan nama <?= strtolower($booking['pelawat']) === 'pentadbir' ? 'pengguna' : 'pelawat' ?>.
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
                                   download="qr_<?= strtolower($booking['pelawat']) === 'pentadbir' ? 'pentadbir' : 'pelawat' ?>_<?= $booking['booking_id'] ?>.png">
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
                        // FIX: Properly parse existing timestamps
                        $timeInVal = $booking['time_in'] ?? '';
                        $timeOutExpVal = $booking['time_out_exp'] ?? '';
                        $timeOutRealVal = $booking['time_out_real'] ?? '';
                        
                        // Convert to time-only format for display - handle empty/null values
                        $timeInFormatted = '';
                        $timeOutExpFormatted = '';
                        $timeOutRealFormatted = '';
                        
                        if ($timeInVal && $timeInVal !== '0000-00-00 00:00:00' && $timeInVal !== '00:00:00') {
                            $timeInFormatted = date('H:i', strtotime($timeInVal));
                        }
                        
                        if ($timeOutExpVal && $timeOutExpVal !== '0000-00-00 00:00:00' && $timeOutExpVal !== '00:00:00') {
                            $timeOutExpFormatted = date('H:i', strtotime($timeOutExpVal));
                        }
                        
                        if ($timeOutRealVal && $timeOutRealVal !== '0000-00-00 00:00:00' && $timeOutRealVal !== '00:00:00') {
                            $timeOutRealFormatted = date('H:i', strtotime($timeOutRealVal));
                        }
                        
                        // Get today's date
                        $todayDate = date('Y-m-d');
                        ?>

                        <div class="col-md-4 mb-3">
                            <label class="form-label"><i class="fas fa-sign-in-alt"></i> Masa Masuk</label>
                            <input type="time" id="time_in" name="time_in" class="form-control"
                                   value="<?= esc($timeInFormatted) ?>"
                                   <?= !$allowEdit ? 'readonly' : '' ?> required>
                            <!-- FIX: Pass the original timestamp value -->
                            <input type="hidden" id="time_in_timestamp" name="time_in_timestamp" 
                                   value="<?= esc($timeInVal) ?>">
                            <div class="time-info">Hari ini: <?= date('d/m/Y') ?></div>
                            <div id="time_error" class="text-danger mt-1"></div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label"><i class="fas fa-sign-out-alt"></i> Jangkaan Masa Keluar</label>
                            <input type="time" id="time_out_exp" name="time_out_exp" class="form-control"
                                   value="<?= esc($timeOutExpFormatted) ?>"
                                   <?= !$allowEdit ? 'readonly' : '' ?> required>
                            <!-- FIX: Pass the original timestamp value -->
                            <input type="hidden" id="time_out_exp_timestamp" name="time_out_exp_timestamp" 
                                   value="<?= esc($timeOutExpVal) ?>">
                            <div class="time-info">Hari ini: <?= date('d/m/Y') ?></div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label"><i class="fas fa-door-closed"></i> Masa Keluar Sebenar</label>
                            <input type="time" id="time_out_real" name="time_out_real" class="form-control"
                                   value="<?= esc($timeOutRealFormatted) ?>" readonly>
                            <!-- FIX: Pass the original timestamp value -->
                            <input type="hidden" id="time_out_real_timestamp" name="time_out_real_timestamp" 
                                   value="<?= esc($timeOutRealVal) ?>">
                            <div class="time-info">Hari ini: <?= date('d/m/Y') ?></div>
                        </div>
                    </div>

                    <?php if ($allowEdit): ?>
                        <button id="submitBtn" class="btn <?= strtolower($booking['pelawat']) === 'pentadbir' ? 'btn-primary' : 'btn-success' ?>" disabled>
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
        const timeInTimestamp = document.getElementById('time_in_timestamp');
        const timeOutExpTimestamp = document.getElementById('time_out_exp_timestamp');
        const timeOutRealTimestamp = document.getElementById('time_out_real_timestamp');
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
        const todayDate = document.getElementById('today_date').value;

        // Store original values from booking table
        const originalName = searchUser.value;
        const originalPhone = phoneInput.value;
        const originalOfficer = searchOfficer.value;

        console.log('Original values from booking table:', {
            name: originalName,
            phone: originalPhone,
            officer: originalOfficer,
            type: pelawatType
        });

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
            checkSubmitEnabled();
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
        
        // Determine user data source based on pelawat type
        const isPentadbir = pelawatType === 'pentadbir';
        
        if (isPentadbir) {
            // For pentadbir: use users table data
            userDataSource = [
                <?php foreach($users as $u): ?>
                {
                    id: "<?= esc($u['Name']) ?>", 
                    name: "<?= esc($u['Name']) ?>", 
                    phone: "<?= esc($u['Phone'] ?? '') ?>"
                },
                <?php endforeach; ?>
            ];
        } else {
            // For pelawat: use guest table data
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
        }

        // ==== Live Search Functions ====
        function attachLiveSearch(input, results, list, hiddenInput, otherInput = null, errorDiv = null) {
            input.addEventListener("input", function() {
                const keyword = this.value.trim().toLowerCase();
                results.innerHTML = "";
                hiddenInput.value = "";

                if (!keyword) {
                    results.style.display = "none";
                    if (errorDiv) errorDiv.style.display = "block";
                    checkSubmitEnabled();
                    return;
                }

                let matches = list.filter(u => u.name && u.name.toLowerCase().includes(keyword));

                if (otherInput) {
                    matches = matches.filter(u => u.name !== otherInput.value.trim());
                }

                if (matches.length === 0) {
                    results.style.display = "none";
                    if (errorDiv) errorDiv.style.display = "block";
                    checkSubmitEnabled();
                    return;
                }

                matches.forEach(u => {
                    const li = document.createElement("li");
                    li.className = "list-group-item list-group-item-action";
                    li.textContent = u.name;

                    li.onclick = () => {
                        input.value = u.name;
                        hiddenInput.value = u.name; // Store NAME not ID

                        // For pentadbir: Don't auto-fill phone from users table
                        // Keep the phone from booking table
                        if (input === searchUser && u.phone && !isPentadbir) {
                            phoneInput.value = u.phone;
                            validatePhone();
                        }

                        if (errorDiv) errorDiv.style.display = "none";
                        results.style.display = "none";
                        checkExactValidation();
                        checkSubmitEnabled();
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
                    checkSubmitEnabled();
                }, 150);
            });
        }

        // Only attach live search if edit is allowed AND we have data
        <?php if($allowEdit): ?>
            if (userDataSource && userDataSource.length > 0) {
                attachLiveSearch(searchUser, document.getElementById('user_results'), userDataSource, userHidden, searchOfficer, userError);
            } else {
                console.warn('User data source is empty');
            }
            attachLiveSearch(searchOfficer, document.getElementById('officer_results'), officerDataSource, officerHidden, searchUser, officerError);
        <?php endif; ?>

        // ==== Exact Match Validation ====
        function isExactNameMatch(inputValue, list) {
            if (!list || list.length === 0) return false;
            const cleaned = inputValue.trim().toLowerCase();
            return list.some(u => u.name && u.name.trim().toLowerCase() === cleaned);
        }

        function checkExactValidation() {
            // USER EXACT MATCH CHECK - Only check if we have data
            if (userDataSource && userDataSource.length > 0) {
                const userValid = isExactNameMatch(searchUser.value, userDataSource);
                if (!userValid && searchUser.value.trim() !== '') {
                    userError.style.display = "block";
                    userHidden.value = "";
                } else {
                    userError.style.display = "none";
                }
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
            checkSubmitEnabled();
        });

        // ==== Name Validation ====
        function validateName() {
            const name = searchUser.value.trim();
            // Basic validation: at least 2 characters
            if (name.length < 2) {
                userError.style.display = "block";
                userError.textContent = "Nama mesti sekurang-kurangnya 2 aksara.";
                return false;
            }
            return true;
        }

        searchUser.addEventListener('input', function() {
            validateName();
            checkSubmitEnabled();
        });

        // ==== Time Validation and Timestamp Conversion ====
        const minTime = "08:00";
        const maxTime = "18:00";

        // Only add event listeners if edit is allowed
        <?php if($allowEdit): ?>
            // Set min and max time constraints
            [timeIn, timeOutExp].forEach(el => {
                el.setAttribute('min', minTime);
                el.setAttribute('max', maxTime);
                
                el.addEventListener('input', function() {
                    // Ensure time is within allowed range
                    if (this.value < minTime) this.value = minTime;
                    if (this.value > maxTime) this.value = maxTime;
                    
                    // Convert time to timestamp
                    updateTimestamps();
                    
                    // Auto-set time_out_real when time_out_exp changes
                    if (el === timeOutExp && timeOutExp.value) {
                        timeOutReal.value = timeOutExp.value;
                        updateTimestamps();
                    }
                    
                    validateTimes();
                });
            });
        <?php endif; ?>

        // Function to update timestamp values
        function updateTimestamps() {
            if (timeIn.value) {
                const timeInDateTime = new Date(`${todayDate}T${timeIn.value}`);
                timeInTimestamp.value = timeInDateTime.toISOString().slice(0, 19).replace('T', ' ');
            }
            
            if (timeOutExp.value) {
                const timeOutExpDateTime = new Date(`${todayDate}T${timeOutExp.value}`);
                timeOutExpTimestamp.value = timeOutExpDateTime.toISOString().slice(0, 19).replace('T', ' ');
            }
            
            if (timeOutReal.value) {
                const timeOutRealDateTime = new Date(`${todayDate}T${timeOutReal.value}`);
                timeOutRealTimestamp.value = timeOutRealDateTime.toISOString().slice(0, 19).replace('T', ' ');
            }
        }

        function validateTimes() {
            timeError.textContent = '';
            timeError.style.display = 'none';
            
            const inVal = timeIn.value;
            const outVal = timeOutExp.value;
            
            if (!inVal || !outVal) {
                checkSubmitEnabled();
                return false;
            }
            
            // Convert times to Date objects for comparison
            const inTime = new Date(`${todayDate}T${inVal}`);
            const outTime = new Date(`${todayDate}T${outVal}`);
            
            // Calculate difference in minutes
            const diffMinutes = (outTime - inTime) / (1000 * 60);
            
            // Validation rules
            if (inVal === outVal) {
                timeError.textContent = "Masa keluar tidak boleh sama dengan masa masuk.";
                timeError.style.display = 'block';
                return false;
            }
            
            if (outTime <= inTime) {
                timeError.textContent = "Masa keluar dijangka mesti selepas masa masuk.";
                timeError.style.display = 'block';
                return false;
            }
            
            if (diffMinutes <= 30) {
                timeError.textContent = "Masa keluar dijangka mesti lebih daripada 30 minit selepas masa masuk.";
                timeError.style.display = 'block';
                return false;
            }
            
            // Also check maximum time difference (optional - 10 hours max)
            if (diffMinutes > 600) { // 10 hours = 600 minutes
                timeError.textContent = "Masa keluar dijangka tidak boleh melebihi 10 jam dari masa masuk.";
                timeError.style.display = 'block';
                return false;
            }
            
            return true;
        }

        // Initialize timestamps on page load
        updateTimestamps();
        
        // Add event listeners for time validation (only if edit is allowed)
        <?php if($allowEdit): ?>
            timeIn.addEventListener('change', function() {
                updateTimestamps();
                validateTimes();
            });
            
            timeOutExp.addEventListener('change', function() {
                updateTimestamps();
                validateTimes();
            });
        <?php endif; ?>

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

        // ==== Enable/Disable Submit Button ====
        function checkSubmitEnabled() {
            <?php if($allowEdit): ?>
                checkExactValidation();
                
                const phoneValid = validatePhone();
                const nameValid = validateName();
                const timeValid = validateTimes();
                
                const reasonVal = reasonInput.value.trim();
                const reasonValid = reasonError.style.display !== 'block' && 
                                    reasonVal.length >= 10 && 
                                    reasonVal.length <= 50;
                
                // For pelawat, we need to handle the case where userDataSource might be empty
                const userValid = !userDataSource || userDataSource.length === 0 ? 
                    (searchUser.value.trim() !== "" && userHidden.value.trim() !== "") : 
                    (userHidden.value.trim() !== "");
                
                const allValid = 
                    phoneValid &&
                    nameValid &&
                    timeValid &&
                    userValid &&
                    officerHidden.value.trim() !== "" &&
                    searchUser.value.trim() !== "" &&
                    searchOfficer.value.trim() !== "" &&
                    searchUser.value.trim().toLowerCase() !== searchOfficer.value.trim().toLowerCase() &&
                    reasonValid
                ;

                if (submitBtn) {
                    submitBtn.disabled = !allValid;
                    if (allValid) {
                        submitBtn.classList.remove('disabled');
                    } else {
                        submitBtn.classList.add('disabled');
                    }
                }
            <?php endif; ?>
        }

        // Hide dropdown when clicking outside (only if edit is allowed)
        <?php if($allowEdit): ?>
        document.addEventListener("click", function(e) {
            if (!searchUser.contains(e.target)) {
                document.getElementById('user_results').style.display = "none";
            }
            if (!searchOfficer.contains(e.target)) {
                document.getElementById('officer_results').style.display = "none";
            }
        });
        <?php endif; ?>

        // Initial validation
        checkSubmitEnabled();
    });
    </script>
</body>
</html>