<?php

namespace App\Models;

use CodeIgniter\Model;

class GuestModel extends Model
{
    protected $table      = 'guest';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'tel']; // columns you want to allow

    protected $useTimestamps = true; // optional, if your table has created_at/updated_at
}
