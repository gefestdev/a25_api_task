<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller {

    public function create(Request $request): JsonResponse{

        $messages = [
            'email.required' => 'Поле <email> обязательно для заполнения',
            'password.required' => 'Поле <password> обязательно для заполнения',
            'email.unique' => 'Пользователь с такой почтой уже существует',
            'email.max' => 'Превышено максимальное число символов',
            'password.max' => 'Превышено максимальное число символов',
            'email.email' => 'Введенная строка не является почтой'
        ];

        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:employees|max:255|email',
            'password' => 'required|max:255'
        ], $messages);

        if($validator->fails()){
            $response = $validator->messages();
            return response()->json($response, Response::HTTP_BAD_REQUEST);
        }

        $response = Employee::create([
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->json($response);
    }
}