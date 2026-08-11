<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit User</title>
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('src/slidercaptcha.css') ?>">
  <style>
    body { font-family: 'Montserrat', sans-serif; background: #e0f7f9; }
    .form-container { background: #fff; max-width: 700px; margin: 40px auto; padding: 25px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    h2 { text-align: center; color: #007a91; }
    label { display: block; margin-top: 15px; font-weight: 600; }
    input, select { width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc; }
    .error { color: red; font-size: 13px; margin-top: 3px; display: none; }
    button { width: 100%; background-color: #00bfa5; color: white; border: none; padding: 10px; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: 0.3s; }
    button:disabled { background-color: #ccc; cursor: not-allowed; }
    button:hover:not(:disabled) { background-color: #009e87; }
    .back-btn { display: block; width: fit-content; margin: 20px auto; background-color: #007a91; color: #fff; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: 0.3s; }
    .back-btn:hover { background-color: #005f6b; }
    .captcha-wrapper { margin-bottom: 12px; }

    /* Overlay Modal */
    #overlay { position: fixed; top:0; left:0; width:100%; height:100%; background-color: rgba(0,77,82,0.6); display:none; justify-content:center; align-items:center; z-index:9999; backdrop-filter: blur(4px); }
    #messageBox { background:#fff; border-radius:16px; padding:30px 40px; text-align:center; width:90%; max-width:400px; box-shadow:0 4px 15px rgba(0,0,0,0.25); position:relative; animation: fadeIn 0.3s ease; }
    #messageBox h2 { color:#007a91; margin-bottom:10px; font-size:1.6rem; }
    #messageBox p { color:#333; font-size:1rem; }
    #closeBtn { position:absolute; top:10px; right:15px; font-size:22px; cursor:pointer; color:#666; transition:0.2s ease; }
    #closeBtn:hover { color:#ff6b6b; }
    @keyframes fadeIn { from { opacity:0; transform:scale(0.95); } to { opacity:1; transform:scale(1); } }
  </style>
</head>
<body>

<script src="<?= base_url('js/function.js') ?>"></script>

<div class="form-container">
  <?php if(session()->getFlashdata('success')): ?>
<div style="color:green; font-weight:bold; margin-bottom:15px;">
    <?= session()->getFlashdata('success') ?>
</div>
<?php endif; ?>

<?php if(session()->getFlashdata('error')): ?>
<div style="color:red; font-weight:bold; margin-bottom:15px;">
    <?= session()->getFlashdata('error') ?>
</div>
<?php endif; ?>

  <h2>Edit Pentadbir Lain</h2>
  <form id="editUserForm" action="<?= base_url('updateUser/' . $user['Id']) ?>" method="post" novalidate>

    <!-- Name -->
    <label for="Name">Nama</label>
    <input type="text" name="Name" id="Name" value="<?= esc($user['Name']) ?>" required>
    <div id="errorName" class="error">Nama limit: min 10, max 40 characters (A-Z, a-z, @, .)</div>

    <!-- Email -->
    <label for="Email">Email</label>
    <input type="email" name="Email" id="Email" value="<?= esc($user['Email']) ?>" required>
    <div id="errorEmail" class="error">Email: min 10, max 30 characters, letters, numbers, @ and . allowed</div>

    <!-- Phone -->
    <label for="Phone">No Telefon</label>
    <input type="text" name="Phone" id="Phone" value="<?= esc($user['Phone'] ?? '') ?>" required maxlength="10">
    <div id="errorPhone" class="error">Phone Number limit: 10 numerical characters, must start with 0. No alphabets.</div>

    <!-- Password -->
    <label for="Password">Kata Laluan</label>
    <input type="password" name="Password" id="Password" maxlength="25">
    <div id="errorPassword" class="error">
      Kata laluan mestilah mengandungi:<br>
      • Sekurang-kurangnya 8 karakter<br>
      • Sekurang-kurangnya 2 huruf besar<br>
      • Sekurang-kurangnya 2 huruf kecil<br>
      • Sekurang-kurangnya 2 nombor<br>
      • Sekurang-kurangnya 1 karakter unik
    </div>

    <!-- Division -->
    <label for="Division">Bahagian</label>
    <select name="Division" id="Division" required>
      <option value="">-- Pilih Bahagian --</option>
      <?php if (!empty($divisions)): foreach($divisions as $division): ?>
      <option value="<?= esc($division['name']) ?>" <?= ($user['Division'] == $division['id']) ? 'selected' : '' ?>><?= esc($division['name']) ?></option>
      <?php endforeach; else: ?>
      <option disabled>Tiada bahagian tersedia</option>
      <?php endif; ?>
    </select>

    <!-- Active -->
    <label for="Active">Status</label>
    <select name="Active" id="Active" required>
      <option value="Aktif" <?= ($user['Active']==='Aktif')?'selected':'' ?>>Aktif</option>
      <option value="Tidak Aktif" <?= ($user['Active']==='Tidak Aktif')?'selected':'' ?>>Tidak Aktif</option>
    </select>

    <!-- CAPTCHA -->
    <div class="captcha-wrapper" id="captchaLoginContainer">
      <div id="captchaLogin" class="my-3"></div>
    </div>
    <input type="hidden" id="captchaVerified" name="captchaVerified" value="false">

<br><br>
    <div style="margin-top: 20px; text-align:center;">
      <button type="submit" class="submit-btn" disabled>💾 Simpan Pengguna</button>
    </div>
  </form>
  <a href="<?= base_url('userlist') ?>" class="back-btn">← Kembali</a>
</div>

<!-- Overlay Modal -->
<div id="overlay">
  <div id="messageBox">
    <span id="closeBtn">&times;</span>
    <h2>⚠️ Amaran!</h2>
    <p id="modal-message">Sila selesaikan CAPTCHA sebelum meneruskan aktiviti.</p>
  </div>
</div>

<script src="<?= base_url('src/longbow.slidercaptcha.js') ?>"></script>
<script>
const overlay = document.getElementById('overlay');
const modalMessage = document.getElementById('modal-message');
const closeBtn = document.getElementById('closeBtn');
function showMessage(message){ modalMessage.textContent=message; overlay.style.display='flex'; }
closeBtn.addEventListener("click",()=>overlay.style.display="none");
window.addEventListener("click",e=>{if(e.target==overlay) overlay.style.display="none";});

// Disable right-click & keyboard shortcuts
document.addEventListener("contextmenu",e=>{ e.preventDefault(); showMessage("⚠️ Right-click is disabled on this page."); });
document.addEventListener("keydown",e=>{
  if(e.ctrlKey&&(e.key.toLowerCase()==="s"||e.key.toLowerCase()==="u")){ e.preventDefault(); showMessage("⚠️ This keyboard shortcut is disabled."); }
  if(e.ctrlKey&&e.shiftKey&&["i","j","c"].includes(e.key.toLowerCase())){ e.preventDefault(); showMessage("⚠️ Developer tools are disabled."); }
  if(e.key==="F12"){ e.preventDefault(); showMessage("⚠️ Developer tools are disabled."); }
});

// ====== Input Validation ======
let passwordValid = false;
const NameInput = document.getElementById("Name");
const EmailInput = document.getElementById("Email");
const PhoneInput = document.getElementById("Phone");
const PasswordInput = document.getElementById("Password");
const DivisionInput = document.getElementById("Division");
const ActiveInput = document.getElementById("Active");

function validateName(){
  const value = NameInput.value.trim();
  const valid = /^[A-Za-z@. ]{10,40}$/.test(value);
  document.getElementById("errorName").style.display = valid?"none":"block";
  return valid;
}
function validateEmail(){
  const value = EmailInput.value.trim();
  const lengthOK = value.length>=10 && value.length<=30;
  const allowed = /^[A-Za-z0-9@.]+$/.test(value);
  const valid = lengthOK && allowed && value.includes("@") && value.includes(".");
  document.getElementById("errorEmail").style.display=valid?"none":"block";
  return valid;
}
function validatePhone(){
  const value = PhoneInput.value.trim();
  const valid = /^0\d{9}$/.test(value);
  document.getElementById("errorPhone").style.display=valid?"none":"block";
  return valid;
}
function validatePassword(){
  const pw = PasswordInput.value;
  const errors=[];
  if(pw.length<8) errors.push("Sekurang-kurangnya 8 karakter");
  if((pw.match(/[A-Z]/g)||[]).length<2) errors.push("Sekurang-kurangnya 2 huruf besar");
  if((pw.match(/[a-z]/g)||[]).length<2) errors.push("Sekurang-kurangnya 2 huruf kecil");
  if((pw.match(/\d/g)||[]).length<2) errors.push("Sekurang-kurangnya 2 nombor");
  if((pw.match(/[^A-Za-z0-9]/g)||[]).length<1) errors.push("Sekurang-kurangnya 1 karakter unik");
  if(errors.length>0){ passwordValid=false; document.getElementById("errorPassword").innerHTML="Kata laluan mestilah mengandungi:<br>• "+errors.join("<br>• "); document.getElementById("errorPassword").style.display="block"; }
  else { passwordValid=true; document.getElementById("errorPassword").style.display="none"; }
  return passwordValid;
}

function updateSubmitButton(){
  const captchaOK = document.getElementById("captchaVerified").value==="true";
  const btn=document.querySelector(".submit-btn");
  if(validateName() && validateEmail() && validatePhone() && validatePassword() && DivisionInput.value && ActiveInput.value && captchaOK){ btn.disabled=false; btn.style.display="block"; }
  else{ btn.disabled=true; btn.style.display="none"; }
}

// Input events
[NameInput, EmailInput, PhoneInput, PasswordInput, DivisionInput, ActiveInput].forEach(el=>{
  el.addEventListener("input", updateSubmitButton);
});
[DivisionInput, ActiveInput].forEach(el=>el.addEventListener("change", updateSubmitButton));
PasswordInput.addEventListener("input", validatePassword);

// ===== CAPTCHA =====
function createCaptchaOptions(id, containerSelector, submitSelector){
  const container=document.querySelector(containerSelector);
  if(!container) return null;
  const baseWidth=280, baseHeight=120;
  const containerWidth=container.offsetWidth||baseWidth;
  const scale=containerWidth/baseWidth;
  return {
    id:id,
    width:containerWidth,
    height:Math.round(baseHeight*scale),
    sliderL:Math.round(42*scale),
    sliderR:Math.round(9*scale),
    offset:Math.max(3,Math.round(5*scale)),
    loadingText:'Loading...',
    failedText:'Try again',
    barText:'Slide right to fill',
    repeatIcon:'fa fa-redo',
    setSrc:()=>`<?= base_url('src/images/Pic') ?>${Math.floor(Math.random()*5)}.jpg`,
    onSuccess:()=>{ document.getElementById('captchaVerified').value="true"; updateSubmitButton(); },
    onFail:()=>{ document.getElementById('captchaVerified').value="false"; updateSubmitButton(); },
    onRefresh:()=>{ document.getElementById('captchaVerified').value="false"; updateSubmitButton(); }
  };
}

function initLoginCaptcha(){
  const el=document.getElementById("captchaLogin");
  if(!el) return;
  el.innerHTML="";
  const btn=document.querySelector('form#editUserForm .submit-btn');
  if(btn){ btn.disabled=true; btn.style.display="none"; }
  sliderCaptcha(createCaptchaOptions("captchaLogin","#captchaLoginContainer",'form#editUserForm .submit-btn'));
}

window.addEventListener('DOMContentLoaded',()=>{
  initLoginCaptcha();
  document.getElementById('editUserForm').addEventListener('submit', e=>{
    if(document.getElementById('captchaVerified').value!=="true"){ e.preventDefault(); showMessage("⚠️ Sila selesaikan CAPTCHA sebelum meneruskan aktiviti."); }
  });
  window.addEventListener('resize',()=>{
    clearTimeout(window.resizeCaptchaTimer);
    window.resizeCaptchaTimer=setTimeout(initLoginCaptcha,250);
  });
});
</script>
</body>
</html>
