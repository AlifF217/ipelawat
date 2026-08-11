<?php 

namespace App\Controllers;

use App\Models\UserModel;

class Home extends BaseController
{
public function homepage()
{
    $session = session();

    return view('homepage', [
        'error'   => $session->getFlashdata('error'),
        'success'=> $session->getFlashdata('success')
    ]);
}

public function login()
{
    $session = session();
    $request = $this->request;

    $email = trim($request->getPost('Email'));
    $password = $request->getPost('Password');

    $userModel = new UserModel();
    $user = $userModel->where('Email', $email)->first();

    if ($user && password_verify($password, $user['Password'])) {
        // ✅ Save user data to session
        $session->set([
            'user_id'    => $user['Id'],
            'name'       => $user['Name'],
            'email'      => $user['Email'],
            'level'      => strtolower($user['Level']),
            'isLoggedIn' => true
        ]);

        // ✅ Redirect user based on level
        switch (strtolower($user['Level'])) {
            case 'pentadbir':
                return redirect()->to('menu');
            case 'superadmin':
                return redirect()->to('menu');
            default:
                return redirect()->to('menu');
        }

    } else {
        $session->setFlashdata('error', 'Email atau password yang salah');
        return redirect()->to('/educated');
    }
}

public function signup()
{
    $session = session();
    $request = $this->request;

    $name = trim($request->getPost('Name'));
    $email = trim($request->getPost('Email'));
    $password = $request->getPost('Password');

    $userModel = new UserModel();

    // 1️⃣ Check if email or name already exists
    $existingUser = $userModel
        ->where('Email', $email)
        ->orWhere('Name', $name)
        ->first();

    if ($existingUser) {
        if ($existingUser['Email'] === $email && $existingUser['Name'] === $name) {
            $session->setFlashdata('error', 'Nama dan Email sudah didaftar');
        } elseif ($existingUser['Email'] === $email) {
            $session->setFlashdata('error', 'Email sudah didaftar');
        } elseif ($existingUser['Name'] === $name) {
            $session->setFlashdata('error', 'Nama sudah digunakan');
        }
        return redirect()->to('/educated');
    }

    // 2️⃣ Validate email domain
    $allowedDomains = ['gmail.com','selangor.gov.my' ,'yahoo.com', 'outlook.com', 'hotmail.com', 'icloud.com', 'protonmail.com'];
    $domain = substr(strrchr($email, "@"), 1);
    if (!in_array(strtolower($domain), $allowedDomains)) {
        $session->setFlashdata('error', 'Sila gunakan alamat email yang sah (Gmail, Yahoo, Outlook).');
        return redirect()->to('/educated');
    }

    // 3️⃣ Validate password strength
    $pattern = '/^(?=(?:.*[A-Z]){2,})(?=(?:.*[a-z]){2,})(?=(?:.*\d){2,})(?=(?:.*[!@#$%^&*(),.?":{}|<>]){1,}).{8,20}$/';
    if (!preg_match($pattern, $password)) {
        $session->setFlashdata('error',
            'Kata laluan mestilah panjang sebanyak 8–20 karakter, mengandungi sekurang-kurangnya 2 Huruf besar, 2 Huruf kecil, 2 nombor, dan 1 huruf/karakter unik.'
        );
        return redirect()->to('/educated');
    }

    // 4️⃣ Create user (default level = user)
    $userModel->insert([
        'Name'     => $name,
        'Email'    => $email,
        'Password' => password_hash($password, PASSWORD_DEFAULT),
        'Level'    => 'pentadbir'
    ]);

    // ✅ Instead of auto-login, show success message on same page
    $session->setFlashdata('success', 'Pendaftaran berjaya! Sila log masuk menggunakan data yang didaftar.');
    return redirect()->to('/educated'); // stay on the same page
}


    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }

    public function accessdenied()
{
    return view('accessdenied');
}
}
