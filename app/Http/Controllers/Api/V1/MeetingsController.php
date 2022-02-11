<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

use App\Models\MeetingDay;
use App\Http\Controllers\Controller;

class MeetingsController extends Controller
{
    public function getMeetings(Request $request) {
        return MeetingDay::with('meeting')->filter($request->all())->get();
    }
}