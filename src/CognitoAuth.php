<?php namespace Almajal\Auth;

/**
*  A sample class to Handle coginto 0auth 
*
*  @author Ahmed Sami
*/
class CognitoAuth{


    public static $instance;

    public static $user = null;

    public static $config = [
    'endPoint'      => '', //domain
    'clientId'     => '', //clientId
    'clientSecret' => '', //clientSecret
    'redirectUri'  => '' //redirectUri
    ];

    public function __construct( $newConfig = []  ) {
      self::Config($newConfig);
        self::$instance = $this;
    }

    public static function getInstance($newConfig  = [] ) {
        if (self::$instance === null) {
            self::$instance = new self( $newConfig);
        }
        return self::$instance;
    }


    public static function Config($newConfig){
      self::$config = array_merge(self::$config , $newConfig);
      return self::$instance;
    }

    public static function MakeLoginUrl($code = ''){

      return  self::$config['endPoint'] . "login?"
                    ."mySession=".$code
                    ."&response_type=code"
                    ."&client_id=" . self::$config['clientId']
                    ."&redirect_uri=" . self::$config['redirectUri'];

    }


        public static function MakeLogoutUrl($code = ''){

      return  self::$config['endPoint'] . "logout?"
                    ."mySession=".$code
                    ."&client_id=" . self::$config['clientId']
                    ."&redirect_uri=" . self::$config['redirectUri'];

    }

    public static function GetTokensByCode (  $code ){

        if (  empty ($code)  ){
          throw new \exception('0auth code is empty !');
        }



      $curl = curl_init();
 
        $params = array(
          CURLOPT_URL =>  self::$config['endPoint'] . "token?"
                    ."code=".$code
                    ."&grant_type=authorization_code"
                    ."&client_id=" . self::$config['clientId']
                    ."&client_secret=" .self::$config['clientSecret']
                    ."&redirect_uri=" . self::$config['redirectUri'],
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_NOBODY => false, 
          CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/x-www-form-urlencoded",
                "accept: *",
                "accept-encoding: gzip, deflate",
          ));
 
        curl_setopt_array($curl, $params);
 
        $response = curl_exec($curl);
        $error = curl_error($curl);
 
        curl_close($curl);



 
        if ($error) {
            
            throw new \exception( "cURL Error: " . $error);
        }
        else
        {
            $response = json_decode($response, true); 

            if(array_key_exists("access_token", $response)){
              self::$user = $response;
              return self::$instance;
            }
          
            if(array_key_exists("error", $response)) 
              throw new \exception ("cURL Error: Something went wrong! Please contact IT department. ERROR MSG : " . $response["error"]);

            //echo "cURL Error: Something went wrong! Please contact IT department.";
        }

    }


    public static function getAccessToken(){
      if ( self::$user == null  or !array_key_exists("access_token" , self::$user ))
        return false;


      return self::$user['access_token'];


    }

        public static function getIdToken( $decode = false  ){
      if ( self::$user == null  or !array_key_exists("id_token" , self::$user ))
        return false;

      if ( $decode == true  ){
        return self::DecodeIdToken();
      }


      return self::$user['id_token'];


    }

        public static function getRefreshToken(){
      if ( self::$user == null  or !array_key_exists("refresh_token" , self::$user ))
        return false;


      return self::$user['refresh_token'];


    }


    public static function getUser(){
      if ( self::$user == null  )
        return false;
      
      return self::$user;
    }


    public static function DecodeIdToken(){
      if ( self::$user == null  or !array_key_exists("id_token" , self::$user ))
        throw new \exception('can not decode IdToken it\'s null');


        
    list($header, $payload, $signature) = explode (".", self::$user['id_token'] );

    return base64_decode($payload);
    }

}