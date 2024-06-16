<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    private $count = 10;

    public function run(): void
    {
        $count = $this->count;
        
        for ($i=0; $i < $count; $i++) { 
            Employee::create([
                'email' => Str::random(5) . '@mail.ru',
                'password' => Hash::make('password')
            ]);
        }
    }
}
