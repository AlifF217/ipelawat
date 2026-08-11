<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Access Denied</title>

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,800" rel="stylesheet">
  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdn.bootcss.com/font-awesome/5.7.2/css/all.min.css">
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="style.css">
</head>

  <style>
    body {
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      background: #e0f7f9;
      font-family: 'Montserrat', sans-serif;
      overflow: hidden;
    }

    .container {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
      text-align: center;
      padding: 40px 30px;
      max-width: 500px;
      animation: fadeIn 0.4s ease;
    }

    h1 {
      font-size: 28px;
      color: #d9534f;
      margin-bottom: 15px;
    }

    p {
      color: #555;
      margin-bottom: 30px;
    }

    .btn-return {
      background-color: #007bff;
      color: #fff;
      border: none;
      padding: 10px 25px;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .btn-return:hover {
      background-color: #0056b3;
    }

    .icon {
      font-size: 60px;
      color: #d9534f;
      margin-bottom: 10px;
    }

    /* Overlay Styles */
    #overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.6);
      justify-content: center;
      align-items: center;
      z-index: 9999;
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    #overlay.active {
      display: flex;
      opacity: 1;
    }

    #messageBox {
      background: #fff;
      padding: 30px 40px;
      border-radius: 12px;
      text-align: center;
      box-shadow: 0 6px 20px rgba(0,0,0,0.2);
      position: relative;
      animation: slideDown 0.3s ease;
      max-width: 400px;
    }

    #messageBox h2 {
      color: #d9534f;
      margin-bottom: 10px;
    }

    #messageBox p {
      color: #333;
      margin-bottom: 20px;
    }

    #closeBtn {
      position: absolute;
      top: 10px;
      right: 15px;
      font-size: 22px;
      cursor: pointer;
      color: #999;
    }

    #closeBtn:hover {
      color: #000;
    }

    @keyframes slideDown {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: scale(0.95); }
      to { opacity: 1; transform: scale(1); }
    }
  </style>
<body>
  <div class="container">
    <i class="fas fa-ban icon"></i>
    <h1>Akses Dihalang</h1>
    <p>Anda tidak dibenarkan untuk mengakses halaman ini.</p>
    <button class="btn-return" onclick="window.location.href='/'">
      <i class="fas fa-home"></i> Kembali ke Halaman Pengguna
    </button>
  </div>

  <!-- Security Overlay -->
      <?= view('security_prompt') ?>
</body>
</html>
