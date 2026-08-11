<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Database;

class Dashboard extends Controller
{
   public function index()
{
    $db = Database::connect();
    $today = date('Y-m-d');
    $yearNow = date('Y');
    $monthNow = date('n');

    // -------------------------------------------
    // ✅ 1) DAILY, WEEKLY, MONTHLY, YEARLY COUNTS (BOTH PELAWAT AND PENTADBIR)
    // -------------------------------------------
    $sqlToday = "SELECT COUNT(*) AS c 
                 FROM booking 
                 WHERE DATE(time_in) = ? 
                 AND (pelawat = 'PELAWAT' OR pelawat = 'PENTADBIR')";
    $visitorToday = (int) $db->query($sqlToday, [$today])->getRow()->c;

    $weekStart = date('Y-m-d', strtotime('-6 days'));
    $sqlWeek = "SELECT COUNT(*) AS c 
                FROM booking 
                WHERE DATE(time_in) BETWEEN ? AND ? 
                AND (pelawat = 'PELAWAT' OR pelawat = 'PENTADBIR')";
    $visitorWeek = (int) $db->query($sqlWeek, [$weekStart, $today])->getRow()->c;

    $sqlMonth = "SELECT COUNT(*) AS c 
                 FROM booking 
                 WHERE YEAR(time_in) = ? 
                 AND MONTH(time_in) = ? 
                 AND (pelawat = 'PELAWAT' OR pelawat = 'PENTADBIR')";
    $visitorMonth = (int) $db->query($sqlMonth, [$yearNow, $monthNow])->getRow()->c;

    $sqlYear = "SELECT COUNT(*) AS c 
                FROM booking 
                WHERE YEAR(time_in) = ? 
                AND (pelawat = 'PELAWAT' OR pelawat = 'PENTADBIR')";
    $visitorYear = (int) $db->query($sqlYear, [$yearNow])->getRow()->c;

    // Additional: Individual counts for display
    $sqlTodayPelawatOnly = "SELECT COUNT(*) AS c 
                            FROM booking 
                            WHERE DATE(time_in) = ? AND pelawat = 'PELAWAT'";
    $pelawatTodayOnly = (int) $db->query($sqlTodayPelawatOnly, [$today])->getRow()->c;
    
    $sqlTodayPentadbirOnly = "SELECT COUNT(*) AS c 
                              FROM booking 
                              WHERE DATE(time_in) = ? AND pelawat = 'PENTADBIR'";
    $pentadbirTodayOnly = (int) $db->query($sqlTodayPentadbirOnly, [$today])->getRow()->c;

    // -------------------------------------------
    // ✅ 2) Monthly VISITOR counts using TIMESTAMP RANGE
    // -------------------------------------------

    $months = [];
    $pelawatMonthly = [];
    $pentadbirMonthly = [];
    $totalMonthly = []; // Add total for each month

    for ($m = 1; $m <= 12; $m++) {
        $start = "$yearNow-" . str_pad($m, 2, '0', STR_PAD_LEFT) . "-01 00:00:00";
        $end = date("Y-m-d H:i:s", strtotime("$start +1 month"));

        // For chart label e.g. "Jan"
        $months[] = date('M', strtotime($start));

        // PELAWAT only
        $sqlP = "SELECT COUNT(*) AS c FROM booking
                 WHERE time_in >= ? AND time_in < ?
                 AND pelawat = 'PELAWAT'";
        $countP = (int) $db->query($sqlP, [$start, $end])->getRow()->c;
        $pelawatMonthly[] = $countP;

        // PENTADBIR only
        $sqlD = "SELECT COUNT(*) AS c FROM booking
                 WHERE time_in >= ? AND time_in < ?
                 AND pelawat = 'PENTADBIR'";
        $countD = (int) $db->query($sqlD, [$start, $end])->getRow()->c;
        $pentadbirMonthly[] = $countD;

        // TOTAL (Both PELAWAT and PENTADBIR)
        $sqlTotal = "SELECT COUNT(*) AS c FROM booking
                     WHERE time_in >= ? AND time_in < ?
                     AND (pelawat = 'PELAWAT' OR pelawat = 'PENTADBIR')";
        $countTotal = (int) $db->query($sqlTotal, [$start, $end])->getRow()->c;
        $totalMonthly[] = $countTotal;
    }

    // -------------------------------------------
    // ✅ 3) Top officers sorted by count (BOTH PELAWAT AND PENTADBIR)
    // -------------------------------------------

    $sqlTopOfficers = "SELECT officer, COUNT(*) AS cnt
                       FROM booking
                       WHERE (pelawat = 'PELAWAT' OR pelawat = 'PENTADBIR')
                         AND officer IS NOT NULL
                         AND officer <> ''
                       GROUP BY officer
                       ORDER BY cnt DESC
                       LIMIT 3";
    $topOfficers = $db->query($sqlTopOfficers)->getResultArray();

    $officerLabels = array_column($topOfficers, 'officer');
    $officerCounts = array_map('intval', array_column($topOfficers, 'cnt'));

    // -------------------------------------------
    // ✅ 4) Totals
    // -------------------------------------------
    $totalBookings = (int) $db->query("SELECT COUNT(*) AS c FROM booking")->getRow()->c;
    
    // Total visitors (BOTH PELAWAT and PENTADBIR)
    $totalVisitors = (int) $db->query("SELECT COUNT(*) AS c FROM booking 
                                       WHERE pelawat = 'PELAWAT' OR pelawat = 'PENTADBIR'")->getRow()->c;
    
    // Total PELAWAT only
    $totalPelawatOnly = (int) $db->query("SELECT COUNT(*) AS c FROM booking 
                                          WHERE pelawat = 'PELAWAT'")->getRow()->c;
    
    // Total PENTADBIR only
    $totalPentadbirOnly = (int) $db->query("SELECT COUNT(*) AS c FROM booking 
                                            WHERE pelawat = 'PENTADBIR'")->getRow()->c;
    
    $totalUsers = (int) $db->query("SELECT COUNT(*) AS c FROM users")->getRow()->c;
    $activeUsers = (int) $db->query("SELECT COUNT(*) AS c FROM users WHERE Active = 'Aktif'")->getRow()->c;
    $inactiveUsers = $totalUsers - $activeUsers;

    $totalDivisions = 0;
    try {
        $totalDivisions = (int) $db->query("SELECT COUNT(*) AS c FROM divisions")->getRow()->c;
    } catch (\Throwable $e) {}

    // return to view
    return view('dashboard', [
        // Visitor counts (BOTH PELAWAT and PENTADBIR)
        'visitorToday' => $visitorToday,
        'visitorWeek' => $visitorWeek,
        'visitorMonth' => $visitorMonth,
        'visitorYear' => $visitorYear,
        
        // Individual counts for display
        'pelawatTodayOnly' => $pelawatTodayOnly,
        'pentadbirTodayOnly' => $pentadbirTodayOnly,
        
        // Monthly data for chart
        'months' => $months,
        'pelawatMonthly' => $pelawatMonthly,       // PELAWAT only
        'pentadbirMonthly' => $pentadbirMonthly,   // PENTADBIR only
        'totalMonthly' => $totalMonthly,           // TOTAL (Both)
        
        // Top officers
        'officerLabels' => $officerLabels,
        'officerCounts' => $officerCounts,
        
        // Totals
        'totalBookings' => $totalBookings,
        'totalVisitors' => $totalVisitors,
        'totalPelawatOnly' => $totalPelawatOnly,
        'totalPentadbirOnly' => $totalPentadbirOnly,
        'totalUsers' => $totalUsers,
        'activeUsers' => $activeUsers,
        'inactiveUsers' => $inactiveUsers,
        'totalDivisions' => $totalDivisions
    ]);
}
}
?>
