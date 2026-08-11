<div class="container mt-5">
  <div class="card shadow p-4 mx-auto" style="max-width: 500px;">
    <h3 class="text-center mb-3">Edit Profile</h3>

    <?= view('security_prompt') ?>

    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger">
        <?= implode('<br>', (array) session()->getFlashdata('error')) ?>
      </div>
    <?php elseif (session()->getFlashdata('success')): ?>
      <div class="alert alert-success">
        <?= esc(session()->getFlashdata('success')) ?>
      </div>
    <?php endif; ?>

    <div class="text-center mb-3">
      <img src="<?= esc($profilePic) ?>" alt="Profile Picture" class="profile-img">
    </div>

    <form action="<?= base_url('profile/update') ?>" method="post" enctype="multipart/form-data" id="editProfileForm">
      <?= csrf_field() ?>

      <!-- NAME -->
      <div class="mb-3">
        <label class="form-label">Nama</label>
        <input type="text" id="Name" name="Name" class="form-control"
               value="<?= esc($name) ?>" maxlength="40" placeholder="Masukkan nama penuh anda">
        <div id="errorName" class="text-danger mt-1" style="display:none;">
          Nama mesti 10–40 aksara. Huruf sahaja. Simbol dibenarkan: @ dan .
        </div>
      </div>

      <!-- EMAIL -->
      <div class="mb-3">
        <label class="form-label">E-mel</label>
        <input type="text" id="Email" name="Email" class="form-control"
               value="<?= esc($email) ?>" maxlength="30" placeholder="Masukkan e-mel anda">
        <div id="errorEmail" class="text-danger mt-1" style="display:none;">
          Email mesti 10–30 aksara, mengandungi @ dan .
        </div>
      </div>

      <!-- PHONE -->
      <div class="mb-3">
        <label class="form-label">Nombor Telefon</label>
        <input type="tel" id="Phone" name="Phone" class="form-control" maxlength="10"
               value="<?= esc($phone) ?>" placeholder="Masukkan nombor telefon anda"
               pattern="0[0-9]{9}" title="Nombor telefon mesti 10 digit dan bermula dengan 0" required>
        <div id="errorPhone" class="text-danger mt-1" style="display:none;">
          Nombor telefon mesti 10 digit dan bermula dengan 0.
        </div>
      </div>

      <!-- DIVISION -->
      <div class="mb-3">
        <label class="form-label">Bahagian</label>
        <select name="Division" id="division" class="form-select" required>
          <option value="">-- pilih Bahagian --</option>
          <?php foreach ($divisions as $d): ?>
            <option value="<?= esc($d['name']) ?>" <?= ($division == $d['name']) ? 'selected' : '' ?>>
              <?= esc($d['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- PROFILE PICTURE -->
      <div class="mb-3">
        <label class="form-label">Gambar Profil</label>
        <input type="file" name="ProfilePicture" id="profile_picture" class="form-control">
        <div class="form-text">Maksimum 2MB, JPG/PNG sahaja.</div>
        <div id="fileError" class="text-danger mt-1" style="display:none;"></div>
      </div>

      <button type="submit" id="submitBtn" class="btn btn-primary w-100">💾 Simpan Perubahan</button>
      <a href="<?= base_url('profile') ?>" class="btn btn-secondary w-100 mt-2">⬅ Kembali</a>
    </form>
  </div>
</div>

<script>
/* ============================================================
   EXISTING INPUT ELEMENTS
============================================================ */
const nameInput = document.getElementById("Name");
const emailInput = document.getElementById("Email");
const phoneInput = document.getElementById("Phone");
const submitBtn = document.getElementById("submitBtn");

/* Error elements */
const errorName = document.getElementById("errorName");
const errorEmail = document.getElementById("errorEmail");
const errorPhone = document.getElementById("errorPhone");

/* ============================================================
   EXISTING VALIDATION FUNCTIONS
============================================================ */
function validateName() {
    const value = nameInput.value.trim();
    const regex = /^[A-Za-z@.\s]{10,40}$/;
    const valid = regex.test(value);
    errorName.style.display = valid ? "none" : "block";
    return valid;
}

function validateEmail() {
    const value = emailInput.value.trim();
    const lengthOK = value.length >= 10 && value.length <= 30;
    const containsAt = value.includes("@");
    const containsDot = value.includes(".");
    const allowed = /^[A-Za-z0-9@.]+$/.test(value);
    const valid = lengthOK && containsAt && containsDot && allowed;
    errorEmail.style.display = valid ? "none" : "block";
    return valid;
}

function validatePhone() {
    const value = phoneInput.value.trim();
    const regex = /^0\d{9}$/;
    const valid = regex.test(value);
    errorPhone.style.display = valid ? "none" : "block";
    return valid;
}

/* ============================================================
   FILE VALIDATION (UNCHANGED)
============================================================ */
const fileInput = document.getElementById('profile_picture');
const fileError = document.getElementById('fileError');

fileInput.addEventListener('change', () => {
    const file = fileInput.files[0];
    if (!file) {
      fileError.style.display = 'none';
      updateSubmitButton();
      return;
    }
    const allowedTypes = ['image/jpeg', 'image/png'];
    const maxSize = 2 * 1024 * 1024;
    if (!allowedTypes.includes(file.type)) {
      fileError.textContent = 'Format tidak sah! Sila pilih JPG atau PNG.';
      fileError.style.display = 'block';
      submitBtn.disabled = true;
    } else if (file.size > maxSize) {
      fileError.textContent = 'Saiz fail melebihi 2MB!';
      fileError.style.display = 'block';
      submitBtn.disabled = true;
    } else {
      fileError.style.display = 'none';
      updateSubmitButton();
    }
});

/* ============================================================
   UPDATE SUBMIT BUTTON
============================================================ */
function updateSubmitButton() {
    const allValid = validateName() && validateEmail() && validatePhone();
    submitBtn.disabled = !allValid;
}

/* Attach listeners */
[nameInput, emailInput, phoneInput].forEach(el => {
    el.addEventListener("input", updateSubmitButton);
});

/* ============================================================
   ERROR MODAL FUNCTION (showMessage)
============================================================ */
function showMessage(message) {
  let overlay = document.getElementById("overlay");
  let messageBox = document.getElementById("modal-message");
  if (!overlay) {
    // Create overlay dynamically if not exists
    overlay = document.createElement("div");
    overlay.id = "overlay";
    overlay.style.position = "fixed";
    overlay.style.top = "0";
    overlay.style.left = "0";
    overlay.style.width = "100%";
    overlay.style.height = "100%";
    overlay.style.backgroundColor = "rgba(0,77,82,0.6)";
    overlay.style.display = "flex";
    overlay.style.justifyContent = "center";
    overlay.style.alignItems = "center";
    overlay.style.zIndex = "9999";
    overlay.innerHTML = `
      <div id="messageBox" style="background:#fff; border-radius:16px; padding:30px 40px; text-align:center; max-width:400px; box-shadow:0 4px 15px rgba(0,0,0,0.25); position:relative;">
        <span id="closeBtn" style="position:absolute; top:10px; right:15px; font-size:22px; cursor:pointer; color:#666;">&times;</span>
        <h2 style="color:#007a91;">Amaran!</h2>
        <p id="modal-message"></p>
      </div>
    `;
    document.body.appendChild(overlay);
    document.getElementById("closeBtn").addEventListener("click", () => {
      overlay.style.display = "none";
    });
    overlay.addEventListener("click", (e) => {
      if (e.target.id === "overlay") overlay.style.display = "none";
    });
    messageBox = document.getElementById("modal-message");
  }
  messageBox.textContent = message;
  overlay.style.display = "flex";
}

/* ============================================================
   FORM SUBMISSION: ERROR CHECKING
============================================================ */
document.getElementById("editProfileForm").addEventListener("submit", (e) => {
    let valid = true;

    if (!validateName()) {
        showMessage("⚠️ Nama mesti 10–40 aksara. Huruf sahaja. Simbol dibenarkan: @ dan .");
        valid = false;
    }
    if (!validateEmail()) {
        showMessage("⚠️ Email mesti 10–30 aksara, mengandungi @ dan .");
        valid = false;
    }
    if (!validatePhone()) {
        showMessage("⚠️ Nombor telefon mesti 10 digit dan bermula dengan 0.");
        valid = false;
    }
    if (!valid) e.preventDefault();
});
</script>
