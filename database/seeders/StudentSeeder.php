<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = [
            [
                'name' => 'student',
                'email' => 'student@gmail.com',
                'role' => 2,
                'password' => bcrypt('11223344'),
            ],
        ];

        foreach ($students as $student) {
            User::create($student);
        }
    }
}
