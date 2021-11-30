<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class FacebookController extends Controller
{
    public function redirect(){
        return Socialite::driver('facebook')->redirect();
    }

    public function callback(){
        $socialite = Socialite::driver('facebook')->stateless()->user();

        $isUser = User::where("facebook_id",$socialite->id)->first();
        if(isset($isUser)){
            Auth::login($isUser);
        }else{
            $user = new User();
            $user->name = $socialite->name;
            if(isset($socialite->email)){
                $user->email = $socialite->email;
            }
            $user->photo = $socialite->avatar;
            $user->facebook_id = $socialite->id;
            $user->password = Hash::make(uniqid());
            $user->save();

            Auth::login(User::find($user->id));
        }
       return redirect()->route("home");
    }
}
