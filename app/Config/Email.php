<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public string $fromEmail;
    public string $fromName;
    public string $recipients = '';

    public string $userAgent = 'CodeIgniter';

    public string $protocol;
    public string $mailPath = '/usr/sbin/sendmail';
    public string $SMTPHost;
    public string $SMTPUser;
    public string $SMTPPass;
    public int $SMTPPort;
    public int $SMTPTimeout = 10;
    public bool $SMTPKeepAlive = false;
    public string $SMTPCrypto;
    public bool $wordWrap = true;
    public int $wrapChars = 76;
    public string $mailType;
    public string $charset = 'UTF-8';
    public bool $validate = true;
    public int $priority = 3;
    public string $CRLF = "\r\n";
    public string $newline = "\r\n";
    public bool $BCCBatchMode = false;
    public int $BCCBatchSize = 200;
    public bool $DSN = false;

    public function __construct()
    {
        parent::__construct();

        // Load values from .env
        $this->fromEmail   = env('MAIL_FROM_ADDRESS', 'noreply@example.com');
        $this->fromName    = env('MAIL_FROM_NAME', 'Your App');
        $this->protocol    = env('MAIL_PROTOCOL', 'smtp');
        $this->SMTPHost    = env('MAIL_HOST', '');
        $this->SMTPUser    = env('MAIL_USER', '');
        $this->SMTPPass    = env('MAIL_PASS', '');
        $this->SMTPPort    = (int) env('MAIL_PORT', 465);
        $this->SMTPCrypto  = env('MAIL_SMTP_CRYPTO', 'ssl');
        $this->mailType    = env('MAIL_TYPE', 'html');
    }
}
