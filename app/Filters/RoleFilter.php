<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\Exceptions\PageNotFoundException;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // Check login first
        if (!$session->get('logged_in')) {
            return redirect()->to(base_url('login'));
        }

        // If no specific role required, allow access
        if (!$arguments) {
            return;
        }

        // Get user role from session
        $userRole = $session->get('role');
        $allowedRoles = $arguments;

        // Block if role mismatch
        if (!in_array($userRole, $allowedRoles)) {
            // Optional: return 403 or redirect to access denied page
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing here
    }
}
