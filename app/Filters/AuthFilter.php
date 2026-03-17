<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    
    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. Ensure the user is logged in
        if (!session()->get('logged_in')) {
          
            $currentUrl = current_url();

            return redirect()
                ->to('/login?next=' . urlencode($currentUrl))
                ->with('error', 'Authentication required.');
        }

      
        $userId = session()->get('user_id');
        $userModel = model('App\Models\UserModel');
        $user = $userModel->find($userId);

      
        if (!$user) {
            session()->destroy();
            return redirect()->to('/login')->with('error', 'User account not found.');
        }

        // 4. Handle the Password Reset Logic
        $currentPath = trim($request->getUri()->getPath(), '/');
        
        // Allowed paths while in the "Must Change" state
        $allowedPaths = ['auth/change-password', 'auth/update-password', 'logout'];

       
        if ((int)$user['password_changed'] === 0) {
            if (!in_array($currentPath, $allowedPaths)) {
                return redirect()
                    ->to('/auth/change-password')
                    ->with('info', 'For security reasons, you must change your password to proceed.');
            }
        }
        
       
        if ((int)$user['password_changed'] === 1 && $currentPath === 'auth/change-password') {
            return redirect()->to('/index');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        
    }
}