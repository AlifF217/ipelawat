<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .profile-img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #007bff;
      margin-bottom: 10px;
    }
  </style>
</head>
<body class="bg-light">

<div class="container mt-5">
  <div class="card shadow p-4 mx-auto" style="max-width: 500px;">
    <h3 class="text-center mb-3">Edit Profile</h3>

    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger"><?= implode('<br>', (array) session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <div class="text-center mb-3">
      <img src="<?= esc($profilePic) ?>" alt="Profile Picture" class="profile-img">
    </div>

    <form action="<?= base_url('profile/update') ?>" method="post" enctype="multipart/form-data">
      <?= csrf_field() ?>

      <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" class="form-control" value="<?= esc($name) ?>" readonly>
      </div>

      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="text" class="form-control" value="<?= esc($email) ?>" readonly>
      </div>

      <div class="mb-3">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" class="form-control" value="<?= esc($phone) ?>" placeholder="Enter your phone number">
      </div>

      <div class="mb-3">
        <label class="form-label">Profile Picture</label>
        <input type="file" name="profile_picture" class="form-control">
        <div class="form-text">Max 2MB, JPG/PNG only.</div>
      </div>

      <button type="submit" class="btn btn-primary w-100">💾 Save Changes</button>
      <a href="<?= base_url('superadminprofile') ?>" class="btn btn-secondary w-100 mt-2">⬅ Cancel</a>

    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
