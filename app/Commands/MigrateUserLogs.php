<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Exception;

class MigrateUserLogs extends BaseCommand
{
    protected $group = 'Migration';
    protected $name = 'migrate:user-logs';
    protected $description = 'Update user_logs.uid to new user IDs in batches from old system';
    protected $batchSize = 5000; // adjust batch size as needed

    public function run(array $params)
    {
        try {
            $db = \Config\Database::connect('default');
            $db->query('SELECT 1');
            CLI::write('Connected to database', 'green');
        } catch (Exception $e) {
            CLI::error('DB connection failed: ' . $e->getMessage());
            return;
        }

        $lastId = $this->getLastProcessedId($db);
        CLI::write("Starting from user_logs ID: {$lastId}", 'yellow');

        $logs = $db->table('user_logs ul')
            ->select('ul.id AS log_id, u.id AS new_uid')
            ->join('users u', 'u.old_user_id = ul.uid', 'inner')
            ->where('ul.id >', $lastId)
            ->where('u.role', 'applicant')
            ->orderBy('ul.id', 'ASC')
            ->limit($this->batchSize)
            ->get()
            ->getResult();

        if (empty($logs)) {
            CLI::write('All user_logs updated.', 'green');
            return;
        }

        foreach ($logs as $log) {
            $db->table('user_logs')
                ->where('id', $log->log_id)
                ->update(['uid' => $log->new_uid]);
        }

        $newLastId = end($logs)->log_id;
        $this->saveLastProcessedId($db, $newLastId);

        CLI::write("Processed batch up to user_logs ID: {$newLastId}", 'green');
    }

    private function getLastProcessedId($db)
    {
        $row = $db->table('migration_progress')
            ->where('name', 'user_logs')
            ->get()
            ->getRow();

        return $row ? (int)$row->last_id : 0;
    }

    private function saveLastProcessedId($db, $lastId)
    {
        $exists = $db->table('migration_progress')
            ->where('name', 'user_logs')
            ->countAllResults();

        if ($exists) {
            $db->table('migration_progress')
                ->where('name', 'user_logs')
                ->update(['last_id' => $lastId]);
        } else {
            $db->table('migration_progress')
                ->insert(['name' => 'user_logs', 'last_id' => $lastId]);
        }
    }
}