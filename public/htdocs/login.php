<?php
include "db.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = $_POST['Email'];
    $password = $_POST['Password'];

$stmt = $conn->prepare("SELECT Id, Name, Email, Password FROM users WHERE Email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['Password'])) {
           $_SESSION['user_id'] = $row['Id'];
$_SESSION['name'] = $row['Name'];
$_SESSION['email'] = $row['Email']; // ✅ Add this line

            // Redirect to menu page
            header("Location: menu.php");
            exit();
        } else {
            $message = "❌ Invalid password!";
        }
    } else {
        $message = "❌ No user found with that email!";
    }

    // Failed login → return with popup
    echo "
    <script>
        localStorage.setItem('popupMessage', " . json_encode($message) . ");
        window.location.href = 'index.php';
    </script>
    ";
}
?>
