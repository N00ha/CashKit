<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Question;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    public function show()
    {
        $userId = Auth::id(); // Retrieve authenticated user's ID
        $goals = Goal::where('user_id', $userId)->get();
        if ($goals->isEmpty()) {
            return response()->json(['message' => 'You have not set any goals yet!'], 404);
        }
        return response()->json($goals);
    }
    public function store(Request $request)
    {
        $requestData = $request->validate([
            'name' => 'required|string',
            'budget' => 'required|integer',
            'start' => 'required|date_format:Y-m-d',
            'end' => 'required|date_format:Y-m-d',
        ]);

        $startDate = $requestData['start'];
        $endDate = $requestData['end'];
        $userId = Auth::id(); // Retrieve authenticated user's ID
        $budget = $requestData['budget'];

        $question = Question::where('user_id', $userId)->latest()->first();
        if (!$question)
        {
            return response()->json(["message" => "This user does not answer the questions."]);
        }
        $saveAmount = $question->saving;

        $start = Carbon::createFromFormat('Y-m-d', $startDate);
        $end = Carbon::createFromFormat('Y-m-d', $endDate);
        $period = $start->diffInMonths($end);
        $savingNeed = intval($budget / $period);

        if ($savingNeed > $saveAmount)
        {
            return response()->json(["message" => "This Goal is invalid. You need to increase your saving amount or extend the end date."]);
        }

        $question->saving -= $savingNeed;
        $question->save();
        Goal::create(array_merge($requestData, ['user_id' => $userId]));
        return response()->json([
            'message' => 'Your Goal was created successfully. You need to save '.$savingNeed.' per month to achieve your goal after '.$period.' months. Your remaining save amount is '.$question->saving,
            'status' => 'success'
        ], 200);
    }

    public function update(Request $request, $goal_id)
    {
        $goal = Goal::findOrFail($goal_id);

        $updatedData = $request->validate([
            'name' => 'required|string',
            'budget' => 'required|integer',
            'start' => 'required|date_format:Y-m-d',
            'end' => 'required|date_format:Y-m-d',
        ]);
        $userId = Auth::id(); // Retrieve authenticated user's ID
        $goal->update(array_merge($updatedData, ['user_id' => $userId]));
        return response()->json($goal, 200);
    }

    public function destroy($goal_id)
    {
        $userId = Auth::id(); // Retrieve authenticated user's ID
        $goal = Goal::where('user_id', $userId)->findOrFail($goal_id);
        $goal->delete();
        return response([
            'message' => 'Goal deleted successfully',
            'status' => 'success'
        ], 200);
    }

}

