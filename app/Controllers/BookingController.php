<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Database;

class BookingController extends Controller
{
    public function index()
    {
        return view('booking_calendar');
    }

    // ✅ Get events for FullCalendar (Day, Week, Month)
    public function getBookings()
    { 
        $db = Database::connect();

        $fields = $db->getFieldNames('booking');
        $primaryKey = in_array('booking_id', $fields) ? 'booking_id' : 'id';

        $rows = $db->table('booking')
            ->select("
                $primaryKey,
                name,
                phone_no,
                pelawat,
                DATE_FORMAT(time_in, '%Y-%m-%dT%H:%i:%s') AS start_time,
                DATE_FORMAT(
                    CASE 
                        WHEN time_out_real IS NULL OR time_out_real = '' OR time_out_real = '0000-00-00 00:00:00' THEN NULL
                        WHEN time_out_exp IS NULL  OR time_out_exp = ''  OR time_out_exp = '0000-00-00 00:00:00' THEN NULL
                        ELSE COALESCE(time_out_real, time_out_exp, time_in)
                    END,
                    '%Y-%m-%dT%H:%i:%s'
                ) AS end_time
            ")
            ->orderBy('time_in', 'ASC')
            ->get()
            ->getResultArray();

        $events = [];

        foreach ($rows as $r) {
            $color = ($r['pelawat'] === 'PELAWAT') ? '#28a745' : '#0d6efd';

            $events[] = [
                'id'     => $r[$primaryKey],
                'title'  => $r['name'] . " (" . $r['phone_no'] . ")",
                'start'  => $r['start_time'],
                'end'    => $r['end_time'] ?? $r['start_time'],
                'allDay' => false,
                'color'  => $color,
            ];
        }

        return $this->response->setJSON($events);
    }

    // ✅ Daily count
    public function getBookingCounts()
    {
        $db = Database::connect();
        $rows = $db->table('booking')
            ->select("DATE(time_in) as date, COUNT(*) as total")
            ->groupBy("DATE(time_in)")
            ->get()
            ->getResultArray();

        $counts = [];
        foreach ($rows as $row) {
            $counts[$row['date']] = (int)$row['total'];
        }

        return $this->response->setJSON($counts);
    }

    // ✅ Weekly count
    public function getWeeklyCounts()
    {
        $db = Database::connect();
        $rows = $db->table('booking')
            ->select("CONCAT(YEAR(time_in), '-W', LPAD(WEEK(time_in, 1), 2, '0')) as week, COUNT(*) as total")
            ->groupBy("YEAR(time_in), WEEK(time_in, 1)")
            ->get()
            ->getResultArray();

        $counts = [];
        foreach ($rows as $row) {
            $counts[$row['week']] = (int)$row['total'];
        }

        return $this->response->setJSON($counts);
    }

    // ✅ Monthly count
    public function getMonthlyCounts()
    {
        $db = Database::connect();
        $rows = $db->table('booking')
            ->select("DATE_FORMAT(time_in, '%Y-%m') as month, COUNT(*) as total")
            ->groupBy("YEAR(time_in), MONTH(time_in)")
            ->get()
            ->getResultArray();

        $counts = [];
        foreach ($rows as $row) {
            $counts[$row['month']] = (int)$row['total'];
        }

        return $this->response->setJSON($counts);
    }

    // ✅ Yearly count
    public function getYearlyCounts()
    {
        $db = Database::connect();
        $rows = $db->table('booking')
            ->select("YEAR(time_in) as year, COUNT(*) as total")
            ->groupBy("YEAR(time_in)")
            ->get()
            ->getResultArray();

        $counts = [];
        foreach ($rows as $row) {
            $counts[$row['year']] = (int)$row['total'];
        }

        return $this->response->setJSON($counts);
    }

    // ✅ View bookings for a specific date
   // ✅ View bookings for a specific date
public function viewByDate($date = null)
{
    if (!$date) {
        return redirect()->to(base_url('booking'));
    }

    $db = Database::connect();
    $bookings = $db->table('booking')
        ->where('DATE(time_in)', $date)
        ->orderBy('time_in', 'ASC')
        ->get()
        ->getResultArray();

    // Get user level from session
    $session = session();
    $userLevel = $session->get('level') ?? 'pentadbir'; // Default to 'pentadbir' if not set

    return view('booking_by_date', [
        'date'      => $date,
        'bookings'  => $bookings,
        'userLevel' => $userLevel // Pass user level to view
    ]);
}

    // ✅ NEW — Unified handler for day, week, month, year views
    // ✅ NEW — Unified handler for day, week, month, year views
public function bookingByDateTable($type = null, $date = null)
{
    $db = Database::connect();
    $builder = $db->table('booking');

    switch ($type) {
        case 'day':
            $builder->where('DATE(time_in)', $date);
            $title = "Tempahan pada " . date('d F Y', strtotime($date));
            break;

        case 'week':
            $start = date('Y-m-d', strtotime($date));
            $end = date('Y-m-d', strtotime($date . ' +6 days'));
            $builder->where('DATE(time_in) >=', $start)
                    ->where('DATE(time_in) <=', $end);
            $title = "Minggu " . date('d M', strtotime($start)) . " - " . date('d M Y', strtotime($end));
            break;

        case 'month':
            $yearMonth = explode('-', $date);
            $builder->where('YEAR(time_in)', $yearMonth[0])
                    ->where('MONTH(time_in)', $yearMonth[1]);
            $title = "Bulan " . date('F Y', strtotime($date . '-01'));
            break;

        case 'year':
            $builder->where('YEAR(time_in)', $date);
            $title = "Tahun $date";
            break;

        default:
            return redirect()->to('/booking');
    }

    $bookings = $builder->orderBy('time_in', 'ASC')->get()->getResultArray();
    
    // Get user level from session
    $session = session();
    $userLevel = $session->get('level') ?? 'pentadbir'; // Default to 'pentadbir' if not set

    return view('booking_by_date', [
        'type'     => $type,
        'date'     => $date,
        'title'    => $title,
        'bookings' => $bookings,
        'userLevel' => $userLevel // Pass user level to view
    ]);
}


   public function getDailyVisitors($date)
{
    $db = \Config\Database::connect();

    $rows = $db->table('booking')
        ->select("
            name,
            phone_no,
            pelawat AS number,
            DATE_FORMAT(time_in, '%H:%i') AS time
        ")
        ->where('DATE(time_in)', $date)
        ->orderBy('time_in', 'ASC')
        ->get()
        ->getResultArray();

    return $this->response->setJSON($rows);
}
}
