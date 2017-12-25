<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Carbon;
use Response;
use Illuminate\Support\Facades\Route;
use GuzzleHttp\Client;
use App\User;
use App\oAuthClient;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
class LoginController extends Controller
{
    //
    function login()
    {
    	
    	 	$client = new Client();
	        $input = Input::json();

        	$user = User::where('email', "=", $input->get('email'))->first();

        	$clients = oAuthClient::where("id", "=", $user['client_id'])->first();
            if($input->get('grant_type'))
            {
                $grant_type=$input->get('grant_type');
            }
            else
            {
                $grant_type='password';
            }
            try {
		            $response = $client->request('POST','http://passport.com/oauth/token',[
		            'form_params' => [
		            'grant_type' => $grant_type,
		            'client_id' => $clients['id'],
		            'client_secret' => $clients['secret'],
		            'username'=>$input->get('email'),
		            'password'=>$input->get('password'),
		            'code' => '',
		            ]]);
		            
		            $getContents = $response->getBody()->getContents();
		           	$responseData = json_decode($getContents);
                    $token=$responseData->access_token;

		            $response = $client->request('GET', 'http://passport.com/api/v1/user', [
			    	'headers' => [
			        'Accept' => 'application/json',
			        'Authorization' => 'Bearer '.$token,
			    	]]);

					$getContents = $response->getBody()->getContents(); 
					$responseData = json_decode($getContents); 

		            return Response::json(
		                        array(
		                            'returnType'                  => 'success',
		                            'message'               => 'Login success',
		                            'user'                 =>  $responseData ,
		                            'access_token'			=>	$token
		                ),
		                200
		            );
            }
            catch (ClientException $e) {
                 // return $content = Psr7\str($e->getResponse()); 
                $response = array(
                        'returnType'    => 'error',
                        'message'       => $e->getMessage()
                );
            }
            catch (RequestException $e) {
                 // return $content = Psr7\str($e->getResponse()); 
                $response = array(
                        'returnType'    => 'error',
                        'message'       => $e->getMessage()
                );
            }
            return Response::json($response);
    }
    function user(Request $request)
    {
    	return $request->user();
    }
    
}
