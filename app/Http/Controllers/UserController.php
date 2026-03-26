<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller{

    /**
     * Authenticate an admin user and generate an API token.
     *
     * This method validates the provided email and password credentials against the User model.
     * Upon successful authentication, it creates a new API token with 'pharmacy' ability.
     *
     * @param \Illuminate\Http\Request $request The HTTP request containing email and password.
     *                                          - email (string, required): The user's email address.
     *                                          - password (string, required): The user's password.
     *
     * @return \Illuminate\Http\JsonResponse Returns a JSON response with the following structure:
     *                                        On success (200):
     *                                        - status (bool): true
     *                                        - message (string): "Login successful"
     *                                        - access_token (string): Bearer token for authenticated requests
     *                                        - token_type (string): "Bearer"
     *                                        - pharmacy (User): The authenticated user object
     *
     *                                        On validation error (422):
     *                                        - status (bool): false
     *                                        - message (string): "Validation error"
     *                                        - errors (string): First validation error message
     *
     *                                        On authentication failure (401):
     *                                        - status (bool): false
     *                                        - message (string): "Invalid credentials"
     */
    public function adminLogin(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($validator->fails()){
            return $this->response([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()->first()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if(!$user || ! Hash::check($request->password, $user->password)){
            return $this->response([
                'status' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $user->createToken('admin-token', ['user'])->plainTextToken;

        return $this->response([
            'message' => 'Login successful',
            'status' => true,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'pharmacy' => $user
        ], 200);
    }

}
