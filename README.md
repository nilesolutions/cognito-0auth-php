# PHP cognito 0AUTH 2 


## Requirements

The following versions of PHP are supported:

* PHP 5.6
* PHP 7.1
* PHP 7.2


## Installation

```
composer require almajal/auth
```




## how to use

```
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require 'vendor/autoload.php';

use Almajal\Auth\CognitoAuth;

// TESTING 
CognitoAuth::config([
		'endPoint'      => 'https://yourdomain.auth.eu-central-1.amazoncognito.com/',
		'clientId'      => 'your client id',
		'clientSecret'  => 'your client secret',
		'redirectUri'   => 'https://test.localhost/',
    ]);



if  ( isset ( $_GET['code'] ) ){
	 
	try {

		$getIdToken = CognitoAuth::getInstance()->getTokensByCode($_GET['code'])->getIdToken(true);
		PRINT ( $getIdToken );
		// do your stuff here 

	}
	catch (exception $e)
	{ 
		echo $e->getMessage();
	 }
}
else{

	echo '<a href="' . CognitoAuth::MakeLoginUrl('testing') . '"">login via almajal id</a>';
}


```

