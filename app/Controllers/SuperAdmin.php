<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\DivisionModel;
use CodeIgniter\Controller;

class SuperAdmin extends Controller
{
    public function index()
    {
        // Check user level from session
        $session = session();
        $userLevel = $session->get('level') ?? 'pentadbir';
        
        // Only superadmin can access
        if (strtolower($userLevel) !== 'superadmin') {
            return redirect()->to('accessdenied');
        }
        
        $userModel = new UserModel();
        $users = $userModel->findAll();
        
        return view('user_list', [
            'users' => $users,
            'userLevel' => $userLevel
        ]);
    }
    
    public function viewUser($id)
    {
        // Check user level from session
        $session = session();
        $userLevel = $session->get('level') ?? 'pentadbir';
        
        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Pengguna tidak dapat jumpa");
        }

        return view('view_user', [
            'user' => $user,
            'userLevel' => $userLevel
        ]);
    }

    public function editUser($id)
    {
        // Check user level from session
        $session = session();
        $userLevel = $session->get('level') ?? 'pentadbir';
        
       
        
        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Pengguna tidak dapat jumpa");
        }

        // ✅ Load divisions (added part)
        $divisionModel = new \App\Models\DivisionModel();
        $divisions = $divisionModel->findAll();

        // ✅ Pass divisions along with user data
        return view('edit_user', [
            'user' => $user,
            'divisions' => $divisions,
            'userLevel' => $userLevel
        ]);
    }

    public function updateUser($id)
    {
        // Check user level from session
        $session = session();
        $userLevel = $session->get('level') ?? 'pentadbir';
        
       
        $userModel = new UserModel();
        $request = $this->request;

        $name     = $request->getPost('Name');
        $email    = $request->getPost('Email');
        $phone    = $request->getPost('Phone');
        $division = $request->getPost('Division');
        $active   = $request->getPost('Active');

        // Server-side validation
        if (!preg_match('/^[A-Za-z@.\s]{10,40}$/', $name)) {
            return redirect()->to('editUser/' . $id)->with('error', 'Nama tidak sah.');
        }
        if (!preg_match('/^[A-Za-z0-9@.]{10,30}$/', $email)) {
            return redirect()->to('editUser/' . $id)->with('error', 'Email tidak sah.');
        }
        if (!preg_match('/^0\d{9}$/', $phone)) {
            return redirect()->to('editUser/' . $id)->with('error', 'No telefon tidak sah.');
        }

        // Check for duplicates excluding current user
        if ($userModel->where('Id !=', $id)->where('Name', $name)->first()) {
            return redirect()->to('editUser/' . $id)->with('error', 'Nama sudah wujud.');
        }
        if ($userModel->where('Id !=', $id)->where('Email', $email)->first()) {
            return redirect()->to('editUser/' . $id)->with('error', 'Email sudah wujud.');
        }
        if ($userModel->where('Id !=', $id)->where('Phone', $phone)->first()) {
            return redirect()->to('editUser/' . $id)->with('error', 'No telefon sudah wujud.');
        }

        // Update user
        $userModel->update($id, [
            'Name'     => $name,
            'Email'    => $email,
            'Phone'    => $phone,
            'Division' => $division,
            'Active'   => $active
        ]);

        // Redirect back to the edit page with success message
        return redirect()->to('editUser/' . $id)->with('success', 'Data pengguna berjaya diubah!');
    }
    
    public function deleteUser($id)
    {
        // Check user level from session
        $session = session();
        $userLevel = $session->get('level') ?? 'pentadbir';
        
        // Only superadmin can delete
        if (strtolower($userLevel) !== 'superadmin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Anda tidak mempunyai akses untuk memadam pengguna.'
            ]);
        }
        
        $userModel = new UserModel();
        
        // Check if user exists
        $user = $userModel->find($id);
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pengguna tidak ditemui.'
            ]);
        }
        
        // Prevent deleting superadmin users
        if (strtolower($user['Level']) === 'superadmin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak boleh memadam pengguna Superadmin.'
            ]);
        }
        
        // Delete the user
        $userModel->delete($id);
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Pengguna berjaya dipadam.'
        ]);
    }

    public function addUser()
    {
        // Check user level from session
        $session = session();
        $userLevel = $session->get('level') ?? 'pentadbir';
        
        $divisionModel = new DivisionModel();
        $allDivisions = $divisionModel->findAll();

        // ✅ Build tree recursively
        $tree = $this->buildDivisionTree($allDivisions);
        $data['divisions'] = $this->flattenDivisionTree($tree);
        $data['userLevel'] = $userLevel;

        return view('add_user', $data);
    }

    public function saveUser()
    {
        // Check user level from session
        $session = session();
        $userLevel = $session->get('level') ?? 'pentadbir';
        
        $request = $this->request;

        // Trim and fetch all form inputs
        $name     = trim($request->getPost('Name'));
        $email    = trim($request->getPost('Email'));
        $password = $request->getPost('Password');
        $division = trim($request->getPost('Division'));
        $phone    = trim($request->getPost('Phone'));
        $active   = $request->getPost('Active') ?? 1; // default to active

        $userModel = new UserModel();

        // Save old input to flashdata for repopulation
        $session->setFlashdata('oldInput', [
            'Name'     => $name,
            'Email'    => $email,
            'Division' => $division,
            'Phone'    => $phone,
            'Active'   => $active
        ]);

        // 1️⃣ Check if email or name already exists
        $existingUser = $userModel
            ->where('Email', $email)
            ->orWhere('Name', $name)
            ->first();

        if ($existingUser) {
            if ($existingUser['Email'] === $email && $existingUser['Name'] === $name) {
                $session->setFlashdata('error2', 'Nama dan Email sudah didaftar');
            } elseif ($existingUser['Email'] === $email) {
                $session->setFlashdata('error2', 'Email sudah didaftar');
            } elseif ($existingUser['Name'] === $name) {
                $session->setFlashdata('error2', 'Nama sudah digunakan');
            }
            return redirect()->to('addUser'); // go back to form
        }

        // 2️⃣ Validate email domain
        $allowedDomains = ['selangor.gov.my','gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com', 'icloud.com', 'protonmail.com'];
        $domain = substr(strrchr($email, "@"), 1);
        if (!in_array(strtolower($domain), $allowedDomains)) {
            $session->setFlashdata('error', 'Sila gunakan alamat email yang sah (selangor.gov.my, Gmail, Yahoo, Outlook, Hotmail, iCloud, ProtonMail).');
            return redirect()->to('addUser');
        }

        // 3️⃣ Validate password strength
        $pattern = '/^(?=(?:.*[A-Z]){2,})(?=(?:.*[a-z]){2,})(?=(?:.*\d){2,})(?=(?:.*[!@#$%^&*(),.?":{}|<>]){1,}).{8,20}$/';
        if (!preg_match($pattern, $password)) {
            $session->setFlashdata('error',
                'Kata laluan mesti 8–20 aksara, mengandungi sekurang-kurangnya 2 Huruf besar, 2 Huruf kecil, 2 nombor, dan 1 simbol khas.'
            );
            return redirect()->to('addUser');
        }

        // 4️⃣ Optional: Validate phone format
        if (!empty($phone) && !preg_match('/^0\d{9}$/', $phone)) {
            $session->setFlashdata('error', 'No telefon mesti 10 digit dan bermula dengan 0.');
            return redirect()->to('addUser');
        }

        // 5️⃣ Optional: Validate division selected
        if (empty($division)) {
            $session->setFlashdata('error', 'Sila pilih bahagian.');
            return redirect()->to('addUser');
        }

        // 6️⃣ Insert user with Active status
        $userModel->insert([
            'Name'     => $name,
            'Email'    => $email,
            'Password' => password_hash($password, PASSWORD_DEFAULT),
            'Division' => $division,
            'Phone'    => $phone,
            'Level'    => 'pentadbir',
            'Active'   => $active
        ]);

        // 7️⃣ Success message
        $session->setFlashdata('success', 'Pendaftaran berjaya! Sila log masuk menggunakan data yang didaftar.');
        return redirect()->to('addUser'); // back to form
    }

    // ✅ Helper to build hierarchical tree
    private function buildDivisionTree($divisions, $parentId = null)
    {
        $branch = [];
        foreach ($divisions as $division) {
            if ($division['parent_id'] == $parentId) {
                $children = $this->buildDivisionTree($divisions, $division['id']);
                if ($children) {
                    $division['children'] = $children;
                }
                $branch[] = $division;
            }
        }
        return $branch;
    }

    // ✅ Flatten tree for dropdown (with indentation)
    private function flattenDivisionTree($tree, $depth = 0, &$flatList = [])
    {
        foreach ($tree as $node) {
            $indent = str_repeat('— ', $depth);
            $flatList[] = [
                'id'   => $node['id'],
                'name' => $indent . $node['name'],
            ];
            if (!empty($node['children'])) {
                $this->flattenDivisionTree($node['children'], $depth + 1, $flatList);
            }
        }
        return $flatList;
    }
}