<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function __invoke()
    {
        return view('dashboard', [
            'scripts' => $this->scriptVariables(),
        ]);
    }

    protected function scriptVariables()
    {
        return [
            'user' => Auth::user(),
            'path' => config('constants.dashboard.path')
        ];
    }
}
