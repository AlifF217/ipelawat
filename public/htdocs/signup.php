<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate input
    if (empty($_POST['Name']) || empty($_POST['Email']) || empty($_POST['Password'])) {
        $message = "❌ Please fill in all fields";
    } else {
        $name = trim($_POST['Name']);
        $email = trim($_POST['Email']);
        $password = password_hash($_POST['Password'], PASSWORD_DEFAULT);

        // Check if email already exists
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE Email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $message = "❌ Email already registered";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (Name, Email, Password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $password);

            if ($stmt->execute()) {
                $message = "✅ Registration successful!";
            } else {
                $message = "❌ Error: " . $stmt->error;
            }
            $stmt->close();
        }
        $check_stmt->close();
    }

    // Output JavaScript to show modal + redirect
    echo "
    <script>
        localStorage.setItem('popupMessage', " . json_encode($message) . ");
        window.location.href = 'index.php';
    </script>
    ";
}
?>