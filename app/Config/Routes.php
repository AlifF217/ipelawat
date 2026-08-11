<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ✅ Public homepage
$routes->get('educated', 'Home::homepage');

$routes->get('educated', function() {
    return redirect()->to(base_url('pentadbirhome'));
});
// ✅ Default homepage → Pelawat page
$routes->get('/', function() {
    return redirect()->to(base_url('pelawat'));
});

// ✅ Public homepage (optional direct access)
$routes->get('pelawat', 'MenuController::index');
$routes->get('accessdenied', 'Home::accessdenied');

// 🚫 Prevent direct access to login, signup, logout (GET)
$routes->get('login', fn() => redirect()->to(base_url('accessdenied')));
$routes->get('signup', fn() => redirect()->to(base_url('accessdenied')));

// ✅ Allow only POST form submissions
$routes->post('login', 'Home::login');
$routes->post('signup', 'Home::signup');
$routes->get('logout', 'Home::logout'); // logout triggered normally by GET

    // 👤 Normal user routes
    $routes->get('pelawat/daftar', 'PelawatController::daftar');
$routes->post('pelawat/simpan', 'PelawatController::simpan');
$routes->post('pelawat/simpanPentadbir', 'PelawatController::simpanPentadbir');
$routes->post('pelawat/getGuestDetails', 'PelawatController::getGuestDetails');
$routes->post('pelawat/getUserDetails', 'PelawatController::getUserDetails');
$routes->post('pelawat/searchGuest', 'PelawatController::searchGuest');
$routes->get('pelawat/edit/(:num)', 'PelawatController::edit/$1');
$routes->post('pelawat/update/(:num)', 'PelawatController::update/$1');

// ✅ Protected routes (require user to be logged in)
$routes->group('', ['filter' => 'auth'], static function (RouteCollection $routes) {

    $routes->get('settings', 'SettingsController::index');
    $routes->get('profile', 'ProfileController::index');
    $routes->get('profile/edit', 'ProfileController::edit');
    $routes->post('profile/update', 'ProfileController::update');
    $routes->get('statistic', 'MenuController::superAdminMenu');

    // 🧩 Role-based menus
    $routes->get('usermenu', 'MenuController::userMenu');

    $routes->get('adminmenu', 'MenuController::adminMenu');

    $routes->get('menu', 'MenuController::superAdminMenu');
    $routes->get('profile', 'ProfileController::superAdminProfile');
    $routes->get('profile/edit', 'ProfileController::edit');
    $routes->post('profile/update', 'ProfileController::update');

    // 🧮 Pentadbir/SuperAdmin-only user list
    $routes->get('userlist', 'MenuController::userList');
    $routes->get('viewUser/(:num)', 'SuperAdmin::viewUser/$1');  // Added view route - NEW
    $routes->post('deleteUser/(:num)', 'SuperAdmin::deleteUser/$1');  // Added delete route - NEW
    $routes->get('editUser/(:num)', 'SuperAdmin::editUser/$1');
    $routes->post('updateUser/(:num)', 'SuperAdmin::updateUser/$1');
    $routes->get('addUser', 'SuperAdmin::addUser');
    $routes->post('saveUser', 'SuperAdmin::saveUser');
    $routes->get('editRegManual/(:num)', 'PentadbirController::edit/$1');
    $routes->post('updateRegManual/(:num)', 'PentadbirController::update/$1');
    $routes->get('regmanual', 'PelawatController::daftarManual');
    $routes->post('regmanual/getUserDetails', 'PelawatController::getUserDetails');
    $routes->post('regmanual/simpan', 'PentadbirController::simpan');

    $routes->get('guest/list', 'GuestController::list');

    // app/Config/Routes.php
    $routes->group('booking_user', function($routes) {
        $routes->get('/', 'BookingUserController::index');                // /booking_user
        $routes->get('view/(:num)', 'BookingUserController::view/$1');   // /booking_user/view/27
        $routes->get('edit/(:num)', 'BookingUserController::edit/$1');   // /booking_user/edit/27
        $routes->post('delete/(:num)', 'BookingUserController::delete/$1'); // POST /booking_user/delete/27
    });

    // =====================
    // 📅 Booking Calendar Routes
    // =====================

    $routes->get('booking', 'BookingController::index');
    $routes->get('booking/getBookings', 'BookingController::getBookings');
    $routes->get('booking/getBookingCounts', 'BookingController::getBookingCounts');

    // ✅ Existing route to view bookings for a specific date
    $routes->get('booking/date/(:segment)', 'BookingController::viewByDate/$1');

    // ✅ Weekly, Monthly, Yearly JSON endpoints (for analytics or charts)
    $routes->get('/booking/getDailyVisitors/(:segment)', 'BookingController::getDailyVisitors/$1');
    $routes->get('booking/getWeeklyCounts', 'BookingController::getWeeklyCounts');
    $routes->get('booking/getMonthlyCounts', 'BookingController::getMonthlyCounts');
    $routes->get('booking/getYearlyCounts', 'BookingController::getYearlyCounts');
    $routes->get('cron/runDailyTask', 'CronController::updateMissingTimes');

    // =====================
    // 📊 Booking By Date Table (new unified view)
    // =====================
    // Example URLs this will match:
    //   booking_by_date_table/day/2025-11-13
    //   booking_by_date_table/week/2025-11-10
    //   booking_by_date_table/month/2025-11
    //   booking_by_date_table/year/2025
    $routes->get('booking_by_date_table/(:segment)/(:segment)', 'BookingController::bookingByDateTable/$1/$2');
    $routes->group('divisions', ['filter' => 'auth'], function ($routes) {
        $routes->get('/', 'DivisionController::index');
        $routes->get('add', 'DivisionController::add');
        $routes->post('save', 'DivisionController::store');
    });
});