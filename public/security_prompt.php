<!-- Security Overlay -->
  <div id="overlay">
    <div id="messageBox">
      <span id="closeBtn">&times;</span>
      <h2>Amaran!</h2>
      <p id="modal-message">Aksi ini tidak boleh digunakan disebabkan oleh faktor sekuriti.</p>
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
      showMessage("⚠️ Klik kanan tidak boleh digunakan pada paparan ini.");
    });

    // Disable specific keyboard shortcuts
    document.addEventListener("keydown", function(e) {
      // Ctrl+S or Ctrl+U
      if (e.ctrlKey && (e.key.toLowerCase() === "s" || e.key.toLowerCase() === "u")) {
        e.preventDefault();
        showMessage("⚠️ Kekunci pintas ini tidak boleh digunakan.");
      }

      // Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+Shift+C
      if (e.ctrlKey && e.shiftKey && 
          (e.key.toLowerCase() === "i" || e.key.toLowerCase() === "j" || e.key.toLowerCase() === "c")) {
        e.preventDefault();
        showMessage("⚠️ Developer tools tidak boleh digunakan.");
      }

      // F12
      if (e.key === "F12") {
        e.preventDefault();
        showMessage("⚠️ Developer tools tidak boleh digunakan.");
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