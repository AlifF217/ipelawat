<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;
use Config\Database;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\QRImage;

class PelawatController extends Controller
{
    // Show registration form
    public function daftar()
    {
        $db = Database::connect();
        $userModel = new UserModel();

        // Load officers (pegawai)
        $users = $userModel->findAll();

        // Load guest list for dropdown + datalist (SORT A-Z)
        $guests = $db->table('guest')
            ->select('id, name')
            ->orderBy('name', 'ASC')  // ⭐ SORT A-Z
            ->get()
            ->getResultArray();

        return view('pelawat_daftar', [
            'users'  => $users,
            'guests' => $guests,
        ]);
    }

    // AJAX: return guest details by id
    public function getGuestDetails()
    {
        $db = Database::connect();
        $id = $this->request->getPost('id');

        if (empty($id) || !is_numeric($id)) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'Invalid guest id'
            ]);
        }

        $guest = $db->table('guest')->where('id', $id)->get()->getRowArray();

        if (! $guest) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Not found']);
        }

        return $this->response->setJSON([
            'id'     => $guest['id'],
            'name'   => $guest['name'] ?? '',
            'tel'    => $guest['tel'] ?? '',
            'reason' => $guest['reason'] ?? '',
        ]);
    }

    // Handle submission (insert or reuse guest)
    public function simpan()
    {
        $db = Database::connect();
        $request = service('request');

        $name        = trim($request->getPost('name'));
        $tel         = trim($request->getPost('phone_no'));
        $officer     = trim($request->getPost('officer'));
        $reason      = trim($request->getPost('reason'));
        $timeIn      = $request->getPost('time_in');
        $timeOutExp  = $request->getPost('time_out_exp');
        $timeOutReal = $request->getPost('time_out_real');

        if (! $name || ! $tel || ! $reason) {
            session()->setFlashdata('error', 'Nama, telefon dan sebab lawatan diperlukan.');
            return redirect()->back()->withInput();
        }

        // Check if guest exists
        $guest = $db->table('guest')
            ->where('name', $name)
            ->where('tel', $tel)
            ->get()
            ->getRowArray();

        if ($guest) {
            $guestId = $guest['id'];
        } else {
            $db->table('guest')->insert([
                'name'   => $name,
                'tel'    => $tel,
                'reason' => $reason,
            ]);
            $guestId = $db->insertID();
        }

        $db->table('booking')->insert([
            'name'         => $name,
            'phone_no'     => $tel,
            'officer'      => $officer,
            'reason'       => $reason,
            'time_in'      => $timeIn,
            'time_out_exp' => $timeOutExp,
            'time_out_real'=> $timeOutReal,
            'pelawat'      => 'pelawat'
        ]);

        $bookingId = $db->insertID();

        session()->setFlashdata('success', 'Pendaftaran berjaya!');
        return redirect()->to(base_url("pelawat/edit/$bookingId"));
    }

    // Manual registration
    public function daftarManual()
    {
        $db = Database::connect();
        $userModel = new UserModel();

        $users = $userModel->findAll();

        return view('pendaftaran_manual', [
            'users' => $users
        ]);
    }

    public function getUserDetails()
    {
        $db = Database::connect();
        $id = $this->request->getPost('id');

        if (empty($id) || !is_numeric($id)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Id pengguna tidak sah']);
        }

        $user = $db->table('users')->where('Id', $id)->get()->getRowArray();

        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Pengguna tidak wujud']);
        }

        return $this->response->setJSON([
            'id'    => $user['Id'],
            'Name'  => $user['Name'] ?? '',
            'Phone' => $user['Phone'] ?? ''
        ]);
    }

    public function simpanManual()
    {
        $db = Database::connect();
        $request = service('request');

        $name        = trim($request->getPost('name'));
        $tel         = trim($request->getPost('phone_no'));
        $officer     = trim($request->getPost('officer'));
        $reason      = trim($request->getPost('reason'));
        $timeIn      = $request->getPost('time_in');
        $timeOutExp  = $request->getPost('time_out_exp');
        $timeOutReal = $request->getPost('time_out_real');

        if (! $name || ! $tel || ! $reason) {
            session()->setFlashdata('error', 'Nama, telefon dan sebab lawatan diperlukan.');
            return redirect()->back()->withInput();
        }

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

        session()->setFlashdata('success', 'Pendaftaran manual berjaya!');
        return redirect()->to(base_url("pentadbir/edit/$bookingId"));
    }

    public function simpanPentadbir()
    {
        $db = Database::connect();
        $request = service('request');

        $name        = trim($request->getPost('name'));
        $tel         = trim($request->getPost('phone_no'));
        $officer     = trim($request->getPost('officer'));
        $reason      = trim($request->getPost('reason'));
        $timeIn      = $request->getPost('time_in');
        $timeOutExp  = $request->getPost('time_out_exp');
        $timeOutReal = $request->getPost('time_out_real');

        if (! $name || ! $tel || ! $reason) {
            session()->setFlashdata('error', 'Nama, telefon dan sebab lawatan diperlukan.');
            return redirect()->back()->withInput();
        }

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
        return redirect()->to(base_url("edit/$bookingId"));
    }

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

    $editURL = base_url("pelawat/edit/$id");

    $encoded = base64_encode($booking['booking_id']);

    // QR code generation
    $options = new \chillerlan\QRCode\QROptions([
        'version'      => 5,
        'outputType'   => \chillerlan\QRCode\QRCode::OUTPUT_MARKUP_SVG,
        'eccLevel'     => \chillerlan\QRCode\QRCode::ECC_L,
        'scale'        => 5,
    ]);

    $qr = (new \chillerlan\QRCode\QRCode($options))->render($editURL);

    $pngFileName = WRITEPATH . "uploads/qr_$id.png";
    $optionsPNG = new \chillerlan\QRCode\QROptions([
        'version'      => 5,
        'outputType'   => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
        'eccLevel'     => \chillerlan\QRCode\QRCode::ECC_L,
        'scale'        => 5,
    ]);
    $qrPNG = (new \chillerlan\QRCode\QRCode($optionsPNG))->render($editURL);
    file_put_contents($pngFileName, $qrPNG);

    // ✅ Fetch users for officer dropdown
    $userModel = new UserModel();
    $users = $userModel->findAll();

    return view('edit_booking', [
        'booking'    => $booking,
        'qr'         => $qr,
        'allowEdit'  => $allowEdit,
        'deadline'   => "18:00:00",
        'encoded'    => $encoded,
        'qrPNG'      => $qrPNG,
        'editURL'    => $editURL,
        'users'      => $users  // pass this to the view
    ]);
}
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
        return redirect()->to(base_url("pelawat/edit/$id"));
    }


public function searchGuest()
{
    $db = Database::connect();
    $keyword = $this->request->getPost('keyword');

    $results = $db->table('guest')
        ->like('name', $keyword)
        ->limit(10)
        ->get()
        ->getResultArray();

    return $this->response->setJSON($results);
}



}

