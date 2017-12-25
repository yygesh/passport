<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Response;
use Illuminate\Support\Facades\Input;
use App\oAuthClient;
use App\User;
use Exception;
class UserController extends Controller
{
    //
    function register()
    {

		$email= Input::get('email');
		$password=Input::get('password');
		try{

	    	$user = new User(array(
			'name' =>Input::get('name'),
			'email' => Input::get('email'),
			'password' => bcrypt(Input::get('password')),
			));
			$user->save();
			
			$oauth_client=new oAuthClient();
			$oauth_client->user_id=$user->id;
			$oauth_client->name=$user->name;
			$oauth_client->secret=base64_encode(hash_hmac('sha256',$password,$email, true));
			$oauth_client->password_client=1;
			$oauth_client->personal_access_client=0;
			$oauth_client->redirect='';
			$oauth_client->revoked=0;
			$oauth_client->save();

			$user = User::where("id", "=", $user->id)->first();
			$user->client_id=$oauth_client->id;
			$user->save();


		}
		catch (Exception $e) { 
    		// if an exception happened in the try block above 
 			$response = array(
                        'returnType'    => 'error',
                        'message'       => $e->getMessage()
                );
		}
            return Response::json($response);
    }
    function createUserClient(Request $request)
    { 
    		$client = new Client();
	        $input = Input::json();

    		if($input->get('access_token'))
    		{

    		try
    		{

	            $response1 = $client->request('POST', 'http://passport.com/api/oauth/clients', ['json'=>[
	            'name'=>$input->get('name'),
	            'redirect'=>'http://localhost/callback'],
		    	'headers' => [
		        'Accept' => 'application/json',
		        'Authorization' => 'Bearer '.$input->get('access_token'),
		    	]]);

				$getContents1 = $response1->getBody()->getContents(); 
				$responseData1 = json_decode($getContents1); 

				$user = User::where("id", "=", $responseData1->user_id)->first();
				$user->client_id=$responseData1->id;
				$user->save();

				$oauth_client= oAuthClient::where("id", "=", $responseData1->id)->first();
				$oauth_client->password_client=1;
				$oauth_client->save();
	            return Response::json(
	                        array(
	                            'returnType'                  => 'success',
	                            'message'               => 'Login success',
	                            'user'                 =>  $user ,
	                            'access_token'			=>	$input->get('access_token')
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
            catch (Exception $e) { 
    		// if an exception happened in the try block above 
 			$response = array(
                        'returnType'    => 'error',
                        'message'       => $e->getMessage()
                );
			}
            return Response::json($response);
            
    	}

    	else
    	{

    	$email= Input::get('email');
		$password=Input::get('password');
		try{

	    	$user = new User(array(
			'name' =>Input::get('name'),
			'email' => Input::get('email'),
			'password' => bcrypt(Input::get('password')),
			));
			$user->save();
			
			$oauth_client=new oAuthClient();
			$oauth_client->user_id=$user->id;
			$oauth_client->name=$user->name;
			$oauth_client->secret=base64_encode(hash_hmac('sha256',$password,$email, true));
			$oauth_client->password_client=1;
			$oauth_client->personal_access_client=0;
			$oauth_client->redirect='';
			$oauth_client->revoked=0;
			$oauth_client->save();

			$user = User::where("id", "=", $user->id)->first();
			$user->client_id=$oauth_client->id;
			$user->save();


		}
		catch (Exception $e) { 
    		// if an exception happened in the try block above 
 			$response = array(
                        'returnType'    => 'error',
                        'message'       => $e->getMessage()
                );
		}
            return Response::json($response);
    	}

        	
    }
    
      function createPersonalAccessToken(Request $request)
    { 
    		$client = new Client();
	        $input = Input::json();

    		if($input->get('access_token'))
    		{

    		try
    		{

	            $response1 = $client->request('POST', 'http://passport.com/api/oauth/personal-access-tokens', ['json'=>[
	            'name'=>$input->get('name'),
	            'scopes'=>['place-orders']],
		    	'headers' => [
		        'Accept' => 'application/json',
		        'Authorization' => 'Bearer '.$input->get('access_token'),
		    	]]);

				$getContents1 = $response1->getBody()->getContents(); 
				$responseData1 = json_decode($getContents1); 

				// $user = User::where("id", "=", $responseData1->user_id)->first();
				// $user->client_id=$responseData1->id;
				// $user->save();

				// $oauth_client= oAuthClient::where("id", "=", $responseData1->id)->first();
				// $oauth_client->password_client=1;
				// $oauth_client->save();
	            return Response::json(
	                        array(
	                            'returnType'                  => 'success',
	                            'message'               => 'Login success',
	                            'user'                 =>  $responseData1 
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
            catch (Exception $e) { 
    		// if an exception happened in the try block above 
 			$response = array(
                        'returnType'    => 'error',
                        'message'       => $e->getMessage()
                );
			}
            return Response::json($response);
            
    	}

    	
    }

    function getUsers(Request $request)
    {
    	$users= User::all();
    	return Response::json(
	                        array(
	                            'returnType'           => 'success',
	                            'message'              => 'Users Successfully Extracted',
	                            'users'                 =>  $users
	                ),
	                200
	            );
    }
}
