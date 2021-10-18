<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DoubleController extends Controller
{
    public function updateBalance(Request $request)
    {
        $user = $request->user();
        if ($request->balance && $request->balance >= 0){
            $user->update($request->only('balance'));
        }

        return response()->json($user);
    }
}
