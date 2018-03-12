<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Facebookapilib {

	public function __construct()
	{
            $this->CI = & get_instance();
		
            if (file_exists('Facebook\autoload.php')) {
                    require('Facebook\autoload.php');
            } else {
                    require('Facebook/autoload.php');
            }

            $this->CI->config->load('facebook');
	}
        
	// use this to call Facebook API
	public function call($method=null, $url=null, $param=null, $data=null, $content=false)
	{
		$fb = new Facebook\Facebook([
		  'app_id' => $this->CI->config->item('app_id'),
		  'app_secret' => $this->CI->config->item('app_secret'),
		  'default_graph_version' => $this->CI->config->item('default_graph_version'),
		  ]);

		$accesstoken = $this->CI->config->item('accesstoken');
		
		try {			
                    if ($method == 'post') {
                        $response = $fb->post($url.$param , $data, $accesstoken);	
                        $content = $response->getBody();
                        
                    } else {
                        $response = $fb->get($url.$param , $accesstoken);		
                        $results = $response->getGraphList();
                        //var_dump($results); exit;
                        foreach ($results as $result) {   

                                $response2 = $fb->get('/'.$result['id'].'?fields=from', $accesstoken);
                                $results2 = $response2->getGraphObject();
                                foreach ($results2 as $result2) {

                                        $content .= $this->process($result, $result2);  
                                        break;
                                }
                        }
                    } 
		  
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
		  return 'Graph returned an error: ' . $e->getMessage();
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
		  return 'Facebook SDK returned an error: ' . $e->getMessage();
		}
		return $content;
	}
	
	// example what to do the data retrieved from Facebook API Call
	public function process($data=null, $data2=null)
	{
		if( isset($data['id'])  && !$this->exists($data) )
		{
			if($this->save($data, $data2))
			{
				return "Visitor Post " . $data['id'] . " Saved!   " . $data2['name'] . " - " . $data['message'] . "<br/><br/>"; 

			}
		}
	}
	
	// example how to save data to mysql database
	function save($data=null,$data2=null,$result=false)
	{
		if( isset($data['id'] ) )
		{		
			$input=array( 'status' =>  '1', 'prioritas' =>  '3', 'object_id' =>  $data['id'], 'waktu' =>  $data['created_time']->format('Y-m-d H:i:s'), 'nama' =>  $data2['name'], 'isi' =>  $data['message'], 'via_facebook' =>  '1' );			
			$result=$this->CI->db->insert('aduan',$input);
                        $id_aduan = $this->CI->db->insert_id();
                        
			$input2=array( 'waktu_detail' =>  $data['created_time']->format('Y-m-d H:i:s'), 'isi_detail' =>  $data['message'], 'aduan' =>  $id_aduan );			
			$result=$this->CI->db->insert('detail_aduan',$input2);
                        
			$input3=array( 'waktu_status_aduan' =>  $data['created_time']->format('Y-m-d H:i:s'), 'status_id_status' =>  '1', 'aduan_id_aduan' =>  $id_aduan );			
			$result=$this->CI->db->insert('status_aduan',$input3);
		}
		return $result;
	}

	// example checking whether the data have already saved in the database
	function exists($data=null,$result=false)
	{
		if( isset($data['id'] )) 
		{		
			$this->CI->db->where('object_id',$data['id']);
			$query=$this->CI->db->get('aduan',1,0);
			if($query->num_rows()>0)
			{
				$result=true;
			}
		}
		return $result;
	}
	
	// example for other operations that you may specify as needed, such as retrieving the last object id        
	function lastobjectid($result=false)
	{		
            $this->CI->db->where('via_facebook','1');
            $this->CI->db->order_by("waktu", "desc"); 
            $query=$this->CI->db->get('aduan',1,0);
            if($query->num_rows()>0)
            {
                foreach ($query->result() as $data)
                    $result=$data->waktu;
            }
            return $result;
	}

}

/* End of file Facebookapilib.php */