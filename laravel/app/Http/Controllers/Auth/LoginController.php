<?php

namespace App\Http\Controllers\Auth;

use App\Enums\ConsentType;
use App\Http\Controllers\Controller;
use App\Models\Pdpa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    function index()
    {
        $latest_pdpa = Pdpa::where('consent_type' ,ConsentType::PRIVACY)
            ->orderBy('version', 'desc')
            ->first();
        return view('auth.login.index2', [
            'pdpa' => $latest_pdpa
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:100'],
            'password' => ['required'],
        ]);

        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']])) {

            $request->session()->regenerate();
            $user = Auth::user();

            if (strcmp($user->status, 0) == 0) {
                Auth::logout();
                $request->session()->invalidate();
                return back()->withInput()->withErrors([
                    'status' => __('auth.login_fail_status'),
                ]);
            }
            $user = User::where('username', $request->username)->first();
            $request->session()->put('user.name', $user->name);
            $department_name = ($user->department) ? $user->department->name : '-';
            $request->session()->put('user.department_name', $department_name);
            $branch_name = ($user->branch) ? $user->branch->name : '-';
            $request->session()->put('user.branch_name', $branch_name);
            $role_name = ($user->role) ? $user->role->name : '-';
            $request->session()->put('user.role_name', $role_name);
            return redirect()->route('admin.home');
        } else {
            return back()->withInput()->withErrors([
                'status' => __('auth.login_fail_username_or_password'),
            ]);
        }
    }

    function logout(Request $request)
    {

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
