<?php
namespace App\Http\Controllers\Auth;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Ilzrv\LaravelSteamAuth\SteamAuthenticator;
use Ilzrv\LaravelSteamAuth\SteamUserDto;
use App\Models\User;
use App\Models\Role;
use Inertia\Inertia;
use Inertia\Response;
use Ilzrv\LaravelSteamAuth\Exceptions\Validation\ValidationException;
use Ilzrv\LaravelSteamAuth\Exceptions\Authentication\SteamResponseNotValidAuthenticationException;

final class SteamAuthController
{
    public function __invoke(
        Request $request,
        Redirector $redirector,
        Client $client,
        HttpFactory $httpFactory,
        AuthManager $authManager,
    ) {
        $auth = new SteamAuthenticator(new Uri($request->getUri()), $client, $httpFactory);

        try {
            $auth->auth();
        } catch (ValidationException|SteamResponseNotValidAuthenticationException) {
            //$returnTo = url('/auth/steam');
            $returnTo = route('steam.auth');
            $realm = config('app.url');

            $qs = http_build_query([
                'openid.ns' => 'http://specs.openid.net/auth/2.0',
                'openid.mode' => 'checkid_setup',
                'openid.return_to' => $returnTo,
                'openid.realm' => $realm,
                'openid.identity' => 'http://specs.openid.net/auth/2.0/identifier_select',
                'openid.claimed_id' => 'http://specs.openid.net/auth/2.0/identifier_select',
            ]);

            $url = 'https://steamcommunity.com/openid/login?'.$qs;

            return $redirector->away($url); //! IMPORTANTE: away() para URL externa
        }

        //? validado -> obtener usuario y loguear
        $steamUser = $auth->getSteamUser();

        $user = User::firstOrCreate(
            ['steamid' => $steamUser->getSteamId()],
            [
                'name' => $steamUser->getPersonaName(),
                'steam_nick' => $steamUser->getPersonaName(),
                'steam_avatar' => $steamUser->getAvatarFull(),
            ]
        );
        
        //? Asignar rol 'user' si no tiene ningún rol
        if ($user->roles()->count() === 0) {
            $role = Role::where('name', 'User')->first();

            if($role){
                $user->assignRole($role);
            }
        }

        // dd($steamUser->getSteamId());
        // die();

        $authManager->login($user, true);
        return redirect()->intended(route('home'));
    }

    public function login(Request $request, Redirector $redirector){
        return redirect()->route('steam.auth');
    }

    public function logout(
        Request $request,
        Redirector $redirector,
        AuthManager $authManager,
    ) {
        $authManager->logout();
        return redirect()->route('home');
    }
}