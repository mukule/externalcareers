<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Ramsey\Uuid\Uuid;

class MigrateUserDetails extends BaseCommand
{
    protected $group       = 'Migration';
    protected $name        = 'migrate:userdetails';
    protected $description = 'Migrate old user details into users and user_details tables';
    protected $batchSize   = 5;

    public function run(array $params)
    {
        $db     = \Config\Database::connect('default');
        $lastId = $this->getLastProcessedId($db);

        CLI::write("Starting from old user ID: {$lastId}", 'yellow');

        $oldUsers = $db->table('users')
            ->where('old_user_id >', $lastId)
            ->where('old_user_id IS NOT', null)
            ->orderBy('old_user_id', 'ASC')
            ->limit($this->batchSize)
            ->get()
            ->getResult();

        if (empty($oldUsers)) {
            CLI::write('No records to migrate. Done.', 'green');
            return;
        }

        $lastProcessedId = $lastId; // track inside loop

        foreach ($oldUsers as $user) {
            $oldDetails = $db->table('users_details_old')
                ->where('user_id', $user->old_user_id)
                ->get()
                ->getRow();

            if (!$oldDetails) {
                CLI::write("Skipping — no old details for old_user_id: {$user->old_user_id}", 'red');
                $lastProcessedId = $user->old_user_id; // still advance past it
                continue;
            }

            $db->transStart();

            $db->table('users')
                ->where('id', $user->id)
                ->update([
                    'first_name' => $oldDetails->fname,
                    'last_name'  => $oldDetails->sname,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            $detailsData = [
                'user_id'                    => $user->id,
                'national_id'                => $oldDetails->idno,
                'gender_id'                  => $oldDetails->gender,
                'dob'                        => $oldDetails->dob,
                'phone'                      => $oldDetails->phoneno,
                'ethnicity_id'               => null,
                'county_of_origin_id'        => $oldDetails->county_birth,
                'county_of_residence_id'     => $oldDetails->county_residence,
                'country_of_birth_id'        => $oldDetails->country_birth,
                'country_of_residence_id'    => $oldDetails->country_residence,
                'marital_status_id'          => $oldDetails->marital_status,
                'field_of_study_id'          => $oldDetails->field_of_study_id,
                'highest_level_of_study_id'  => $oldDetails->education_level_id,
                'disability_status'          => $oldDetails->disability,
                'disability_number'          => $oldDetails->disability_no,
                'created_at'                 => $oldDetails->date_created,
                'updated_at'                 => $oldDetails->date_modified ?? date('Y-m-d H:i:s'),
            ];

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

            $db->transComplete();

            if ($db->transStatus() === false) {
                CLI::write("Transaction failed for old_user_id: {$user->old_user_id}", 'red');
                // Decide: break entirely, or skip and continue?
                break;
            }

            $lastProcessedId = $user->old_user_id;
            CLI::write("Processed old_user_id: {$user->old_user_id} → new user_id: {$user->id}", 'green');
        }

        $this->saveLastProcessedId($db, $lastProcessedId);
        CLI::write("Batch complete. Last processed old_user_id: {$lastProcessedId}", 'cyan');
    }

    private function getLastProcessedId($db): int
    {
        $row = $db->table('migration_progress')
            ->where('name', 'user_details')
            ->get()
            ->getRow();

        return $row ? (int) $row->last_id : 0;
    }

    private function saveLastProcessedId($db, int $lastId): void
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