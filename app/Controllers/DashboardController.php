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

        // 1) Pelawat counts: today / last 7 days / this month / this year (only pelawat = 'PELAWAT')
        $sqlToday = "SELECT COUNT(*) AS c FROM booking WHERE DATE(time_in) = ?";
        $todayCount = (int) $db->query($sqlToday, [$today])->getRow()->c;

        $weekStart = date('Y-m-d', strtotime('-6 days')); // 7-day window inclusive
        $sqlWeek = "SELECT COUNT(*) AS c FROM booking WHERE DATE(time_in) BETWEEN ? AND ?";
        $weekCount = (int) $db->query($sqlWeek, [$weekStart, $today])->getRow()->c;

        $sqlMonth = "SELECT COUNT(*) AS c FROM booking WHERE YEAR(time_in) = ? AND MONTH(time_in) = ?";
        $monthCount = (int) $db->query($sqlMonth, [$yearNow, $monthNow])->getRow()->c;

        $sqlYear = "SELECT COUNT(*) AS c FROM booking WHERE YEAR(time_in) = ?";
        $yearCount = (int) $db->query($sqlYear, [$yearNow])->getRow()->c;

        // But user asked specifically for "Pelawat (visitor)" — filter to pelawat='PELAWAT' for the 4 boxes:
        $sqlTodayPelawat = "SELECT COUNT(*) AS c FROM booking WHERE DATE(time_in) = ? AND pelawat = 'PELAWAT'";
        $pelawatToday = (int) $db->query($sqlTodayPelawat, [$today])->getRow()->c;

        $sqlWeekPelawat = "SELECT COUNT(*) AS c FROM booking WHERE DATE(time_in) BETWEEN ? AND ? AND pelawat = 'PELAWAT'";
        $pelawatWeek = (int) $db->query($sqlWeekPelawat, [$weekStart, $today])->getRow()->c;

        $sqlMonthPelawat = "SELECT COUNT(*) AS c FROM booking WHERE YEAR(time_in) = ? AND MONTH(time_in) = ? AND pelawat = 'PELAWAT'";
        $pelawatMonth = (int) $db->query($sqlMonthPelawat, [$yearNow, $monthNow])->getRow()->c;

        $sqlYearPelawat = "SELECT COUNT(*) AS c FROM booking WHERE YEAR(time_in) = ? AND pelawat = 'PELAWAT'";
        $pelawatYear = (int) $db->query($sqlYearPelawat, [$yearNow])->getRow()->c;

        // 2) Top officers by visitor count (pelawat = 'PELAWAT') — top 10
        $sqlTopOfficers = "SELECT officer, COUNT(*) AS cnt 
                           FROM booking 
                           WHERE pelawat = 'PELAWAT' AND officer IS NOT NULL AND officer <> ''
                           GROUP BY officer
                           ORDER BY cnt DESC
                           LIMIT 10";
        $topOfficers = $db->query($sqlTopOfficers)->getResultArray();

        // flatten labels & values
        $officerLabels = [];
        $officerCounts = [];
        foreach ($topOfficers as $r) {
            $officerLabels[] = $r['officer'];
            $officerCounts[] = (int) $r['cnt'];
        }

        // 3) Monthly series (Jan..Dec) for current year for both PELAWAT and PENTADBIR
        $months = [];
        $pelawatMonthly = [];
        $pentadbirMonthly = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[] = date('M', mktime(0, 0, 0, $m, 1));
            // PELAWAT
            $sqlM1 = "SELECT COUNT(*) AS c FROM booking WHERE YEAR(time_in) = ? AND MONTH(time_in) = ? AND pelawat = 'PELAWAT'";
            $c1 = (int) $db->query($sqlM1, [$yearNow, $m])->getRow()->c;
            $pelawatMonthly[] = $c1;

            // PENTADBIR
            $sqlM2 = "SELECT COUNT(*) AS c FROM booking WHERE YEAR(time_in) = ? AND MONTH(time_in) = ? AND pelawat = 'PENTADBIR'";
            $c2 = (int) $db->query($sqlM2, [$yearNow, $m])->getRow()->c;
            $pentadbirMonthly[] = $c2;
        }

        // Additional: totals (for small cards)
        $totalBookings = (int) $db->query("SELECT COUNT(*) AS c FROM booking")->getRow()->c;

        // optional: total users and divisions placeholders (you can replace them with real queries)
        $totalUsers = (int) $db->query("SELECT COUNT(*) AS c FROM users")->getRow()->c;
        $activeUsers = (int) $db->query("SELECT COUNT(*) AS c FROM users WHERE Active = 'Aktif'")->getRow()->c;
        $inactiveUsers = $totalUsers - $activeUsers;
        $totalDivisions = 0;
        try {
            $totalDivisions = (int) $db->query("SELECT COUNT(*) AS c FROM divisions")->getRow()->c;
        } catch (\Throwable $e) {
            // ignore if table doesn't exist — keep 0
            $totalDivisions = 0;
        }

        // pass to view
        $data = [
            // stat cards for pelawat
            'pelawatToday'   => $pelawatToday,
            'pelawatWeek'    => $pelawatWeek,
            'pelawatMonth'   => $pelawatMonth,
            'pelawatYear'    => $pelawatYear,

            // top officers
            'officerLabels'  => $officerLabels,
            'officerCounts'  => $officerCounts,

            // monthly series
            'months'         => $months,
            'pelawatMonthly' => $pelawatMonthly,
            'pentadbirMonthly'=> $pentadbirMonthly,

            // totals & placeholders
            'totalBookings'  => $totalBookings,
            'totalUsers'     => $totalUsers,
            'activeUsers'    => $activeUsers,
            'inactiveUsers'  => $inactiveUsers,
            'totalDivisions' => $totalDivisions,
        ];

        return view('dashboard', $data);
    }
}
