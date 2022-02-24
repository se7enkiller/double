<?php

namespace App\Http\Controllers;

use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Ilzrv\LaravelSteamAuth\SteamAuth;
use Ilzrv\LaravelSteamAuth\SteamData;

class SteamAuthController extends Controller
{
    use AuthenticatesUsers;
    /**
     * The SteamAuth instance.
     *
     * @var SteamAuth
     */
    protected $steamAuth;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * SteamAuthController constructor.
     *
     * @param SteamAuth $steamAuth
     */
    public function __construct(SteamAuth $steamAuth)
    {
        $this->steamAuth = $steamAuth;
    }

    /**
     * Get user data and login
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login()
    {
        if (!$this->steamAuth->validate()) {
            return $this->steamAuth->redirect();
        }

        $data = $this->steamAuth->getUserData();

        if (is_null($data)) {
            return $this->steamAuth->redirect();
        }

        Auth::login(
            $this->firstOrCreate($data),
            true
        );
        $user = $this->firstOrCreate($data);
        $dataAttempt = [
            'name' => $user->name,
            'password' => $user->steam_id
        ];
        $token = $this->guard()->attempt($dataAttempt);
        $this->guard()->setToken($token);
        session(['token' => $token]);

        return redirect($this->redirectTo);
    }

    public function getToken()
    {
        $token = session('token');
        if (!$token) {
            return response()->json(['token' => $token]);
        }
        return 'true';
    }

    /**
     * Get the first user by SteamID or create new
     *
     * @param SteamData $data
     * @return User|\Illuminate\Database\Eloquent\Model
     */
    protected function firstOrCreate(SteamData $data)
    {
        return User::firstOrCreate([
            'steam_id' => $data->getSteamId(),
        ], [
            'name' => $data->getPersonaName(),
            'password' => bcrypt($data->getSteamId()),
            'photo_url' => $data->getAvatarFull(),
        ]);
    }
}
