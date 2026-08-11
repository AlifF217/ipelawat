<?php

namespace App\Models;

use CodeIgniter\Model;

class PelawatModel extends Model
{
    protected $table = 'booking'; // your table name
    protected $primaryKey = 'booking_id';
    protected $allowedFields = [
        'name', 'phone_no', 'officer', 'reason', 
        'time_in', 'time_out_exp', 'time_out_real'
    ];
}
