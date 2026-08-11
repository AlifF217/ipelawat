<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Database;

class CronController extends Controller
{
    public function updateMissingTimes()
{
    $db = Database::connect();

    // Get all rows needing updates
    $rows = $db->table('booking')
        ->select("booking_id, time_in, time_out_exp, time_out_real")
        ->get()
        ->getResultArray();

    foreach ($rows as $row) {
        $id = $row['booking_id'];
        $date = date('Y-m-d', strtotime($row['time_in'])); // extract date from time_in

        $exp = $row['time_out_exp'];
        $real = $row['time_out_real'];

        // Check if time_out_exp is empty or 00:00:00
        $isEmpty = (
            $exp === null ||
            $exp === '' ||
            $exp === '0000-00-00 00:00:00' ||
            substr($exp, 11) === '00:00:00'
        );

        if ($isEmpty) {
            // Set both to 18:00:00
            $newTime = $date . " 18:00:00";

            $db->table('booking')->where('booking_id', $id)->update([
                'time_out_exp'  => $newTime,
                'time_out_real' => $newTime
            ]);

        } else {
            // Copy time_out_exp → time_out_real
            $db->table('booking')->where('booking_id', $id)->update([
                'time_out_real' => $exp
            ]);
        }
    }

    return $this->response->setJSON([
        'status' => 'success',
        'message' => 'Masa Keluar telah diubah'
    ]);
}
}
