<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrateInOrder extends Command
{

    public function __construct()
    {
        parent::__construct();
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate_in_order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute the migrations in the order specified in the file app/Console/Comands/MigrateInOrder.php \n Drop all the table in db before execute the command.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $migrations = [
            '2023_11_05_031622_create_roles_table.php',
            '2014_10_12_000000_create_users_table.php',
            '2023_11_05_035953_add_role_id_to_users.php',
            '2014_10_12_100000_create_password_reset_tokens_table.php',
            '2019_08_19_000000_create_failed_jobs_table.php',
            '2019_12_14_000001_create_personal_access_tokens_table.php',
            '2023_05_27_085705_create_categories_table.php',
            '2023_05_21_032213_create_universities_table.php',
            '2023_05_20_160553_create_companies_table.php',
            '2023_11_06_044632_create_jobtypes_table.php',
            '2023_05_20_163440_create_job_listings_table.php',
            '2023_05_21_031847_create_students_table.php',
            '2023_11_05_040343_create_student_roles_table.php',
            '2023_11_05_040537_add_student_role_to_students.php',
            '2023_05_21_045222_create_applications_table.php',
            '2023_05_27_085352_create_joblistings_category_table.php',
            '2023_05_27_101922_create_student_category_table.php',
            '2023_06_16_133656_create_student_joblisting_table.php',
            '2023_06_26_121757_add_is_closed_to_joblistings.php',
            '2023_06_27_142849_add_curriculum_vitae_to_students.php',
            '2023_06_20_073048_add_resume_to_students_table.php',
            '2023_06_25_133331_create_contact_us_table.php',
            '2023_05_28_070838_add_image_to_users_table.php',
        ];
        foreach ($migrations as $migration) {
            $basePath = 'database/migrations/';
            $migrationName = trim($migration);
            $path = $basePath . $migrationName;
            $this->call('migrate:refresh', [
                '--path' => $path,
            ]);
        }
    }
}
