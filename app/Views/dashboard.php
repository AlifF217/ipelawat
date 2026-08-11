<!DOCTYPE html>
<html lang="en">
<div id="dashboard-wrapper">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
<br>
  <link href="<?= base_url('vendor/fontawesome-free/css/all.min.css') ?>" rel="stylesheet">
  <link href="<?= base_url('css/sb-admin-2.min.css') ?>" rel="stylesheet">

  <style>
    #dashboard-wrapper .stat-number { font-size: 1.6rem; font-weight: 700; }
     #dashboard-wrapper .stat-label { font-size: 0.9rem; color: #6c757d; }

    #dashboard-wrapper {
        font-size: 1.15rem; /* Global font size increase */
    }

     #dashboard-wrapper h1, #dashboard-wrapper  h2, #dashboard-wrapper  h3, #dashboard-wrapper  h4, #dashboard-wrapper  h5, #dashboard-wrapper  h6 {
        font-size: 1.35em; /* Larger heading sizes */
    }

     #dashboard-wrapper .stat-number {
        font-size: 2rem !important; /* Bigger numbers in cards */
        font-weight: 700;
    }

     #dashboard-wrapper .text-xs {
        font-size: 0.95rem !important; /* Title labels */
    }

     #dashboard-wrapper .small {
        font-size: 1rem !important; /* Small text but readable */
    }

     #dashboard-wrapper .card-body {
        font-size: 1.1rem; /* Increase card text */
    }

     #dashboard-wrapper .card-header h6 {
        font-size: 1.2rem !important;
    }
</style>

</head>

<body id="page-top">

<div id="wrapper">

  <div id="content-wrapper" class="d-flex flex-column">
    <div id="content">

      <div class="container-fluid">

        <br>
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
          <h1 class="h3 mb-0 text-gray-800">Dashboard — Analitik Pelawat (<?= date('Y') ?>)</h1>
          <div class="small text-muted">
            <i class="fas fa-user-friends"></i> Total: <?= esc($totalVisitors) ?> | 
            <i class="fas fa-user"></i> Luar: <?= esc($totalPelawatOnly) ?> | 
            <i class="fas fa-user-tie"></i> Dalaman: <?= esc($totalPentadbirOnly) ?>
          </div>
        </div>

        <!-- ===================== STAT CARDS ===================== -->
        <div class="row mb-4">

          <!-- Today -->
          <div class="col-md-3 mb-3">
            <a href="<?= base_url('booking') ?>?view=day&date=<?= date('Y-m-d') ?>" style="text-decoration:none;">
              <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                  <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Pelawat — Hari ini</div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800 stat-number"><?= esc($visitorToday) ?></div>
                  <div class="text-muted small">
                    <span class="text-primary">Luar: <?= esc($pelawatTodayOnly) ?></span> | 
                    <span class="text-danger">Dalaman: <?= esc($pentadbirTodayOnly) ?></span>
                  </div>
                </div>
              </div>
            </a>
          </div>

          <!-- 7 Days -->
          <div class="col-md-3 mb-3">
            <a href="<?= base_url('booking') ?>?view=week&date=<?= date('Y-m-d') ?>" style="text-decoration:none;">
              <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                  <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Pelawat — 7 hari yang lepas</div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800 stat-number"><?= esc($visitorWeek) ?></div>
                  <div class="text-muted small">Jumlah pelawat dalam 7 hari</div>
                </div>
              </div>
            </a>
          </div>

          <!-- Month -->
          <div class="col-md-3 mb-3">
            <a href="<?= base_url('booking') ?>?view=month&date=<?= date('Y-m-d') ?>" style="text-decoration:none;">
              <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                  <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Pelawat — Bulan ini</div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800 stat-number"><?= esc($visitorMonth) ?></div>
                  <div class="text-muted small">Jumlah pelawat pada bulan ini</div>
                </div>
              </div>
            </a>
          </div>

          <!-- Year -->
          <div class="col-md-3 mb-3">
            <a href="<?= base_url('booking') ?>?view=year&year=<?= date('Y') ?>" style="text-decoration:none;">
              <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                  <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pelawat — Tahun ini</div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800 stat-number"><?= esc($visitorYear) ?></div>
                  <div class="text-muted small">Jumlah pelawat pada tahun ini</div>
                </div>
              </div>
            </a>
          </div>

        </div>

        <!-- ===================== CHARTS ===================== -->
        <div class="row">

          <!-- Monthly Chart -->
          <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
              <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Pelawat Bulanan (<?= date('Y') ?>) — LUAR vs DALAMAN</h6>
                <div class="small text-muted">Total: <span id="totalVisitorsYear"><?= esc($visitorYear) ?></span> pelawat</div>
              </div>

              <div class="card-body">
                <div style="height: 350px;">
                  <canvas id="monthlyChart"></canvas>
                </div>
                <hr>
                <div class="small text-muted">Jumlah pelawat bulanan untuk PELAWAT luar dan dalaman.</div>
              </div>
            </div>
          </div>

          <!-- Top Admin Bar Chart -->
          <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
              <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Pentadbir dengan jumlah Pelawat tertinggi</h6>
              </div>

              <div class="card-body">
                <div style="height: 350px;">
                  <canvas id="topOfficersChart"></canvas>
                </div>
                <hr>
                <div class="small text-muted">Pentadbir teratas berdasarkan Pendaftaran Pelawat.</div>
              </div>
            </div>
          </div>

        </div>

        <!-- ===================== SUMMARY CARD ===================== -->
        <div class="row">
          <div class="col-12">
            <div class="card mb-4 shadow">
              <div class="card-body">
                <div class="small text-muted">
                  <strong>Ringkasan:</strong> 
                  Jumlah Pendaftaran: <?= esc($totalBookings) ?> · 
                  Jumlah Pelawat (Luar+Dalaman): <?= esc($totalVisitors) ?> · 
                  Jumlah Pentadbir: <?= esc($totalUsers) ?> (<?= esc($activeUsers) ?> Aktif, <?= esc($inactiveUsers) ?> Tidak Aktif) · 
                  Jumlah Bahagian: <?= esc($totalDivisions) ?>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div><!-- /.container-fluid -->

    </div>
  </div>
</div>

<!-- JS Files -->
<script src="<?= base_url('vendor/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('vendor/jquery-easing/jquery.easing.min.js') ?>"></script>
<script src="<?= base_url('vendor/chart.js/Chart.min.js') ?>"></script>
<script src="<?= base_url('js/sb-admin-2.min.js') ?>"></script>

<script>
const months = <?= json_encode($months) ?>;
const pelawatMonthly = <?= json_encode($pelawatMonthly) ?>;
const pentadbirMonthly = <?= json_encode($pentadbirMonthly) ?>;
const totalMonthly = <?= json_encode($totalMonthly) ?>;

const officerLabels = <?= json_encode($officerLabels) ?>;
const officerCounts = <?= json_encode($officerCounts) ?>;

// Monthly Chart - Updated to show total line
new Chart(document.getElementById('monthlyChart'), {
    type: 'line',
    data: {
        labels: months,
        datasets: [
            {
                label: 'Pelawat (Luar)',
                data: pelawatMonthly,
                fill: false,
                tension: 0.3,
                borderWidth: 2,
                pointRadius: 3,
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)'
            },
            {
                label: 'Pelawat (Dalaman)',
                data: pentadbirMonthly,
                fill: false,
                tension: 0.3,
                borderWidth: 2,
                pointRadius: 3,
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)'
            },
            {
                label: 'Total (Luar+Dalaman)',
                data: totalMonthly,
                fill: false,
                tension: 0.3,
                borderWidth: 2,
                pointRadius: 3,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderDash: [5, 5]
            }
        ]
    },
    options: { 
        responsive: true, 
        maintainAspectRatio: false,
        plugins: {
            tooltip: {
                mode: 'index',
                intersect: false
            }
        }
    }
});

// Top Officers Chart
new Chart(document.getElementById('topOfficersChart'), {
    type: 'bar',
    data: {
        labels: officerLabels,
        datasets: [{ 
            label: 'Jumlah Pelawat (Luar+Dalaman)', 
            data: officerCounts, 
            borderWidth: 1,
            backgroundColor: 'rgba(54, 162, 235, 0.5)',
            borderColor: 'rgba(54, 162, 235, 1)'
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        scales: { 
            x: { 
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Jumlah Pelawat'
                }
            },
            y: {
                title: {
                    display: true,
                    text: 'Pentadbir'
                }
            }
        },
        plugins: { 
            legend: { 
                display: true,
                position: 'top'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Jumlah Pelawat: ' + context.parsed.x;
                    }
                }
            }
        }
    }
});

// Calculate and display total visitors for the year
document.addEventListener('DOMContentLoaded', function() {
    const totalVisitorsYear = totalMonthly.reduce((a, b) => a + b, 0);
    document.getElementById('totalVisitorsYear').textContent = totalVisitorsYear;
});
</script>

</body>
</div>
</html>