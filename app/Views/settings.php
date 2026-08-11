<?php
// Load session and UserModel
$session = session();
$userModel = new \App\Models\UserModel();

// Get user_id from session
$userId = $session->get('user_id');

// Fetch the full user record from DB
$user = $userModel->find($userId);
$email = $user ? $user['Email'] : 'Not available';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Settings</title>

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,800&display=swap" rel="stylesheet">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.bootcss.com/font-awesome/5.7.2/css/all.min.css">
  <link rel="stylesheet" href="<?= base_url('css/menustyle.css') ?>">
</head>
<body>

<header>
  <nav class="navbar navbar-expand-lg navbar-dark px-3">
    <div class="container-fluid">
      <a class="navbar-brand" href="<?= base_url('menu') ?>">Dashboard</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarMenu">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a href="<?= base_url('profile') ?>" class="nav-link">👤 Profile</a>
          </li>
          <li class="nav-item">
            <a href="<?= base_url('settings') ?>" class="nav-link active">⚙️ Settings</a>
          </li>
          <li class="nav-item">
            <a href="<?= base_url('logout') ?>" class="nav-link logout">🚪 Logout</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</header>

<main>
  <div class="container d-flex justify-content-center align-items-center mt-5">
    <div class="settings-card p-4 shadow rounded">
      <h2>Settings</h2>
      <p><strong>Name:</strong> <?= esc($session->get('name')) ?></p>
      <p><strong>Email:</strong> <?= esc($email) ?></p>
      <p>Here you can update your preferences (feature coming soon!)</p>
      <a href="<?= base_url('menu') ?>" class="btn btn-custom mt-3">⬅ Back to Menu</a>
    </div>
  </div>
</main>

<!-- Security Overlay -->
  <div id="overlay">
    <div id="messageBox">
      <span id="closeBtn">&times;</span>
      <h2>Attention!</h2>
      <p id="modal-message">This action is disabled for security reasons.</p>
    </div>
  </div>

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

  
</body>
</html>
