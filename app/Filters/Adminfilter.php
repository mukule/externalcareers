<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

       
        if (!$session->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please log in to continue.');
        }

      
        if (strtolower($session->get('role')) !== 'admin') {
            return redirect()->back()->with('error', 'Access denied');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
       
        if (strtolower(session()->get('role')) === 'admin' && url_is('login')) {
            return redirect()->back();
        }
    }
}
