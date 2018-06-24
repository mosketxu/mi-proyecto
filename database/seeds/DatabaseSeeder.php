<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncateTables([ //si rejecuto los seeders esto hace que primero vacie las tablas
            'users',
            'skills',
            'professions',
        ]);
        // $this->call(UsersTableSeeder::class);

        // $this->call(ProfessionSeeder::class);
        // $this->call(UserSeeder::class);
        // $this->call(SkillSeeder::class);

        //tambien se puede hacer en una linea
        $this->call([
            ProfessionSeeder::class,
            UserSeeder::class,
            SkillSeeder::class,
        ]);
    }

    protected function truncateTables(array $tables){
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
    }
}
