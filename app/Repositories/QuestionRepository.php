<?php

namespace App\Repositories;

use App\Models\Question;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;

class QuestionRepository {

    public function __construct()
    {

    }

    public function getAll(){
        try {
            $user = User::with(['campas'])
                        ->where('id', Auth::id())
                        ->first();
            return Question::where('company_id', $user->campa->id)
                        ->get();
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }

    public function create($request){
        try {
            $user = User::with(['campas'])
                        ->where('id', Auth::id())
                        ->first();
            $question = new Question();
            $question->company_id = $user->campa->company_id;
            $question->question = $request->json()->get('question');
            $question->description = $request->json()->get('description');
            $question->save();
            return $question;
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }
}
