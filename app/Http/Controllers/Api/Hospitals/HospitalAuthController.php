<?php

namespace App\Http\Controllers\Api\Hospitals;

use App\Http\Controllers\Controller;
use App\Models\Hospitals\HospitalUser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class HospitalAuthController extends Controller
{
    /**
     * Authenticate a hospital user and issue an API token.
     *
     * This method validates the provided email and password credentials against
     * the HospitalUser model. Upon successful authentication, it generates a
     * Sanctum API token with the 'hospital' ability.
     *
     * @param Request $request The HTTP request containing:
     *                          - email (string): The hospital user's email or mobile number
     *                          - password (string): The hospital user's password
     *
     * @return \Illuminate\Http\JsonResponse Returns a JSON response with:
     *                                        - On validation failure (422): Error details
     *                                        - On invalid credentials (401): Authentication failure message
     *                                        - On success (200): Access token, token type, and hospital user data
     */
    public function hospitalLogin(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->response([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()->first()
            ], self::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Assuming you have a HospitalUser model for hospital users
        $hospital = HospitalUser::where('mobile', $request->email)->first();

        if (! $hospital || ! Hash::check($request->password, $hospital->password)) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid credentials'
            ], self::HTTP_UNAUTHORIZED);
        }

        // Create a token for the authenticated hospital user
        $token = $hospital->createToken('hospital-token', ['hospital'])->plainTextToken;

        return $this->response([
            'message' => 'Login successful',
            'status' => true,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'hospital' => $hospital
        ], self::HTTP_OK);
    }

    public function register(Request $request){

        $validator = Validator::make($request->all(),[
            'first_name'=>'required',
            'last_name'=>'required',
            'email'=>'required|email|unique:hospital_users,email',
            'mobile'=>'min:10|max:10',
            'role'=>'required|numeric',
            'gender'=>'required|in:M,F,O'
        ]);

        if($validator->fails()){
            return $this->response([
                'status'=>false,
                'message'=>$validator->errors()->first()
            ], self::HTTP_UNPROCESSABLE_ENTITY);
        }
        try {
            $registration_no = '';
            $hospital = new HospitalUser();
            $hospital->first_name = $request->first_name;
            $hospital->last_name = $request->last_name;
            $hospital->email = $request->email;
            $hospital->mobile = $request->mobile;
            $hospital->role = $request->role;
            $hospital->gender = $request->gender;
            $hospital->password = Hash::make('Password@123');

            if($hospital->save()){
                $registration_no = 'PHARM-' . str_pad($hospital->id, 4, '0', STR_PAD_LEFT);
                $hospital->registration_no = $registration_no;
                $hospital->update();
                return $this->response([
                    'status'=>true,
                    'message'=>'User created successfully'
                ], self::HTTP_CREATED);
            }else{
                return $this->response([
                    'status'=>false,
                    'message'=>'Unable create user due to internal error'
                ], self::HTTP_INTERNAL_SERVER_ERROR);
            }
        }catch(Exception $e){
            return $this->response([
                'status'=>false,
                'message'=>'Error: '.$e->getMessage()
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
        ], self::HTTP_OK);
    }
}
