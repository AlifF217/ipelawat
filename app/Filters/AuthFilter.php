<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // ✅ Get the current URI path (e.g. "pelawat/daftar")
        $uri = service('uri');
        $path = strtolower($uri->getPath());

        // ✅ Pages that can be accessed without login
        $publicPaths = [
            'pelawat/daftar',
            'pelawat/simpan',
            'accessdenied',
            '', // allow homepage if needed
        ];

        // ✅ Allow access to any path that starts with the listed public paths
        foreach ($publicPaths as $public) {
            if (str_starts_with($path, $public)) {
                return; // Skip login check
            }
        }

        // ✅ If not logged in, redirect to access denied
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('accessdenied'));
        }
    }


    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing needed here
    }
}
