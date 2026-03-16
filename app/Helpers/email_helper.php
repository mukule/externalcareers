<?php

use CodeIgniter\Email\Email;

function send_email(
    string $to,
    string $subject,
    string $message,
    string $from = null,
    string $fromName = null,
    array $attachments = [],
    array $cc = [],
    array $bcc = []
) {
    $email = \Config\Services::email();
    $config = new \Config\Email();
    $email->initialize((array) $config);

    
    $from     = $from ?? $config->fromEmail;
    $fromName = $fromName ?? $config->fromName;

    
    $globalBCC = env('BCC'); 
    if (!empty($globalBCC)) {
        
        $globalBCC = array_map('trim', explode(',', $globalBCC));
    } else {
        $globalBCC = [];
    }

    
    $allBCC = array_unique(array_merge($bcc, $globalBCC));


    $email->setFrom($from, $fromName);
    $email->setTo($to);
    if (!empty($cc)) $email->setCC($cc);
    if (!empty($allBCC)) $email->setBCC($allBCC);

    $email->setSubject($subject);
    $email->setMessage($message);

    
    foreach ($attachments as $attachment) {
        if (is_array($attachment) && isset($attachment['content'], $attachment['name'], $attachment['type'])) {
            $email->attach($attachment['content'], 'attachment', $attachment['name'], $attachment['type']);
        } elseif (is_string($attachment) && file_exists($attachment)) {
            $email->attach($attachment);
        }
    }

    
    $result = $email->send();

    
    foreach ($attachments as $attachment) {
        if (is_string($attachment) && file_exists($attachment) && strpos(sys_get_temp_dir(), dirname($attachment)) === 0) {
            @unlink($attachment);
        }
    }

    if ($result) {
        return true;
    }

    return $email->printDebugger(['headers', 'subject', 'body']);
}
