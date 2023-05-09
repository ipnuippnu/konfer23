<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Models\User;
use App\Rules\MatchOldPasswordRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function __invoke(Request $request)
    {
        return $this->showView($request->user(), 'Profil Saya');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old-pass' => 'required|current_password',
            'password' => 'required|confirmed'
        ]);

        $request->user()->update([
            'password' => bcrypt($request->get('password'))
        ]);

        return response()->json([], 204);
    }

    public function profileUpdate(ProfileRequest $request)
    {
        $data = array_filter($request->only('name', 'email', 'gender', 'jabatan', 'phone', 'bio'));
        $data['phone'] = preg_replace("/^\+?(0|62)?8/", "628", $data['phone']);

        DB::transaction(function() use($request, $data) {
            $request->user()->fill($data)->save();
        });

        return response()->json([], 204);
    }

    public function changePicture(Request $request)
    {
        $request->validate([
            'photo' => 'required|image'
        ]);

        $request->user()->fill([
            'avatar' => $request->file('photo')->store('profile', ['disk' => 'public'])
        ])->save();

        return response()->json([], 204);

    }

    private function showView(User $user, $title)
    {
        return view('admin.profile', compact('user', 'title'));
    }
}