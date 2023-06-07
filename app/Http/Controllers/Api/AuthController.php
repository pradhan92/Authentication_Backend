<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //registers the customer
    public function register(Request $request){
       try{
         //return $request->all();
         $data = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|unique:customers,email',
            'phone' => 'required|unique:customers,phone',
            'password' => 'required',
         ]);
         if($data->fails()){
            return response()->json(['message' => $data->messages()],400);
         }else{
        $customer = new Customer();
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->password = Hash::make($request->password);
        $customer->save();
         }
        return response()->json(['message' => 'Registration successful','success' => true],201);
       }catch(Exception $e){
        print($e);
       }
    }

    //login the customer
    public function login(Request $request){
      $data = Validator::make($request->all(),[
         'email' => 'required',
         'password' => 'required',
      ]);
      $user = Customer::where('email',$request->email)->first();
      if(!$user || !Hash::check($request->password,$user->password)){
         return response()->json(['message'=>'Invalid Email or Password']);
      }
      $token = $user->createToken($request->email)->plainTextToken;
      return response()->json(['success'=>true, 'token'=>$token,'user'=>$user],200);
    }

     //Logout

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([

            'message' => 'success'
        ],200);
    }
    public function changepassword(Request $request)
    {
        $user = Customer::find($request->user()->id);
        $user->password = Hash::make($request->password);
        $user->update();
        return response()->json(['message' => 'success']);
    }
}
