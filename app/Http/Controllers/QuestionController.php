<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    public function show()
    {
//     Get the authenticated user's ID
    $userId = Auth::id();

    // Find the last question related to the user ID
    $question = Question::where('user_id', $userId)->latest()->first();

    if (!$question) {
        return response()->json(['message' => 'You have not answer the questions yet.'], 404);
    }

    return response()->json($question);
   }

    protected $choices = [
        'money_spending' => ['Groceries','Phones', 'Personal Care', 'Clothing', 'Other'],
        'home_status' => ['I rent', 'I own', 'Other'],
        'dept' => ['Credit Cards', 'House Loans', 'Personal Loans', 'Other'],
        'marital_status' => ['Single', 'Married', 'Divorced', 'Widowed'],
        'income_period' => ['Weekly', 'Monthly'],
        'financial_goals' => ['Tracking incomes and expenses', 'Manage depts, loans', 'Cut down expenses', 'Saving', 'Manage all money in one place', 'Other goals']
    ];

    protected function validateAndConvertData(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'money_spending' => 'required|integer|between:0,' . (count($this->choices['money_spending']) - 1),
            'home_status' => 'required|integer|between:0,' . (count($this->choices['home_status']) - 1),
            'dept' => 'required|integer|between:0,' . (count($this->choices['dept']) - 1),
            'marital_status' => 'required|integer|between:0,' . (count($this->choices['marital_status']) - 1),
            'income_period' => 'required|integer|between:0,' . (count($this->choices['income_period']) - 1),
            'financial_goals' => 'required|integer|between:0,' . (count($this->choices['financial_goals']) - 1),
            'children_number' => 'nullable|integer',
            'income' => 'required|integer',
            'saving' => 'nullable|integer',
        ]);

        // Convert indices to values based on choices
        foreach ($this->choices as $field => $choiceList) {
            $validatedData[$field] = $choiceList[$validatedData[$field]];
        }

        return $validatedData;
    }

    public function store(Request $request)
    {
        $userId = Auth::id();
        $validatedData = $this->validateAndConvertData($request);

        $validatedData = array_merge($validatedData,['user_id' => $userId]);
        $question = Question::create($validatedData);

        return response()->json($question, 201);
    }

    public function update(Request $request)
    {
        $userId = Auth::id();
        $question = Question::where('user_id', $userId)->latest()->first();
        // Validate and convert data
        $validatedData = $this->validateAndConvertData($request);
        $validatedData = array_merge($validatedData,['user_id' => $userId]);
        $question->update($validatedData);

        return response()->json($question, 200);
    }
    public function destroy()
    {
        $userId = Auth::id();
        $question = Question::where('user_id', $userId)->latest()->first();
        $question->delete();
        return response()->json([], 204);
    }

}




