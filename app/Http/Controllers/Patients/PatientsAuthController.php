<?php

namespace App\Http\Controllers\Patients;
use App\Http\Controllers\Controller;
use App\Models\Patients\PatientUser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PatientsAuthController extends Controller
{
    /**
     * Authenticate a patient and generate an access token
     *
     * @param Request $request The HTTP request containing patient credentials
     * @return \Illuminate\Http\JsonResponse JSON response with authentication result
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * Expected request parameters:
     * - mobile (string, required): Patient mobile number (10 digits)
     * - password (string, required): Patient password
     *
     */
    public function patientLogin(Request $request){
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

        $patient = PatientUser::where('mobile', $request->mobile)->first();
        if (! $patient || ! Hash::check($request->password, $patient->password)) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid credentials'
            ], self::HTTP_UNAUTHORIZED);
        }

        $token = $patient->createToken('patient-token', ['patient'])->plainTextToken;

        return $this->response([
            'message' => 'Login successful',
            'status' => true,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'patient' => $patient
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
            $patient = new PatientUser();
            $patient->email = $request->email;
            $patient->first_name = $request->first_name;
            $patient->last_name = $request->last_name;
            $patient->mobile = $request->mobile;
            $patient->gender = $request->gender;
            $patient->password = Hash::make('Patient@123');

            if($patient->save()){
                $registration_no = 'PATIENT-' . str_pad($patient->id, 4, '0', STR_PAD_LEFT);
                $patient->registration_no = $registration_no;
                $patient->update();
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
