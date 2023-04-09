<?php

namespace App\Http\Controllers;

use App\Models\Delegator;

class WelcomeController extends Controller
{
    public function __invoke()
    {
        $data = [
            'delegator' => $delegator = Delegator::find(\Sso::credential()->id),
            'step' => $delegator?->step->step,
        ];

        return view('dashboard', $data);
    }
}
