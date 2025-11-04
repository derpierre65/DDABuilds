<?php

namespace App\Http\Controllers;

use App\Auth\SteamAuth;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AuthController extends Controller
{
	public function auth(Request $request, SteamAuth $steamAuth)
	{
		if ( $request->query('debug') && app()->isLocal() ) {
			// /api/auth/steam?debug=steamID
			/** @var User $user */
			$user = User::query()->find($request->query('debug'));
			if ( $user ) {
				Auth::login($user, true);
			}

			return redirect('/auth/');
		}

		if ( $steamAuth->isValidRequest() ) {
			$userId = $steamAuth->auth();
			if ( $userId ) {
				$userInfo = $steamAuth->getUserInfo();
				/** @var User $user */
				$user = User::query()->updateOrCreate([
					'id' => $userId,
				], [
					'name' => $userInfo['personaname'],
					'avatar_hash' => $userInfo['avatarhash'],
				]);

				Auth::login($user, true);

				return redirect('/auth/');
			}
		}

		throw new BadRequestHttpException();
	}

    public function user()
    {
        return new UserResource(auth()->user());
    }

	public function logout() : JsonResponse
	{
		Auth::logout();

		return response()->json(['status' => 'OK']);
	}
}