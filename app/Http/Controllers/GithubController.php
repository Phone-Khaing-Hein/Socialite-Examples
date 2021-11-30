<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Socialite;

class GithubController extends Controller
{
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('github')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
        $socialite = Socialite::driver('github')->user();

        $isUser = User::where("github_id",$socialite->id)->first();
        if(isset($isUser)){
            Auth::login($isUser);
        }else{
            $user = new User();
            $user->name = $socialite->name;
            if(isset($socialite->email)){
                $user->email = $socialite->email;
            }
            $user->photo = $socialite->avatar;
            $user->github_id = $socialite->id;
            $user->password = Hash::make(uniqid());
            $user->save();

            Auth::login(User::find($user->id));
        }
        return redirect()->route("home");
    }
}
