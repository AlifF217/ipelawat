<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class ProfileController extends Controller
{
    public function index()
    {
        $session = session();
        $userModel = new UserModel();
        $userId = $session->get('user_id');

        $user = $userModel->find($userId);

        if (!$user) {
            return redirect()->to('/login')->with('error', 'User not found.');
        }

        $data = [
            'userId'     => $userId,
            'name'       => $user['Name'] ?? 'Tidak diketahui',
            'email'      => $user['Email'] ?? '-',
            'division'   => ucfirst($user['Division'] ?? '-'),
            'phone'      => $user['Phone'] ?? '-',
            'level'      => ucfirst($user['Level'] ?? 'User'),
            'active'     => $user['Active'] ? 'Aktif' : 'Tidak Aktif',
            'profilePic' => !empty($user['ProfilePicture'])
                ? base_url('uploads/profile_pics/' . $user['ProfilePicture'])
                : base_url('images/default-avatar.png'),
        ];

        return view('profile', $data);
    }

    public function edit()
    {
        $session = session();
        $userModel = new UserModel();
        $divisionModel = new \App\Models\DivisionModel();

        $userId = $session->get('user_id');
        $user = $userModel->find($userId);
        $divisions = $divisionModel->findAll();

        $data = [
            'userId' => $userId,
            'name' => $user['Name'] ?? 'Unknown',
            'email' => $user['Email'] ?? 'Not available',
            'phone' => $user['Phone'] ?? '',
            'division' => $user['Division'] ?? '',
            'profilePic' => !empty($user['ProfilePicture'])
                ? base_url('uploads/profile_pics/' . $user['ProfilePicture'])
                : base_url('images/default-avatar.png'),
            'divisions' => $divisions,
        ];

        return view('edit_profile', $data);
    }

    public function update()
    {
        $session = session();
        $userModel = new UserModel();
        $userId = $session->get('user_id');

        // Validation
        $validation = \Config\Services::validation();
        $validation->setRules([
            'Name'   => 'required|min_length[2]|max_length[100]',
            'Email'  => 'required|valid_email',
            'Phone'  => 'permit_empty|min_length[8]|max_length[20]',
            'Division' => 'required',
            'ProfilePicture' => 'if_exist|is_image[ProfilePicture]|max_size[ProfilePicture,2048]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->with('error', $validation->getErrors());
        }

        // Input values
        $newName  = trim($this->request->getPost('Name'));
        $newEmail = trim($this->request->getPost('Email'));
        $newPhone = trim($this->request->getPost('Phone'));
        $division = $this->request->getPost('Division');

    // Duplicate checking
if ($userModel->where('Name', $newName)->where('Id !=', $userId)->first()) {
    return redirect()->back()->with('error', "Nama sudah digunakan oleh pengguna lain.");
}

if ($userModel->where('Email', $newEmail)->where('Id !=', $userId)->first()) {
    return redirect()->back()->with('error', "Email sudah wujud dalam sistem.");
}

if ($userModel->where('Phone', $newPhone)->where('Id !=', $userId)->first()) {
    return redirect()->back()->with('error', "Nombor telefon telah digunakan oleh pengguna lain.");
}

        // Update array
        $data = [
            'Name'     => $newName,
            'Email'    => $newEmail,
            'Phone'    => $newPhone,
            'Division' => $division
        ];

        // File upload
        $img = $this->request->getFile('ProfilePicture');
        if ($img && $img->isValid() && !$img->hasMoved()) {
            $newNameImg = $img->getRandomName();
            $img->move(ROOTPATH . 'public/uploads/profile_pics', $newNameImg);
            $data['ProfilePicture'] = $newNameImg;
        }

        $userModel->update($userId, $data);

return redirect()->to('profile/edit')->with('success', 'Profil berjaya dikemaskini!');
    }

}
