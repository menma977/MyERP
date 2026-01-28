<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\PersonalAccessToken;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Handle a login request to the application.
     *
     * @return array<string, array<string, Carbon|string|null>|int|string>
     */
    public function login(Request $request): array
    {
        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $loginField = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::where($loginField, $request->input('login'))->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['The provided credentials are incorrect.'],
            ]);
        }

        $deviceName = $request->input('device_name', $request->userAgent() ?: 'Unknown Device');
        $companyId = $user->hasCompany()->value('company_id');

        $tokenResult = $user->createToken($deviceName, ['*'], now()->addMinutes(60));
        /** @var PersonalAccessToken $accessToken */
        $accessToken = $tokenResult->accessToken;
        $accessToken->company_id = $companyId ? (int) $companyId : null;
        $accessToken->save();

        $token = $tokenResult->plainTextToken;

        return [
            'user' => [
                'id' => $user->ulid,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 3600,
        ];
    }

    /**
     * Log the user out (revoke the token).
     *
     * @return array{message:string}
     */
    public function logout(): array
    {
        if (Auth::check()) {
            $user = User::find(Auth::id());
            if ($user) {
                $token = $user->currentAccessToken();
                $token?->delete();
            }
        }

        return [
            'message' => 'Successfully logged out',
        ];
    }

    /**
     * Log the user out from all devices (revoke all tokens).
     *
     * @return array{message:string}
     */
    public function logoutAll(): array
    {
        $user = User::find(Auth::id());
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => "User isn't authenticated",
            ])->status(401);
        }

        $user->tokens()->delete();

        return [
            'message' => 'Successfully logged out from all devices',
        ];
    }

    /**
     * Get the authenticated user.
     *
     * @return array<string, array<string, Carbon|string|null>>
     */
    public function me(): array
    {
        $user = Auth::user();

        if (! $user) {
            return [
                'user' => [
                    'id' => null,
                    'name' => null,
                    'username' => null,
                    'email' => null,
                    'email_verified_at' => null,
                    'created_at' => null,
                    'updated_at' => null,
                ],
            ];
        }

        return [
            'user' => [
                'id' => $user->ulid,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
        ];
    }

    /**
     * Get Permissions
     *
     * Retrieve the authenticated user's permissions and information.
     *
     * @return array{user: \Illuminate\Http\Resources\Json\JsonResource, permissions: Collection<int, array{name: string, can: Collection<string, bool>}>}
     *
     * @throws ValidationException If the token is expired
     */
    public function permissions(): array
    {
        if (! Auth::check()) {
            throw ValidationException::withMessages([
                'token' => 'Token expired',
            ])->status(401);
        }

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'token' => 'Token expired',
            ]);
        }

        return [
            'user' => UserResource::make($user),
            'permissions' => PermissionService::grab($user, null),
        ];
    }
}
