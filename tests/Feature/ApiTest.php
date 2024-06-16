<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Salary;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase; // DANGER

    public function testHomePage(): void
    {
        $response = $this->get('/');

        $response->assertStatus(403);
    }

    public function testCreatedEmployee(): void{
        $response = $this->post('/api/employee/create', [
            'email' => fake()->email(),
            'password' => fake()->password()
        ]);

        $response->assertOk();
    }

    public function testCreatedTransaction(): void{
        $employee = Employee::factory()->create();

        $response = $this->post('/api/transaction/create', [
            'employee_id' => $employee->id,
            'hours' => 2.3
        ]);

        $response->assertOk();
    }

    public function testGetUnpaidEmployees(): void{
        $employees = Employee::factory()->count(2)->create();
        $hours = [12.31, 3.52];

        Salary::first()->update([
            'salary' => 300
        ]);

        foreach ($employees as $key => $emp) {
            Transaction::create([
                'employee_id' => $emp->id,
                'hours' => $hours[$key]
            ]);
        }

        $response = $this->get('/api/transaction/getunpaidemployees');
        $response->assertOk();

        $response->assertExactJson([
            $employees[0]->id => 3693,
            $employees[1]->id => 1056,
        ]);
    }

    public function testCommitAll(): void{
        $employee = Employee::factory()->create();

        Transaction::create([
            'employee_id' => $employee->id,
            'hours' => 1
        ]);

        $response = $this->get('/api/transaction/commitall');

        $response->assertOk();
    }
}
