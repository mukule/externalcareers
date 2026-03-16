<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        
        if (!$session->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please log in to continue.');
        }

        $role = strtolower($session->get('role'));

        
        if ($role === 'admin') {
            return null;
        }

       
        if ($arguments && !in_array($role, $arguments)) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        
    }
}
