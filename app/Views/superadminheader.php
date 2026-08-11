  <!-- Responsive Bootstrap Navbar -->
  <header>
    <nav class="navbar navbar-expand-lg navbar-dark px-3">
      <div class="container-fluid">
<a href="<?= base_url('menu') ?>" class="navbar-brand">
    <img src="<?= base_url('images/i-pelawat.png') ?>" 
         alt="i-Pelawat" 
         style="height: 70px; width: auto;" 
         draggable="false">
</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu" aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarMenu">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a href="<?= base_url('profile') ?>" class="nav-link">Profil</a>
            </li>
            <li class="nav-item">
              <a href="<?= base_url('userlist') ?>" class="nav-link">Senarai Pentadbir</a>
            </li>
            <li class="nav-item">
              <a href="<?= base_url('statistic') ?>" class="nav-link">Statistik dan Data</a>
            </li>
               <li class="nav-item">
              <a href="<?= base_url('regmanual') ?>" class="nav-link">Daftar Manual</a>
            </li>
               <li class="nav-item">
              <a href="<?= base_url('booking') ?>" class="nav-link">Laporan</a>
            </li>
               <li class="nav-item">
              <a href="<?= base_url('logout') ?>" class="nav-link logout">🚪 Log keluar</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>