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
  
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
  
  <!-- jQuery (required for DataTables) -->
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  
  <!-- DataTables JS -->
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

  <link rel="stylesheet" href="<?= base_url('css/menustyle.css') ?>">
  <script src="<?= base_url('js/function.js') ?>"></script>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  
  <style>
    body {
      transform: scale(0.8);
      transform-origin: top left;
      width: 125%; /* prevent content from shrinking horizontally */
    }
    
    /* DataTables specific styling to prevent conflicts */
    #guestTable_wrapper {
      margin-top: 20px;
    }
    
    #guestTable {
      background: white;
      border-radius: 8px;
      overflow: hidden;
      margin-bottom: 20px;
    }
    
    #guestTable th {
      background-color: #3bbcc3;
      color: white;
    }
    
    .dataTables_wrapper .dataTables_filter {
      float: right;
      margin-bottom: 10px;
    }
    
    .dataTables_wrapper .dataTables_length {
      float: left;
      margin-bottom: 10px;
    }
    
    .dataTables_wrapper .dataTables_paginate {
      float: right;
      margin-top: 10px;
    }
    
    .dataTables_wrapper .dataTables_info {
      float: left;
      margin-top: 10px;
    }
    
    /* Ensure proper spacing */
    .container.mt-5 {
      margin-top: 3rem !important;
    }
  </style>
</head>
<body>

  <!-- Responsive Bootstrap Navbar -->
  <?= view('superadminheader') ?>

  <main class="text-center mt-5">
    <?= view('guest_list_table') ?>
  </main>

  <?= view('security_prompt') ?>

  <script>
  $(document).ready(function() {
    // Initialize DataTables if table exists
    if ($('#guestTable').length) {
      $('#guestTable').DataTable({
        "pageLength": 10,
        "lengthMenu": [[10, 20, 50, 100, -1], [10, 20, 50, 100, "Semua"]],
        "order": [[1, 'asc']], // Sort by Name column (2nd column) ascending
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
          },
          "processing": "Memproses..."
        },
        "dom": '<"top"lf>rt<"bottom"ip><"clear">',
        "initComplete": function() {
          // Add Bootstrap classes to DataTables elements
          $('.dataTables_length label').addClass('form-label mb-0');
          $('.dataTables_filter label').addClass('form-label mb-0');
          $('.dataTables_filter input').addClass('form-control form-control-sm');
          $('.dataTables_length select').addClass('form-select form-select-sm');
        },
        "drawCallback": function(settings) {
          // Re-number the index column on each page draw
          var api = this.api();
          var startIndex = api.page.info().start;
          
          api.column(0, {page: 'current'}).nodes().each(function(cell, i) {
            cell.innerHTML = startIndex + i + 1;
          });
        }
      });
    }
  });
  </script>

</body>
</html>