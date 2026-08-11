<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Pelawat & Pentadbir pada tarikh <?= esc($title) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

  <!-- jQuery (required for DataTables) -->
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

  <!-- DataTables JS -->
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

  <link rel="stylesheet" href="<?= base_url('css/menustyle.css') ?>">

  <style>
    :root {
      --main-color: #6fdce0;
      --main-dark: #3bbcc3;
    } 

    body {
      background-color:#f9f9f9;
      transform: scale(0.8);
      transform-origin: top left;
      width: 125%;
      color: black;
    }

    .container { margin-top: 40px; }
    table { 
      background: white; 
      border-radius: 8px; 
      overflow: hidden; 
      color: black;
    }
    th { 
      background-color: var(--main-dark); 
      color: white; 
    }
    .search-input { max-width: 300px; color: black; }
    .toggle-btns .btn { 
      min-width: 100px; 
      color: black; 
      border-color: var(--main-dark);
    }
    .toggle-btns .btn.active { 
      background-color: var(--main-dark); 
      color: black; 
      border-color: var(--main-dark); 
    }
    .badge.bg-success { background-color: var(--main-dark); color: black; }
    .badge.bg-danger { background-color: #e63946; color: black; }
    .btn-secondary { background-color: var(--main-dark); border-color: var(--main-dark); color: black; }
    .btn-secondary:hover { background-color: var(--main-color); color: black; }
    .btn-info { background-color: var(--main-color); border-color: var(--main-color); color: black; }
    .btn-warning { background-color: #ffba08; border-color: #ffba08; color: black; }
    .btn-danger { background-color: #e63946; border-color: #e63946; color: black; }
  </style>
</head>
<body>
  <?php
  // Ensure $periodType is set (with default)
  $periodType = $periodType ?? 'daily';
  
  // Use $title from controller (already formatted in Bahasa Melayu)
  $displayDate = $title ?? $date;
  
  // Get user level - default to 'pentadbir' if not set
  $userLevel = $userLevel ?? 'pentadbir';
  ?>

  <?= view('security_prompt') ?>

  <div class="container">
    <h2 class="mb-4" style="color:black;">📅 Pelawat & Pentadbir pada <?= esc($displayDate) ?></h2>

    <div class="d-flex justify-content-between align-items-center mb-3">
      <a href="<?= base_url('booking') ?>" class="btn btn-secondary">← Kembali ke kalendar laporan</a>
    </div>

    <div class="mb-3 toggle-btns">
      <button class="btn btn-outline-success active" data-type="pelawat">PELAWAT</button>
      <button class="btn btn-outline-success" data-type="pentadbir">PENTADBIR</button>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered table-striped" id="bookingTable">
        <thead>
          <tr>
            <th>Nama</th>
            <th>Tel</th>
            <th>Pegawai</th>
            <th>Tujuan</th>
            <th>Masa Masuk</th>
            <th>Jangkaan Masa Keluar</th>
            <th>Masa Keluar Sebenar</th>
            <th>Jenis Pendaftar</th>
            <th>Nota Tambahan</th>
            <th>Tindakan</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($bookings)): ?>
            <tr>
              <?php for ($i = 0; $i < 10; $i++): ?>
                <td>&nbsp;</td>
              <?php endfor; ?>
            </tr>
          <?php else: ?>
            <?php 
            $counts = [];
            foreach ($bookings as $b):
                $nameKey = strtolower($b['name']);
                $periodKey = date('Y-m-d', strtotime($b['time_in']));
                $countKey = $nameKey . '_' . $periodKey;
                $counts[$countKey] = ($counts[$countKey] ?? 0) + 1;

                switch($periodType) {
                    case 'monthly': $periodText = "bulan ini"; break;
                    case 'yearly': $periodText = "tahun ini"; break;
                    default: $periodText = "hari ini"; 
                }

                $nota = ($counts[$countKey] === 1) ? "Pendaftaran kali pertama pada {$periodText}" : "Pendaftaran kali ke {$counts[$countKey]} pada {$periodText}";
            ?>
              <tr data-type="<?= esc(strtolower($b['pelawat'])) ?>">
                <td><?= esc($b['name']) ?></td>
                <td><?= esc($b['phone_no']) ?></td>
                <td><?= esc($b['officer']) ?></td>
                <td><?= esc($b['reason']) ?></td>
                <td><?= esc($b['time_in']) ?></td>
                <td><?= esc($b['time_out_exp']) ?></td>
                <td><?= esc($b['time_out_real']) ?></td>
               <td>
  <?php if (strtolower($b['pelawat']) === 'pentadbir'): ?>
    <span class="badge" style="background-color: #198754;">PENTADBIR</span>
  <?php else: ?>
    <span class="badge" style="background-color: #0d6efd;">PELAWAT</span>
  <?php endif; ?>
</td>
                <td><?= esc($nota) ?></td>
                <td>
                  <a href="<?= base_url('booking_user/view/'.$b['booking_id']) ?>" class="btn btn-sm btn-info">Lihat</a>
                  <a href="<?= base_url('booking_user/edit/'.$b['booking_id']) ?>" class="btn btn-sm btn-warning">Edit</a>
                  
                  <?php if (strtolower($userLevel) === 'superadmin'): ?>
                    <a href="#" 
                       class="btn btn-sm btn-danger delete-btn" 
                       data-id="<?= esc($b['booking_id']) ?>">Padam</a>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script>
    // Toggle Pelawat/Pentadbir
    const toggleButtons = document.querySelectorAll('.toggle-btns .btn');
    toggleButtons.forEach(btn => {
      btn.addEventListener('click', function() {
        toggleButtons.forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        const type = this.getAttribute('data-type');
        const rows = document.querySelectorAll('#bookingTable tbody tr');
        rows.forEach(row => {
          row.style.display = row.getAttribute('data-type') === type || row.querySelectorAll('td').length === 0 ? '' : 'none';
        });
      });
    });

    // Initialize DataTable
    $(document).ready(function() {
        $('#bookingTable').DataTable({
          "pageLength": 5,
          "lengthMenu": [5, 10, 20, 50, 100],
          "order": [],
          "columnDefs": [
            { "type": "num", "targets": [4,5,6] }
          ],
          "language": {
            "lengthMenu": "Paparkan _MENU_ rekod per halaman",
            "zeroRecords": "Tiada rekod ditemui",
            "info": "Paparan halaman _PAGE_ daripada _PAGES_",
            "infoEmpty": "Tiada rekod tersedia",
            "infoFiltered": "(disaring daripada _MAX_ rekod keseluruhan)",
            "search": "Carian:",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Seterusnya",
                "previous": "Sebelum"
            }
          }
        });
    });
  </script>
  
  <?php if (strtolower($userLevel) === 'superadmin'): ?>
  <script>
    // Only initialize delete functionality for superadmin
    $('.delete-btn').click(function(e) {
        e.preventDefault();

        const bookingId = $(this).data('id');
        if(confirm('Adakah anda pasti mahu memadam rekod ini?')) {
            $.ajax({
                url: '<?= base_url('booking_user/delete') ?>/' + bookingId,
                type: 'POST', // must be POST
                success: function() {
                    alert('Rekod berjaya dipadam!');
                    location.reload(); // reload after deletion
                },
                error: function(xhr, status, error) {
                    alert('Ralat: tidak dapat memadam rekod. ' + error);
                }
            });
        }
    });
  </script>
  <?php endif; ?>

</body>
</html>