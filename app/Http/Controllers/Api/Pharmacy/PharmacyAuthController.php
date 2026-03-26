<?php

namespace App\Http\Controllers\Api\Phamacy;

use App\Http\Controllers\Controller;
use App\Models\Pharmacy\PharmacyUser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PharmacyAuthController extends Controller
{


    /**
     * Authenticate a pharmacy user and issue an access token.
     *
     * @param \Illuminate\Http\Request $request The HTTP request containing mobile and password
     * @return \Illuminate\Http\JsonResponse A JSON response with authentication status and access token
     *
     * @throws \Illuminate\Validation\ValidationException If mobile or password validation fails
     *
     * Request body:
     * - mobile (string, required): The pharmacy user's mobile number (10 digits)
     * - password (string, required): The pharmacy user's password
     *
     */
    public function pharmacyLogin(Request $request){

        $validator = Validator::make($request->all(), [
            'mobile' => 'required|min:10|max:10',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->response([
                'status' => false,
                'message' => $validator->errors()->first()
            ], self::HTTP_UNPROCESSABLE_ENTITY);
        }

        $pharmacy = PharmacyUser::where('mobile', $request->mobile)->first();

        if (! $pharmacy || ! Hash::check($request->password, $pharmacy->password)) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid credentials'
            ], self::HTTP_UNAUTHORIZED);
        }

        $token = $pharmacy->createToken('pharmacy-token', ['pharmacy'])->plainTextToken;

        return $this->response([
            'message' => 'Login successful',
            'status' => true,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'pharmacy' => $pharmacy
        ], self::HTTP_OK);
    }

    public function register(Request $request){

        $validator = Validator::make($request->all(), [
            'first_name'=>'required',
            'last_name'=>'required',
            'email'=>'email',
            'mobile'=>'required|min:10|max:10|unique:patient_users,mobile',
            'gender'=>'required|in:M,F,O'
        ]);

        if($validator->fails()){
            return $this->response([
                'status'=>false,
                'message'=>$validator->errors()->first()
            ], self::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $pharmacy = new PharmacyUser();
            $pharmacy->email = $request->email;
            $pharmacy->first_name = $request->first_name;
            $pharmacy->last_name = $request->last_name;
            $pharmacy->mobile = $request->mobile;
            $pharmacy->gender = $request->gender;
            $pharmacy->password = Hash::make('Pharmacy@123');

            if($pharmacy->save()){
                $registration_no = 'PATIENT-' . str_pad($pharmacy->id, 4, '0', STR_PAD_LEFT);
                $pharmacy->registration_no = $registration_no;
                $pharmacy->update();
                return $this->response([
                    'status'=>true,
                    'message'=>'User created successfully'
                ], self::HTTP_CREATED);
            }
        }catch(Exception $e){
            return $this->response([
                'status'=>false,
                'message'=>'Erorr '.$e->getMessage()
            ], self::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /* Logout */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->response([
            'status' => true,
            'message' => 'Logged out successfully'
        ],self::HTTP_OK);
    }

    /* Logout from all devices */
    public function logoutAllDevices(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out from all devices'
        ],self::HTTP_OK);
    }
}
