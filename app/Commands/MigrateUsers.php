<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Exception;
use Ramsey\Uuid\Uuid;

class MigrateUsers extends BaseCommand
{
    protected $group = 'Migration';
    protected $name = 'migrate:users';
    protected $description = 'Migrate users from old_users_temp to users table in batches';

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

        $users = $db->table('old_users_temp')
            ->where('id >', $lastId)
            ->orderBy('id', 'ASC')
            ->limit($this->batchSize)
            ->get()
            ->getResult();

        if (empty($users)) {
            CLI::write('Migration complete.', 'green');
            return;
        }

        // Normalize emails from batch
        $emails = array_map(function ($u) {
            return strtolower(trim($u->emailadd));
        }, $users);

        // Fetch existing emails from DB (normalized)
        $existing = $db->table('users')
            ->select('LOWER(email) as email')
            ->whereIn('LOWER(email)', $emails)
            ->get()
            ->getResultArray();

        $existingEmails = array_column($existing, 'email');

        $insertData = [];
        $seenEmails = []; // prevent duplicates within batch

        foreach ($users as $u) {
            $email = strtolower(trim($u->emailadd));

            if (empty($email)) {
                CLI::write("Skipping empty email (ID: {$u->id})", 'red');
                continue;
            }

            if (in_array($email, $existingEmails) || in_array($email, $seenEmails)) {
                CLI::write("Skipping duplicate: {$email}", 'yellow');
                continue;
            }

            $seenEmails[] = $email;

            $randomPassword = bin2hex(random_bytes(4));

            $insertData[] = [
                'uuid'             => Uuid::uuid4()->toString(),
                'old_user_id'      => $u->id,
                'first_name'       => null,
                'last_name'        => null,
                'email'            => $email,
                'password'         => password_hash($randomPassword, PASSWORD_DEFAULT),
                'activation_token' => $u->activation_code,
                'role'             => 'applicant',
                'password_changed' => 0,
                'access_level'     => $u->accesses,
                'active'           => ($u->active == 1 ? 1 : 0),
                'last_login'       => $u->last_accessed,
                'created_at'       => $u->date_created,
                'updated_at'       => date('Y-m-d H:i:s'),
            ];
        }

        // SAFE INSERT (no crash on duplicates)
        if (!empty($insertData)) {
            $db->table('users')->ignore(true)->insertBatch($insertData);
            CLI::write('Inserted (or skipped duplicates safely): ' . count($insertData), 'green');
        } else {
            CLI::write('Nothing to insert.', 'yellow');
        }

        // Move forward regardless (since duplicates are now safely ignored)
        $newLastId = end($users)->id;
        $this->saveLastProcessedId($db, $newLastId);

        CLI::write("Processed up to ID: {$newLastId}", 'green');
    }

    private function getLastProcessedId($db)
    {
        $row = $db->table('migration_progress')
            ->where('name', 'users')
            ->get()
            ->getRow();

        return $row ? (int)$row->last_id : 0;
    }

    private function saveLastProcessedId($db, $lastId)
    {
        $exists = $db->table('migration_progress')
            ->where('name', 'users')
            ->countAllResults();

        if ($exists) {
            $db->table('migration_progress')
                ->where('name', 'users')
                ->update(['last_id' => $lastId]);
        } else {
            $db->table('migration_progress')
                ->insert(['name' => 'users', 'last_id' => $lastId]);
        }
    }
}