<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Menu</title>

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,800&display=swap" rel="stylesheet">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdn.bootcss.com/font-awesome/5.7.2/css/all.min.css">

  <link rel="stylesheet" href="<?= base_url('css/menustyle.css') ?>">
  <script src="<?= base_url('js/function.js') ?>"></script>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<body>

  <!-- Responsive Bootstrap Navbar -->
  <header>
    <nav class="navbar navbar-expand-lg navbar-dark px-3">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu" aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarMenu">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a href="<?= base_url('profile') ?>" class="nav-link">👤 Profile</a>
            </li>
            <li class="nav-item">
              <a href="<?= base_url('logout') ?>" class="nav-link logout">🚪 Logout</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>

  <main class="text-center mt-5">
    <h2>Welcome, <?= esc($name) ?> 🎉</h2>
    <p>You have successfully logged in.</p>
  </main>

  <!-- Security Overlay -->
  <div id="overlay">
    <div id="messageBox">
      <span id="closeBtn">&times;</span>
      <h2>Attention!</h2>
      <p id="modal-message">This action is disabled for security reasons.</p>
    </div>
  </div>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- ================== Security Restrictions ================== -->
  <script>
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
    document.addEventListener("contextmenu", function(e) {
      e.preventDefault();
      showMessage("⚠️ Right-click is disabled on this page.");
    });

    // Disable specific keyboard shortcuts
    document.addEventListener("keydown", function(e) {
      // Ctrl+S or Ctrl+U
      if (e.ctrlKey && (e.key.toLowerCase() === "s" || e.key.toLowerCase() === "u")) {
        e.preventDefault();
        showMessage("⚠️ This keyboard shortcut is disabled.");
      }

      // Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+Shift+C
      if (e.ctrlKey && e.shiftKey && 
          (e.key.toLowerCase() === "i" || e.key.toLowerCase() === "j" || e.key.toLowerCase() === "c")) {
        e.preventDefault();
        showMessage("⚠️ Developer tools are disabled.");
      }

      // F12
      if (e.key === "F12") {
        e.preventDefault();
        showMessage("⚠️ Developer tools are disabled.");
      }
    });

    // Hide overlay on load
    window.addEventListener("load", function() {
      const overlay = document.getElementById("overlay");
      overlay.style.display = "none";
    });
  </script>

<script>
  // Display backend messages (error or success) from CodeIgniter using showMessage()
  document.addEventListener("DOMContentLoaded", function () {
    const errorMessage = <?= json_encode(session()->getFlashdata('error')) ?>;
    const successMessage = <?= json_encode(session()->getFlashdata('success')) ?>;

    if (errorMessage) {
      if (typeof showMessage === "function") {
        showMessage(errorMessage);
      } else {
        alert(errorMessage);
      }
    }

    if (successMessage) {
      if (typeof showMessage === "function") {
        showMessage(successMessage);
      } else {
        alert(successMessage);
      }
    }
  });
</script>

</body>
</html>
