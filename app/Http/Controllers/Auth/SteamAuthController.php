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
            $returnTo = url('/auth/steam');
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

        $user = \App\Models\User::firstOrCreate(
            ['steamid' => $steamUser->getSteamId()],
            [
                'name' => $steamUser->getPersonaName(),
                'steam_nick' => $steamUser->getPersonaName(),
                'steam_avatar' => $steamUser->getAvatarFull(),
            ]
        );

        // dd($steamUser->getSteamId());
        // die();

        $authManager->login($user, true);
        return redirect()->intended('/');
    }
}