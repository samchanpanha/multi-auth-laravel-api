<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function signIn(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        $rules = [
            'email' => 'required|email:rfc,dns|max:255',
            'password' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules,$this->validationMessages());

        if ($validator->fails()) {return  response()->json(["message" => $validator->errors()->first()],400);}

        if(customer::where('email',$email)->count() <= 0 ) return response( array( "message" => "Email number does not exist"  ), 400 );

        $customer = customer::where('email',$email)->first();

        if(password_verify($password,$customer->password)){
            $customer->last_login = Carbon::now();
            $customer->save();
            return response( array( "message" => "Sign In Successful", "data" => [
                "customer" => $customer,

                // Below the customer key passed as the second parameter sets the role
                // anyone with the auth token would have only customer access rights
                "token" => $customer->createToken('Personal Access Token',['customer'])->accessToken
            ]  ), 200 );
        } else {
            return response( array( "message" => "Wrong Credentials." ), 400 );
        }
    }

    public function dashboard(Request $request) {
        $customer = $request->user();
        // the full object of the customer as containted in the able would
        // be available now

    }
}
