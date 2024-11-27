<?php


namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class AddressController extends Controller
{
    public function store(Request $request)
    {
        $emailValidator = $this->validateEmail();
        $addressValidator = $this->validateAddress();


        if ($emailValidator->fails() || $addressValidator->fails()) {
            return response()->json(['message' => 'Failed',
                'email' => $emailValidator->messages(),
                'address' => $addressValidator->messages()]);
        }
        $user = User::where('email', $request->get('email'))->firstOrFail();


        if ($user->address) {
            return response()->json(['message' => 'User has address already', 'data' => null], 404);
        }
        $address = new Address($addressValidator->validated());


        if($user->address()->save($address)){
            return response()->json(['message' => 'Address added successfully', 'data' => $address], 200);
        }
        return response()->json(['message' => 'Failed to add address', 'data' => null], 400);
    }


    public function validateEmail()
    {
        return Validator::make(request()->all(), [
            'email' => 'required|string|email|max:255|exists:users,email',
        ]);
    }


    public function validateAddress()
    {
        return Validator::make(request()->all(), [
            'country' => 'required|string|min:1|max:255',
            'zipcode' => 'required|string|min:5|max:6',
        ]);
    }




    public function show($id)
    {
        $address= Address::find($id);
        if (!$address) {
            return response()->json([
                'message' => 'La dirección especificada no existe', 'data' => []], 404);
        }
        return response()->json(['message' => 'Direccion encontrada.', 'data' => $address], 200);
    }


//OTRA FORMA


//    public function show(Address $address)
//    {
//        return response()->json(['message' => 'Address found', 'data' => $address], 200);
//    }



    public function show_user($id)
    {
        $address = Address::with('user')->find($id);
        if (!$address) {
            return response()->json([
                'message' => 'La dirección especificada no fue encontrada.', 'data' => []], 404);
        }
        return response()->json(['message' => 'Usuario encontrado.', 'data' => $address->user], 200);
    }


    //OTRA FORMA


    //public function show_user(Address $address){
    //    return response()->json(['message' => 'User Found: ', 'data' => $address->user], 200);
    //}




}
