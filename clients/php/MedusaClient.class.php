<?php                                                                                                                                                                           
                                                                                                                                                                                
/**                                                                                                                                                                             
 * @Class handles interaction with the medusa API                                                                                                                               
 *                                                                                                                                                                              
 * Usage:                                                                                                                                                                       
 *  $wrms = new MedusaRequest($username, $password);                                                                                                                                     
 *  $response = $wrms->user->timesheet->getTimesheets(array(                                                                                                                    
 *    'person' => $username,                                                                                                                                                    
 *    'start_date' => date('Y-m-d', strtotime('-2 weeks')),                                                                                                                     
 *    'end_date'   => date('Y-m-d', strtotime('-1 weeks'))                                                                                                                      
 *  ));                                                                                                                                                                         
 *                                                                                                                                                                              
 */                                                                                                                                                                             
class MedusaRequest {                                                                                                                                                                    
                                                                                                                                                                                
  /**
   * MedusaClient Version
   */
  const version = '1.x-dev';

  /**                                                                                                                                                                           
   * Request URI                                                                                                                                                                
   */                                                                                                                                                                           
  private static $url = null;                                                                                                                                                   
                                                                                                                                                                                
  /**                                                                                                                                                                           
   * Response object                                                                                                                                                            
   */                                                                                                                                                                           
  private $response = null;                                                                                                                                                     

  /**
   * Request Method
   */              
  private $method = null;
                                
  /**                           
   * Used for __get() and __call() to build up the
   * requested API call                           
   */                                             
  private $method_stack = array();                

  /**
   * Paramters to pass with method
   */                             
  private $parameters = array();  
                                  
  /**                             
   * The current user's session key (null if not authenticated)
   */                                                          
  private $session_id = null;                                  

  /**
   * Constructs a new WRMS request, authenticating along the way
   * if a session key isn't passed in                           
   *                                                            
   * @param string $username the username to auth with, or a session key
   * @param string $password the password to auth with, or null if a session key was provided
   * @throws WRMSException on auth (or other) failure                                        
   * @see WRMS::set_url()                                                                    
   */                                                                                        
  public function __construct($username, $password = null) {                                 
    if ($password === null) {                                                                
      $this->session_id = $username;                                                         
      return;                                                                                
    }                                                                                        

    // Attempt to authenticate
    try {                     
      $request = $this->method('wrms.login')
           ->parameters(array(              
              "username" => $username,      
              "password" => $password,      
              "session_id" => FALSE,        
           ))                               
           ->execute('POST')                
           ->response();                    
      $this->session_id = $request->session_id;

    } catch (Exception $e) {
      throw new WRMSException("Authentication failed: \n " . $e->getMessage());
    }                                                                          
  }                                                                            

  /**
   * Sets the URL of the Medusa API server in use. Persists between requests
   * so you only need to set this once                                      
   *                                                                        
   * @param string $url the URL base of the server, eg. http://api.wrms.catalyst.net.nz/
   */                                                                                   
  public static function set_url($url) {                                                
    if (!preg_match('|/$|', $url)) {                                                    
      $url .= '/';                                                                      
    }                                                                                   
    self::$url = $url;                                                                  
  }                                                                                     

  /**
   * Set the request method
   */                      
  public function method($method) {
    $this->method = $method;       
    return $this;                  
  }                                
                                   
  /**                              
   * Resets the method/param state 
   *                               
   */                              
  public function reset() {        
    $this->parameters = array();   
    $this->method = null;   
    $this->method_stack = array(); 
  }                                

  /**
   * Set method parameters
   *                      
   * @param Array Key/value pairs of parameters
   */                                          
  public function parameters(array $parameters) {
    $this->parameters = array_merge($this->parameters, $parameters);
    return $this;                                                   
  }                                                                 

  /**
   * Send request to Medusa and return response
   */                                          
  public function execute($http_method = 'GET') {
    if (empty(self::$url)) {                     
      throw new WRMSException("Can not execute Medusa Request: URL not set.");
    }                   
    if (!$this->method) {
      throw new WRMSException("Can not execute Medusa Request: Method not defined.");
    }                                                      
    // Ensure session key is present.
    $this->session_key();

    // Does cURL request and returns to $this->response
    return $this->http_request(http_build_query($this->parameters, null, '&'), $http_method);
  }                                                                                          

  /**
   * Access to reponse object
   */                        
  public function response() {
    return $this->parse_response();
  }                                

  /**
   * Retrive/set session key.
   */                   
  public function session_key() {
    if ($session_id = array_shift(func_get_args())) {
      $this->session_id = $session_id;
    }
    if (!empty($this->session_id)) {
      return $this->session_id;     
    }                               
    throw new WRMSException("Medusa Error: Session key request but no session present.");
  }                                                                                      

  /**
   * Send post request
   *                  
   * drupal_http_request doesn't send POST data
   * so we have to write our own here.         
   */                                          
  private function http_request($params = "", $method = 'GET') {
    $ch = curl_init();                                          
    $opts = array(                                              
        CURLOPT_URL => self::$url . $this->method . '.json',    
        CURLOPT_RETURNTRANSFER => TRUE,                         
      );                                                        
    $opts[CURLOPT_POST] = TRUE;                               
    $opts[CURLOPT_POSTFIELDS] = 'session_id=' . $this->session_key();                      
    if ($method == 'POST') {                                    
      $opts[CURLOPT_POSTFIELDS] .= '&' . $params;
    }                                                           
    else {                                                      
      $opts[CURLOPT_URL] .= "?$params";                         
    }                                                           
    curl_setopt_array($ch, $opts);                              
    $this->response = curl_exec($ch);                           
    $info = curl_getinfo($ch);                                  
    if ($this->response === false || $info['http_code'] != 200) {
      throw new WRMSException(strtr("HTTP request to %url failed.", array('%url' => self::$url)));
    }                                                                                             
    curl_close($ch);                                                                              

    $this->reset();

    return $this;
  }              

  /**
   * Turns http response into usable data
   */                                    
  protected function parse_response() {  
    $response = json_decode($this->response);
    if ($response->status->code != 200) {
      throw new WRMSException($response->status->message, $response->status->code, $response->status->message);
    }
    return $response->data;
  }

  /**
   * Generic call handler that accepts method calls to Medusa
   * and translates them into REST methods to execute
   */
  public function __call($name, $arguments) {
    $this->method_stack[] = $name;
    $this->method = 'wrms.'.implode('.', $this->method_stack);
    $this->method_stack = array();

    $this->parameters($arguments);

    return $this->execute()->response();
  }

  /**
   * The partner to __call() that populates the method call
   * path as we go
   */
  public function __get($name) {
    $this->method_stack[] = $name;
    return $this;
  }

  /**
   * Debug dump function.
   *
   * Don't use this for production.
   */
  public function dumpResponse() {
    throw new WRMSException($this->response, 500, 'DEBUG');
  }

}

class WRMSException extends Exception {
  public $status;
  public $code;
  public $message;

  public function __construct($message, $code = null, $status = '') {
    $this->message = $message;
    $this->code = $code;
    $this->status = $status;
  }

  public function __toString() {
    return $this->message.($this->code ? '   ('.$this->code.') ' : '   ').$this->status;
  }
}

