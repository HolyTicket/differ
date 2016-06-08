<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\QuestionCategory;

class QuestionsController extends Controller
{
    public function index() {
        $categories = QuestionCategory::orderBy('sort', 'asc')->get();
        return view('questions.index', compact('categories'));
    }
}