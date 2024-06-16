<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller{
    private function checkEmployeeHours(Request $request): bool{
        $errorStasus = false;

        $employee = DB::table('transactions')->join('employees', function($join){
            $join->on('employees.id', '=', 'transactions.employee_id');
        })
        ->where('employee_id', $request->employee_id)
        ->where('transactions.created_at', 'like', date('Y-m-d') . '%')
        ->sum('hours');

        if(round((float)$employee + $request->hours, 2) > 24){
            $errorStasus = true;
        }

        return $errorStasus;
    }

    public function create(Request $request): JsonResponse{
        $messages = [
            'employee_id.exists' => 'Введенный пользователь не найден',
            'employee_id.required' => 'Поле не может быть пустым',
            'hours.required' => 'Поле не может быть пустым',
            'hours.regex' => 'Неверный формат числа. Число должно иметь максимум 2 знака перед запятой и не может быть отрицательным'
        ];

        $validator = Validator::make($request->all(),[
            'employee_id' => 'required|exists:employees,id',
            'hours' => 'required|regex:/^\d{1,2}(\.\d+)?$/'
        ], $messages);

        if($validator->fails()){
            $response = $validator->messages();
            return response()->json($response, Response::HTTP_BAD_REQUEST);
        }

        if($this->checkEmployeeHours($request)){
            $response = ["error" => "Сумма трудозатрат пользователя больше 24-х часов"];
            return response()->json($response, Response::HTTP_BAD_REQUEST);
        }

        $response = Transaction::create([
            'employee_id' => $request->employee_id,
            'hours' => $request->hours    
        ]);

        return response()->json($response);
    }

    public function getUnpaidEmployees(): JsonResponse{
        $salary = Salary::first()->salary;
        $transactionsRes = [];

        $transactions = DB::table('transactions')->join('employees', function($join){
            $join->on('employees.id', '=', 'transactions.employee_id');
        })
        ->where('transactions.status', 0)
        ->select('employees.id', DB::raw("SUM(transactions.hours*$salary) as sum"))
        ->groupBy('employees.id')
        ->get();

        foreach ($transactions as $item) {
            $transactionsRes[$item->id] = round($item->sum, 2);
        }

        return response()->json($transactionsRes);
    }

    public function commitAll() : void{
        Transaction::where('status', 0)->update(['status' => 1]);
    }
}