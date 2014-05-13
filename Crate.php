<?php

	class Crate {

	    private $_config = array();
	    private $_state = null;
	    private $_curl_handle = null;	    
	    const API_BASE = '_sql?pretty';
	    const SERVER = '108.161.135.84:4200/';
	    /**
	     * @param array $config 
	     */
	    public function __construct( $config){
		
	        $this->_config = $config;
	    }
	 
	   public function insert_query($table_name, $data){
	    $fields_array = array();
		$data_array = array();
	    
		$placeHolder = array();
		
		foreach($data as $key=>$value){
			
			array_push($placeHolder, "?");
			
			array_push($fields_array, $key);
		 
			 if(!is_string($value) && is_numeric($value)){
			
				array_push($data_array, $value);
			 
			 }else if(is_string($value) && $value =="true" || $value == "false"){
			 
					array_push($data_array, $value =="true"?'true':'false');		 
			 }else{		
					array_push($data_array, '"'.$value.'"');
  			 }
		}
		
		$insertQry = '{"stmt":
			"insert into '.$table_name.' ('.implode($fields_array, ", ").') values ('.implode($placeHolder, ", ").')",
			"args": ['.implode($data_array, ", ").']
		}';
		
			
		return $this->post("",$insertQry);	
	   }
	   
	   public function update_query(){
	   
	   }
	   
	   public function select_query(){
	   
	   }
	    
	    /**
	     * POST to an authenciated API endpoint w/ payload
	     *
	     * @param array $payload
	     * @return array
	     */
	    public function post($payload){
	    	 
	    	return $this->fetch($payload, 'POST');
	    	 
	    }
	     
	    /**
	     * GET an authenticated API endpoind w/ payload
	     *
	     * @param array $payload
	     * @return array
	     */
	    public function get($payload ){
	    	 
	    	return $this->fetch($payload);
	    	 
	    }
	     		 
		/**
	     * Upload blob with file path and table.
	     * @param array $file_path, $table
	     * @return sha1 hax key
	     */
		 
		 public function uploadBlob($file_path, $table){
		 $sha1file = sha1_file($file_path);
		 $this->put(self::SERVER.'_blobs/'.$table.'/'.$sha1file, $file_path);
		 return $sha1file;
		 }
		 
		 
	    /**
	     * PUT to an authenciated API endpoint w/ payload
	     *
	     * @param array $payload
	     * @return array
	     */
	     public function put($base, $payload ){
	    	return $this->_makeRequest($base, $payload, "PUT",  $headers = array(),  $curl_options = array());	    	 
	    }
	    
	    /**
	     * Make an authenticated API request to the endpoint
	     * Headers are for additional headers to be sent along with the request. 
	     * Curl options are additional curl options that may need to be set
	     * 
	     * @param array $payload
	     * @param string $method
	     * @param array $headers
	     * @param array $curl_options
	     * @return array 
	     */
	    public function fetch($payload, $method = 'GET', array $headers = array(), array $curl_options = array()){
	        
	        $endpoint = self::SERVER.self::API_BASE;
	        $headers[] = 'x-li-format: json';
	        
	        return $this->_makeRequest($endpoint, $payload, $method, $headers, $curl_options);
	        
	    }
	    
	    /**
	     * Get debug info from the CURL request
	     * 
	     * @return array
	     */
	    public function getDebugInfo(){
	        
	        return $this->_debug_info;
   
	    }
	    
	    /**
	     * Make a CURL request
	     * 
	     * @param string $url
	     * @param array $payload
	     * @param string $method
	     * @param array $headers
	     * @param array $curl_options
	     * @throws \RuntimeException
	     * @return array
	     */
	    protected function _makeRequest($url,  $payload , $method = 'GET', array $headers = array(), array $curl_options = array()){

	        $ch = $this->_getCurlHandle();
			
			if($method=='PUT'){
				$filesize = filesize($payload);
				$fh = fopen($payload, 'r');
			
				$options = array(
					CURLOPT_CONNECTTIMEOUT => 2,
					CURLOPT_BINARYTRANSFER => true,
					CURLOPT_URL => $url,
					CURLOPT_SSL_VERIFYPEER => false,
					CURLOPT_HEADER => false
				);

				$options[CURLOPT_PUT] = true;
				$options[CURLOPT_INFILE] = $fh;
				$options[CURLOPT_INFILESIZE] = $filesize;
			
			
			}else{			
				$options = array(
					CURLOPT_CUSTOMREQUEST => strtoupper($method),
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_URL => $url,
					CURLOPT_HTTPHEADER => $headers,
					CURLOPT_SSL_VERIFYPEER => false,
					CURLOPT_FOLLOWLOCATION => true
				);

				if (!empty($payload)){
					$options[CURLOPT_POST] = true;
					$options[CURLOPT_POSTFIELDS] = $payload;
					$headers[] = 'Content-Length: ' . strlen($options[CURLOPT_POSTFIELDS]);
					$options[CURLOPT_HTTPHEADER] = $headers;
				}
			}
	        
	        if (!empty($curl_options)){
	            $options = array_merge($options, $curl_options);
	        }
	        curl_setopt_array($ch, $options);
	        $response = curl_exec($ch);
			if($method=='PUT') fclose($fh);
			
		   $this->_debug_info = curl_getinfo($ch);
	        
	        if ($response === false){
	            throw new \RuntimeException('Request Error: ' . curl_error($ch));
	        }
	        
	        $response = json_decode($response, true);
	     
	        return $response;
	    }
	    
	    protected function _getCurlHandle(){
	    
	    	if (!$this->_curl_handle){
	    		$this->_curl_handle = curl_init();
	    	}
	    		
	    	return $this->_curl_handle;
	    
	    }
	    
	    public function __destruct(){
	    
	    	if ($this->_curl_handle){
	    		curl_close($this->_curl_handle);
	    	}
	    
	    }
	    
	}
