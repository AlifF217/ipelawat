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
<style>
  body{
           transform: scale(0.8);
        transform-origin: top left;
        width: 125%; /* prevent content from shrinking horizontally */
  }

        ul { list-style-type: none; }
        .division { margin: 5px 0; }
        .division > span {
            font-weight: 500;
            color: #0d6efd;
        }

        .tree ul {
    list-style: none;
    padding-left: 25px;
    position: relative;
}

.tree ul::before {
    content: '';
    border-left: 2px solid #ccc;
    position: absolute;
    top: 0;
    bottom: 0;
    left: 10px;
}

.tree li {
    margin: 6px 0;
    padding-left: 20px;
    position: relative;
    font-size: 16px;
}

.tree li::before {
    content: '';
    border-top: 2px solid #ccc;
    position: absolute;
    top: 12px;
    left: 10px;
    width: 20px;
}

.tree li:last-child::before {
    background: white;
    height: auto;
}
.division > span {
    color: #0d6efd;
    font-weight: 600;
}
.tree ul {
    list-style: none;
    padding-left: 25px;
    position: relative;
}

.tree ul::before {
    content: "";
    border-left: 2px solid #ccc;
    position: absolute;
    top: 0;
    bottom: 0;
    left: 10px;
}

.tree li {
    margin: 6px 0;
    padding-left: 20px;
    position: relative;
    font-size: 17px;
}

.tree li::before {
    content: "";
    border-top: 2px solid #ccc;
    position: absolute;
    top: 12px;
    left: 10px;
    width: 20px;
}

/* Expand / collapse arrows */
.tree-toggle {
    cursor: pointer;
    font-size: 16px;
    margin-right: 6px;
    color: #0d6efd;
    font-weight: bold;
}

.tree-toggle.empty {
    visibility: hidden;
}

/* Hide child nodes when collapsed */
.tree ul.collapsed {
    display: none;
}

.node-text {
    font-weight: 600;
    color: #0d6efd;
}
</style>
<body>

  <!-- Responsive Bootstrap Navbar -->
<?= view('superadminheader') ?>


  <main class="text-center mt-5">
     <?= view('div_hierarchy_add_form') ?>
  </main>

  <?= view('security_prompt') ?>


</body>
</html>
