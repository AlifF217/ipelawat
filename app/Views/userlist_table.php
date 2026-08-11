<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Senarai Pengguna</title>
<!-- Google Fonts -->
<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<style>
    body {
        margin: 0;
        padding: 0;
        background-color: #f9f9f9;
        transform: scale(0.8);
        transform-origin: top left;
        width: 125%;
        color: black;
    }
    
    .container-wrapper {
        max-width: 1200px;
        margin: 40px auto;
        padding: 20px 30px;
        background: #fff; 
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    /* Add spacing for DataTables elements */
    .dataTables_wrapper { margin-top: 20px; }
    .dataTables_filter { margin-bottom: 15px; }
    .dataTables_length { margin-bottom: 15px; }
    .dataTables_info { margin-top: 15px; }
    .dataTables_paginate { margin-top: 15px; text-align: right; }

    h1 {
        text-align: center;
        color: #007a91;
        margin-bottom: 30px;
    }

    .btn-container-row {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-bottom: 30px;
        flex-wrap: wrap;
    }

    .add-btn {
        flex: 1;
        text-align: center;
        background-color: #00bfa5;
        color: #fff;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        transition: 0.3s;
        text-decoration: none;
    }

    .add-btn:hover { background-color: #009e87; }

    #userTable_wrapper { width: 100%; }

    table.dataTable {
        border-radius: 10px;
        overflow: hidden;
        width: 100% !important;
    }

    table.dataTable thead th {
        background: linear-gradient(to right, #6fdde0, #3bbcc3);
        color: #fff;
        font-weight: 600;
    }

    table.dataTable tbody tr:hover { background-color: #e6f7f8; }

    .action-link {
        color: #007a91;
        font-weight: 600;
        text-decoration: none;
        transition: 0.2s;
        margin-right: 10px;
        cursor: pointer;
    }

    .action-link:hover {
        color: #005f6b;
        text-decoration: underline;
    }
    
    .delete-link {
        color: #dc3545;
    }
    
    .delete-link:hover {
        color: #bd2130;
    }

    .default-text {
        color: #999;
        font-style: italic;
    }

    .back-btn {
        display: inline-block;
        margin: 30px auto 0;
        padding: 10px 25px;
        background-color: #007a91;
        color: #fff;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: 0.3s;
    }

    .back-btn:hover { background-color: #005f6b; }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .btn-container-row { flex-direction: column; }
        .add-btn { flex: unset; width: 100%; }
    }
</style>
</head>
<body>
<?= view('security_prompt') ?>
<link rel="stylesheet" href="<?= base_url('css/menustyle.css') ?>">

<!-- Add CSRF token meta tag for AJAX requests -->
<meta name="csrf-token" content="<?= csrf_hash() ?>">

<div class="container-wrapper">

<h1>📋 Senarai Pengguna</h1>

<div class="btn-container-row">
    <a href="<?= base_url('addUser') ?>" class="add-btn">＋ Tambah Pentadbir Baru</a>
    <a href="<?= base_url('/divisions') ?>" class="add-btn">＋ Tambah Bahagian</a>
    <a href="<?= base_url('guest/list') ?>" class="add-btn">＋ Senarai Pelawat Luar</a>
</div>

<table id="userTable" class="display stripe hover" style="width:100%">
  <thead>
    <tr>
      <th>Nama</th>
      <th>Email</th>
      <th>Bahagian</th>
      <th>No Telefon</th>
      <th>Status</th>
      <th>Tindakan</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($users)): ?>
      <?php foreach ($users as $user): ?>
        <?php if ($user['Level'] !== 'superadmin'): ?>
          <tr id="user-row-<?= esc($user['Id']) ?>">
            <td><?= esc($user['Name']) ?></td>
            <td><?= esc($user['Email']) ?></td>
              <td><?= !empty($user['Division']) ? esc($user['Division']) : '<span class="default-text">Tidak diisi</span>' ?></td>
        <td><?= !empty($user['Phone']) ? esc($user['Phone']) : '<span class="default-text">Tidak diisi</span>' ?></td>
        <td><?= !empty($user['Active']) ? esc($user['Active']) : '<span class="default-text">Tidak Aktif</span>' ?></td>
            <td>
                <!-- View button - available for all users -->
                <a href="<?= base_url('viewUser/' . $user['Id']) ?>" class="action-link">👁️ Lihat</a>
                
                <!-- Edit button - available for all users -->
                <a href="<?= base_url('editUser/' . $user['Id']) ?>" class="action-link">✏️ Edit</a>
                
                <!-- Delete button - only for superadmin -->
                <?php if (isset($userLevel) && strtolower($userLevel) === 'superadmin'): ?>
                    <!-- Use data attributes instead of inline onclick -->
                    <a href="#" 
                       class="action-link delete-link delete-user-btn" 
                       data-id="<?= esc($user['Id']) ?>" 
                       data-name="<?= esc($user['Name']) ?>">🗑️ Padam</a>
                <?php endif; ?>
            </td>
          </tr>
        <?php endif; ?> 
      <?php endforeach; ?>
    <?php else: ?>
      <tr>
        <td colspan="6" style="text-align:center;">Tiada data pengguna dijumpai</td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>

<a href="<?= base_url('menu') ?>" class="back-btn">← Kembali ke Menu</a>

</div> <!-- container-wrapper -->

<script>
// Function to delete user
function deleteUser(userId, userName) {
    // Show confirmation dialog FIRST
    if(!confirm('Adakah anda pasti mahu memadam pengguna "' + userName + '"?')) {
        // User clicked Cancel - do nothing
        return false;
    }
    
    // User clicked OK - proceed with deletion
    // Get CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    $.ajax({
        url: '<?= base_url('deleteUser') ?>/' + userId,
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        data: {
            '<?= csrf_token() ?>': csrfToken
        },
        success: function(response) {
            try {
                // Check if response is JSON
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                
                if (response.success) {
                    alert(response.message);
                    // Remove the row from the table
                    $('#user-row-' + userId).fadeOut(300, function() {
                        $(this).remove();
                        // Refresh DataTable
                        $('#userTable').DataTable().draw();
                    });
                } else {
                    alert('Ralat: ' + response.message);
                }
            } catch (e) {
                // If response is not JSON, assume success and reload
                alert('Pengguna berjaya dipadam.');
                location.reload();
            }
        },
        error: function(xhr, status, error) {
            console.error('Delete error:', xhr.responseText);
            alert('Ralat: tidak dapat memadam pengguna. Sila cuba lagi.');
        }
    });
    
    return false; // Prevent default action
}

$(document).ready(function() {
    $('#userTable').DataTable({
        "pageLength": 10,
        "lengthMenu": [10, 20, 50, 100],
        "order": [[0, 'asc']],
        "columnDefs": [
            { "orderable": false, "targets": 5 } // disable sorting on 'Tindakan'
        ],
        "language": {
            "search": "Cari:",
            "lengthMenu": "Tunjukkan _MENU_ baris",
            "info": "Memaparkan _START_ hingga _END_ daripada _TOTAL_ baris",
            "infoEmpty": "Tiada data tersedia",
            "infoFiltered": "(ditapis daripada _MAX_ jumlah baris)",
            "zeroRecords": "Tiada padanan dijumpai",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Seterusnya",
                "previous": "Sebelumnya"
            }
        },
        "responsive": true
    });
    
    // Handle delete button clicks - SINGLE event handler
    $(document).on('click', '.delete-user-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const userId = $(this).data('id');
        const userName = $(this).data('name');
        
        // Call delete function
        deleteUser(userId, userName);
        
        return false;
    });
});
</script>
</body>
</html>