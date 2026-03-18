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

    protected $batchSize = 300;

    protected $options = [
        'batch' => 'Number of users to process per run (default 500)',
    ];

    public function run(array $params)
    {
        // ----------------------------
        // Step 0: Connect to DB
        // ----------------------------
        try {
            $newDb = \Config\Database::connect('default');
            $newDb->query('SELECT 1')->getRow();
            CLI::write('✅ Connected to database', 'green');
        } catch (Exception $e) {
            CLI::error('❌ Database connection failed: ' . $e->getMessage());
            return;
        }

        // ----------------------------
        // Step 0.5: Set batch size from CLI option
        // ----------------------------
        $batchOption = CLI::getOption('batch');
        if ($batchOption && is_numeric($batchOption)) {
            $this->batchSize = (int)$batchOption;
            CLI::write("Batch size set to {$this->batchSize} via CLI option", 'yellow');
        } else {
            CLI::write("Using default batch size: {$this->batchSize}", 'yellow');
        }

        // ----------------------------
        // Step 1: Get last processed ID
        // ----------------------------
        $lastId = $this->getLastProcessedId($newDb);
        CLI::write("Starting from ID: {$lastId}", 'yellow');

        // ----------------------------
        // Step 2: Fetch batch from temp table
        // ----------------------------
        $users = $newDb->table('old_users_temp')
            ->where('id >', $lastId)
            ->orderBy('id', 'ASC')
            ->limit($this->batchSize)
            ->get()
            ->getResult();

        if (empty($users)) {
            CLI::write('✅ Migration complete. No more users to process.', 'green');
            return;
        }

        // ----------------------------
        // Step 3: Check duplicates
        // ----------------------------
        $emails = array_column($users, 'emailadd');
        $existing = $newDb->table('users')
            ->select('email')
            ->whereIn('email', $emails)
            ->get()
            ->getResultArray();

        $existingEmails = array_column($existing, 'email');

        // ----------------------------
        // Step 4: Prepare insert data
        // ----------------------------
        $insertData = [];

        foreach ($users as $u) {
            if (in_array($u->emailadd, $existingEmails)) {
                CLI::write("⚠️ Skipping duplicate: {$u->emailadd}", 'yellow');
                continue;
            }

            $randomPassword = bin2hex(random_bytes(4)); // 8-char temp password

            $insertData[] = [
                'uuid'             => Uuid::uuid4()->toString(),
                'old_user_id'      => $u->id,
                'first_name'       => null,
                'last_name'        => null,
                'email'            => $u->emailadd,
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

            CLI::write("Will insert: {$u->emailadd} | temp password: {$randomPassword}", 'yellow');
        }

        // ----------------------------
        // Step 5: Insert batch
        // ----------------------------
        if (!empty($insertData)) {
            $newDb->table('users')->insertBatch($insertData);
            CLI::write('✅ Inserted ' . count($insertData) . ' users.', 'green');
        } else {
            CLI::write('⚠️ No new users to insert (all duplicates).', 'yellow');
        }

        // ----------------------------
        // Step 6: Update last processed ID
        // ----------------------------
        $newLastId = end($users)->id;
        $this->saveLastProcessedId($newDb, $newLastId);
        CLI::write("✅ Processed up to ID: {$newLastId}", 'green');
        CLI::write('Batch complete. Run again to continue migration.', 'green');
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





