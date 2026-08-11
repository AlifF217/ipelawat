<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;
use Config\Database;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class PentadbirController extends Controller
{
    // ================================
    // 1. MANUAL REGISTRATION FORM
    // ================================
    public function daftar()
    {
        $db = Database::connect();
        $userModel = new UserModel();

        // Load all users for dropdown
        $users = $userModel->findAll();

        return view('pendaftaran_manual', [
            'users' => $users
        ]);
    }

    // ================================
    // 2. AJAX: GET USER DETAILS
    // ================================
    public function getUserDetails()
    {
        $db = Database::connect();
        $id = $this->request->getPost('id');

        if (empty($id) || !is_numeric($id)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid user id']);
        }

        $user = $db->table('users')->where('Id', $id)->get()->getRowArray();

        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'User not found']);
        }

        return $this->response->setJSON([
            'id'    => $user['Id'],
            'Name'  => $user['Name'] ?? '',
            'Phone' => $user['Phone'] ?? ''
        ]);
    }

// ================================
// 3. SAVE MANUAL BOOKING
// ================================
public function simpan()
{
    $request = service('request');
    $db = Database::connect();

    $name        = trim($request->getPost('name')); // hidden input from form
    $tel         = trim($request->getPost('phone_no'));
    $officer     = trim($request->getPost('officer'));
    $reason      = trim($request->getPost('reason'));
    $timeIn      = $request->getPost('time_in');
    $timeOutExp  = $request->getPost('time_out_exp');
    $timeOutReal = $request->getPost('time_out_real');

    // Basic validation
    if (!$name || !$tel || !$reason) {
        session()->setFlashdata('error', 'Nama, telefon dan sebab lawatan diperlukan.');
        return redirect()->back()->withInput();
    }

    // Optional: validate phone format (10 digits, starts with 0)
    if (!preg_match('/^0\d{9}$/', $tel)) {
        session()->setFlashdata('error', 'Nombor telefon tidak sah.');
        return redirect()->back()->withInput();
    }

    // Optional: validate time (changed from 1 hour to 30 minutes)
    if ($timeIn && $timeOutExp) {
        $inDate = new \DateTime($timeIn);
        $outDate = new \DateTime($timeOutExp);
        $diff = $outDate->getTimestamp() - $inDate->getTimestamp();
        if ($diff < 1800) { // less than 30 minutes (1800 seconds)
            session()->setFlashdata('error', 'Masa keluar dijangka mesti sekurang-kurangnya 30 minit selepas masa masuk.');
            return redirect()->back()->withInput();
        }
    }

    // Insert new booking
    $db->table('booking')->insert([
        'name'         => $name,
        'phone_no'     => $tel,
        'officer'      => $officer,
        'reason'       => $reason,
        'time_in'      => $timeIn,
        'time_out_exp' => $timeOutExp,
        'time_out_real'=> $timeOutReal,
        'pelawat'      => 'pentadbir'
    ]);

    $bookingId = $db->insertID();
    session()->setFlashdata('success', 'Pendaftaran Pentadbir berjaya!');

    return redirect()->to(base_url("editRegManual/$bookingId"));
}


// ================================ 
// 4. EDIT BOOKING PAGE (WITH QR) - UPDATED 
public function edit($id)
{
    $db = Database::connect();
    $booking = $db->table('booking')->where('booking_id', $id)->get()->getRowArray();
    if (!$booking) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Rekod tidak dijumpai.");
    }

    date_default_timezone_set('Asia/Kuala_Lumpur');
    $currentTime = date('H:i');
    $allowEdit = ($currentTime >= "08:00" && $currentTime <= "18:00");

    $editURL = base_url("editRegManual/$id");

    // QR Code SVG
    $options = new \chillerlan\QRCode\QROptions([
        'version' => 5,
        'outputType' => \chillerlan\QRCode\QRCode::OUTPUT_MARKUP_SVG,
        'eccLevel' => \chillerlan\QRCode\QRCode::ECC_L,
        'scale' => 5,
    ]);
    $qr = (new \chillerlan\QRCode\QRCode($options))->render($editURL);

    // QR PNG
    $pngPath = WRITEPATH . "uploads/qr_pentadbir_$id.png";
    $optionsPNG = new \chillerlan\QRCode\QROptions([
        'version' => 5,
        'outputType' => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
        'eccLevel' => \chillerlan\QRCode\QRCode::ECC_L,
        'scale' => 5,
    ]);
    $qrPNG = (new \chillerlan\QRCode\QRCode($optionsPNG))->render($editURL);
    file_put_contents($pngPath, $qrPNG);

    $userModel = new UserModel();
    $users = $userModel->findAll();

    // Prefill user
    $selectedUser = null;
    foreach ($users as $u) {
        if ($u['Id'] == $booking['name']) { // assuming booking['name'] stores user id
            $selectedUser = $u;
            break;
        }
    }

    // Prefill officer
    $selectedOfficer = null;
    foreach ($users as $u) {
        if ($u['Name'] == $booking['officer']) {
            $selectedOfficer = $u;
            break;
        }
    }

    return view('edit_pentadbir', [
        'booking' => $booking,
        'qr' => $qr,
        'qrPNG' => base_url("writable/uploads/qr_pentadbir_$id.png"),
        'allowEdit' => $allowEdit,
        'editURL' => $editURL,
        'users' => $users,
        'selectedUser' => $selectedUser,
        'selectedOfficer' => $selectedOfficer
    ]);
}

    // ================================
    // 5. UPDATE BOOKING
    // ================================
    public function update($id)
    {
        $db = Database::connect();

        $data = [
            'name'         => $this->request->getPost('name'),
            'phone_no'     => $this->request->getPost('phone_no'),
            'officer'      => $this->request->getPost('officer'),
            'reason'       => $this->request->getPost('reason'),
            'time_in'      => $this->request->getPost('time_in'),
            'time_out_exp' => $this->request->getPost('time_out_exp'),
            'time_out_real'=> $this->request->getPost('time_out_real')
        ];

        $db->table('booking')->where('booking_id', $id)->update($data);

        session()->setFlashdata('success', 'Rekod berjaya dikemaskini!');

        return redirect()->to(base_url("editRegManual/$id"));
    }
}
