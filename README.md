# Codeigniter Facebook Graph SDK v5.6.2 Library, based on Facebook SDK for PHP (v5)


## Installation

1. put the files into the correct folder (e.g. controller, library, etc)

2. modify the "application\config\facebook.php", set token for Facebook Graph SDK 

3. modify the "application\libraries\Facebookapilib.php", to support operations that you need

4. an example controller has been provided, contains get & post API call


## Example Controller

<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Example controller 
*/
class Facebookapi extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // set maximum execution time to infinity
        set_time_limit(0);
        $this->db = $this->load->database('default', true);
        $this->load->library('facebookapilib');
    }
	

    // example get operation
    public function index()
    {		

        $since = $this->facebookapilib->lastobjectid(); 
        $param = null;
        if($since) {
                $param= '?since=' . strtotime($since);
        }
        print_r( $this->facebookapilib->call('get', '/suarawargaindonesia/visitor_posts', $param) );
    }                                        
                     

    // example POST operation to post comments
    public function post()
    {		   
		$object_id = 'object_id';
		
		$method='post';
		$url = '/'.$object_id.'/comments' ;
		$data= array("id" => $object_id , 
			"message" => 'message comment' );
		
		$respond = $this->facebookapilib->call($method, $url, null, $data);                            
		if ($respond == '' OR (is_string($respond) && strpos($respond, 'error') !== false)) {
			var_dump($respond); 
			exit;
		}
	}
			
}
?>