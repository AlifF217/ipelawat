<?php

namespace App\Controllers;

use App\Models\PelawatModel;
use App\Models\UserModel;
use App\Models\GuestModel;
use CodeIgniter\Controller;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
 
class BookingUserController extends Controller
{
    protected $pelawatModel;
    protected $userModel;
    protected $guestModel;

    public function __construct()
    {
        $this->pelawatModel = new PelawatModel();
        $this->userModel = new UserModel();
        $this->guestModel = new GuestModel();
        helper(['url', 'form']);
    }

    // ================================
    // 1. LIST BOOKINGS WITH TYPE TOGGLE
    // ================================
    public function index()
    {
        $type = $this->request->getGet('type') ?? 'pelawat';
        $bookings = $this->pelawatModel->where('pelawat', $type)->findAll();

        return view('booking_user_table', [
            'bookings' => $bookings,
            'type'     => $type
        ]);
    }

    // ================================
    // 2. VIEW BOOKING (WITH QR)
    // ================================
    public function view($booking_id)
    {
        $booking = $this->pelawatModel->find($booking_id);

        if (!$booking) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Booking ID $booking_id not found");
        }

        date_default_timezone_set('Asia/Kuala_Lumpur');
        $currentTime = date('H:i');
        $allowEdit = ($currentTime >= '08:00' && $currentTime <= '18:00');

        // QR URL for editing
        $editURL = base_url("booking_user/edit/$booking_id");

        return view('booking_view', [
            'booking'   => $booking,
            'allowEdit' => $allowEdit,
            'qr'        => $this->generateQR($editURL),
            'qrPNG'     => $this->generateQRPNG($editURL),
            'editURL'   => $editURL
        ]);
    }

    // ================================
    // 3. EDIT BOOKING
    // ================================
    public function edit($booking_id)
    {
        $booking = $this->pelawatModel->find($booking_id);

        if (!$booking) {
            return redirect()->back()->with('error', 'Booking not found');
        }

        // Determine if editing is allowed (8:00 - 18:00)
        date_default_timezone_set('Asia/Kuala_Lumpur');
        $hour = (int) date('H');
        $allowEdit = $hour >= 8 && $hour < 18;

        // Generate the edit URL
        $editURL = base_url("booking_user/edit/$booking_id");

        // Generate QR code
        $qr = $this->generateQR($editURL);
        $qrPNG = $this->generateQRPNG($editURL);

        // Load user list from users table (for pentadbir and officers)
        $users = $this->userModel->select('Name, Phone')->findAll();
        
        // Load guest list from guest table
        $guests = $this->guestModel->select('name, tel')->findAll();
        
        // Get selected data
        $selectedUser = null;
        $selectedOfficer = null;
        
        // Determine if this is pentadbir or pelawat
        $isPentadbir = (isset($booking['pelawat']) && strtolower($booking['pelawat']) === 'pentadbir');
        
        // Get user/guest data based on type
        if ($isPentadbir) {
            // For pentadbir: get from users table
            if (!empty($booking['name'])) {
                $userResult = $this->userModel->select('Name, Phone')
                                           ->where('Name', $booking['name'])
                                           ->first();
            }
            
            if (!empty($userResult)) {
                $selectedUser = [
                    'Name' => $userResult['Name'] ?? ($userResult->Name ?? ''),
                    'Phone' => $userResult['Phone'] ?? ($userResult->Phone ?? '')
                ];
            } else {
                // Fallback to booking data
                $selectedUser = [
                    'Name' => $booking['name'] ?? '',
                    'Phone' => $booking['phone_no'] ?? ''
                ];
            }
        } else {
            // For pelawat: get from guest table
            if (!empty($booking['name'])) {
                $guestResult = $this->guestModel->select('name, tel')
                                             ->where('name', $booking['name'])
                                             ->first();
            }
            
            if (!empty($guestResult)) {
                $selectedUser = [
                    'Name' => $guestResult['name'] ?? ($guestResult->name ?? ''),
                    'Phone' => $guestResult['tel'] ?? ($guestResult->tel ?? '')
                ];
            } else {
                // Fallback to booking data
                $selectedUser = [
                    'Name' => $booking['name'] ?? '',
                    'Phone' => $booking['phone_no'] ?? ''
                ];
            }
        }
        
        // Get officer data (always from users table)
        if (!empty($booking['officer'])) {
            $officerResult = $this->userModel->select('Name, Phone')
                                          ->where('Name', $booking['officer'])
                                          ->first();
            
            if (!empty($officerResult)) {
                $selectedOfficer = [
                    'Name' => $officerResult['Name'] ?? ($officerResult->Name ?? $booking['officer']),
                    'Phone' => $officerResult['Phone'] ?? ($officerResult->Phone ?? null)
                ];
            } else {
                $selectedOfficer = [
                    'Name' => $booking['officer'],
                    'Phone' => null
                ];
            }
        }

        // For edit_pentadbir_form.php compatibility, pass all data
        return view('booking_edit', [
            'booking'        => $booking,
            'allowEdit'      => $allowEdit,
            'qr'             => $qr,
            'qrPNG'          => $qrPNG,
            'editURL'        => $editURL,
            'users'          => $users,
            'guests'         => $guests,
            'selectedUser'    => $selectedUser,
            'selectedOfficer' => $selectedOfficer,
            'isPentadbir'    => $isPentadbir
        ]);
    }

    // ================================
    // 4. UPDATE BOOKING - FIXED VERSION
    // ================================
    public function update($booking_id)
    {
        // Get database connection
        $db = \Config\Database::connect();
        
        // Check if edit is allowed (8:00 AM - 6:00 PM)
        date_default_timezone_set('Asia/Kuala_Lumpur');
        $currentTime = date('H:i');
        if (!($currentTime >= '08:00' && $currentTime <= '18:00')) {
            session()->setFlashdata('error', 'Pengemaskinian hanya dibenarkan antara jam 8:00 pagi hingga 6:00 petang.');
            return redirect()->back()->withInput();
        }

        // Get time values from POST
        $timeInTime = $this->request->getPost('time_in');
        $timeOutExpTime = $this->request->getPost('time_out_exp');
        $timeOutRealTime = $this->request->getPost('time_out_real');
        
        // Get date values from hidden fields - THESE MUST MATCH THE VIEW
        $timeInDate = $this->request->getPost('time_in_date');
        $timeOutExpDate = $this->request->getPost('time_out_exp_date');
        $timeOutRealDate = $this->request->getPost('time_out_real_date');
        
        // Debug logging - Check what's being received
        error_log("DEBUG - Received POST data:");
        error_log("time_in_time: " . $timeInTime);
        error_log("time_in_date: " . $timeInDate);
        error_log("time_out_exp_time: " . $timeOutExpTime);
        error_log("time_out_exp_date: " . $timeOutExpDate);
        error_log("time_out_real_time: " . $timeOutRealTime);
        error_log("time_out_real_date: " . $timeOutRealDate);

        // Combine date and time to create full datetime - FIXED VERSION
        $timeIn = null;
        $timeOutExp = null;
        $timeOutReal = null;
        
        // For time_in: use provided date or today's date if empty
        if (!empty($timeInTime)) {
            if (empty($timeInDate)) {
                $timeInDate = date('Y-m-d');
                error_log("DEBUG - time_in_date was empty, using today: " . $timeInDate);
            }
            // Create proper datetime string
            $timeIn = $timeInDate . ' ' . $timeInTime . ':00';
            error_log("DEBUG - Generated time_in: " . $timeIn);
        } else {
            error_log("DEBUG - time_in_time is empty!");
        }
        
        // For time_out_exp: use provided date or same date as time_in if empty
        if (!empty($timeOutExpTime)) {
            if (empty($timeOutExpDate)) {
                // If time_in has a date, use it; otherwise use today
                $timeOutExpDate = !empty($timeInDate) ? $timeInDate : date('Y-m-d');
                error_log("DEBUG - time_out_exp_date was empty, using: " . $timeOutExpDate);
            }
            // Create proper datetime string
            $timeOutExp = $timeOutExpDate . ' ' . $timeOutExpTime . ':00';
            error_log("DEBUG - Generated time_out_exp: " . $timeOutExp);
        } else {
            error_log("DEBUG - time_out_exp_time is empty!");
        }
        
        // For time_out_real: use provided date or same date as time_in if empty
        if (!empty($timeOutRealTime)) {
            if (empty($timeOutRealDate)) {
                // If time_in has a date, use it; otherwise use today
                $timeOutRealDate = !empty($timeInDate) ? $timeInDate : date('Y-m-d');
                error_log("DEBUG - time_out_real_date was empty, using: " . $timeOutRealDate);
            }
            // Create proper datetime string
            $timeOutReal = $timeOutRealDate . ' ' . $timeOutRealTime . ':00';
            error_log("DEBUG - Generated time_out_real: " . $timeOutReal);
        } else {
            error_log("DEBUG - time_out_real_time is empty!");
        }

        // Validate time constraints (only if both times are provided)
        if (!empty($timeIn) && !empty($timeOutExp)) {
            if (!$this->validateTimeConstraints($timeIn, $timeOutExp)) {
                session()->setFlashdata('error', 'Masa keluar mesti sekurang-kurangnya 30 minit selepas masa masuk (dan tidak melebihi 10 jam).');
                return redirect()->back()->withInput();
            }
        }

        // Get name and officer from POST
        $name = $this->request->getPost('name');
        $officer = $this->request->getPost('officer');
        
        // Get booking data to determine pelawat type
        $booking = $db->table('pelawat')->where('booking_id', $booking_id)->get()->getRowArray();
        
        if (!$booking) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Booking ID $booking_id not found");
        }
        
        // Determine pelawat type
        $isPentadbir = (isset($booking['pelawat']) && strtolower($booking['pelawat']) === 'pentadbir');
        
        // Find user ID based on type
        $userId = '';
        if ($isPentadbir) {
            // For pentadbir: check if name exists in users table
            $user = $db->table('users')->select('Id')->where('Name', $name)->get()->getRowArray();
            $userId = $user ? $user['Id'] : '0';
        } else {
            // For pelawat: check if name exists in guest table
            $guest = $db->table('guest')->select('id')->where('name', $name)->get()->getRowArray();
            $userId = $guest ? $guest['id'] : '0';
        }

        // Prepare data for update - CRITICAL: Ensure datetime fields are properly set
        $data = [
            'user_id'      => $userId,
            'name'         => $name,
            'phone_no'     => $this->request->getPost('phone_no'),
            'officer'      => $officer,
            'reason'       => $this->request->getPost('reason'),
            'updated_at'   => date('Y-m-d H:i:s')
        ];

        // Set time fields - always set them, even if empty (to prevent 0000-00-00)
        if (!empty($timeIn)) {
            // Validate the datetime format
            if (strtotime($timeIn) !== false) {
                $data['time_in'] = $timeIn;
                error_log("DEBUG - Setting time_in: " . $timeIn);
            } else {
                error_log("ERROR - Invalid time_in format: " . $timeIn);
                session()->setFlashdata('error', 'Format masa masuk tidak sah.');
                return redirect()->back()->withInput();
            }
        } else {
            // If time_in is empty, use a default valid datetime
            $data['time_in'] = date('Y-m-d') . ' 08:00:00';
            error_log("DEBUG - Using default time_in: " . $data['time_in']);
        }
        
        if (!empty($timeOutExp)) {
            // Validate the datetime format
            if (strtotime($timeOutExp) !== false) {
                $data['time_out_exp'] = $timeOutExp;
                error_log("DEBUG - Setting time_out_exp: " . $timeOutExp);
            } else {
                error_log("ERROR - Invalid time_out_exp format: " . $timeOutExp);
                session()->setFlashdata('error', 'Format jangkaan masa keluar tidak sah.');
                return redirect()->back()->withInput();
            }
        } else {
            // If time_out_exp is empty, use a default valid datetime
            $data['time_out_exp'] = date('Y-m-d') . ' 17:00:00';
            error_log("DEBUG - Using default time_out_exp: " . $data['time_out_exp']);
        }
        
        if (!empty($timeOutReal)) {
            // Validate the datetime format
            if (strtotime($timeOutReal) !== false) {
                $data['time_out_real'] = $timeOutReal;
                error_log("DEBUG - Setting time_out_real: " . $timeOutReal);
            } else {
                error_log("ERROR - Invalid time_out_real format: " . $timeOutReal);
                // Don't fail on time_out_real, it can be NULL
                if (!empty($timeOutReal) && strtotime($timeOutReal) !== false) {
    $data['time_out_real'] = $timeOutReal;
}
            }
        } else {
            // time_out_real can be NULL
            if (!empty($timeOutReal) && strtotime($timeOutReal) !== false) {
    $data['time_out_real'] = $timeOutReal;
}
            error_log("DEBUG - Setting time_out_real to NULL");
        }

        // Debug the data being saved
        error_log("DEBUG - Data to be saved: " . print_r($data, true));

        // Validation checks
        $reason = $this->request->getPost('reason');
        if (strlen(trim($reason)) < 10 || strlen(trim($reason)) > 50) {
            session()->setFlashdata('error', 'Sebab lawatan mesti antara 10 hingga 50 aksara.');
            return redirect()->back()->withInput();
        }

        $phone = $this->request->getPost('phone_no');
        if (!preg_match('/^0[0-9]{9}$/', $phone)) {
            session()->setFlashdata('error', 'Nombor telefon mesti bermula dengan 0 dan tepat 10 digit.');
            return redirect()->back()->withInput();
        }

        // Validate that officer exists in users table
        $officerExists = $db->table('users')->where('Name', $officer)->get()->getRowArray();
        if (!$officerExists) {
            session()->setFlashdata('error', 'Pegawai yang dipilih tidak wujud dalam sistem.');
            return redirect()->back()->withInput();
        }

        // Validate that user exists in appropriate table
        if ($isPentadbir) {
            $userExists = $db->table('users')->where('Name', $name)->get()->getRowArray();
            if (!$userExists) {
                session()->setFlashdata('error', 'Nama pengguna tidak wujud dalam sistem.');
                return redirect()->back()->withInput();
            }
        } else {
            $guestExists = $db->table('guest')->where('name', $name)->get()->getRowArray();
            if (!$guestExists) {
                session()->setFlashdata('error', 'Nama pelawat tidak wujud dalam sistem.');
                return redirect()->back()->withInput();
            }
        }

        // Update the record
        $result = $db->table('pelawat')->where('booking_id', $booking_id)->update($data);
        
        // Log the result
        error_log("DEBUG - Update result: " . ($result ? 'Success' : 'Failed'));
        
        if ($result) {
            // Verify the update
            $updatedRecord = $db->table('pelawat')->where('booking_id', $booking_id)->get()->getRowArray();
            error_log("DEBUG - Updated record times:");
            error_log("time_in: " . ($updatedRecord['time_in'] ?? 'NULL'));
            error_log("time_out_exp: " . ($updatedRecord['time_out_exp'] ?? 'NULL'));
            error_log("time_out_real: " . ($updatedRecord['time_out_real'] ?? 'NULL'));
        }

        session()->setFlashdata('success', 'Rekod berjaya dikemaskini!');

        return redirect()->to(base_url("booking_user/view/$booking_id"));
    }

    // ================================
    // 5. UPDATE REGISTRATION MANUALLY (for the updateRegManual route)
    // ================================
    public function updateRegManual($booking_id)
    {
        return $this->update($booking_id);
    }

    // ================================
    // 6. DELETE BOOKING
    // ================================
    public function delete($booking_id)
    {
        // Either POST or AJAX only
        if (!$this->request->isAJAX() && $this->request->getMethod() !== 'post') {
            return redirect()->back();
        }

        $booking = $this->pelawatModel->find($booking_id);

        if ($booking) {
            $this->pelawatModel->delete($booking_id);
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false]);
    }

    // ================================
    // 7. VALIDATE TIME CONSTRAINTS
    // ================================
    private function validateTimeConstraints($timeIn, $timeOutExp)
    {
        if (empty($timeIn) || empty($timeOutExp)) {
            return false;
        }

        $inTime = strtotime($timeIn);
        $outTime = strtotime($timeOutExp);
        
        // Check if time out is after time in
        if ($outTime <= $inTime) {
            return false;
        }
        
        // Check if at least 30 minutes difference (new requirement)
        $diffMinutes = ($outTime - $inTime) / 60;
        if ($diffMinutes <= 30) {
            return false;
        }
        
        // Check if not more than 10 hours (optional constraint)
        if ($diffMinutes > 600) {
            return false;
        }
        
        return true;
    }

    // ================================
    // 10. PRIVATE QR GENERATION METHODS
    // ================================
    private function generateQR(string $url): string
    {
        $options = new QROptions([
            'version'    => 5,
            'outputType' => QRCode::OUTPUT_MARKUP_SVG,
            'eccLevel'   => QRCode::ECC_L,
            'scale'      => 5,
        ]);

        return (new QRCode($options))->render($url);
    }

    private function generateQRPNG(string $url): string
    {
        $pngPath = WRITEPATH . "uploads/qr_user_" . md5($url) . ".png";

        $options = new QROptions([
            'version'    => 5,
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel'   => QRCode::ECC_L,
            'scale'      => 5,
        ]);

        $qrPNG = (new QRCode($options))->render($url);
        file_put_contents($pngPath, $qrPNG);

        return base_url("writable/uploads/qr_user_" . md5($url) . ".png");
    }

    // ================================
    // 11. PELAWAT LIST
    // ================================
    public function pelawatList()
    {
        $pelawats = $this->pelawatModel->findAll();
        return view('pelawat_list', ['pelawats' => $pelawats]);
    }

    // ================================
    // 12. BOOKINGS BY DATE
    // ================================
    public function bookingsByDate($date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }
        
        $periodType = service('uri')->getSegment(2);
        
        switch($periodType) {
            case 'month':
                $periodType = 'monthly';
                $startDate = date('Y-m-01', strtotime($date));
                $endDate = date('Y-m-t', strtotime($date));
                $bookings = $this->pelawatModel
                    ->where('DATE(time_in) >=', $startDate)
                    ->where('DATE(time_in) <=', $endDate)
                    ->findAll();
                $displayDate = date('F Y', strtotime($date));
                break;
            case 'year':
                $periodType = 'yearly';
                $year = date('Y', strtotime($date));
                $bookings = $this->pelawatModel
                    ->where('YEAR(time_in)', $year)
                    ->findAll();
                $displayDate = $year;
                break;
            default:
                $periodType = 'daily';
                $bookings = $this->pelawatModel
                    ->where('DATE(time_in)', $date)
                    ->findAll();
                $displayDate = date('d/m/Y', strtotime($date));
        }

        return view('booking_date_list', [
            'bookings' => $bookings,
            'date' => $displayDate,
            'periodType' => $periodType
        ]);
    }
}