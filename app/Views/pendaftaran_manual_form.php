<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Pendaftaran Manual</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('css/menustyle.css') ?>">
  <link rel="stylesheet" href="<?= base_url('src/slidercaptcha.css') ?>">
  <style>
    .autofilled { background: #e6ffef; }
    #user_results, #officer_results { 
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
    #user_results li, #officer_results li { 
      padding: 8px 12px; 
      border-bottom: 1px solid #eee; 
      cursor: pointer; 
    }
    #user_results li:hover,
    #officer_results li:hover { 
      background: #f0f0f0; 
      cursor: pointer; 
    }
    #user_results li:last-child,
    #officer_results li:last-child { 
      border-bottom: none; 
    }
    .error-message {
      color: #dc3545;
      font-size: 0.875em;
      margin-top: 0.25rem;
      display: none;
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
    @media (max-width: 768px) {
      .time-input-group {
        flex-direction: column;
        gap: 10px;
      }
    }
    body {
      background: #f2f9fb;
      transform: scale(0.8);
      transform-origin: top left;
      width: 125%;
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
<body>

<?= view('security_prompt') ?>

<div class="container mt-5 p-4 bg-white rounded shadow" style="max-width:700px;">
  <h2 class="text-center mb-4 text-primary"><i class="fas fa-user-edit me-2"></i> Pendaftaran Manual</h2>

  <?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
  <?php endif; ?>
  <?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
  <?php endif; ?>

  <form id="manualForm" action="<?= base_url('regmanual/simpan') ?>" method="post">

    <!-- User -->
    <div class="mb-3 position-relative">
      <label class="form-label">Pilih Nama Pengguna <span class="text-danger">*</span></label>
      <input type="text" id="search_user" class="form-control" placeholder="Taip nama pengguna..." autocomplete="off" required>
      <ul id="user_results" class="list-group shadow"></ul>
      <input type="hidden" name="name" id="user_selected" required>
      <div id="user_error_message" class="error-message"></div>
    </div>

    <!-- Phone -->
    <div class="mb-3">
      <label class="form-label">No. Telefon <span class="text-danger">*</span></label>
      <input type="text" name="phone_no" id="phone_no" class="form-control" maxlength="10" required>
      <div id="phone_error_message" class="error-message"></div>
    </div>

    <!-- Officer -->
    <div class="mb-3 position-relative">
      <label class="form-label">Pegawai <span class="text-danger">*</span></label>
      <input type="text" id="search_officer" class="form-control" placeholder="Taip nama pegawai..." autocomplete="off" required>
      <ul id="officer_results" class="list-group shadow"></ul>
      <input type="hidden" name="officer" id="officer_selected" required>
      <div id="officer_error_message" class="error-message"></div>
    </div>

    <!-- Reason -->
    <div class="mb-3">
      <label class="form-label">Sebab Lawatan <span class="text-danger">*</span></label>
      <textarea name="reason" id="reason" class="form-control" maxlength="50" required></textarea>
      <div id="reason_error_message" class="error-message"></div>
    </div>

    <!-- Time -->
    <div class="mb-3">
      <label class="form-label">Masa Lawatan <span class="text-danger">*</span></label>
      <small class="text-muted d-block mb-2">(Hari ini sahaja: <?= date('d/m/Y') ?> | Waktu pejabat: 8:00 pagi - 6:00 petang)</small>
      
      <div class="time-input-group">
        <div class="time-input-wrapper">
          <label class="form-label small">Masa Masuk</label>
          <input type="datetime-local" name="time_in" id="time_in" class="form-control" required>
          <div id="time_in_error_message" class="error-message"></div>
        </div>
        
        <div class="time-input-wrapper">
          <label class="form-label small">Masa Keluar (Dijangka)</label>
          <input type="datetime-local" name="time_out_exp" id="time_out_exp" class="form-control" required>
          <div id="time_out_error_message" class="error-message"></div>
        </div>
      </div>
      
      <div id="time_error_message" class="error-message mt-2"></div>
    </div>

    <!-- Hidden fields for server submission -->
    <input type="hidden" name="time_out_real" id="time_out_real">
    <input type="hidden" name="visit_date" id="visit_date" value="<?= date('Y-m-d') ?>">

    <!-- CAPTCHA -->
    <div class="captcha-wrapper" id="captchaLoginContainer">
      <div id="captchaLogin" class="my-3"></div>
    </div>
    <input type="hidden" id="captchaVerified" name="captchaVerified" value="false">

    <br><br>
    <div class="submit-area text-center">
      <button type="submit" id="submitBtn" class="btn btn-primary w-100" disabled>Daftar Manual</button>
    </div>
  </form>

  <div class="text-center my-4">
    <a href="<?= base_url('menu') ?>" class="btn btn-primary px-4 py-2">← Kembali ke Dashboard</a>
  </div>
</div>

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('src/longbow.slidercaptcha.js') ?>"></script>
<script src="<?= base_url('js/function.js') ?>"></script>

<script>
document.addEventListener("DOMContentLoaded", function(){
  /* ========================= TIME HELPER FUNCTIONS ========================= */
  function parseDateTimeString(dateTimeStr) {
    if (!dateTimeStr) return null;
    return new Date(dateTimeStr);
  }

  function formatDateTime(date) {
    if (!date) return '';
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    return `${year}-${month}-${day}T${hours}:${minutes}`;
  }

  function isWithinOfficeHours(date) {
    if (!date) return false;
    const hours = date.getHours();
    const minutes = date.getMinutes();
    const totalMinutes = hours * 60 + minutes;
    return totalMinutes >= 480 && totalMinutes <= 1080; // 8:00 AM = 480, 6:00 PM = 1080
  }

  function isToday(date) {
    if (!date) return false;
    const today = new Date();
    return date.getDate() === today.getDate() &&
           date.getMonth() === today.getMonth() &&
           date.getFullYear() === today.getFullYear();
  }

  /* ========================= GLOBAL VARIABLES ========================= */
  const submitBtn = document.getElementById('submitBtn');
  const timeIn = document.getElementById('time_in');
  const timeOutExp = document.getElementById('time_out_exp');
  const timeOutReal = document.getElementById('time_out_real');
  const visitDate = document.getElementById('visit_date');
  const phoneInput = document.getElementById('phone_no');
  const userHidden = document.getElementById('user_selected');
  const officerHidden = document.getElementById('officer_selected');
  const reasonInput = document.getElementById('reason');
  
  // Error message elements
  const userErrorMessage = document.getElementById('user_error_message');
  const phoneErrorMessage = document.getElementById('phone_error_message');
  const officerErrorMessage = document.getElementById('officer_error_message');
  const reasonErrorMessage = document.getElementById('reason_error_message');
  const timeErrorMessage = document.getElementById('time_error_message');
  const timeInErrorMessage = document.getElementById('time_in_error_message');
  const timeOutErrorMessage = document.getElementById('time_out_error_message');

  // Get DOM elements for search inputs and results
  const searchUserInput = document.getElementById('search_user');
  const searchOfficerInput = document.getElementById('search_officer');
  const userResults = document.getElementById('user_results');
  const officerResults = document.getElementById('officer_results');

  /* ========================= USER AND OFFICER DATA ========================= */
  // Get all user names from PHP
  const allUserNames = <?= json_encode(array_column($users ?? [], 'Name')) ?>;
  
  // Sort the names alphabetically
  allUserNames.sort();

  // Track selected names to exclude from other dropdown
  let selectedUserName = '';
  let selectedOfficerName = '';

  /* ========================= USER VALIDATION ========================= */
  function validateUser() {
    const name = searchUserInput.value.trim();
    
    // Clear previous error message
    userErrorMessage.textContent = '';
    userErrorMessage.style.display = 'none';
    
    if (!name) {
      searchUserInput.classList.remove('is-valid', 'is-invalid');
      userHidden.value = "";
      selectedUserName = '';
      updateOfficerDropdown();
      
      // Show error for required field
      userErrorMessage.textContent = 'Sila pilih pengguna.';
      userErrorMessage.style.display = 'block';
      searchUserInput.classList.add('is-invalid');
      return false;
    }
    
    // Check if the name exists in the list
    const exists = allUserNames.includes(name);
    
    if (!exists) {
      searchUserInput.classList.remove('is-valid');
      searchUserInput.classList.add('is-invalid');
      userErrorMessage.textContent = 'Pengguna tidak wujud. Sila pilih daripada senarai.';
      userErrorMessage.style.display = 'block';
      userHidden.value = "";
      selectedUserName = '';
      updateOfficerDropdown();
      return false;
    }
    
    // Check if user and officer names are the same
    if (name === selectedOfficerName) {
      searchUserInput.classList.remove('is-valid');
      searchUserInput.classList.add('is-invalid');
      userErrorMessage.textContent = 'Pengguna dan pegawai tidak boleh sama.';
      userErrorMessage.style.display = 'block';
      userHidden.value = "";
      selectedUserName = '';
      updateOfficerDropdown();
      return false;
    }
    
    searchUserInput.classList.remove('is-invalid');
    searchUserInput.classList.add('is-valid');
    // For this form, we store the name itself since there's no ID
    userHidden.value = name;
    selectedUserName = name;
    updateOfficerDropdown();
    return true;
  }

  /* ========================= OFFICER VALIDATION ========================= */
  function validateOfficer() {
    const name = searchOfficerInput.value.trim();
    
    // Clear previous error message
    officerErrorMessage.textContent = '';
    officerErrorMessage.style.display = 'none';
    
    if (!name) {
      searchOfficerInput.classList.remove('is-valid', 'is-invalid');
      officerHidden.value = "";
      selectedOfficerName = '';
      updateUserDropdown();
      
      // Show error for required field
      officerErrorMessage.textContent = 'Sila pilih pegawai.';
      officerErrorMessage.style.display = 'block';
      searchOfficerInput.classList.add('is-invalid');
      return false;
    }
    
    // Check if the name exists in the list
    const exists = allUserNames.includes(name);
    
    if (!exists) {
      searchOfficerInput.classList.remove('is-valid');
      searchOfficerInput.classList.add('is-invalid');
      officerErrorMessage.textContent = 'Pegawai tidak wujud. Sila pilih daripada senarai.';
      officerErrorMessage.style.display = 'block';
      officerHidden.value = "";
      selectedOfficerName = '';
      updateUserDropdown();
      return false;
    }
    
    // Check if user and officer names are the same
    if (name === selectedUserName) {
      searchOfficerInput.classList.remove('is-valid');
      searchOfficerInput.classList.add('is-invalid');
      officerErrorMessage.textContent = 'Pegawai dan pengguna tidak boleh sama.';
      officerErrorMessage.style.display = 'block';
      officerHidden.value = "";
      selectedOfficerName = '';
      updateUserDropdown();
      return false;
    }
    
    searchOfficerInput.classList.remove('is-invalid');
    searchOfficerInput.classList.add('is-valid');
    officerHidden.value = name;
    selectedOfficerName = name;
    updateUserDropdown();
    return true;
  }

  /* ========================= UPDATE DROPDOWNS ========================= */
  function updateUserDropdown() {
    const keyword = searchUserInput.value.toLowerCase();
    
    userResults.innerHTML = "";
    
    // Filter users excluding the selected officer
    const filteredUsers = allUserNames.filter(userName => {
      const matchesKeyword = userName.toLowerCase().includes(keyword);
      const notSelectedAsOfficer = userName !== selectedOfficerName;
      return matchesKeyword && notSelectedAsOfficer;
    });
    
    if (!keyword || filteredUsers.length === 0) {
      userResults.style.display = "none";
      return;
    }
    
    filteredUsers.forEach(userName => {
      const li = document.createElement("li");
      li.className = "list-group-item";
      li.textContent = userName;
      li.style.cursor = "pointer";
      
      // Use mousedown event instead of click to ensure it fires before blur
      li.addEventListener('mousedown', function(e) {
        e.preventDefault(); // Prevent input blur
        searchUserInput.value = userName;
        userHidden.value = userName;
        selectedUserName = userName;
        userResults.style.display = "none"; // Hide dropdown immediately after selection
        validateUser();
        updateSubmitState();
        
        // Also hide the officer dropdown if it's showing
        officerResults.style.display = "none";
      });
      
      userResults.appendChild(li);
    });
    userResults.style.display = "block";
  }

  function updateOfficerDropdown() {
    const keyword = searchOfficerInput.value.toLowerCase();
    
    officerResults.innerHTML = "";
    
    // Filter officers excluding the selected user
    const filteredOfficers = allUserNames.filter(userName => {
      const matchesKeyword = userName.toLowerCase().includes(keyword);
      const notSelectedAsUser = userName !== selectedUserName;
      return matchesKeyword && notSelectedAsUser;
    });
    
    if (!keyword || filteredOfficers.length === 0) {
      officerResults.style.display = "none";
      return;
    }
    
    filteredOfficers.forEach(userName => {
      const li = document.createElement("li");
      li.className = "list-group-item";
      li.textContent = userName;
      li.style.cursor = "pointer";
      
      // Use mousedown event instead of click to ensure it fires before blur
      li.addEventListener('mousedown', function(e) {
        e.preventDefault(); // Prevent input blur
        searchOfficerInput.value = userName;
        officerHidden.value = userName;
        selectedOfficerName = userName;
        officerResults.style.display = "none"; // Hide dropdown immediately after selection
        validateOfficer();
        updateSubmitState();
        
        // Also hide the user dropdown if it's showing
        userResults.style.display = "none";
      });
      
      officerResults.appendChild(li);
    });
    officerResults.style.display = "block";
  }

  /* ========================= DROPDOWN VISIBILITY CONTROL ========================= */
  // Track which dropdown is active
  let activeDropdown = null;

  // User input focus handler
  searchUserInput.addEventListener("focus", function() {
    // Set active dropdown to user
    activeDropdown = 'user';
    
    // Show user results if there's text
    if (searchUserInput.value.trim()) {
      updateUserDropdown();
    }
  });

  // Officer input focus handler
  searchOfficerInput.addEventListener("focus", function() {
    // Set active dropdown to officer
    activeDropdown = 'officer';
    
    // Show officer results if there's text
    if (searchOfficerInput.value.trim()) {
      updateOfficerDropdown();
    }
  });

  /* ========================= HIDE DROPDOWNS WHEN TYPING IN OTHER INPUTS ========================= */
  // List of all non-dropdown inputs
  const otherInputs = [phoneInput, reasonInput, timeIn, timeOutExp];
  
  // Function to hide all dropdowns
  function hideAllDropdowns() {
    userResults.style.display = "none";
    officerResults.style.display = "none";
    activeDropdown = null;
  }
  
  // Apply event listeners to all other inputs
  otherInputs.forEach(input => {
    // Hide dropdowns when input gets focus
    input.addEventListener('focus', hideAllDropdowns);
    
    // Hide dropdowns when typing starts
    input.addEventListener('input', hideAllDropdowns);
    
    // Hide dropdowns when clicking on the input
    input.addEventListener('mousedown', hideAllDropdowns);
    
    // Hide dropdowns when clicking anywhere on the input (including text selection)
    input.addEventListener('click', hideAllDropdowns);
    
    // Hide dropdowns on keydown (for keyboard navigation)
    input.addEventListener('keydown', hideAllDropdowns);
  });

  /* ========================= USER SEARCH EVENT LISTENERS ========================= */
  searchUserInput.addEventListener("input", function() {
    // Only update user dropdown if user input is active
    if (activeDropdown === 'user') {
      updateUserDropdown();
    }
    validateUser();
    updateSubmitState();
  });

  searchUserInput.addEventListener("blur", function() {
    setTimeout(() => {
      // Only hide user dropdown if user input is still active
      if (activeDropdown === 'user') {
        userResults.style.display = "none";
      }
    }, 200);
    validateUser();
    updateSubmitState();
  });

  /* ========================= OFFICER SEARCH EVENT LISTENERS ========================= */
  searchOfficerInput.addEventListener("input", function() {
    // Only update officer dropdown if officer input is active
    if (activeDropdown === 'officer') {
      updateOfficerDropdown();
    }
    validateOfficer();
    updateSubmitState();
  });

  searchOfficerInput.addEventListener("blur", function() {
    setTimeout(() => {
      // Only hide officer dropdown if officer input is still active
      if (activeDropdown === 'officer') {
        officerResults.style.display = "none";
      }
    }, 200);
    validateOfficer();
    updateSubmitState();
  });

  /* ========================= GLOBAL CLICK HANDLER ========================= */
  document.addEventListener("click", function(e) {
    // Check if click is outside user search area
    if (!searchUserInput.contains(e.target) && !userResults.contains(e.target)) {
      userResults.style.display = "none";
    }
    
    // Check if click is outside officer search area
    if (!searchOfficerInput.contains(e.target) && !officerResults.contains(e.target)) {
      officerResults.style.display = "none";
    }
    
    // Reset active dropdown if clicking outside both search areas
    if (!searchUserInput.contains(e.target) && !searchOfficerInput.contains(e.target)) {
      activeDropdown = null;
    }
  });

  // Prevent dropdown clicks from bubbling up
  userResults.addEventListener("click", function(e) {
    e.stopPropagation(); // Prevent event from bubbling up
  });

  officerResults.addEventListener("click", function(e) {
    e.stopPropagation(); // Prevent event from bubbling up
  });

  /* ========================= PHONE VALIDATION ========================= */
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
      
      // Show error for required field
      phoneErrorMessage.textContent = 'Sila isi nombor telefon.';
      phoneErrorMessage.style.display = 'block';
      phoneInput.classList.add('is-invalid');
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
    hideAllDropdowns(); // Ensure dropdowns are hidden when typing phone
  });

  phoneInput.addEventListener("blur", function() {
    validatePhone();
    updateSubmitState();
  });

  /* ========================= REASON VALIDATION ========================= */
  function validateReason() {
    const r = reasonInput.value.trim();
    const isValid = r.length >= 10 && r.length <= 50;
    
    // Clear previous error message
    reasonErrorMessage.textContent = '';
    reasonErrorMessage.style.display = 'none';
    
    if (!r) {
      reasonInput.classList.remove('is-valid', 'is-invalid');
      
      // Show error for required field
      reasonErrorMessage.textContent = 'Sila isi sebab lawatan.';
      reasonErrorMessage.style.display = 'block';
      reasonInput.classList.add('is-invalid');
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
    hideAllDropdowns(); // Ensure dropdowns are hidden when typing reason
  });

  reasonInput.addEventListener("blur", function() {
    validateReason();
    updateSubmitState();
  });

  /* ========================= TIME VALIDATION ========================= */
  function validateTime() {
    const timeInValue = timeIn.value;
    const timeOutValue = timeOutExp.value;
    
    // Reset all time error messages
    timeErrorMessage.textContent = '';
    timeErrorMessage.style.display = 'none';
    timeInErrorMessage.textContent = '';
    timeInErrorMessage.style.display = 'none';
    timeOutErrorMessage.textContent = '';
    timeOutErrorMessage.style.display = 'none';
    timeIn.classList.remove('is-invalid', 'is-valid');
    timeOutExp.classList.remove('is-invalid', 'is-valid');
    
    // Check if both times are filled
    if (!timeInValue) {
      timeInErrorMessage.textContent = "Sila isi masa masuk";
      timeInErrorMessage.style.display = 'block';
      timeIn.classList.add('is-invalid');
    }
    
    if (!timeOutValue) {
      timeOutErrorMessage.textContent = "Sila isi masa keluar";
      timeOutErrorMessage.style.display = 'block';
      timeOutExp.classList.add('is-invalid');
    }
    
    if (!timeInValue || !timeOutValue) {
      timeErrorMessage.textContent = "Sila isi kedua-dua masa masuk dan keluar";
      timeErrorMessage.style.display = 'block';
      return false;
    }
    
    const timeInDate = parseDateTimeString(timeInValue);
    const timeOutDate = parseDateTimeString(timeOutValue);
    
    // Check if dates are today
    if (!isToday(timeInDate)) {
      timeInErrorMessage.textContent = "Tarikh mesti hari ini sahaja";
      timeInErrorMessage.style.display = 'block';
      timeIn.classList.add('is-invalid');
      return false;
    }
    
    if (!isToday(timeOutDate)) {
      timeOutErrorMessage.textContent = "Tarikh mesti hari ini sahaja";
      timeOutErrorMessage.style.display = 'block';
      timeOutExp.classList.add('is-invalid');
      return false;
    }
    
    // Check if times are within office hours
    let hasError = false;
    
    if (!isWithinOfficeHours(timeInDate)) {
      timeInErrorMessage.textContent = "Masa masuk mesti antara 8:00 pagi hingga 6:00 petang";
      timeInErrorMessage.style.display = 'block';
      timeIn.classList.add('is-invalid');
      hasError = true;
    } else {
      timeIn.classList.add('is-valid');
    }
    
    if (!isWithinOfficeHours(timeOutDate)) {
      timeOutErrorMessage.textContent = "Masa keluar mesti antara 8:00 pagi hingga 6:00 petang";
      timeOutErrorMessage.style.display = 'block';
      timeOutExp.classList.add('is-invalid');
      hasError = true;
    } else {
      timeOutExp.classList.add('is-valid');
    }
    
    if (hasError) return false;
    
    // Check if times are the same
    if (timeOutDate.getTime() === timeInDate.getTime()) {
      timeErrorMessage.textContent = "Masa masuk dan keluar tidak boleh sama";
      timeErrorMessage.style.display = 'block';
      timeIn.classList.add('is-invalid');
      timeOutExp.classList.add('is-invalid');
      return false;
    }
    
    // Check if time_out is after time_in
    if (timeOutDate.getTime() <= timeInDate.getTime()) {
      timeErrorMessage.textContent = "Masa keluar mesti selepas masa masuk";
      timeErrorMessage.style.display = 'block';
      timeIn.classList.add('is-invalid');
      timeOutExp.classList.add('is-invalid');
      return false;
    }
    
    // Check if duration is at least 30 minutes
    const duration = (timeOutDate.getTime() - timeInDate.getTime()) / (1000 * 60); // in minutes
    if (duration < 30) {
      timeErrorMessage.textContent = "Masa keluar mesti sekurang-kurangnya 30 minit selepas masa masuk";
      timeErrorMessage.style.display = 'block';
      timeOutExp.classList.add('is-invalid');
      return false;
    }
    
    // All validations passed
    timeIn.classList.remove('is-invalid');
    timeOutExp.classList.remove('is-invalid');
    timeIn.classList.add('is-valid');
    timeOutExp.classList.add('is-valid');
    
    // Update hidden field for server - extract just the time part
    const timeOutTime = timeOutDate.getHours().toString().padStart(2, '0') + ':' + 
                       timeOutDate.getMinutes().toString().padStart(2, '0');
    timeOutReal.value = timeOutTime;
    
    return true;
  }

  function autoAdjustTimeOut() {
    if (!timeIn.value) return;
    
    const timeInDate = parseDateTimeString(timeIn.value);
    if (!timeInDate) return;
    
    const timeOutDate = parseDateTimeString(timeOutExp.value);
    
    // If time_out is not set or is before/equal to time_in, set it to 30 minutes later
    if (!timeOutDate || timeOutDate.getTime() <= timeInDate.getTime()) {
      const newOutDate = new Date(timeInDate.getTime() + 30 * 60 * 1000); // 30 minutes later
      // Ensure it's within office hours (max 6:00 PM)
      const maxTime = new Date(timeInDate);
      maxTime.setHours(18, 0, 0, 0);
      if (newOutDate.getTime() > maxTime.getTime()) {
        newOutDate.setTime(maxTime.getTime());
      }
      timeOutExp.value = formatDateTime(newOutDate);
    } else {
      const duration = (timeOutDate.getTime() - timeInDate.getTime()) / (1000 * 60);
      if (duration < 30) {
        // If duration is less than 30 minutes, adjust to minimum 30 minutes
        const newOutDate = new Date(timeInDate.getTime() + 30 * 60 * 1000);
        // Ensure it's within office hours (max 6:00 PM)
        const maxTime = new Date(timeInDate);
        maxTime.setHours(18, 0, 0, 0);
        if (newOutDate.getTime() > maxTime.getTime()) {
          newOutDate.setTime(maxTime.getTime());
        }
        timeOutExp.value = formatDateTime(newOutDate);
      }
    }
  }

  function autoAdjustTimeIn() {
    if (!timeOutExp.value) return;
    
    const timeOutDate = parseDateTimeString(timeOutExp.value);
    if (!timeOutDate) return;
    
    const timeInDate = parseDateTimeString(timeIn.value);
    
    // If time_in is not set or is after/equal to time_out, set it to 30 minutes before
    if (!timeInDate || timeInDate.getTime() >= timeOutDate.getTime()) {
      const newInDate = new Date(timeOutDate.getTime() - 30 * 60 * 1000); // 30 minutes before
      // Ensure it's within office hours (min 8:00 AM)
      const minTime = new Date(timeOutDate);
      minTime.setHours(8, 0, 0, 0);
      if (newInDate.getTime() < minTime.getTime()) {
        newInDate.setTime(minTime.getTime());
      }
      timeIn.value = formatDateTime(newInDate);
    } else {
      const duration = (timeOutDate.getTime() - timeInDate.getTime()) / (1000 * 60);
      if (duration < 30) {
        // If duration is less than 30 minutes, adjust to minimum 30 minutes
        const newInDate = new Date(timeOutDate.getTime() - 30 * 60 * 1000);
        // Ensure it's within office hours (min 8:00 AM)
        const minTime = new Date(timeOutDate);
        minTime.setHours(8, 0, 0, 0);
        if (newInDate.getTime() < minTime.getTime()) {
          newInDate.setTime(minTime.getTime());
        }
        timeIn.value = formatDateTime(newInDate);
      }
    }
  }

  // Time input event listeners
  timeIn.addEventListener("change", function() {
    autoAdjustTimeOut();
    validateTime();
    updateSubmitState();
    hideAllDropdowns(); // Ensure dropdowns are hidden when changing time
  });

  timeIn.addEventListener("input", function() {
    hideAllDropdowns(); // Ensure dropdowns are hidden when typing in time input
  });

  timeIn.addEventListener("blur", function() {
    autoAdjustTimeOut();
    validateTime();
    updateSubmitState();
  });

  timeOutExp.addEventListener("change", function() {
    autoAdjustTimeIn();
    validateTime();
    updateSubmitState();
    hideAllDropdowns(); // Ensure dropdowns are hidden when changing time
  });

  timeOutExp.addEventListener("input", function() {
    hideAllDropdowns(); // Ensure dropdowns are hidden when typing in time input
  });

  timeOutExp.addEventListener("blur", function() {
    autoAdjustTimeIn();
    validateTime();
    updateSubmitState();
  });

  /* ========================= CAPTCHA ========================= */
  const captchaVerified = document.getElementById('captchaVerified');
  
  sliderCaptcha({
    id: "captchaLogin",
    width: 280,
    height: 120,
    sliderL: 42,
    sliderR: 9,
    offset: 5,
    barText: "Slide right to verify",
    failedText: "Try again",
    repeatIcon: "fa fa-redo",
    setSrc: function() { 
      return "<?= base_url('src/images/Pic') ?>" + Math.floor(Math.random() * 5) + ".jpg"; 
    },
    onSuccess: function() { 
      captchaVerified.value = "true";
      updateSubmitState();
    },
    onFail: function() { 
      captchaVerified.value = "false";
      updateSubmitState();
    },
    onRefresh: function() { 
      captchaVerified.value = "false";
      updateSubmitState();
    }
  });

  /* ========================= SUBMIT LOGIC ========================= */
  function updateSubmitState() {
    const userValid = validateUser();
    const phoneValid = validatePhone();
    const officerValid = validateOfficer();
    const reasonValid = validateReason();
    const timeValid = validateTime();
    const captchaValid = captchaVerified.value === "true";
    
    // Enable button only if ALL validations pass
    const allValid = userValid && phoneValid && officerValid && reasonValid && timeValid && captchaValid;
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

  // Set default times within office hours
  function getDefaultTimes() {
    const now = new Date();
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
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
    const defaultTimeIn = new Date(today.getTime() + defaultTimeInMinutes * 60 * 1000);
    
    // Set time out to 30 minutes later (max 6:00 PM)
    const defaultTimeOutMinutes = Math.min(defaultTimeInMinutes + 30, 1080);
    const defaultTimeOut = new Date(today.getTime() + defaultTimeOutMinutes * 60 * 1000);
    
    return { 
      defaultTimeIn: formatDateTime(defaultTimeIn), 
      defaultTimeOut: formatDateTime(defaultTimeOut) 
    };
  }

  // Initialize form
  function initializeForm() {
    // Set default times
    const defaultTimes = getDefaultTimes();
    timeIn.value = defaultTimes.defaultTimeIn;
    timeOutExp.value = defaultTimes.defaultTimeOut;
    
    // Update hidden field - extract just the time part from time_out_exp
    const timeOutDate = parseDateTimeString(timeOutExp.value);
    if (timeOutDate) {
      const timeOutTime = timeOutDate.getHours().toString().padStart(2, '0') + ':' + 
                         timeOutDate.getMinutes().toString().padStart(2, '0');
      timeOutReal.value = timeOutTime;
    }
    
    // Trigger validation for all fields
    validateUser();
    validatePhone();
    validateOfficer();
    validateReason();
    validateTime();
    updateSubmitState();
  }

  // Initialize form on load
  initializeForm();

  /* ========================= PREVENT SUBMIT IF INVALID ========================= */
  document.getElementById('manualForm').addEventListener("submit", function(e){
    // Final validation check
    const userValid = validateUser();
    const phoneValid = validatePhone();
    const officerValid = validateOfficer();
    const reasonValid = validateReason();
    const timeValid = validateTime();
    const captchaValid = captchaVerified.value === "true";
    
    if (!(userValid && phoneValid && officerValid && reasonValid && timeValid && captchaValid)) {
      e.preventDefault();
      
      // Show an alert with summary of errors
      let errorMessages = [];
      
      if (!userValid) errorMessages.push("• Pengguna tidak sah");
      if (!phoneValid) errorMessages.push("• Nombor telefon tidak sah");
      if (!officerValid) errorMessages.push("• Pegawai tidak sah");
      if (!reasonValid) errorMessages.push("• Sebab lawatan tidak sah");
      if (!timeValid) errorMessages.push("• Masa lawatan tidak sah");
      if (!captchaValid) errorMessages.push("• CAPTCHA tidak disahkan");
      
      alert("Sila betulkan kesilapan berikut sebelum menghantar:\n\n" + errorMessages.join('\n'));
      
      // Scroll to first error
      const firstError = document.querySelector('.is-invalid');
      if (firstError) {
        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        firstError.focus();
      }
      
      return false;
    }
    
    // Ensure time_out_real is updated before submission
    const timeOutDate = parseDateTimeString(timeOutExp.value);
    if (timeOutDate) {
      const timeOutTime = timeOutDate.getHours().toString().padStart(2, '0') + ':' + 
                         timeOutDate.getMinutes().toString().padStart(2, '0');
      timeOutReal.value = timeOutTime;
    }
    
    return true;
  });
});
</script>

</body>
</html>