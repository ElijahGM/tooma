<?php
namespace Tooma\Api;

class Sender{
	private $authKey;
	private $cert;
	private $identifier=null;
	private $endpoint="http://www.tooma.co.ke/api/v1/";
    private $user_cert_path;
    private $cert_path=null;
    private $response;
    private $_on_success_observers=[];
    private $_on_error_observers=[];
    public $lastCall="";
    public $lastParams = [];
    public $params = [];
	public function __construct($token='',$cert_path=null)
	{
		$this->authKey        = $token;
		$this->cert_path      = $cert_path;			
		$this->loadCert();
	}	
	private function loadCert()
	{
		if(is_null($this->cert_path)){

			$this->cert_path = __DIR__."/ssl/tooma.pem";
		}

		if(file_exists($this->cert_path)){
           $this->cert = trim(file_get_contents($this->cert_path));
		   return true;
		}
	   throw new \Exception("Error Invalid certificate path",560);
	   
	}
	private function prepare($method,$args){ 
	  $this->lastCall   = $method;
	  $this->lastParams = $args;
	  switch ($method) {
	  	case 'login':
	  		if(isset($args['username'])){
	  			$this->identifier=$args['username'];
	  			
	  		}
	  		break;
	  	
	  	default:
	  		# code...
	  		break;
	  }
	}
	public function getResponse()
	{
		return $this->response;
	}
	public function __call($method,$args){ 

	  $params = isset($args[0])?$args[0]:[];
	  $this->prepare($method,$params);
      return $this->curl_send($this->endpoint.$this->fromCamelCase($method),$params);
	}
	private function fire($event='onSuccess')
	{
		if(!$this->response) return null;
		$observers = ($event==='onSuccess')
					            ?$this->_on_success_observers
					            :$this->_on_error_observers;
		$value     = ($event==='onSuccess')
					            ?"data"
					            :"error";
		foreach ($observers as $fn) {
			if($event==='onSuccess'){
				$fn($this->response->{$value},new Pagination($this,$this->response->pagination));
			}else{
				$fn($this->response->{$value});
			}
					            
			
		}
	}
	public function withCsv($path){ 
	  $this->params['csv_content']=Reader::read($path);
     return $this;
	}
	public function thenPing($url){ 
      $this->params['callback_url']=$url;
	  return $this;
	}
	public function withTemplate($template){ 
	  $this->params['template']=$template;
	  return $this;
	}
	public function withParams($args){ 
	  $this->params['template_args']=$args;
	  return $this;
	}
	public function schedule($format){ 
       $this->params['scheduled']=$format;
	   return $this;
	}
	public function onSuccess(\Closure $fn){ 
	  if(is_callable($fn)){
	  	$this->_on_success_observers[]=$fn;
	  }else{
	  	throw new \Exception("Invalid function", 500);	  	
	  }
	  return $this;
	}
	public function onError(\Closure $fn){ 
	  if(is_callable($fn)){
	  	$this->_on_error_observers[]=$fn;
	  }else{
	  	throw new \Exception("Invalid function", 500);	  	
	  }
	  return $this;
	}
	private function curl_send($url,$data){ 
       // Get cURL resource
		// exit(json_encode(['data' =>$this->messages]));
		$curl = curl_init();
		// Set some options - we are passing in a useragent too here
		$content = $this->encrypt($data,$key);
		$headers = [
              "X-Authorization: $this->authKey",
              "X-Identifier: ".$this->identifier, 
              "X-Content: ".$content,
              "X-Key: ".$key,               
              "Content-type: application/json"
		    ];

		 curl_setopt_array($curl, array(
		    CURLOPT_RETURNTRANSFER => 1,
		    CURLINFO_HEADER_OUT=>true,		   
		    CURLOPT_URL => $url,
		    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            // CURLOPT_CUSTOMREQUEST => "POST",
		    CURLOPT_USERAGENT => 'Tooma/5.0 (X11; Tooma Client; Version i686; rv:28.0)',
		    CURLOPT_HTTPHEADER=>$headers,
		   
		));	

        $resp = curl_exec($curl);
		$this->response = json_decode($resp);
	  
		if(is_object($this->response) && $this->response->success){
			$this->fire();
		}else{
			$this->fire('onError');
		}
		$information = curl_getinfo($curl);	
			
		curl_close($curl);
      
		return $this;
	}
	private function encrypt($source,&$key=null){
		$source = is_array($source)?json_encode($source):$source;
		
		if(function_exists("openssl_get_publickey") && function_exists("openssl_public_encrypt")){
			$pub_key = openssl_get_publickey($this->cert);	
			$data=[];
		    if(openssl_seal($source,$crypttext,$ekeys,[$pub_key])){
		        $data = base64_encode($crypttext);
                $key  = base64_encode($ekeys[0]);
		       
		    }else{		    	
		      exit("Error encrypting data :: ".openssl_error_string());
		    }
		     openssl_free_key($pub_key);
		    
            return $data;
		}
		throw new \Exception("Missing library php openssl, we are working to ensure this library can run on any server",526);
	}
	/**
	 * Converts camelCase string to have spaces between each.
	 * @param $input
	 * @return string
	 **/
	private function fromCamelCase($input) {
	       
	        $a = preg_split(
		        '/(^[^A-Z]+|[A-Z][^A-Z]+)/',
		        $input,
		        -1, /* no limit for replacement count */
		        PREG_SPLIT_NO_EMPTY /*don't return empty elements*/
		            | PREG_SPLIT_DELIM_CAPTURE /*don't strip anything from output array*/
		    );
	        return strtolower(join($a, "-"));
	}
}