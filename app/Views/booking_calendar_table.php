<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Kalendar Pendaftaran</title>
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
    color: black; /* Global text color */
  }

  :root {
    --main-color: #6fdce0;
    --main-dark: #3bbcc3; /* Darker shade */
  }

  /* Ensure default text is black */
  * {
    color: black;
  }

  .calendar {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 10px;
    margin-top: 30px;
  }

  .calendar-day {
    background: white;
    border-radius: 8px;
    padding: 12px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s ease;
    border: 1px solid var(--main-color);
  }

  .calendar-day:hover {
    background-color: var(--main-dark);
    transform: scale(1.05);
  }

  .calendar-header {
    background: var(--main-dark);
    color: white; /* Keep readable */
    text-align: center;
    padding: 10px;
    border-radius: 6px;
    font-weight: 600;
    margin-bottom: 10px;
  }

  .count-badge {
    display: inline-block;
    background-color: red;
    color: white;
    border-radius: 10px;
    font-size: 0.8rem;
    padding: 2px 8px;
    margin-top: 6px;
  }

  /* Buttons */
  .btn-group .btn.active {
    background-color: var(--main-dark) !important;
    color: black !important;
    border-color: var(--main-dark) !important;
  }

  .btn-outline-success {
    color: black !important;
    border-color: var(--main-dark) !important;
  }

  .btn-outline-success:hover {
    background-color: var(--main-dark) !important;
    color: black !important;
  }

  .stats-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 10px;
    margin-top: 20px;
  }

  .stats-container2 {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    grid-template-rows: repeat(2, auto);
    gap: 12px;
  }

  .stats-card {
    background: white;
    border-radius: 8px;
    padding: 15px;
    border: 1px solid var(--main-color);
    text-align: center;
    min-width: 150px;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .stats-card:hover {
    background-color: var(--main-dark);
    transform: scale(1.05);
  }

  /* Search and Controls Styles */
  .table-controls {
    background: white;
    border-radius: 8px;
    padding: 15px;
    margin: 20px 0;
    border: 1px solid #dee2e6;
  }

  .search-box {
    position: relative;
  }

  .search-box i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
  }

  .search-box input {
    padding-left: 35px;
  }

  .page-size-select {
    width: auto !important;
    min-width: 100px;
  }

  .sortable {
    cursor: pointer;
    user-select: none;
  }

  .sortable:hover {
    background-color: rgba(0,0,0,0.05);
  }

  .sortable i {
    margin-left: 5px;
    font-size: 0.8em;
    opacity: 0.6;
  }

  .pagination .page-link {
    color: #333;
  }

  .pagination .active .page-link {
    background-color: var(--main-dark);
    border-color: var(--main-dark);
    color: white;
  }

  .table th {
    background-color: #e9f7f8 !important;
    border-bottom: 2px solid var(--main-color) !important;
  }

  .table-hover tbody tr:hover {
    background-color: rgba(59, 188, 195, 0.1) !important;
  }

  .table-sm td, .table-sm th {
    padding: 8px !important;
  }
</style>
</head>

<body>
  <?= view('security_prompt') ?>

  <div class="container text-center mt-4">
    <h2 class="text-success mb-1">📅 Kalendar Pendaftaran Pelawat</h2>
    <p class="text-muted mb-3">Lihat bilangan pelawat berdasarkan hari, minggu, bulan atau tahun.</p>

    <!-- Sorting Buttons -->
    <div class="btn-group mb-4" role="group">
      <button class="btn btn-outline-success active" id="btn-day">Hari</button>
      <button class="btn btn-outline-success" id="btn-week">Minggu</button>
      <button class="btn btn-outline-success" id="btn-month">Bulan</button>
      <button class="btn btn-outline-success" id="btn-year">Tahun</button>
    </div>

    <!-- Navigation -->
    <div class="btn-group mb-4 ms-2" role="group">
      <button class="btn btn-outline-success" id="btn-prev">← Sebelum</button>
      <button class="btn btn-outline-success" id="btn-next">Seterusnya →</button>
    </div>

    <div id="calendarContainer"></div>

    <div class="text-center my-4">
      <a href="<?= base_url('menu') ?>" class="btn btn-primary px-4 py-2">← Kembali ke Dashboard</a>
    </div>
  </div>

  <script>
    let currentDate = new Date();
    let currentView = 'day';
    let currentYearGroupStart = currentDate.getFullYear() - 2; // Start 2 years before current

    async function fetchCounts() {
      const res = await fetch("<?= base_url('booking/getBookingCounts') ?>");
      return res.json();
    }

    async function loadBookings() {
      const counts = await fetchCounts();
      const container = document.getElementById('calendarContainer');
      container.innerHTML = '';

      switch (currentView) {
        case 'day': renderDayView(container, counts); break;
        case 'week': renderWeekView(container, counts); break;
        case 'month': renderMonthView(container, counts); break;
        case 'year': renderYearView(container, counts); break;
      }
    }

    // Function to format time to 12-hour format with AM/PM
    function formatTime(timeStr) {
      if (!timeStr) return '';
      
      // If time is already in HH:MM format
      if (timeStr.includes(':')) {
        const [hours, minutes] = timeStr.split(':');
        const hour = parseInt(hours, 10);
        const minute = parseInt(minutes, 10);
        
        const ampm = hour >= 12 ? 'p.m.' : 'a.m.';
        const hour12 = hour % 12 || 12;
        
        return `${hour12}.${minute.toString().padStart(2, '0')} ${ampm}`;
      }
      
      return timeStr;
    }

    // Function to sort table data
    function sortTableData(data, column, direction) {
      return [...data].sort((a, b) => {
        let aVal = a[column];
        let bVal = b[column];
        
        if (column === 'time') {
          // Convert time to comparable format
          aVal = a.time || '';
          bVal = b.time || '';
          
          // Parse time to minutes for proper sorting
          const parseTime = (timeStr) => {
            if (!timeStr) return 0;
            const match = timeStr.match(/(\d+):(\d+)/);
            if (match) {
              let hours = parseInt(match[1]);
              const minutes = parseInt(match[2]);
              // Handle 12-hour format
              if (timeStr.toLowerCase().includes('pm') && hours < 12) hours += 12;
              if (timeStr.toLowerCase().includes('am') && hours === 12) hours = 0;
              return hours * 60 + minutes;
            }
            return 0;
          };
          
          aVal = parseTime(a.time);
          bVal = parseTime(b.time);
        }
        
        if (typeof aVal === 'string') {
          aVal = aVal.toLowerCase();
          bVal = bVal.toLowerCase();
        }
        
        if (aVal < bVal) return direction === 'asc' ? -1 : 1;
        if (aVal > bVal) return direction === 'asc' ? 1 : -1;
        return 0;
      });
    }

    // Function to render pagination
    function renderPagination(currentPage, totalPages) {
      let paginationHTML = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
      
      // Previous button
      paginationHTML += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                          <a class="page-link" href="#" data-page="${currentPage - 1}">«</a>
                        </li>`;
      
      // Page numbers
      const startPage = Math.max(1, currentPage - 2);
      const endPage = Math.min(totalPages, startPage + 4);
      
      for (let i = startPage; i <= endPage; i++) {
        paginationHTML += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                          </li>`;
      }
      
      // Next button
      paginationHTML += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                          <a class="page-link" href="#" data-page="${currentPage + 1}">»</a>
                        </li>`;
      
      paginationHTML += '</ul></nav>';
      return paginationHTML;
    }

    // ✅ DAY VIEW with Visitor Table
    async function renderDayView(container, counts) {
      const dateStr = currentDate.toISOString().split('T')[0];
      const count = counts[dateStr] || 0;

      // Fetch visitor table data
      let visitors = await fetch(`<?= base_url('booking/getDailyVisitors') ?>/${dateStr}`)
        .then(res => res.json());

      // Deduplicate by name, keeping earliest time
      const seen = {};
      visitors.forEach(v => {
        if (!seen[v.name] || v.time < seen[v.name].time) {
          seen[v.name] = v;
        }
      });
      
      // Format time and prepare data
      let allVisitors = Object.values(seen);
      allVisitors.forEach(v => {
        v.formattedTime = formatTime(v.time);
      });

      // State for table
      let currentPage = 1;
      let pageSize = 10;
      let sortColumn = 'time';
      let sortDirection = 'asc';
      let searchTerm = '';
      let filteredVisitors = [...allVisitors];

      // Function to render table with current state
      function renderTable() {
        // Apply search filter
        filteredVisitors = allVisitors.filter(v => {
          if (!searchTerm) return true;
          const term = searchTerm.toLowerCase();
          return (
            (v.name && v.name.toLowerCase().includes(term)) ||
            (v.phone_no && v.phone_no.includes(term)) ||
            (v.number && v.number.toLowerCase().includes(term)) ||
            (v.formattedTime && v.formattedTime.toLowerCase().includes(term))
          );
        });

        // Apply sorting
        const sortedVisitors = sortTableData(filteredVisitors, sortColumn, sortDirection);
        
        // Apply pagination
        const totalPages = Math.ceil(sortedVisitors.length / pageSize);
        const startIndex = (currentPage - 1) * pageSize;
        const endIndex = startIndex + pageSize;
        const paginatedVisitors = sortedVisitors.slice(startIndex, endIndex);

        // Build table HTML
        let tableHTML = `
          <div class="table-controls">
            <div class="row align-items-center mb-3">
              <div class="col-md-6">
                <div class="search-box">
                  <i class="fas fa-search"></i>
                  <input type="text" class="form-control" id="searchInput" placeholder="Cari pelawat..." value="${searchTerm}">
                </div>
              </div>
              <div class="col-md-6 text-end">
                <div class="d-inline-block me-2">
                  <span class="me-2">Papar:</span>
                  <select class="form-select form-select-sm page-size-select d-inline-block w-auto" id="pageSizeSelect">
                    <option value="5" ${pageSize === 5 ? 'selected' : ''}>5</option>
                    <option value="10" ${pageSize === 10 ? 'selected' : ''}>10</option>
                    <option value="25" ${pageSize === 25 ? 'selected' : ''}>25</option>
                    <option value="50" ${pageSize === 50 ? 'selected' : ''}>50</option>
                    <option value="100" ${pageSize === 100 ? 'selected' : ''}>100</option>
                    <option value="all" ${pageSize === 9999 ? 'selected' : ''}>Semua</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm">
              <thead class="table-success">
                <tr>
                  <th scope="col" style="width: 60px;">#</th>
                  <th scope="col" style="width: 120px;" class="sortable" data-column="time">Masa
                    <i class="fas fa-sort${sortColumn === 'time' ? (sortDirection === 'asc' ? '-up' : '-down') : ''}"></i>
                  </th>
                  <th scope="col" class="sortable" data-column="name">Nama
                    <i class="fas fa-sort${sortColumn === 'name' ? (sortDirection === 'asc' ? '-up' : '-down') : ''}"></i>
                  </th>
                  <th scope="col" class="sortable" data-column="phone_no">Telefon
                    <i class="fas fa-sort${sortColumn === 'phone_no' ? (sortDirection === 'asc' ? '-up' : '-down') : ''}"></i>
                  </th>
                  <th scope="col" class="sortable" data-column="number">Jenis Pelawat
                    <i class="fas fa-sort${sortColumn === 'number' ? (sortDirection === 'asc' ? '-up' : '-down') : ''}"></i>
                  </th>
                </tr>
              </thead>
              <tbody>`;

        if (paginatedVisitors.length === 0) {
          tableHTML += `
                <tr>
                  <td colspan="5" class="text-center">${searchTerm ? 'Tiada keputusan carian.' : 'Tiada pelawat untuk hari ini.'}</td>
                </tr>`;
        } else {
          const startNumber = (currentPage - 1) * pageSize + 1;
          paginatedVisitors.forEach((v, index) => {
            tableHTML += `
                <tr>
                  <td class="text-center">${startNumber + index}</td>
                  <td>${v.formattedTime}</td>
                  <td>${v.name || '-'}</td>
                  <td>${v.phone_no || '-'}</td>
                  <td>${v.number || '-'}</td>
                </tr>`;
          });
        }

        tableHTML += `</tbody></table></div>`;

        // Add pagination if needed
        if (totalPages > 1) {
          tableHTML += `<div class="mt-3">${renderPagination(currentPage, totalPages)}</div>`;
        }

        // Add summary
        tableHTML += `<div class="mt-2 text-muted small">
                        Menunjukkan ${paginatedVisitors.length} daripada ${filteredVisitors.length} pelawat. 
                        ${searchTerm ? '(Pencarian aktif)' : ''}
                      </div>`;

        return tableHTML;
      }

      // Function to update table
      function updateTable() {
        const tableContainer = document.getElementById('visitorsTableContainer');
        if (tableContainer) {
          tableContainer.innerHTML = renderTable();
          attachTableEvents();
        }
      }

      // Function to attach event listeners to table controls
      function attachTableEvents() {
        // Search input
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
          let searchTimeout;
          searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
              searchTerm = e.target.value;
              currentPage = 1;
              updateTable();
            }, 300);
          });
        }

        // Page size select
        const pageSizeSelect = document.getElementById('pageSizeSelect');
        if (pageSizeSelect) {
          pageSizeSelect.addEventListener('change', (e) => {
            pageSize = e.target.value === 'all' ? 9999 : parseInt(e.target.value);
            currentPage = 1;
            updateTable();
          });
        }

        // Sortable headers
        document.querySelectorAll('.sortable').forEach(header => {
          header.addEventListener('click', () => {
            const column = header.dataset.column;
            if (sortColumn === column) {
              sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
              sortColumn = column;
              sortDirection = 'asc';
            }
            updateTable();
          });
        });

        // Pagination links
        document.querySelectorAll('.page-link').forEach(link => {
          link.addEventListener('click', (e) => {
            e.preventDefault();
            const page = parseInt(link.dataset.page);
            if (page && page !== currentPage) {
              currentPage = page;
              updateTable();
            }
          });
        });
      }

      // DAY CARD
      const cardHTML = `
        <div class="stats-container">
          <div class="stats-card" 
               onclick="window.location.href='<?= base_url('booking_by_date_table/day/') ?>${dateStr}'">
            <strong>${dateStr}</strong><br>
            <span class="count-badge">${count}</span>
          </div>
        </div>
      `;

      container.innerHTML = `
        <div class="calendar-header">
          Hari Ini: ${currentDate.toLocaleDateString('ms-MY', {
            day: 'numeric', month: 'long', year: 'numeric'
          })}
        </div>

        ${cardHTML}

        <h5 class="mt-4">Senarai Pelawat</h5>
        <div id="visitorsTableContainer">${renderTable()}</div>
      `;

      // Attach event listeners after initial render
      setTimeout(() => {
        attachTableEvents();
      }, 100);
    }

    // ✅ WEEK VIEW — Show each day (Mon–Sun) with its date and count
    function renderWeekView(container, counts) {
      const startOfWeek = new Date(currentDate);
      const day = startOfWeek.getDay();
      const diff = (day === 0 ? -6 : 1) - day; // Adjust to Monday
      startOfWeek.setDate(startOfWeek.getDate() + diff);

      const days = [];
      for (let i = 0; i < 7; i++) {
        const d = new Date(startOfWeek);
        d.setDate(startOfWeek.getDate() + i);
        const dateStr = d.toISOString().split('T')[0];
        const dayName = d.toLocaleDateString('ms-MY', { weekday: 'short' });
        const count = counts[dateStr] || 0;
        days.push({ dayName, dateStr, count });
      }

      const weekLabel = `${days[0].dateStr} – ${days[6].dateStr}`;
      let html = `<div class="calendar-header">Minggu: ${weekLabel}</div>`;
      html += `<div class="calendar">`;
      days.forEach(day => {
        html += `
          <div class="calendar-day" onclick="window.location.href='<?= base_url('booking_by_date_table/day/') ?>${day.dateStr}'">
            <strong>${day.dayName}</strong><br>${day.dateStr}<br>
            <span class="count-badge">${day.count}</span>
          </div>`;
      });
      html += `</div>`;
      container.innerHTML = html;
    }

    // ✅ MONTH VIEW
    function renderMonthView(container, counts) {
      const year = currentDate.getFullYear();
      const monthCounts = {};
      for (const [date, count] of Object.entries(counts)) {
        const d = new Date(date);
        if (d.getFullYear() === year) {
          const m = d.getMonth();
          monthCounts[m] = (monthCounts[m] || 0) + count;
        }
      }

      const statsContainer = document.createElement('div');
      statsContainer.classList.add('stats-container2');

      for (let m = 0; m < 12; m++) {
        const monthName = new Date(year, m).toLocaleString('ms-MY', { month: 'long' });
        const val = monthCounts[m] || 0;

        const card = document.createElement('div');
        card.classList.add('stats-card');
        card.innerHTML = `<strong>${monthName}</strong><br><span class="count-badge">${val}</span>`;
        card.onclick = () => window.location.href = "<?= base_url('booking_by_date_table/month/') ?>" + year + "-" + String(m + 1).padStart(2, '0');
        statsContainer.appendChild(card);
      }

      container.innerHTML = `<div class="calendar-header">Tahun ${year}</div>`;
      container.appendChild(statsContainer);
    }

    // ✅ YEAR VIEW — Show 5-year group (e.g. 2021–2025)
    function renderYearView(container, counts) {
      const yearCounts = {};
      for (const [date, count] of Object.entries(counts)) {
        const d = new Date(date);
        const year = d.getFullYear();
        yearCounts[year] = (yearCounts[year] || 0) + count;
      }

      const statsContainer = document.createElement('div');
      statsContainer.classList.add('stats-container');

      const startYear = currentYearGroupStart;
      const endYear = startYear + 4;
      const groupLabel = `${startYear} – ${endYear}`;

      for (let y = startYear; y <= endYear; y++) {
        const total = yearCounts[y] || 0;
        const card = document.createElement('div');
        card.classList.add('stats-card');
        card.innerHTML = `<strong>${y}</strong><br><span class="count-badge">${total}</span>`;
        card.onclick = () => window.location.href = "<?= base_url('booking_by_date_table/year/') ?>" + y;
        statsContainer.appendChild(card);
      }

      container.innerHTML = `<div class="calendar-header">Tahun ${groupLabel}</div>`;
      container.appendChild(statsContainer);
    }

    // ✅ Navigation Arrows
    document.getElementById('btn-prev').addEventListener('click', () => {
      if (currentView === 'day') currentDate.setDate(currentDate.getDate() - 1);
      else if (currentView === 'week') currentDate.setDate(currentDate.getDate() - 7);
      else if (currentView === 'month') currentDate.setFullYear(currentDate.getFullYear() - 1);
      else if (currentView === 'year') currentYearGroupStart -= 5;
      loadBookings();
    });

    document.getElementById('btn-next').addEventListener('click', () => {
      if (currentView === 'day') currentDate.setDate(currentDate.getDate() + 1);
      else if (currentView === 'week') currentDate.setDate(currentDate.getDate() + 7);
      else if (currentView === 'month') currentDate.setFullYear(currentDate.getFullYear() + 1);
      else if (currentView === 'year') currentYearGroupStart += 5;
      loadBookings();
    });

    // ✅ View Switch
    ['day', 'week', 'month', 'year'].forEach(view => {
      document.getElementById(`btn-${view}`).addEventListener('click', () => {
        document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
        document.getElementById(`btn-${view}`).classList.add('active');
        currentView = view;
        loadBookings();
      });
    });

    document.addEventListener('DOMContentLoaded', loadBookings);
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);

    const view = urlParams.get('view');
    const date = urlParams.get('date');
    const year = urlParams.get('year');

    if (view) {
        currentView = view;
    }

    if (date) {
        currentDate = new Date(date);
    }

    if (year) {
        currentYearGroupStart = parseInt(year);
    }

    loadBookings();

    // Update button states
    document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
    if (view) {
        document.getElementById(`btn-${view}`).classList.add('active');
    }
});

  </script>
</body>
</html>