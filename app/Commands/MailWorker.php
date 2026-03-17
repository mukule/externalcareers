<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\MailQueueModel;

class MailWorker extends BaseCommand
{
    protected $group       = 'Queue';
    protected $name        = 'mail:run';
    protected $description = 'Sends each email row independently.';

    public function run(array $params)
    {
        $model = new MailQueueModel();
        $emails = $model->whereIn('status', ['pending', 'failed'])
                        ->where('attempts <', 5)
                        ->orderBy('created_at', 'ASC')
                        ->findAll(20); // Process 20 at a time

        if (empty($emails)) {
            CLI::write("Queue is empty.", "white");
            return;
        }

        foreach ($emails as $row) {
            $model->update($row['id'], ['status' => 'processing']);

            $result = process_queued_email($row);

            if ($result === true) {
                $model->update($row['id'], [
                    'status'  => 'sent',
                    'sent_at' => date('Y-m-d H:i:s'),
                    'error_message' => null
                ]);
                CLI::write("Sent to: {$row['to_email']}", "green");
            } else {
                $model->update($row['id'], [
                    'status'        => 'failed',
                    'attempts'      => $row['attempts'] + 1,
                    'error_message' => $result
                ]);
                CLI::error("Failed: {$row['to_email']}");
            }
        }
    }
}