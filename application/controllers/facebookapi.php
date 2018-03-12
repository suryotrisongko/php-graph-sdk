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