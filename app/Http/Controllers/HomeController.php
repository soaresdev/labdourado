<?php

namespace App\Http\Controllers;

use App\Doctor;
use App\GuideSadt;
use App\Lot;
use App\Operator;
use App\Patient;
use App\Provider;
use App\User;
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
            'app_url' => config('app.url'),
            'path' => config('constants.dashboard.path')
        ];
    }

    public function getResume()
    {
        $users = User::all(['id'])->count();
        $providers = Provider::all(['id'])->count();
        $patients = Patient::all(['id'])->count();
        $doctors = Doctor::all(['id'])->count();
        $operators = Operator::all(['id'])->count();
        $lots = Lot::all()->all();
        $guides = GuideSadt::all()->all();
        return $this->message->info()->setData([
            'users' => $users,
            'providers' => $providers,
            'patients' => $patients,
            'doctors' => $doctors,
            'operators' => $operators,
            'lots' => $lots,
            'guides' => $guides
        ])->getResponse();
    }
}
