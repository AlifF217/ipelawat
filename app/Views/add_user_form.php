<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Tambah Pentadbir Baru</title>
  <style>
    h1 { text-align: center; color: #007a91; }
    form { width: 400px; margin: 30px auto; background: #fff; padding: 25px 30px; border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    label { font-weight: 600; color: #004d52; display: block; margin-bottom: 6px; }
    input, select { width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; margin-bottom: 5px; font-family: inherit; }
    .error { color: red; font-size: 13px; margin-bottom: 10px; display: none; }
    .error2 { color: red; font-size: 20px; margin-bottom: 10px; display: none; }
    .success { color: green; font-size: 14px; margin-bottom: 10px; text-align: center; }
    button { width: 100%; background-color: #00bfa5; color: white; border: none; padding: 10px; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: 0.3s; }
    button:hover { background-color: #009e87; }
    .back-btn { display: block; width: fit-content; margin: 20px auto; background-color: #007a91; color: #fff; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: 0.3s; }
    .back-btn:hover { background-color: #005f6b; }
    .captcha-wrapper { margin-bottom: 12px; }

    /* Modal Overlay */
    #overlay {
      position: fixed;
      top: 0; left: 0; width: 100%; height: 100%;
      background-color: rgba(0, 77, 82, 0.6);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 9999;
      backdrop-filter: blur(4px);
    }
    #messageBox {
      background: #fff;
      border-radius: 16px;
      padding: 30px 40px;
      text-align: center;
      width: 90%;
      max-width: 400px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.25);
      position: relative;
      animation: fadeIn 0.3s ease;
    }
    #messageBox h2 { color: #007a91; margin-bottom: 10px; font-size: 1.6rem; }
    #messageBox p { color: #333; font-size: 1rem; }
    #closeBtn {
      position: absolute;
      top: 10px; right: 15px;
      font-size: 22px;
      cursor: pointer;
      color: #666;
      transition: 0.2s ease;
    }
    #closeBtn:hover { color: #ff6b6b; }
    @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
  </style>
</head>

<body>

<link rel="stylesheet" href="<?= base_url('src/slidercaptcha.css') ?>">
<script src="<?= base_url('js/function.js') ?>"></script>
<?= view('security_prompt') ?>
<link rel="stylesheet" href="<?= base_url('css/menustyle.css') ?>">

<h1>Tambah Pengguna Baru</h1>

<?php if(session()->getFlashdata('error')): ?>
    <div class="error" style="display:block; text-align:center;"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<?php if(session()->getFlashdata('error2')): ?>
    <div class="error2" style="display:block; text-align:center;"><?= session()->getFlashdata('error2') ?></div>
<?php endif; ?>

<?php if(session()->getFlashdata('success')): ?>
    <div class="success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<?php $oldInput = session()->getFlashdata('oldInput') ?? []; ?>

<form id="addUserForm" action="<?= base_url('saveUser') ?>" method="post" novalidate>

  <!-- NAMA -->
  <label for="Name">Nama:</label>
  <input type="text" name="Name" id="Name" maxlength="40" 
         value="<?= isset($oldInput['Name']) ? esc($oldInput['Name']) : '' ?>" required>
  <div id="errorName" class="error">Nama mesti 10–40 aksara, huruf sahaja. Simbol dibenarkan: @ .</div>

  <!-- EMAIL -->
  <label for="Email">Email:</label>
  <input type="email" name="Email" id="Email" maxlength="30" 
         value="<?= isset($oldInput['Email']) ? esc($oldInput['Email']) : '' ?>" required>
  <div id="errorEmail" class="error">Email mesti 10–30 aksara, mengandungi @ dan .</div>

  <!-- PASSWORD -->
  <label for="Password">Kata Laluan:</label>
  <input type="password" name="Password" id="Password" maxlength="25" required>
  <div id="errorPassword" class="error">
    Kata laluan mesti mempunyai minimum:<br>
    • 8 aksara<br>
    • 2 huruf besar<br>
    • 2 huruf kecil<br>
    • 2 nombor<br>
    • 1 simbol khas
  </div>

  <!-- DIVISION -->
  <label for="Division">Bahagian:</label>
  <select name="Division" id="Division" required>
    <option value="">-- Pilih Bahagian --</option>
    <?php if (!empty($divisions)): ?>
        <?php foreach ($divisions as $division): ?>
            <option value="<?= esc($division['name']) ?>" 
                <?= (isset($oldInput['Division']) && $oldInput['Division'] == $division['name']) ? 'selected' : '' ?> >
                <?= esc($division['name']) ?>
            </option>
        <?php endforeach; ?>
    <?php else: ?>
        <option disabled>Tiada bahagian tersedia</option>
    <?php endif; ?>
  </select>

<!-- PHONE -->
<label for="Phone">No Telefon:</label>
<input type="tel" name="Phone" id="Phone" 
       value="<?= isset($oldInput['Phone']) ? esc($oldInput['Phone']) : '' ?>"
       maxlength="10"
       pattern="0[0-9]{9}" 
       title="No telefon mesti 10 digit dan bermula dengan 0"
       required>
<div id="errorPhone" class="error" style="display:none;">
    No telefon mesti 10 digit dan bermula dengan 0.
</div>

<script>
const phoneInput = document.getElementById("Phone");
const errorPhone = document.getElementById("errorPhone");

// Remove non-digit characters instantly
phoneInput.addEventListener("input", function () {
    this.value = this.value.replace(/\D/g, '');
    // Hide error while typing
    errorPhone.style.display = "none";
});

// Validate format on blur
phoneInput.addEventListener("blur", function() {
    const regex = /^0\d{9}$/;
    if (!regex.test(this.value)) {
        errorPhone.style.display = "block";
    } else {
        errorPhone.style.display = "none";
    }
});
</script>


  <!-- ACTIVE -->
  <label for="Active">Status:</label>
  <select name="Active" id="Active" required>
      <option value="1" <?= (isset($oldInput['Active']) && $oldInput['Active'] == 1) ? 'selected' : '' ?>>Aktif</option>
      <option value="0" <?= (isset($oldInput['Active']) && $oldInput['Active'] == 0) ? 'selected' : '' ?>>Tidak Aktif</option>
  </select>

  <!-- CAPTCHA -->
  <div class="captcha-wrapper" id="captchaLoginContainer">
    <div id="captchaLogin" class="my-3"></div>
  </div>
  <input type="hidden" id="captchaVerified" name="captchaVerified" value="false">

  <br><br>
  <div class="submit-area" align="center">
    <button type="submit" class="submit-btn" disabled>💾 Simpan Pengguna</button>
  </div>
</form>

<a href="<?= base_url('userlist') ?>" class="back-btn">← Kembali ke Senarai Pengguna</a>

<!-- Overlay Modal -->
<div id="overlay">
  <div id="messageBox">
    <span id="closeBtn">&times;</span>
    <h2>⚠️ Amaran!</h2>
    <p id="modal-message">Sila selesaikan CAPTCHA sebelum meneruskan aktiviti.</p>
  </div>
</div>

<script src="<?= base_url('src/longbow.slidercaptcha.js') ?>"></script>
<script src="<?= base_url('js/function.js') ?>"></script>

<script>
/* ============================= MODAL ============================= */
const overlay = document.getElementById('overlay');
const modalMessage = document.getElementById('modal-message');
const closeBtn = document.getElementById('closeBtn');

function showMessage(message){
    modalMessage.textContent = message;
    overlay.style.display = "flex";
}

closeBtn.addEventListener("click", () => overlay.style.display = "none");
window.addEventListener('click', e => { if(e.target == overlay) overlay.style.display = "none"; });

/* ============================= INPUT VALIDATION ============================= */
let passwordValid = false;

function validateName() {
    const value = Name.value.trim();
    const regex = /^[A-Za-z@. ]{10,40}$/;
    const valid = regex.test(value);
    errorName.style.display = valid ? "none" : "block";
    return valid;
}

function validateEmail() {
    const value = Email.value.trim();
    const lengthOK = value.length >= 10 && value.length <= 30;
    const containsAt = value.includes("@");
    const containsDot = value.includes(".");
    const allowed = /^[A-Za-z0-9@.]+$/.test(value);

    const valid = lengthOK && containsAt && containsDot && allowed;
    errorEmail.style.display = valid ? "none" : "block";
    return valid;
}

function validatePhone() {
    const value = Phone.value.trim();
    const regex = /^0\d{9}$/;
    const valid = regex.test(value);
    errorPhone.style.display = valid ? "none" : "block";
    return valid;
}

document.addEventListener("DOMContentLoaded", () => {
    const passwordInput = document.getElementById("Password");
    const passwordError = document.getElementById("errorPassword");

    function validatePasswordIndividual() {
        const password = passwordInput.value;
        const errors = [];

        if(password.length < 8) errors.push("Sekurang-kurangnya 8 karakter");
        if((password.match(/[A-Z]/g)||[]).length < 2) errors.push("Sekurang-kurangnya 2 huruf besar");
        if((password.match(/[a-z]/g)||[]).length < 2) errors.push("Sekurang-kurangnya 2 huruf kecil");
        if((password.match(/\d/g)||[]).length < 2) errors.push("Sekurang-kurangnya 2 nombor");
        if((password.match(/[^A-Za-z0-9]/g)||[]).length < 1) errors.push("Sekurang-kurangnya 1 simbol khas");

        if(errors.length>0){
            passwordValid = false;
            passwordError.style.display = "block";
            passwordError.innerHTML = "Kata laluan mestilah mengandungi: <br>• " + errors.join("<br>• ");
        } else {
            passwordValid = true;
            passwordError.style.display = "none";
            passwordError.textContent = "";
        }
        updateSubmitButton();
    }

    passwordInput.addEventListener("input", validatePasswordIndividual);
});

/* ============================= SUBMIT BUTTON LOGIC ============================= */
function updateSubmitButton() {
    const btn = document.querySelector(".submit-btn");
    const captchaOK = document.getElementById("captchaVerified").value === "true";

    if(validateName() && validateEmail() && passwordValid && validatePhone() && captchaOK){
        btn.disabled=false;
        btn.style.display="block";
    } else {
        btn.disabled=true;
        btn.style.display="none";
    }
}

// Event listeners for inputs
[Name, Email, Password, Phone, Division, Active].forEach(el => {
    el.addEventListener("input", updateSubmitButton);
});
[Division, Active].forEach(el => el.addEventListener("change", updateSubmitButton));

/* ============================= CAPTCHA ============================= */
let loginCaptcha = null;

function createCaptchaOptions(id, containerSelector) {
    const container = document.querySelector(containerSelector);
    if(!container) return null;
    const baseWidth = 280, baseHeight = 120;
    const containerWidth = container.offsetWidth || baseWidth;
    const scale = containerWidth / baseWidth;

    return {
        id: id,
        width: containerWidth,
        height: Math.round(baseHeight*scale),
        sliderL: Math.round(42*scale),
        sliderR: Math.round(9*scale),
        offset: Math.max(3, Math.round(5*scale)),
        loadingText:'Loading...',
        failedText:'Try again',
        barText:'Slide right to fill',
        repeatIcon:'fa fa-redo',
        setSrc: function(){ return `<?= base_url('src/images/Pic') ?>${Math.floor(Math.random()*5)}.jpg`; },
        onSuccess: function(){ document.getElementById('captchaVerified').value="true"; updateSubmitButton(); },
        onFail: function(){ document.getElementById('captchaVerified').value="false"; updateSubmitButton(); },
        onRefresh: function(){ document.getElementById('captchaVerified').value="false"; updateSubmitButton(); }
    };
}

function initLoginCaptcha(){
    const el=document.getElementById("captchaLogin");
    if(!el) return;
    el.innerHTML="";
    const btn=document.querySelector('form#addUserForm .submit-btn');
    if(btn) btn.style.display="none";

    loginCaptcha = sliderCaptcha(createCaptchaOptions("captchaLogin","#captchaLoginContainer"));
}

window.addEventListener('DOMContentLoaded', function(){
    initLoginCaptcha();

    const form=document.getElementById('addUserForm');
    if(form){
        form.addEventListener('submit', function(e){
            const verified=document.getElementById('captchaVerified')?.value==="true";
            if(!verified){
                e.preventDefault();
                showMessage("⚠️ Sila selesaikan CAPTCHA sebelum meneruskan aktiviti.");
            }
        });

        // ======= ENTER KEY CHECK =======
        form.addEventListener('keydown', function(event){
            if(event.key==="Enter"){
                const verified=document.getElementById('captchaVerified')?.value==="true";
                if(!verified){
                    event.preventDefault();
                    showMessage("⚠️ Sila selesaikan CAPTCHA sebelum meneruskan aktiviti.");
                }
            }
        });
    }

    // Reinitialize captcha on window resize
    let resizeTimer;
    window.addEventListener('resize', function(){
        clearTimeout(resizeTimer);
        resizeTimer=setTimeout(initLoginCaptcha, 250);
    });
});
</script>
</body>
</html>
