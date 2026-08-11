// ==========================
// Toggle Sign In / Sign Up Panels
// ==========================
const signUpButton = document.getElementById('signUp');
const signInButton = document.getElementById('signIn');
const container = document.getElementById("container");

signUpButton.addEventListener('click', () => {
  container.classList.add("right-panel-active");
});

signInButton.addEventListener('click', () => {
  container.classList.remove("right-panel-active");
});

// ==========================
// Security Restrictions
// ==========================
function showMessage(message) {
  const overlay = document.getElementById("overlay");
  const messageText = document.getElementById("modal-message");
  messageText.innerText = message;
  overlay.style.display = "flex";
}

function hideMessage() {
  document.getElementById("overlay").style.display = "none";
}

document.getElementById("closeBtn").addEventListener("click", hideMessage);
document.getElementById("overlay").addEventListener("click", function(e) {
  if (e.target.id === "overlay") hideMessage();
});

// Disable right-click
document.addEventListener('contextmenu', function (e) {
  e.preventDefault();
  showMessage("⚠️ Klik-kanan tidak boleh digunakan di paparan ini.");
});

// Disable certain keyboard shortcuts
document.addEventListener('keydown', function (e) {
  if ((e.ctrlKey && (e.key.toLowerCase() === 's' || e.key.toLowerCase() === 'u'))) {
    e.preventDefault();
    showMessage("⚠️ Pintasan papan kekunci ini tidak boleh digunakan.");
  }

  if (e.ctrlKey && e.shiftKey && 
      (e.key.toLowerCase() === 'i' || e.key.toLowerCase() === 'j' || e.key.toLowerCase() === 'c')) {
    e.preventDefault();
    showMessage("⚠️ Alat pembangun tidak boleh digunakan.");
  }

  if (e.key === 'F12') {
    e.preventDefault();
    showMessage("⚠️ Alat pembangun tidak boleh digunakan.");
  }


});

// ==========================
// Show stored registration message if exists
// ==========================
window.addEventListener("load", function () {
  // Always hide on load
  const overlay = document.getElementById("overlay");
  overlay.style.display = "none";

  // Only show if popupMessage is found
  const msg = localStorage.getItem("popupMessage");
  if (msg && msg.trim() !== "") {
    showMessage(msg);
    localStorage.removeItem("popupMessage"); // clear after showing
  }
});

// ==========================
// Toggle for Mobile Portrait (Vertical Slide)
// ==========================
const mobileSignUpBtn = document.getElementById("mobileSignUp");
const mobileSignInBtn = document.getElementById("mobileSignIn");

function updateMobileToggle() {
  if (!container.classList.contains("portrait-mode")) {
    // hide buttons outside portrait mode
    if (mobileSignUpBtn) mobileSignUpBtn.style.display = "none";
    if (mobileSignInBtn) mobileSignInBtn.style.display = "none";
    return;
  }

  if (container.classList.contains("mobile-signup-active")) {
    // Currently on Sign Up → show Sign In button
    if (mobileSignInBtn) mobileSignInBtn.style.display = "inline-block";
    if (mobileSignUpBtn) mobileSignUpBtn.style.display = "none";
  } else {
    // Currently on Sign In → show Sign Up button
    if (mobileSignUpBtn) mobileSignUpBtn.style.display = "inline-block";
    if (mobileSignInBtn) mobileSignInBtn.style.display = "none";
  }
}

// Mobile button actions
if (mobileSignUpBtn && mobileSignInBtn) {
  mobileSignUpBtn.addEventListener("click", () => {
    container.classList.add("mobile-signup-active");
    setTimeout(initSignupCaptcha, 500);
    updateMobileToggle();
  });

  mobileSignInBtn.addEventListener("click", () => {
    container.classList.remove("mobile-signup-active");
    setTimeout(initLoginCaptcha, 500);
    updateMobileToggle();
  });
}

// ==========================
// Orientation Detection
// ==========================
function updateLayoutMode() {
  const isPortrait = window.matchMedia("(orientation: portrait)").matches;
  if (isPortrait) {
    container.classList.add("portrait-mode");
    container.classList.remove("right-panel-active"); // reset desktop toggle
  } else {
    container.classList.remove("portrait-mode");
    container.classList.remove("mobile-signup-active"); // reset mobile toggle
  }
  updateMobileToggle(); // keep buttons 

  
}


// Run on load + on orientation change
window.addEventListener("load", updateLayoutMode);
window.addEventListener("resize", updateLayoutMode);


function scaleContainerLandscape() {
  const container = document.getElementById('container');
  if (!container) return;

  const vw = window.innerWidth;
  const vh = window.innerHeight;

  const desktopWidth = 960;
  const desktopHeight = 600;

  // Detect if device is landscape
  const isLandscape = window.matchMedia("(orientation: landscape)").matches;

  if (isLandscape && vw <= 1024) { // apply only on mobile landscape
    // Scale based on width and height, but never exceed 1
    const scale = Math.min(vw / desktopWidth, vh / desktopHeight, 1);
    container.style.transform = `scale(${scale})`;
    container.style.marginTop = `${(vh - desktopHeight * scale) / 2}px`; // vertical centering
  } else {
    // Portrait or desktop: reset scale and margin
    container.style.transform = '';
    container.style.marginTop = '';
  }
}

// Run on load + resize
window.addEventListener('load', scaleContainerLandscape);
window.addEventListener('resize', scaleContainerLandscape);
window.addEventListener('orientationchange', scaleContainerLandscape);


//disable enter on forms
