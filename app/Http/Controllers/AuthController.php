<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log; // Ensure this is imported

class AuthController extends Controller
{
    /**
     * Handle user registration.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            // Validate the incoming request data for registration.
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'], // 'confirmed' checks for 'password_confirmation' field
            ]);

            // Generate a simple, random API token for the new user.
            $apiToken = Str::random(60);

            // Create a new user record in the database.
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password), // Hash the password before saving for security.
                'api_token' => $apiToken, // Store the generated token.
            ]);

            // Ensure the user object was actually created and is an instance of User model
            if (!$user instanceof User) {
                Log::error('Registration Error: User object not created correctly or not an instance of User model.', ['user_data' => $request->all()]);
                return response()->json([
                    'message' => 'Registration failed due to an internal server error. Please try again later.'
                ], 500);
            }

            // Return a successful response with user data and the generated token.
            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user,
                'token' => $apiToken, // Provide the token to the client.
            ], 201); // 201 Created status code.

        } catch (ValidationException $e) {
            // Catch validation exceptions and return them as JSON.
            // This ensures specific validation messages are returned.
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422); // 422 Unprocessable Entity status code.
        } catch (\Exception $e) {
            // Catch any other unexpected exceptions during registration.
            Log::error('Registration Error: ' . $e->getMessage(), ['exception' => $e, 'request_data' => $request->all()]);
            return response()->json([
                'message' => 'Registration failed due to an unexpected error.',
                'error' => config('app.debug') ? $e->getMessage() : 'An internal server error occurred.' // Show detailed error only in debug mode
            ], 500);
        }
    }

    /**
     * Handle user login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            // Validate the incoming request data for login.
            $request->validate([
                'email' => ['required', 'string', 'email'],
                'password' => ['required', 'string'],
            ]);

            // Attempt to authenticate the user using provided credentials.
            if (!Auth::attempt($request->only('email', 'password'))) {
                // If authentication fails, return a specific JSON error for invalid credentials.
                return response()->json([
                    'message' => 'Login failed',
                    'errors' => [
                        'email' => ['The provided credentials do not match our records.']
                    ]
                ], 401); // 401 Unauthorized status code.
            }

            // Get the authenticated user instance.
            $user = Auth::user();

            // IMPORTANT CHECK: Ensure $user is a valid User model instance.
            // This should ideally not be null if Auth::attempt was successful.
            if (!$user instanceof User) {
                Log::error('Login Error: Auth::user() did not return an instance of App\\Models\\User after successful attempt.', [
                    'user_returned_type' => gettype($user),
                    'user_returned_class' => is_object($user) ? get_class($user) : 'N/A'
                ]);
                return response()->json([
                    'message' => 'Login failed due to an internal authentication issue. Please try again.'
                ], 500);
            }

            // Generate a new API token for the authenticated user on each successful login.
            // This effectively "refreshes" their token and invalidates previous ones.
            $apiToken = Str::random(60);

            // Update the user's api_token in the database.
            // forceFill is used because 'api_token' is in $hidden in the User model,
            // but we still want to update it directly.
            $user->forceFill([
                'api_token' => $apiToken,
            ])->save();

            // Return a successful response with user data and the new token.
            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'token' => $apiToken, // Provide the new token to the client.
            ], 200); // 200 OK status code.

        } catch (ValidationException $e) {
            // Catch validation exceptions and return them as JSON.
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422); // 422 Unprocessable Entity status code.
        } catch (\Exception $e) {
            // Catch any other unexpected exceptions during login.
            Log::error('Login Error: ' . $e->getMessage(), ['exception' => $e, 'request_data' => $request->all()]);
            return response()->json([
                'message' => 'Login failed due to an unexpected error.',
                'error' => config('app.debug') ? $e->getMessage() : 'An internal server error occurred.'
            ], 500);
        }
    }

    /**
     * Handle user logout (clear the current token in the database).
     * (No changes needed here as you confirmed it's working)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user instanceof User) {
                Log::warning('Logout Attempt: User not authenticated or invalid user object.', [
                    'user_returned_type' => gettype($user),
                    'user_returned_class' => is_object($user) ? get_class($user) : 'N/A'
                ]);
                return response()->json(['message' => 'Logout failed: User not authenticated or invalid token.'], 401);
            }

            $user->forceFill([
                'api_token' => null,
            ])->save();

            return response()->json([
                'message' => 'Logged out successfully',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Logout Error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => 'Logout failed due to an unexpected error.',
                'error' => config('app.debug') ? $e->getMessage() : 'An internal server error occurred.'
            ], 500);
        }
    }

    /**
     * Get the authenticated user's details.
     * (No changes needed here)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'User' => $request->user(),
        ], 200);
    }
}