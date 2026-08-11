<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Pendaftaran Pelawat</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.bootcss.com/font-awesome/5.7.2/css/all.min.css">
  <link rel="stylesheet" href="<?= base_url('css/menustyle.css') ?>">

  <style>
    .autofilled { background: #e6ffef; }

    #guest_results, #officer_results {
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

    #guest_results li, #officer_results li {
      padding: 8px 12px;
      border-bottom: 1px solid #eee;
      cursor: pointer;
    }

    #guest_results li:hover,
    #officer_results li:hover {
      background: #f0f0f0;
      cursor: pointer;
    }

    #guest_results li:last-child,
    #officer_results li:last-child {
      border-bottom: none;
    }

    .field-error {
      display: none;
      font-size: 14px;
      margin-top: 5px;
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
  </style>

  <?= view('security_prompt') ?>
  <link rel="stylesheet" href="<?= base_url('src/slidercaptcha.css') ?>">
</head>

<body style="background:#f2f9fb;">

<div class="container mt-5">

<?php if (session()->getFlashdata('success')): ?>
  <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
  <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="p-4 bg-white rounded shadow">
<h2 class="text-center mb-4 text-success">
  <i class="fas fa-user-plus me-2"></i>Pendaftaran Pelawat
</h2> 

<form id="pelawatForm" action="<?= base_url('pelawat/simpan') ?>" method="post">
<?= csrf_field() ?>

<!-- SEARCH GUEST -->
<div class="mb-3 position-relative">
  <label class="form-label">Cari Nama Pelawat</label>
  <input type="text" id="search_guest" class="form-control" placeholder="Taip nama pelawat...">
  <ul id="guest_results" class="list-group shadow"></ul>
</div>

<!-- NAME -->
<div class="mb-3">
  <label class="form-label">Nama <span class="text-danger">*</span></label>
  <input type="text" name="name" id="name_pelawat" maxlength="40" class="form-control" required>
  <div id="name_error" class="text-danger field-error">
    Nama mesti minimum 10 aksara dan hanya huruf & ruang.
  </div>
</div>

<!-- PHONE -->
<div class="mb-3">
  <label class="form-label">No. Telefon <span class="text-danger">*</span></label>
  <input type="text" name="phone_no" id="phone_pelawat" maxlength="10" class="form-control" required>
  <div id="phone_error" class="text-danger field-error">
    No telefon mesti bermula dengan 0 dan 10 digit panjang.
  </div>
</div>

<!-- REASON -->
<div class="mb-3">
  <label class="form-label">Sebab Lawatan <span class="text-danger">*</span></label>
  <textarea name="reason" id="reason_pelawat" class="form-control" rows="3" required></textarea>
  <div id="reason_error" class="text-danger field-error">
    Sebab lawatan mesti 10–50 aksara.
  </div>
</div>

<!-- OFFICER -->
<div class="mb-3 position-relative">
  <label class="form-label">Pilih Pegawai <span class="text-danger">*</span></label>
  <input type="text" id="search_officer" class="form-control" placeholder="Taip nama pegawai..." required>
  <ul id="officer_results" class="list-group shadow"></ul>
  <div id="officer_error" class="text-danger field-error">
    Pegawai tidak sah. Sila pilih dari senarai.
  </div>
</div>

<!-- TIME -->
<div class="mb-3">
  <label class="form-label">Masa Lawatan <span class="text-danger">*</span></label>
  <small class="text-muted d-block mb-2">(Hari ini sahaja: <?= date('d/m/Y') ?> | Waktu pejabat: 8:00 pagi - 6:00 petang)</small>
  
  <!-- Hidden fields for storing the full timestamps (for MySQL TIMESTAMP) -->
  <input type="hidden" name="time_in" id="time_in_full">
  <input type="hidden" name="time_out_exp" id="time_out_exp_full">
  <input type="hidden" name="time_out_real" id="time_out_real_full">
  
  <div class="time-input-group">
    <div class="time-input-wrapper">
      <label class="form-label small">Masa Masuk</label>
      <input type="time" id="time_in_input" class="form-control" min="08:00" max="18:00" step="300" required>
      <div class="time-display small text-muted mt-1" id="time_in_display"></div>
    </div>
    
    <div class="time-input-wrapper">
      <label class="form-label small">Masa Keluar (Dijangka)</label>
      <input type="time" id="time_out_exp_input" class="form-control" min="08:00" max="18:00" step="300" required>
      <div class="time-display small text-muted mt-1" id="time_out_exp_display"></div>
    </div>
  </div>
  
  <div id="time_error" class="text-danger field-error mt-2"></div>
</div>

<!-- Hidden fields for server submission -->
<input type="hidden" name="officer" id="officer_selected">

<!-- CAPTCHA -->
<div id="captchaPelawat"></div>
<input type="hidden" id="captchaVerifiedPelawat" value="false">

<br><br>
<button type="submit" id="submitPelawat" class="btn btn-primary w-100" disabled>
  <i class="fas fa-user-plus"></i> Daftar Pelawat
</button>

<div class="text-center mt-3">
  <a href="<?= base_url('pelawat') ?>" class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i> Kembali ke Halaman Utama
  </a>
</div>

</form>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('src/longbow.slidercaptcha.js') ?>"></script>

<script>
/* ================= GLOBAL VARIABLES ================= */
let isFormValid = false;
const submitBtn = document.getElementById("submitPelawat");
const captchaVerifiedPelawat = document.getElementById("captchaVerifiedPelawat");
const officerList = <?= json_encode(array_column($users ?? [], 'Name')) ?>;

/* ================= CAPTCHA ================= */
sliderCaptcha({
  id: "captchaPelawat",
  width: 280,
  height: 130,
  sliderL: 42,
  sliderR: 9,
  offset: 5,
  barText: "Slide right to verify",
  failedText: "Try again",
  repeatIcon: "fa fa-redo",
  setSrc: function () {
    return "<?= base_url('src/images/Pic') ?>" + Math.floor(Math.random() * 5) + ".jpg";
  },
  onSuccess: function () {
    submitPelawat.style.display = "block";
    captchaVerifiedPelawat.value = "true";
    validateForm();
  },
  onFail: function () {
    submitPelawat.style.display = "none";
    captchaVerifiedPelawat.value = "false";
  },
  onRefresh: function () {
    submitPelawat.style.display = "none";
    captchaVerifiedPelawat.value = "false";
  }
});

/* ================= ELEMENTS ================= */
const nameInput = document.getElementById("name_pelawat");
const phoneInput = document.getElementById("phone_pelawat");
const reasonInput = document.getElementById("reason_pelawat");
const searchGuest = document.getElementById("search_guest");
const guestResults = document.getElementById("guest_results");
const searchOfficer = document.getElementById("search_officer");
const officerResults = document.getElementById("officer_results");
const officerHidden = document.getElementById("officer_selected");

// Time elements
const timeInInput = document.getElementById("time_in_input");
const timeOutExpInput = document.getElementById("time_out_exp_input");
const timeInFull = document.getElementById("time_in_full");
const timeOutExpFull = document.getElementById("time_out_exp_full");
const timeOutRealFull = document.getElementById("time_out_real_full");

/* ================= TIME HELPER FUNCTIONS ================= */
function parseTimeString(timeStr) {
  // Parse "HH:MM" string into minutes since midnight
  const [hours, minutes] = timeStr.split(':').map(Number);
  return hours * 60 + minutes;
}

function formatTimeString(minutes) {
  // Convert minutes since midnight to "HH:MM" format
  const hours = Math.floor(minutes / 60);
  const mins = minutes % 60;
  return `${hours.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}`;
}

function isWithinOfficeHours(timeStr) {
  const minutes = parseTimeString(timeStr);
  // 8:00 AM = 480 minutes, 6:00 PM = 1080 minutes
  return minutes >= 480 && minutes <= 1080;
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
  const errorElement = document.getElementById("time_error");
  
  // Reset error
  errorElement.style.display = "none";
  errorElement.textContent = "";
  timeInInput.classList.remove('is-invalid', 'is-valid');
  timeOutExpInput.classList.remove('is-invalid', 'is-valid');
  
  // Check if both times are filled
  if (!timeInValue || !timeOutValue) {
    errorElement.textContent = "Sila isi kedua-dua masa masuk dan keluar";
    errorElement.style.display = "block";
    timeInInput.classList.add('is-invalid');
    timeOutExpInput.classList.add('is-invalid');
    return false;
  }
  
  // Check if times are within office hours
  if (!isWithinOfficeHours(timeInValue)) {
    errorElement.textContent = "Masa masuk mesti antara 8:00 pagi hingga 6:00 petang";
    errorElement.style.display = "block";
    timeInInput.classList.add('is-invalid');
    return false;
  }
  
  if (!isWithinOfficeHours(timeOutValue)) {
    errorElement.textContent = "Masa keluar mesti antara 8:00 pagi hingga 6:00 petang";
    errorElement.style.display = "block";
    timeOutExpInput.classList.add('is-invalid');
    return false;
  }
  
  const timeInMinutes = parseTimeString(timeInValue);
  const timeOutMinutes = parseTimeString(timeOutValue);
  
  // Check if time_out is after time_in
  if (timeOutMinutes <= timeInMinutes) {
    errorElement.textContent = "Masa keluar mesti selepas masa masuk";
    errorElement.style.display = "block";
    timeInInput.classList.add('is-invalid');
    timeOutExpInput.classList.add('is-invalid');
    return false;
  }
  
  // Check if duration is at least 30 minutes
  const duration = timeOutMinutes - timeInMinutes;
  if (duration < 30) {
    errorElement.textContent = "Masa keluar mesti sekurang-kurangnya 30 minit selepas masa masuk";
    errorElement.style.display = "block";
    timeOutExpInput.classList.add('is-invalid');
    return false;
  }
  
  // Check maximum 10 hours difference (optional)
  if (duration > 600) { // 10 hours = 600 minutes
    errorElement.textContent = "Masa keluar tidak boleh melebihi 10 jam dari masa masuk";
    errorElement.style.display = "block";
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

function setDefaultTimes() {
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
  timeInInput.value = formatTimeString(defaultTimeInMinutes);
  
  // Set time out to 30 minutes later (max 6:00 PM)
  const defaultTimeOutMinutes = Math.min(defaultTimeInMinutes + 30, 1080);
  timeOutExpInput.value = formatTimeString(defaultTimeOutMinutes);
  
  // Update timestamp fields
  updateTimestampFields();
}

/* ================= VALIDATION FUNCTIONS ================= */
function validateName(){
  const value = nameInput.value.trim();
  const isValid = /^[A-Za-z\s]{10,40}$/.test(value);
  const errorElement = document.getElementById("name_error");
  
  if (isValid) {
    nameInput.classList.remove('is-invalid');
    nameInput.classList.add('is-valid');
    errorElement.style.display = "none";
  } else {
    nameInput.classList.remove('is-valid');
    nameInput.classList.add('is-invalid');
    errorElement.style.display = "block";
  }
  return isValid;
}

function validatePhone(){
  let value = phoneInput.value.replace(/\D/g,'');
  value = value.slice(0, 10);
  phoneInput.value = value;
  
  const isValid = value.startsWith("0") && value.length === 10;
  const errorElement = document.getElementById("phone_error");
  
  if (isValid) {
    phoneInput.classList.remove('is-invalid');
    phoneInput.classList.add('is-valid');
    errorElement.style.display = "none";
  } else {
    phoneInput.classList.remove('is-valid');
    phoneInput.classList.add('is-invalid');
    errorElement.style.display = "block";
  }
  return isValid;
}

function validateReason(){
  const value = reasonInput.value.trim();
  const isValid = value.length >= 10 && value.length <= 50;
  const errorElement = document.getElementById("reason_error");
  
  if (isValid) {
    reasonInput.classList.remove('is-invalid');
    reasonInput.classList.add('is-valid');
    errorElement.style.display = "none";
  } else {
    reasonInput.classList.remove('is-valid');
    reasonInput.classList.add('is-invalid');
    errorElement.style.display = "block";
  }
  return isValid;
}

function validateOfficer(){
  const value = searchOfficer.value.trim();
  const isValid = officerList.includes(value);
  const errorElement = document.getElementById("officer_error");
  
  if (isValid) {
    searchOfficer.classList.remove('is-invalid');
    searchOfficer.classList.add('is-valid');
    errorElement.style.display = "none";
    officerHidden.value = value;
  } else {
    searchOfficer.classList.remove('is-valid');
    searchOfficer.classList.add('is-invalid');
    errorElement.style.display = "block";
    officerHidden.value = "";
  }
  return isValid;
}

function validateTime(){
  return validateTimeInputs();
}

function validateForm(){
  const nameValid = validateName();
  const phoneValid = validatePhone();
  const reasonValid = validateReason();
  const officerValid = validateOfficer();
  const timeValid = validateTime();
  const captchaValid = captchaVerifiedPelawat.value === "true";
  
  isFormValid = nameValid && phoneValid && reasonValid && officerValid && timeValid && captchaValid;
  submitBtn.disabled = !isFormValid;
  
  return isFormValid;
}

/* ================= EVENT LISTENERS ================= */
[nameInput, phoneInput, reasonInput, searchOfficer].forEach(el => {
  if (el) {
    el.addEventListener("input", validateForm);
    el.addEventListener("blur", validateForm);
  }
});

// Time input event listeners
timeInInput.addEventListener("change", function() {
  autoAdjustTimeOut();
  validateForm();
});

timeInInput.addEventListener("input", function() {
  updateTimestampFields();
  autoAdjustTimeOut();
  validateForm();
});

timeOutExpInput.addEventListener("change", function() {
  autoAdjustTimeIn();
  validateForm();
});

timeOutExpInput.addEventListener("input", function() {
  updateTimestampFields();
  autoAdjustTimeIn();
  validateForm();
});

// Set default times on page load
window.addEventListener('DOMContentLoaded', () => {
  setDefaultTimes();
  validateForm();
});

/* ================= GUEST SEARCH ================= */
searchGuest.addEventListener("input", function(){
  const keyword = this.value.trim();
  if(!keyword){ 
    guestResults.style.display = "none"; 
    return; 
  }

  fetch("<?= site_url('pelawat/searchGuest') ?>", {
    method: "POST",
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: new URLSearchParams({
      keyword: keyword,
      <?= csrf_token() ?>: "<?= csrf_hash() ?>"
    })
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }
    return response.json();
  })
  .then(data => {
    guestResults.innerHTML = "";
    
    if(!data || data.length === 0){
      guestResults.style.display = "none";
      return;
    }

    data.forEach(guest => {
      const li = document.createElement("li");
      li.className = "list-group-item";
      li.textContent = guest.name || guest.Name || '';
      li.style.cursor = "pointer";
      
      li.addEventListener('click', () => {
        nameInput.value = guest.name || guest.Name || '';
        phoneInput.value = guest.phone_no || guest.phone || '';
        reasonInput.value = guest.reason || '';
        searchGuest.value = '';
        guestResults.style.display = "none";
        validateForm();
      });
      
      guestResults.appendChild(li);
    });
    guestResults.style.display = "block";
  })
  .catch(error => {
    console.error('Error:', error);
    guestResults.style.display = "none";
  });
});

// Hide guest results when clicking outside
document.addEventListener("click", function(e){
  if (!searchGuest.contains(e.target) && !guestResults.contains(e.target)) {
    guestResults.style.display = "none";
  }
});

/* ================= OFFICER AUTOCOMPLETE ================= */
searchOfficer.addEventListener("input", function(){
  const keyword = this.value.toLowerCase();
  officerResults.innerHTML = "";
  
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
      validateForm();
    });
    
    officerResults.appendChild(li);
  });
  
  officerResults.style.display = "block";
});

// Hide officer results when clicking outside
document.addEventListener("click", function(e){
  if (!searchOfficer.contains(e.target) && !officerResults.contains(e.target)) {
    officerResults.style.display = "none";
  }
});

/* ================= FORM SUBMISSION ================= */
document.getElementById("pelawatForm").addEventListener("submit", function(e){
  if (!validateForm()) {
    e.preventDefault();
    alert("Sila betulkan semua kesilapan sebelum menghantar.");
    return false;
  }
  
  // Ensure all timestamp fields are up to date
  updateTimestampFields();
  
  // Ensure officer value is set
  if (!officerHidden.value) {
    officerHidden.value = searchOfficer.value.trim();
  }
  
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