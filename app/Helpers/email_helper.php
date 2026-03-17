<?php

use App\Models\MailQueueModel;

/**
 * PRODUCER: Loops through all recipients and creates individual rows.
 */
function send_email(
    string $to,
    string $subject,
    string $message,
    string $from = null,
    string $fromName = null,
    array $attachments = [], // Ignored for now
    array $cc = [],
    array $bcc = []
) {
    $mailQueueModel = new MailQueueModel();
    
    // 1. Collect all unique recipients into one flat list
    // We treat everyone as a "To" recipient in the database for independent tracking
    $recipients = array_unique(array_merge([$to], $cc, $bcc));
    $recipients = array_filter($recipients); // Remove any empty values

    $insertedCount = 0;

    foreach ($recipients as $recipient) {
        $data = [
            'to_email'      => trim($recipient),
            'subject'       => $subject,
            'body'          => $message,
            'status'        => 'pending',
            'attempts'      => 0,
            'error_message' => json_encode(['from' => $from, 'fromName' => $fromName]),
            'created_at'    => date('Y-m-d H:i:s'),
        ];

        if ($mailQueueModel->insert($data)) {
            $insertedCount++;
        }
    }

    return $insertedCount > 0 ? true : "Failed to queue any emails.";
}

/**
 * CONSUMER: Processes a single row at a time.
 */
function process_queued_email(array $row)
{
    $email = \Config\Services::email();
    $config = new \Config\Email();
    $email->initialize((array) $config);

    $metadata = json_decode($row['error_message'], true) ?? [];
    $from     = $metadata['from'] ?? $config->fromEmail;
    $fromName = $metadata['fromName'] ?? $config->fromName;

    $email->setFrom($from, $fromName);
    $email->setTo($row['to_email']);
    $email->setSubject($row['subject']);
    $email->setMessage($row['body']);
    $email->setMailType('html');

    // Note: We don't set CC/BCC here because they have their own rows now!
    
    if ($email->send()) {
        return true;
    }

    return $email->printDebugger(['headers']);
}