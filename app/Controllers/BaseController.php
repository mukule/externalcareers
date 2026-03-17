<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\UserModel;

abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * Helpers loaded automatically upon class instantiation.
     *
     * @var list<string>
     */
    protected $helpers = ['url', 'form', 'text'];

    /**
     * Global data shared across all views.
     *
     * @var array
     */
    protected array $data = [];

   
    protected $session;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

     
        $this->session = service('session');

      
        $appConfig = config('App');
        $this->data['app_name']       = env('app.name', $appConfig->name ?? 'Kengen Careers Portal');
        $this->data['app_short_name'] = env('app.short_name', 'Kengen Careers Portal');
        $this->data['footer_text']    = '© ' . date('Y') . ' ' . $this->data['app_name'] . '. All Rights Reserved.';
        $this->data['base_url']       = base_url();

        
        $userId = $this->session->get('user_id');
        if ($userId) {
            $userModel = new UserModel();
            $this->data['user'] = $userModel->find($userId);
        } else {
            $this->data['user'] = null;
        }

       
        \Config\Services::renderer()->setData($this->data);
    }
}
