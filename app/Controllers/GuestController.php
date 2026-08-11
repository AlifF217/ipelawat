<?php

namespace App\Controllers;

use App\Models\GuestModel;
use CodeIgniter\Controller;

class GuestController extends Controller
{
    protected $guestModel;

    public function __construct()
    {
        $this->guestModel = new GuestModel();
        helper(['url', 'form']);
    }

    // List all guests
   public function list()
{
    $q = $this->request->getGet('q'); // get search query
    if ($q) {
        $guests = $this->guestModel
                        ->like('name', $q)
                        ->orLike('tel', $q)
                        ->findAll();
    } else {
        $guests = $this->guestModel->findAll();
    }

    return view('guest_list', [
        'guests' => $guests
    ]);
}
}
