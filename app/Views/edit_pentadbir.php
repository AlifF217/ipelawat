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
 <style>
   body {
      background-color: #f9f9f9;
      transform: scale(0.8);
      transform-origin: top left;
      width: 125%;
    }
 </style>
</head>

<body>

  <!-- Responsive Bootstrap Navbar -->
<?= view('superadminheader') ?>


  <main class="text-center mt-5">
    <?= view('edit_pentadbir_form') ?>


  </main>

  <?= view('security_prompt') ?>


</body>
 <script src="<?= base_url('js/function.js') ?>"></script>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</html>
