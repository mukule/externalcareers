<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Exception;
use Ramsey\Uuid\Uuid;

class MigrateUserDetails extends BaseCommand
{
    protected $group = 'Migration';
    protected $name = 'migrate:userdetails';
    protected $description = 'Migrate old user details into users and user_details tables';
    protected $batchSize = 5; // start small for testing

    public function run(array $params)
    {
        $db = \Config\Database::connect('default');

        $lastId = $this->getLastProcessedId($db);
        CLI::write("Starting from old user ID: {$lastId}", 'yellow');

        // 1️⃣ Fetch old users in batches
        $oldUsers = $db->table('users')
            ->where('old_user_id >', $lastId)
            ->where('old_user_id IS NOT', null)
            ->orderBy('old_user_id', 'ASC')
            ->limit($this->batchSize)
            ->get()
            ->getResult();

        if (empty($oldUsers)) {
            CLI::write('Migration complete.', 'green');
            return;
        }

        foreach ($oldUsers as $user) {
            // 2️⃣ Fetch old details using old_user_id
            $oldDetails = $db->table('users_details_old')
                ->where('user_id', $user->old_user_id)
                ->get()
                ->getRow();

            if (!$oldDetails) {
                CLI::write("No old details found for old user ID: {$user->old_user_id}", 'red');
                continue;
            }

            // 3️⃣ Update names in users table
            $db->table('users')
                ->where('id', $user->id)
                ->update([
                    'first_name' => $oldDetails->fname,
                    'last_name'  => $oldDetails->sname,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            // 4️⃣ Prepare user_details data
            $detailsData = [
                'user_id'                 => $user->id,
                'national_id'             => $oldDetails->idno,
                'gender_id'               => $oldDetails->gender,
                'dob'                     => $oldDetails->dob,
                'phone'                   => $oldDetails->phoneno,
                'ethnicity_id'            => null, // skipping ethnicity
                'county_of_origin_id'     => $oldDetails->county_birth,
                'county_of_residence_id'  => $oldDetails->county_residence,
                'country_of_birth_id'     => $oldDetails->country_birth,
                'country_of_residence_id' => $oldDetails->country_residence,
                'marital_status_id'       => $oldDetails->marital_status,
                'field_of_study_id'       => $oldDetails->field_of_study_id,
                'highest_level_of_study_id'=> $oldDetails->education_level_id,
                'disability_status'       => $oldDetails->disability,
                'disability_number'       => $oldDetails->disability_no,
                'created_at'              => $oldDetails->date_created,
                'updated_at'              => $oldDetails->date_modified ?? date('Y-m-d H:i:s'),
            ];

            // 5️⃣ Insert/update user_details
            $exists = $db->table('user_details')
                ->where('user_id', $user->id)
                ->get()
                ->getRow();

            if ($exists) {
                $db->table('user_details')
                    ->where('user_id', $user->id)
                    ->update($detailsData);
            } else {
                $detailsData['uuid'] = Uuid::uuid4()->toString();
                $db->table('user_details')->insert($detailsData);
            }

            CLI::write("Processed old user ID: {$user->old_user_id} (new user_id: {$user->id})", 'green');
        }

        // 6️⃣ Save last processed old_user_id
        $this->saveLastProcessedId($db, end($oldUsers)->old_user_id);

        CLI::write("Batch processed up to old user ID: " . end($oldUsers)->old_user_id, 'green');
    }

    private function getLastProcessedId($db)
    {
        $row = $db->table('migration_progress')
            ->where('name', 'user_details')
            ->get()
            ->getRow();

        return $row ? (int)$row->last_id : 0;
    }

    private function saveLastProcessedId($db, $lastId)
    {
        $exists = $db->table('migration_progress')
            ->where('name', 'user_details')
            ->countAllResults();

        if ($exists) {
            $db->table('migration_progress')
                ->where('name', 'user_details')
                ->update(['last_id' => $lastId]);
        } else {
            $db->table('migration_progress')
                ->insert(['name' => 'user_details', 'last_id' => $lastId]);
        }
    }
}