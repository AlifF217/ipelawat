<!DOCTYPE html>
<html lang="en">
<head>
  <style>
    body {
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      background: transparent;
           transform: scale(0.8);
        transform-origin: top left;
        width: 125%; /* prevent content from shrinking horizontally */
    }
    .container {
      width: 100%;
      max-width: 600px;
      min-height: 480px;
      margin: 0 auto;
      border-radius: 12px;
      overflow: hidden;
    }
    /* ======= Overlay Modal Styles ======= */
#overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 77, 82, 0.6); /* turquoise-tinted dark overlay */
  display: none;
  justify-content: center;
  align-items: center;
  z-index: 9999;
  backdrop-filter: blur(4px);
}

#messageBox {
  background: #ffffff;
  border-radius: 16px;
  padding: 30px 40px;
  text-align: center;
  width: 90%;
  max-width: 400px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
  position: relative;
  animation: fadeIn 0.3s ease;
}

#messageBox h2 {
  color: #007a91;
  margin-bottom: 10px;
  font-size: 1.6rem;
}

#messageBox p {
  color: #333;
  font-size: 1rem;
}

#closeBtn {
  position: absolute;
  top: 10px;
  right: 15px;
  font-size: 22px;
  cursor: pointer;
  color: #666;
  transition: 0.2s ease;
}

#closeBtn:hover {
  color: #ff6b6b;
}

/* ======= Animation ======= */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}
  </style>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login dan Daftar</title>

  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdn.bootcss.com/font-awesome/5.7.2/css/all.min.css">
  <!-- Slider Captcha CSS -->
      <link rel="stylesheet" href="<?= base_url('src/slidercaptcha.css') ?>">


  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,800" rel="stylesheet">
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">

</head>


<body>

 

  <!-- Container -->
  <div class="container" id="container" style="transform: scale(0.8); margin-top: 92.125px;">
<!-- Mobile Toggle (only visible on portrait mobile) -->

    <!-- Sign Up -->
    <div class="form-container sign-up-container">
<form action="<?= base_url('/signup') ?>" method="POST" id="signupForm" onsubmit="return validatePassword()">
  <h1>Cipta Akaun Baru</h1>
  <span>Guna E-mel untuk pendaftaran</span>
  <div class="form-fields">
    <input type="text" id ="signupName" name="Name" placeholder="Nama" required maxlength="40" />
      <input type="email" id ="signupEmail" name="Email" placeholder="E-mel" required maxlength="30" />
          <div id="signupEmailError" style="color:red; font-size:0.9em; margin-top:4px;"></div>

    <input type="password" id="signupPassword" name="Password" placeholder="Masukkan Kata Laluan" 
             required minlength="8" maxlength="25">
                   <div id="passwordError" style="color:red; font-size:0.9em; margin-top:4px;"></div>
          <div id="captchaSignupContainer">
            <div id="captchaSignup" class="my-3"></div>
          </div>
    <div id="captchaSignupContainer">
      <div id="captchaSignup" class="my-3"></div>
    </div>
  </div>
  <div class="submit-area" align="center">
    <input type="hidden" id="captchaVerifiedSignup" name="captchaVerifiedSignup" value="false">
    <button type="submit" class="submit-btn" style="display:none;">Daftar</button>
  </div>
</form>
    </div>

    <!-- Sign In -->
    <div class="form-container sign-in-container">
<form action="<?= base_url('/login') ?>" id="loginForm" method="POST" >
        <h1>Log Masuk</h1>
        <span>Guna E-mel dan Kata Laluan</span>
        <div class="form-fields">
           <input type="email" id="loginEmail" name="Email" placeholder="E-mel" required maxlength="30" />
          <div id="loginEmailError" class="error-text"></div>

          <input type="password" id="loginPassword" name="Password" placeholder="Kata Laluan" maxlength="25" required />
          <div id="loginPasswordError" class="error-text"></div>
          <div id="captchaLoginContainer">
            <div id="captchaLogin" class="my-3"></div>
          </div>
        </div>
        <div class="submit-area" align="center">
        <input type="hidden" id="captchaVerifiedLogin" name="captchaVerifiedLogin" value="false">
          <button type="submit" class="submit-btn" style="display:none;">Log Masuk</button>
      <div class="link-area">
          <a href="#">Terlupa Kata Laluan?</a>
        </div>
</div>

      </form>
    </div>

    <!-- Overlay -->
    <div class="overlay-container">
      <div class="overlay">
        <div class="overlay-panel overlay-middle">
          <h1>Selamat Kembali!</h1>
          <p>Sila Log Masuk menggunakan E-mel dan Kata Laluan</p>
          <button class="ghost" id="signIn">Log Masuk</button>
        </div>
        <div class="overlay-panel overlay-right">
          <h1>Selamat Datang!</h1>
          <p>Sila masukkan data untuk Pendaftaran Akaun</p>
          <button class="ghost" id="signUp" >Daftar</button>
        </div>
      </div>
    </div>
  </div>

<div class="mobile-toggle d-md-none">
  <button id="mobileSignIn">Log Masuk</button>
  <button id="mobileSignUp">Daftar</button>
</div>
  <!-- Security Overlay -->
<!--  <div id="overlay">
    <div id="messageBox">
      <span id="closeBtn">&times;</span>
      <h2>Amaran!</h2>
      <p id="modal-message">Aksi ini tidak boleh digunakan.</p>
    </div>
  </div>-->
  <?= view('security_prompt') ?> 

  <!-- Scripts -->
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?= base_url('src/longbow.slidercaptcha.js') ?>"></script>
  <script src="<?= base_url('js/function.js') ?>"></script>

  <script>
    let loginCaptcha, signupCaptcha;

    // Helper to create captcha options
    function createCaptchaOptions(id, containerSelector, submitSelector) {
      const container = document.querySelector(containerSelector);
      if (!container) return null;

      const baseWidth = 280;
      const baseHeight = 120;
      const containerWidth = container.offsetWidth;
      const scale = containerWidth / baseWidth;
      const captchaWidth = containerWidth;
      const captchaHeight = Math.round(baseHeight * scale);

      return {
        id: id,
        width: captchaWidth,
        height: captchaHeight,
        sliderL: Math.round(42 * scale),
        sliderR: Math.round(9 * scale),
        offset: Math.max(3, Math.round(5 * scale)),
        loadingText: 'Sedang memuatkan...',
        failedText: 'cuba lagi',
        barText: 'Selesaikan captcha di atas',
        repeatIcon: 'fa fa-redo',
        setSrc: function () {
               const randomNumber = Math.floor(Math.random() * 5); // 0–4
      return `src/images/Pic${randomNumber}.jpg`;
        },
        onSuccess: function () {
          document.querySelector(submitSelector).style.display = "block";
  // Mark correct hidden field as verified
  if (id === "captchaLogin") {
    document.getElementById("captchaVerifiedLogin").value = "true";
  } else if (id === "captchaSignup") {
    document.getElementById("captchaVerifiedSignup").value = "true";
  }

          },
        onFail: function () {
          document.querySelector(submitSelector).style.display = "none";
        },
        onRefresh: function () {
          document.querySelector(submitSelector).style.display = "none";
        }
      };
    }

    // Init login captcha
    function initLoginCaptcha() {
      const el = document.getElementById("captchaLogin");
      if (!el) return;
      el.innerHTML = "";
      document.querySelector('.sign-in-container .submit-btn').style.display = "none";
      loginCaptcha = sliderCaptcha(
        createCaptchaOptions("captchaLogin", "#captchaLoginContainer", ".sign-in-container .submit-btn")
      );
    }

    // Init signup captcha
    function initSignupCaptcha() {
      const el = document.getElementById("captchaSignup");
      if (!el) return;
      el.innerHTML = "";
      document.querySelector('.sign-up-container .submit-btn').style.display = "none";
      signupCaptcha = sliderCaptcha(
        createCaptchaOptions("captchaSignup", "#captchaSignupContainer", ".sign-up-container .submit-btn")
      );
    }

    // Init on load
    window.onload = function () {
      initLoginCaptcha();
      initSignupCaptcha();
    };

    // Re-init on resize
    window.addEventListener("resize", function () {
      initLoginCaptcha();
      initSignupCaptcha();
    });

    // Reset on panel toggle
    document.getElementById("signIn").addEventListener("click", function () {
      setTimeout(initLoginCaptcha, 500);
    });
    document.getElementById("signUp").addEventListener("click", function () {
      setTimeout(initSignupCaptcha, 500);
    });


  </script>

  <!-- Password Validation -->
    <!-- Real-time Validation (Password + Email) -->
  <script>
document.addEventListener("DOMContentLoaded", () => {
    // ======= Signup Fields =======
    const nameInput = document.getElementById("signupName");
    const emailInput = document.getElementById("signupEmail");
    const passwordInput = document.getElementById("signupPassword");
    const signupButton = document.querySelector('.sign-up-container .submit-btn');

    // Error divs
    const nameError = document.createElement("div");
    nameError.id = "signupNameError";
    nameError.style.color = "red";
    nameError.style.fontSize = "0.9em";
    nameError.style.marginTop = "4px";
    nameInput.insertAdjacentElement("afterend", nameError);

    const emailError = document.getElementById("signupEmailError");
    const passwordError = document.getElementById("passwordError");

    const validDomains = ["selangor.gov.my","gmail.com", "yahoo.com", "outlook.com", "hotmail.com", "icloud.com", "protonmail.com"];

    // ======= Helper to check all signup validations =======
    function checkSignupValidity() {
        const nameValid = nameInput.value.trim().length >= 10 && nameInput.value.trim().length <= 40;
        const emailDomain = emailInput.value.split("@")[1];
        const emailValid = !emailDomain || validDomains.includes(emailDomain.toLowerCase());
        
        const password = passwordInput.value;
        const passwordValid = (
            password.length >= 8 &&
            password.length <= 20 &&
            (password.match(/[A-Z]/g) || []).length >= 2 &&
            (password.match(/[a-z]/g) || []).length >= 2 &&
            (password.match(/\d/g) || []).length >= 2 &&
            (password.match(/[!@#$%^&*(),.?":{}|<>]/g) || []).length >= 1
        );

        const captchaVerified = document.getElementById("captchaVerifiedSignup").value === "true";

        signupButton.style.display = (nameValid && emailValid && passwordValid && captchaVerified) ? "block" : "none";
    }

    // ======= Name Validation =======
    nameInput.addEventListener("input", () => {
        const length = nameInput.value.trim().length;
        if (length < 10) {
            nameError.textContent = "Nama mestilah sekurang-kurangnya 10 karakter";
        } else if (length > 40) {
            nameError.textContent = "Nama tidak boleh melebihi 40 karakter";
        } else {
            nameError.textContent = "";
        }
        checkSignupValidity();
    });

    // ======= Email Validation =======
    emailInput.addEventListener("input", () => {
        const domain = emailInput.value.split("@")[1];
        if (domain && !validDomains.includes(domain.toLowerCase())) {
            emailError.textContent = "⚠️ Sila gunakan Alamat E-mel yang sah (e.g., Gmail, Yahoo, Outlook)";
        } else {
            emailError.textContent = "";
        }
        checkSignupValidity();
    });

    // ======= Password Validation =======
    passwordInput.addEventListener("input", () => {
        const password = passwordInput.value;
        const errors = [];

        if (password.length < 8) errors.push("Sekurang-kurangnya 8 karakter");
        if (password.length > 20) errors.push("Tidak lebih dari 20 karakter");
        if ((password.match(/[A-Z]/g) || []).length < 2) errors.push("Sekurang-kurangnya 2 huruf besar");
        if ((password.match(/[a-z]/g) || []).length < 2) errors.push("Sekurang-kurangnya 2 huruf kecil");
        if ((password.match(/\d/g) || []).length < 2) errors.push("Sekurang-kurangnya 2 nombor");
        if ((password.match(/[!@#$%^&*(),.?":{}|<>]/g) || []).length < 1) errors.push("Sekurang-kurangnya 1 karakter unik");

        if (errors.length > 0) {
            passwordError.innerHTML = "Katalaluan mestilah mengandungi: <br>• " + errors.join("<br>• ");
        } else {
            passwordError.textContent = "";
        }
        checkSignupValidity();
    });

    // ======= CAPTCHA check =======
    document.getElementById("captchaVerifiedSignup").addEventListener("change", checkSignupValidity);

    // ======= Login Fields Validation (Email + Password) =======
    const loginEmail = document.getElementById("loginEmail");
    const loginEmailError = document.getElementById("loginEmailError");
    const loginPassword = document.getElementById("loginPassword");
    const loginPasswordError = document.getElementById("loginPasswordError");

    loginEmail.addEventListener("input", () => {
        const domain = loginEmail.value.split("@")[1];
        if (domain && !validDomains.includes(domain.toLowerCase())) {
            loginEmailError.textContent = "⚠️ Sila gunakan alamat E-mel yang sah";
        } else {
            loginEmailError.textContent = "";
        }
    });

    loginPassword.addEventListener("input", () => {
        if (loginPassword.value.length > 20) {
            loginPasswordError.textContent = "Kata laluan tidak boleh melebihi 20 karakter";
        } else {
            loginPasswordError.textContent = "";
        }
    });
});
</script>


<script>
  // Disable Enter key submission until CAPTCHA verified
  document.addEventListener("keydown", function (event) {
    if (event.key === "Enter") {
      const loginActive = document.querySelector(".sign-in-container").style.display !== "none";
      const signupActive = document.querySelector(".sign-up-container").style.display !== "none";
      
      let verified = false;
      if (loginActive) {
        verified = document.getElementById("captchaVerifiedLogin").value === "true";
      } else if (signupActive) {
        verified = document.getElementById("captchaVerifiedSignup").value === "true";
      }

      if (!verified) {
        event.preventDefault();
        if (typeof showMessage === "function") {
          showMessage("⚠️ Sila selesaikan CAPTCHA sebelum meneruskan aktiviti.");
        } else {
          alert("⚠️ Sila selesaikan CAPTCHA sebelum meneruskan aktiviti.");
        }
      }
    }
  });

  // Prevent login form submission if CAPTCHA not verified
  document.getElementById("loginForm").addEventListener("submit", function (event) {
    const verified = document.getElementById("captchaVerifiedLogin").value === "true";
    if (!verified) {
      event.preventDefault();
      if (typeof showMessage === "function") {
        showMessage("⚠️ Sila selesaikan CAPTCHA sebelum menghantar form.");
      } else {
        alert("⚠️ Sila selesaikan CAPTCHA sebelum menghantar form.");
      }
    }
  });

  // Prevent signup form submission if CAPTCHA not verified
  document.getElementById("signupForm").addEventListener("submit", function (event) {
    const verified = document.getElementById("captchaVerifiedSignup").value === "true";
    if (!verified) {
      event.preventDefault();
      if (typeof showMessage === "function") {
        showMessage("⚠️ Sila selesaikan CAPTCHA sebelum menghantar form.");
      } else {
        alert("⚠️ Sila selesaikan CAPTCHA sebelum menghantar form.");
      }
    }
  });
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const errorMessage = <?= json_encode(session()->getFlashdata('error')) ?>;
    const successMessage = <?= json_encode(session()->getFlashdata('success')) ?>;

    if (errorMessage) {
        if (typeof showMessage === "function") {
            showMessage("❌ " + errorMessage);
        } else {
            alert(errorMessage);
        }
    }

    if (successMessage) {
        if (typeof showMessage === "function") {
            showMessage("✅ " + successMessage);
        } else {
            alert(successMessage);
        }
    }
});
</script>

<script>
  // Get PHP flashdata messages
  const phpError = <?= json_encode(session()->getFlashdata('error')) ?>;
  const phpSuccess = <?= json_encode(session()->getFlashdata('success')) ?>;

  // If message exists, show it using showMessage()
  window.addEventListener("load", function () {
    if (phpError) {
      showMessage("❌ " + phpError);
    } else if (phpSuccess) {
      showMessage("✅ " + phpSuccess);
    }
  });
</script>

</body>
</html>
