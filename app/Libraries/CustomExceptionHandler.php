<?php

namespace App\Libraries;

use CodeIgniter\Debug\ExceptionHandlerInterface;
use CodeIgniter\Debug\Exceptions as BaseExceptions;
use Throwable;

class CustomExceptionHandler implements ExceptionHandlerInterface
{
    protected BaseExceptions $config;

    public function __construct(BaseExceptions $config)
    {
        $this->config = $config;
    }

    public function handle(Throwable $exception, int $statusCode = 500): void
    {
        // Log the exception if needed
        log_message('error', $exception->getMessage());

        // Render custom error view
        echo view('errors/html/custom_error', [
            'statusCode' => $statusCode,
            'message'    => $exception->getMessage()
        ]);
    }
}