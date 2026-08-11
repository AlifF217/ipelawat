<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Profile</title>

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,800&display=swap" rel="stylesheet">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdn.bootcss.com/font-awesome/5.7.2/css/all.min.css">

  <link rel="stylesheet" href="<?= base_url('css/menustyle.css') ?>">
  <style>
    body {
      font-family: 'Montserrat', sans-serif;
      background-color: #f8f9fa;
           transform: scale(0.8);
        transform-origin: top left;
        width: 125%; /* prevent content from shrinking horizontally */
  
    }
    .profile-card { 
      max-width: 420px;
      text-align: center;
      background: #fff;
      border-radius: 15px;
    }
    .profile-img {
      width: 120px;
      height: 120px;
      object-fit: cover;
      border-radius: 50%;
      margin-bottom: 20px;
      border: 3px solid #007bff;
    }
    .btn-custom {
      background-color: #007bff;
      color: #fff;
      border-radius: 20px;
    }
    .btn-custom:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>

  <?= view('superadminheader') ?>

  <main>
    <div class="profile-card p-4 shadow">

  <img src="<?= esc($profilePic) ?>" alt="Profile Picture" class="profile-img" draggable="false">
  <h3><?= esc($name) ?></h3>
  <p class="text-muted"><?= esc($level) ?></p>
  <hr>

  <div class="row text-start profile-table">

    <div class="col-6 mb-3">
      <strong>E-mel:</strong><br>
      <span><?= esc($email) ?></span>
    </div>

    <div class="col-6 mb-3">
      <strong>Nombor Telefon:</strong><br>
      <span><?= esc($phone) ?></span>
    </div>

    <div class="col-6 mb-3">
      <strong>Bahagian:</strong><br>
      <span><?= esc($division) ?></span>
    </div>

    <div class="col-6 mb-3">
      <strong>Status Keaktifan:</strong><br>
      <?= esc($active) === 'Aktif' 
        ? '<span class="text-success fw-bold">Aktif</span>' 
        : '<span class="text-danger fw-bold">Tidak Aktif</span>' ?>
    </div>

  </div>

  <div class="text-center">
    <a href="<?= base_url('profile/edit') ?>" class="btn btn-custom mt-3">✏️ Edit Profil</a>
  </div>

</div>
  </main>

  <?= view('security_prompt') ?>

</body>
</html>
