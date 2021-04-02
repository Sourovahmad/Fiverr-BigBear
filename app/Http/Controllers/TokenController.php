<?php

namespace App\Http\Controllers;

use App\Models\square_token;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Wheniwork\OAuth2\Client\Provider\Square;



class TokenController extends Controller
{
    public function index()
    {



        $provider = new Square([
            'clientId'          => '{sandbox-sq0idb-1LkhCuZa_NRCdcpPKZ-wLg}',
            'clientSecret'      => '{EAAAEB_TxnNjs-F_W5o2Xv0i5TYKa-QVNVH7yeK4PC2kyQK4NNeT_Y09MG78AvFr}',
            'redirectUri'       => route('dashboard')
        ]);
        
        if (!isset($_GET['code'])) {
            // If we don't have an authorization code then get one
            $authUrl = $provider->getAuthorizationUrl();
            $_SESSION['oauth2state'] = $provider->state;
            header('Location: '.$authUrl);
            exit;
        
        // Check given state against previously stored one to mitigate CSRF attack
        } elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
        
            unset($_SESSION['oauth2state']);
            exit('Invalid state');
        
        } else {
        
            // Try to get an access token (using the authorization code grant)
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $_GET['code']
            ]);
        
            // Optional: Now you have a token you can look up a users profile data
            try {
        
                // We got an access token, let's now get the user's details
                $userDetails = $provider->getUserDetails($token);
        
                // Use these details to create a new profile
                printf('Hello %s!', $userDetails->firstName);
        
            } catch (Exception $e) {
        
                // Failed to get user details
                exit('Oh dear...');
            }
        
            // Use this to interact with an API on the users behalf

            // $square_token = new square_token;
            // $square_token->client_name = Auth::user()->name;
            // $square_token->access_token = $token->accessToken;
            // $square_token->save();

             echo $token->accessToken;
           
        }

       // return redirect(route('dashboard'));
    }
}
