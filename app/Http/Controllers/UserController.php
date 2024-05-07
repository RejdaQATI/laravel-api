<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/users",
     *     summary="Get all users",
     *     @OA\Response(response=200, description="A list of users"),
     * )
     */
    public function index()
    {
        $users = User::all();
        return response()->json([
            'users' => $users
        ]);
    }

    /**
     * @OA\Post(
     *     path="/users",
     *     summary="Create a new user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="password_confirmation", type="string"),
     *         )
     *     ),
     *     @OA\Response(response=200, description="The newly created user"),
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'string', 'min:6', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]+$/'],
            'password_confirmation' => ['required','string','min:6','regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]+$/'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $user = User::create($request->all());
        return response()->json([
            'user' => $user
        ]);
    }

    /**
     * @OA\Get(
     *     path="/users/{id}",
     *     summary="Get a user by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user to retrieve",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Details of the specified user"),
     *     @OA\Response(response=404, description="User not found"),
     * )
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return response()->json([
            'user' => $user
        ]);
    }

    /**
     * @OA\Put(
     *     path="/users/{id}",
     *     summary="Update a user",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user to update",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="password_confirmation", type="string"),
     *         )
     *     ),
     *     @OA\Response(response=200, description="Updated user"),
     *     @OA\Response(response=404, description="User not found"),
     * )
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => ['nullable', 'string', 'min:6', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]+$/'],
            'password_confirmation' => ['nullable','string','min:6','regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]+$/'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found.'
            ], 404);
        }

        $user->update($request->all());

        return response()->json([
            'user' => $user
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/users/{id}",
     *     summary="Delete a user by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user to delete",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Deleted user"),
     *     @OA\Response(response=404, description="User not found"),
     * )
     */
    public function destroy(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found.'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'user' => $user
        ]);
    }


    /**
     * @OA\Post(
     *     path="/register",
     *     summary="Register a new user",
     *     description="Register a new user with the provided credentials",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123"),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="User created successfully"),
     *     @OA\Response(response=401, description="Validation error"),
     * )
     */

    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'password_confirmation' => ['required', 
            ],
        ]);
    
        if($validator->fails()){
            return response()->json([
                'status'=>false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 401);
        }
        
        $user = User::create($request->all());
        $token = $user->createToken("API TOKEN")->plainTextToken;
        
        return response()->json([
            'status' => true,
            'message' => 'User Created Successfully',
            'token' => $token,
        ], 200);
    }

  /**
     * @OA\Post(
     *     path="/login",
     *     summary="Login as an existing user",
     *     description="Login with the provided email and password",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Login successful"),
     *     @OA\Response(response=401, description="Validation error"),
     * )
     */

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
    
        if($validator->fails()){
            return response()->json([
                'status'=>false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 401);
        }
        if (Auth::attempt($request->all())) {
            if(auth('sanctum')->check()){
            auth()->user()->tokens()->delete();
            }
        
    
        $user = Auth::user();
        $token = $user->createToken("API TOKEN")->plainTextToken;
        return response()->json([
            'status'=>true,
            'message' => 'Login successful',
            'user' => $user,
            "token" => $token,
        ]);

    }
    
}
}
    










