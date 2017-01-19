<?php
/*
	on console.api.ai go to Integrations and click on Actions On Google
		Check "Sign in required for welcome intent"
		On OAuth Linking
			make sure you have client_id in the Client_id field
			Set Grant Type to Implicit
			Set Authorization URL to https://yourwebsite.com/login/googlehome
			Set Scopes to "profile email"
			Leave Client Secret and Token URL blank
		Set your Privacy Policy URL to https://yourwebsite.com/privacy
		Click Authorize, then Preview.
		
		Take note of your 
			YOUR_CLIENT_ID
			YOUR_CLIENT_SECRET
			YOUR_GOOGLE_PROJECT_ID
		 from the google developer console



*/
//https://docs.api.ai/docs/actions-on-google-integration
if(isset($_REQUEST['code'])){
	//Handling the response and exchanging the code. This must be a POST, not a GET
	$url='https://accounts.google.com/o/oauth2/token';
	//set key/value pairs to post - this will return a json string
	$opts=array(
		'client_id'		=> encodeUrl('YOUR_CLIENT_ID'),
		'redirect_uri'	=> "https://yourwebsite.com/login/googlehome",
		'client_secret'	=> encodeUrl('YOUR_CLIENT_SECRET'),
		'code'			=> $_REQUEST['code'],
		'grant_type'	=> 'authorization_code',
		'-headers'		=> array('Content-type: application/x-www-form-urlencoded'),
		'-json'			=> 1 //this is only needed in my code to tell my postURL to convert the json to an array
	);
	$post=postURL($url,$opts);
	//we now have $post['json_array']['access_token']
	if(isset($post['json_array']['access_token'])){
		//redirect to google.com and send the the state they sent.
		$url="https://oauth-redirect.googleusercontent.com/r/YOUR_GOOGLE_PROJECT_ID#";
		$url .= 'access_token='.urlencode($post['json_array']['access_token']);
		$url .= "&token_type=Bearer&state=".$_REQUEST['state'];
		header("Location: {$url}");
		exit;
	}
	else{
		//No access token - redirect them to a page that explains that
		echo '<h1>Access Request Failed</h1><h2>Please try again</h2>';
		exit;
	}
}
else{
	//A New Auth Request Recieved. Capture state that they send me. and redirect to their oauth server
	$url='https://accounts.google.com/o/oauth2/v2/auth?';
	$url .= 'response_type=code';
	$url .= '&client_id='.encodeUrl('YOUR_CLIENT_ID');
	$url .= '&redirect_uri='.encodeUrl('https://yourwebsite.com/login/googlehome');
	$url .= '&scope=email%20profile';
	$url .= '&state='.$_REQUEST['state'];
	header("Location: {$url}");
	exit;
}
?>
