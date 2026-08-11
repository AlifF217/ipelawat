<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Database;

class DbTest extends Controller
{
    public function index()
    {
        try {
            $db = Database::connect();   // Attempt to connect
            echo "Database connected successfully!";
        } catch (\Exception $e) {
            echo "Database connection failed: " . $e->getMessage();
        }
    }
}
