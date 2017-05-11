<?php

/*
 * B+
 * Copyright (C) 2017 Jorge Vieira, José Sousa, Miguel Reboiro-Jato,
 * Noé Vázquez, Bárbara Amorim, Cristina P. Vieira, André Torres, Hugo
 * López-Fernández, and Florentino Fdez-Riverola
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | User Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the management of users
    |
    */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');
    }


    public function all(Request $request){

        return view('auth/manage', [
            'users' => User::all()
        ]);
    }

    public function remove(Request $request){

        $this->validate($request, [
            'id' => 'required|numeric'
        ]);

        User::destroy($request->get('id'));

        return view('auth/manage', [
            'users' => User::all()
        ]);
    }

    public function edit(Request $request){

        $this->validate($request, [
            'id' => 'required|numeric'
        ]);

        return view('auth/edit', [
            'user' => User::findOrFail($request->get('id'))
        ]);
    }

    public function save(Request $request){

        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
            'name' => 'required|min:3|max:255',
            'email' => 'required|email|max:255',
            'old-password' => 'required_with:password',
            'password' => 'string|min:6|confirmed|required_with:old-password',
        ]);

        if($validator->fails()){
            return view('auth/edit', [
                'user' => User::findOrFail($request->get('id'))
            ])->withErrors($validator);
        }
        $user = User::findOrFail($request->get('id'));

        $user->name = $request->get('name');
        $user->email = $request->get('email');

        if($user->id === Auth::user()->id && $request->has('password')){
            if(Hash::check($request->get('old-password'), $user->password)) {
                $user->password = bcrypt($request->get('password'));
            }
            else{
                $validator->errors()->add('old-password', 'The old password is not correct.');
                return view('auth/edit', [
                    'user' => User::findOrFail($request->get('id'))
                ])->withErrors($validator);
            }
        }

        $user->save();

        return view('auth/manage', [
            'users' => User::all()
        ]);
    }
}
