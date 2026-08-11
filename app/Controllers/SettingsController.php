<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class SettingsController extends Controller
{
    public function index()
    {
        $data['name'] = session()->get('name');
        return view('settings', $data);
    }
}
