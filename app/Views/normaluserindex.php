<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Laman Utama - Pelawat</title>

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,800&display=swap" rel="stylesheet">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdn.bootcss.com/font-awesome/5.7.2/css/all.min.css">

  <link rel="stylesheet" href="<?= base_url('css/menustyle.css') ?>">
  <script src="<?= base_url('js/function.js') ?>"></script>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

  <style>
    body {
      font-family: 'Montserrat', sans-serif;
      background: #e8f7fa;
      margin: 0;
      padding: 0;
        transform: scale(0.8);
        transform-origin: top left;
        width: 125%; /* prevent content from shrinking horizontally */
    
    }

    main {
      max-width: 900px;
      margin: 80px auto;
      background-color: white;
      border-radius: 15px;
      padding: 40px 60px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      text-align: center;
    }

    h1 {
      color: #007a91;
      font-weight: 800;
      margin-bottom: 20px;
    }

    p {
      color: #333;
      line-height: 1.7;
      font-size: 16px;
      text-align: justify;
    }

    .highlight {
      color: #00bfa5;
      font-weight: 600;
    }
  </style>
</head>

<body>

  <!-- 🔹 Header (Empty for now, ready for future use) -->
  <?= view('userheader') ?>
    <?= view('security_prompt') ?>
    <link rel="stylesheet" href="<?= base_url('css/menustyle.css') ?>">
  <!-- 🔹 Main Content -->
  <main>
    <h1>Selamat Datang <br> ke <br>
    Sistem i-Pelawat</h1><br><br>
   <!-- <p>
      Sistem ini dibangunkan khas untuk <span class="highlight">SUK Selangor</span> bagi memudahkan pengurusan tempahan bilik mesyuarat. 
      Melalui sistem ini, pengguna boleh membuat tempahan bilik mengikut tarikh, masa, dan pegawai yang terlibat tanpa perlu melalui proses manual.
    </p>

    <p>
      Antara fungsi utama sistem ini termasuklah:
      <ul style="text-align:left; margin:20px auto; max-width:600px;">
        <li>Semakan ketersediaan bilik mesyuarat secara langsung.</li>
        <li>Tempahan bilik mengikut keperluan masa dan kapasiti.</li>
        <li>Notifikasi pengesahan tempahan kepada pengguna.</li>
        <li>Rekod tempahan yang mudah diakses dan diuruskan.</li>
      </ul>
    </p>

    <p>
      Sistem ini bertujuan meningkatkan kecekapan dalam pengurusan fasiliti syarikat, menjimatkan masa, dan memastikan setiap bilik digunakan secara optimum.
    </p>

    <p class="mt-4 text-muted">✨ “Pengurusan bilik mesyuarat kini lebih pantas dan teratur.”</p>
    <div class="mt-4">
    -->
  <a href="<?= base_url('pelawat/daftar') ?>" class="btn btn-success btn-lg px-4 py-2">
    <i class="fas fa-user-plus me-2"></i> Daftar Sebagai Pelawat
  </a>
</div>
  </main>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
