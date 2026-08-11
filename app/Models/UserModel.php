<?php
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';      // your database table name
    protected $primaryKey = 'Id';         // primary key column
    protected $allowedFields = ['Name', 'Email', 'Phone','Division', 'Level' ,'ProfilePicture', 'Password', 'Active'];

    protected $useTimestamps = false;     // set true if your table has created_at, updated_at

    // Optional: if using password hashing, you can add beforeInsert/Update callbacks
}
