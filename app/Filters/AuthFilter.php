<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
   

public function before(RequestInterface $request, $arguments = null)
{
    if (! session()->get('logged_in')) {

        // Get full current URL
        $currentUrl = current_url();

        return redirect()
            ->to('/login?next=' . urlencode($currentUrl))
            ->with('error', 'Authentication required.');
    }

    $userId = session()->get('user_id');
    $userModel = model('App\Models\UserModel');
    $user = $userModel->find($userId);

    if (! $user) {
        session()->destroy();
        return redirect()->to('/login')->with('error', 'User not found.');
    }

    $currentPath = trim($request->getUri()->getPath(), '/');

    if (
        (int) $user['password_changed'] === 1 &&
        $currentPath !== 'auth/change-password' &&
        $currentPath !== 'auth/update-password'
    ) {
        return redirect()
            ->to('/auth/change-password')
            ->with('info', 'You Must Change Your Password !!');
    }
}


    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        
    }
}
