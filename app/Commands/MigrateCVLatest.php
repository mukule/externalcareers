<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Exception;

class MigrateCVLatest extends BaseCommand
{
    protected $group = 'Migration';
    protected $name = 'migrate:cv-latest';
    protected $description = 'Migrate latest cv_intro per user to professional_statements';
    protected $batchSize = 100;

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
        CLI::write("Starting from ID: {$lastId}", 'yellow');

        // Get batch of latest CVs grouped by uid
        $subQuery = $db->table('cv_intro')
            ->select('MAX(id) as latest_id')
            ->groupBy('uid');

        $rows = $db->table('cv_intro ci')
            ->join("({$subQuery->getCompiledSelect()}) as latest", 'ci.id = latest.latest_id')
            ->where('ci.id >', $lastId)
            ->orderBy('ci.id', 'ASC')
            ->limit($this->batchSize)
            ->get()
            ->getResult();

        if (empty($rows)) {
            CLI::write('Migration complete.', 'green');
            return;
        }

        $insertData = [];

        foreach ($rows as $row) {
            // Find the corresponding user_id in new users table
            $user = $db->table('users')
                ->select('id')
                ->where('old_user_id', $row->uid)
                ->get()
                ->getRow();

            if (!$user) {
                CLI::write("No matching user for old UID {$row->uid}, skipping", 'red');
                continue;
            }

            $insertData[] = [
                'user_id'    => $user->id,
                'statement'  => $row->details,
                'completed'  => 0,
                'created_at' => $row->date_created,
                'updated_at' => $row->date_modified,
            ];
        }

        if (!empty($insertData)) {
            $db->table('professional_statements')->ignore(true)->insertBatch($insertData);
            CLI::write('Inserted latest CVs safely: ' . count($insertData), 'green');
        } else {
            CLI::write('Nothing to insert.', 'yellow');
        }

        // Move forward
        $newLastId = end($rows)->id;
        $this->saveLastProcessedId($db, $newLastId);

        CLI::write("Processed up to CV ID: {$newLastId}", 'green');
    }

    private function getLastProcessedId($db)
    {
        $row = $db->table('migration_progress')
            ->where('name', 'cv_latest')
            ->get()
            ->getRow();

        return $row ? (int)$row->last_id : 0;
    }

    private function saveLastProcessedId($db, $lastId)
    {
        $exists = $db->table('migration_progress')
            ->where('name', 'cv_latest')
            ->countAllResults();

        if ($exists) {
            $db->table('migration_progress')
                ->where('name', 'cv_latest')
                ->update(['last_id' => $lastId]);
        } else {
            $db->table('migration_progress')
                ->insert(['name' => 'cv_latest', 'last_id' => $lastId]);
        }
    }
}