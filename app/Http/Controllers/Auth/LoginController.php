<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite;
use Auth;
use App\User;

class LoginController extends Controller
{
    
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    public function redirectToProvider()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return Response
     */
    public function handleProviderCallback()
    {
        try {
            $user = Socialite::driver('facebook')->user();   
            
        } catch (Exception $e) {
            return redirect('auth/facebook');
        }
        $authUser = $this->findOnCreated($user);
        Auth::login($authUser, true);
        
        return redirect()->route('home');
    }

    private function findOnCreated($facebook_user){
        
        $UserCreated = User::where('email',$facebook_user->email)->first();

        if ($UserCreated) {
            return $UserCreated;
        }else{
            return User::create([
                'name' => $facebook_user->name,
                'email' => $facebook_user->email,
                'provider_id' => $facebook_user->id,
                'avatar' => $facebook_user->avatar
            ]);
        }
         
        
        
    }

    
}
