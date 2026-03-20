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
    protected $batchSize   = 200;

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

        $lastProcessedId = $lastId;

        foreach ($oldUsers as $user) {

            try {
                $oldDetails = $db->table('users_details_old')
                    ->where('user_id', $user->old_user_id)
                    ->get()
                    ->getRow();

                if (!$oldDetails) {
                    CLI::write("Skipping — no old details for old_user_id: {$user->old_user_id}", 'red');
                    $lastProcessedId = $user->old_user_id;
                    continue;
                }

                // Update users table
                $db->table('users')
                    ->where('id', $user->id)
                    ->update([
                        'first_name' => $oldDetails->fname,
                        'last_name'  => $oldDetails->sname,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                // Check for immediate DB errors
                $err = $db->error();
                if ($err['code'] !== 0) {
                    throw new \Exception("Users update failed: " . json_encode($err));
                }

                // Clean problematic values
                $dob = ($oldDetails->dob && $oldDetails->dob !== '0000-00-00')
                    ? $oldDetails->dob
                    : null;

                // Map old education IDs to new system (update as needed)
                $educationMap = [
                    1  => 4,  // College Certificate
                    2  => 5,  // Diploma
                    3  => 2,  // Undergraduate
                    4  => 3,  // Masters
                    6  => 5,  // PhD
                    9  => 6,  // Secondary School Certificate
                    10 => 7,  // Craft Certificate
                    14 => 9,  // CPE(Class 7)
                    15 => 8,  // KCPE(Form 4)
                    16 => null // KACE(Form 6), confirm ID
                ];

                $highestLevelId = $educationMap[$oldDetails->education_level_id] ?? null;

                $detailsData = [
                    'user_id'                    => $user->id,
                    'national_id'                => $oldDetails->idno,
                    'gender_id'                  => $oldDetails->gender,
                    'dob'                        => $dob,
                    'phone'                      => $oldDetails->phoneno,
                    'ethnicity_id'               => null,
                    'county_of_origin_id'        => $oldDetails->county_birth,
                    'county_of_residence_id'     => $oldDetails->county_residence,
                    'country_of_birth_id'        => $oldDetails->country_birth,
                    'country_of_residence_id'    => $oldDetails->country_residence,
                    'marital_status_id'          => $oldDetails->marital_status,
                    'field_of_study_id'          => $oldDetails->field_of_study_id,
                    'highest_level_of_study_id'  => $highestLevelId,
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

                // Check for DB errors after insert/update
                $err = $db->error();
                if ($err['code'] !== 0) {
                    throw new \Exception("User_details insert/update failed: " . json_encode($err) .
                        "\nData: " . json_encode($detailsData));
                }

                CLI::write(
                    "Processed old_user_id: {$user->old_user_id} → new user_id: {$user->id}",
                    'green'
                );

            } catch (\Throwable $e) {

                CLI::write(
                    "Failed old_user_id: {$user->old_user_id} | {$e->getMessage()}",
                    'red'
                );

                log_message(
                    'error',
                    "Migration failed for old_user_id {$user->old_user_id}: " . $e->getMessage()
                );
            }

            // Move forward for next batch
            $lastProcessedId = $user->old_user_id;
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