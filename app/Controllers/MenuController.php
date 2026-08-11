<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Database;
use App\Controllers\Dashboard;

class MenuController extends Controller
{
    public function index()
{
    $session = session();

    // ✅ If user not logged in, show pelawat page
    if (!$session->get('isLoggedIn')) {
        return view('normaluserindex'); // show the visitor homepage view
    }

    // ✅ Get user level
    $level = strtolower($session->get('level') ?? 'user');

    // ✅ Redirect based on level
    return match ($level) {
        'pentadbir'      => redirect()->to(base_url('menu')),
        'superadmin' => redirect()->to(base_url('menu')),
        default      => redirect()->to(base_url('menu')),
    };
}

 // ✅ SuperAdmin only — User List
public function userList()
{
    $session = session();
    $userLevel = $session->get('level') ?? 'pentadbir';

    // ✅ Only superadmins and pentadbir can access
    if (
        strtolower($userLevel) !== 'superadmin' &&
        strtolower($userLevel) !== 'pentadbir'
    ) {
        return redirect()->to(base_url('accessdenied'));
    }

    // ✅ Get users
    $db = \Config\Database::connect();
    $query = $db->query('SELECT * FROM users');
    $users = $query->getResultArray();

    // ✅ Pass userLevel to the view
    return view('userlist', [
        'users' => $users,
        'userLevel' => $userLevel
    ]);
}


    // ✅ User menu
    public function userMenu()
    {
        $session = session();

        // Optional: double check access level
        if (strtolower($session->get('level')) !== 'user') {
            return redirect()->to(base_url('accessdenied'));
        }

        return view('menu', ['name' => $session->get('name')]);
    }

    // ✅ Admin menu
   

    // ✅ SuperAdmin menu
    public function superAdminMenu()
{
    $session = session();

if (
    strtolower($session->get('level')) !== 'superadmin' &&
    strtolower($session->get('level')) !== 'pentadbir'
) {
    return redirect()->to(base_url('accessdenied'));
}

    // ✅ Load models
    $userModel     = new \App\Models\UserModel();
    $divisionModel = new \App\Models\DivisionModel();
    $pelawatModel  = new \App\Models\PelawatModel(); // replaces BookingModel

    // ✅ Users
    $totalUsers    = $userModel->countAllResults();
    $activeUsers   = $userModel->where('Active', 'Aktif')->countAllResults();
    $inactiveUsers = $userModel->where('Active', 'Tidak Aktif')->countAllResults();

    // ✅ Divisions
    $totalDivisions = $divisionModel->countAllResults();

    // ✅ Visitors (replacing old bookings)
    $totalVisitors = $pelawatModel->countAllResults();

    $todayVisitors = $pelawatModel
        ->where('DATE(time_in)', date('Y-m-d'))
        ->countAllResults();

    $checkedOutVisitors = $pelawatModel
        ->where('time_out_real IS NOT NULL')
        ->countAllResults();

    // ✅ Monthly Bookings (using PelawatModel)
    $monthlyBookings = $pelawatModel
        ->where('MONTH(time_in)', date('m'))
        ->where('YEAR(time_in)', date('Y'))
        ->countAllResults();

    // ✅ Total Bookings (Pelawat table)
    $totalBookings = $pelawatModel->countAllResults();

    // ✅ Keep your existing code — just add dashboard values
    $data = [
        'name' => $session->get('name'),

        // Dashboard numbers:
        'totalUsers' => $totalUsers,
        'activeUsers' => $activeUsers,
        'inactiveUsers' => $inactiveUsers,

        'totalDivisions' => $totalDivisions,

        'totalVisitors' => $totalVisitors,
        'todayVisitors' => $todayVisitors,
        'checkedOutVisitors' => $checkedOutVisitors,

        'monthlyBookings' => $monthlyBookings,
        'totalBookings' => $totalBookings,
    ];

$dashboard = new \App\Controllers\Dashboard();
$dashboardView = $dashboard->index(); // returns rendered dashboard HTML

return view('menu', [
    'name' => $session->get('name'),
    'dashboard' => $dashboardView
]);

}

}
