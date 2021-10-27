<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Partner;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PartnerController extends Controller
{
    protected function validator(Request $request)
    {
        return Validator::make($request, [
            'first_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:partners',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $partner = Partner::find($user->partner_id);
        $partner->name = $request->name;
        $partner->last_name = $request->last_name;
        $partner->email = $request->email;
        if(!empty($request->password)){
            if (\Hash::check($request->currentPassword, $partner->password))
                $partner->password = bcrypt($request->password);
            else
                return ZERO;
        }
        $partner->save();
        return ONE;
    }
}
