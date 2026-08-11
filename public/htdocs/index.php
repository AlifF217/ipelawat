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
    }
    .container {
      width: 100%;
      max-width: 600px;
      min-height: 480px;
      margin: 0 auto;
      border-radius: 12px;
      overflow: hidden;
    }
  </style>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login & Signup</title>

  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdn.bootcss.com/font-awesome/5.7.2/css/all.min.css">
  <!-- Slider Captcha CSS -->
  <link rel="stylesheet" href="src/slidercaptcha.css">
  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,800" rel="stylesheet">
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="style.css">

</head>


<body>
  <!-- Container -->
  <div class="container" id="container">
<!-- Mobile Toggle (only visible on portrait mobile) -->

    <!-- Sign Up -->
    <div class="form-container sign-up-container">
      <form action="signup.php" method="POST">
        <h1>Create Account</h1>
        <span>Use your email for registration</span>
        <div class="form-fields">
          <input type="text" name="Name" placeholder="Name" required />
          <input type="email" name="Email" placeholder="Email" required />
          <input type="password" name="Password" placeholder="Password" required />
          <div id="captchaSignupContainer">
            <div id="captchaSignup" class="my-3"></div>
          </div>
        </div>
        <div class="submit-area" align="center">
          <button type="submit" class="submit-btn" style="display:none;">Sign Up</button>
        </div>
      </form>
    </div>

    <!-- Sign In -->
    <div class="form-container sign-in-container">
      <form action="login.php" method="POST">
        <h1>Log in</h1>
        <span>Use your email and password</span>
        <div class="form-fields">
          <input type="email" name="Email" placeholder="Email" required />
          <input type="password" name="Password" placeholder="Password" required />
          <div id="captchaLoginContainer">
            <div id="captchaLogin" class="my-3"></div>
          </div>
        </div>
        <div class="submit-area" align="center">
    
          <button type="submit" class="submit-btn" style="display:none;">Log In</button>
      <div class="link-area">
          <a href="#">Forgot your password?</a>
        </div>
</div>

      </form>
    </div>

    <!-- Overlay -->
    <div class="overlay-container">
      <div class="overlay">
        <div class="overlay-panel overlay-middle">
          <h1>Welcome Back!</h1>
          <p>To keep connected, please login with your personal info</p>
          <button class="ghost" id="signIn">Sign In</button>
        </div>
        <div class="overlay-panel overlay-right">
          <h1>Hello, Friend!</h1>
          <p>Enter your personal details and start your journey with us</p>
          <button class="ghost" id="signUp" >Sign Up</button>
        </div>
      </div>
    </div>
  </div>

<div class="mobile-toggle d-md-none">
  <button id="mobileSignIn">Sign In</button>
  <button id="mobileSignUp">Sign Up</button>
</div>

  <!-- Security Overlay -->
  <div id="overlay">
    <div id="messageBox">
      <span id="closeBtn">&times;</span>
      <h2>Attention!</h2>
      <p id="modal-message">This action is disabled for security reasons.</p>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="function.js"></script>
  <script src="src/longbow.slidercaptcha.js"></script>

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
        loadingText: 'Loading...',
        failedText: 'Try again',
        barText: 'Slide right to fill',
        repeatIcon: 'fa fa-redo',
        setSrc: function () {
          return 'src/images/Pic1.jpg';
        },
        onSuccess: function () {
          document.querySelector(submitSelector).style.display = "block";
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
</body>
</html>
