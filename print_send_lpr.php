<?php
/*
 * Class Print_send_lpr - Sends a file to be printed remotely.
 * File must be either preformatted or plain ASCII until some
 * printer driver functionality has been incorpoated.
 */
 
class Print_send_lpr extends Print_send{

    private $host = "192.168.1.1";
    private $port = "6001";
    private $timeout = "5";
    private $err_no;
    private $err_str;

    public function __construct() {
        parent::__construct();
    }
   
    public function set_port($port){
        $this->port = $port;
		$this->debug .= "Port is ".$this->port."\n";
    }
   
    public function set_host($host){
        $this->host = $host;
		$this->debug .= "Host is ".$this->host."\n";
    }
    
    public function set_timeout($timeout){
        $this->timeout = $timeout;
    }
    
    public function get_err_no(){
        return $this->err_no;
    }
    
    public function get_err_str(){
        return $this->err_str;
    }    
   
    public function print_job($queue){        
        //Private static function prints waiting jobs on the queue.
        $this->print_waiting($queue);

        //Open a new connection to send the control file and data.
        $stream = stream_socket_client("tcp://".$this->host.":".$this->port, $this->err_no, $this->err_str, $this->timeout);
		
        if(!$stream){
            return $this->err_no." (".$this->err_str.")";
        } else {                        
            $job = self::get_job_id(); // Get a new id for this job
            
            //Set printer to receive file
            fwrite($stream, chr(2).$queue."\n");
            $this->debug .= "Confirmation of receive cmd: ".ord(fread($stream, 1))."\n";
            
            //Send Control file.
            (isset($_SERVER['SERVER_NAME'])) ? $server = $_SERVER['SERVER_NAME'] : $server = "me";//Might be CLI and not have _SERVER
            $ctrl = "H".$server."\nPphp\nfdfA".$job.$server."\n";
            fwrite($stream, chr(2).strlen($ctrl)." cfA".$job.$server."\n");
            $this->debug .= "Confirmation of sending of control file cmd: ".ord(fread($stream, 1))."\n";
           
            fwrite($stream, $ctrl.chr(0)); //Write null to indicate end of stream
            $this->debug .= "Confirmation of sending of control file itself: ".ord(fread($stream, 1))."\n";
                       
            if (is_readable($this->data)){
                // It's a filename, rather than just some ascii text that needs printing.  Open and stream.
                if (strstr(strtolower($_ENV["OS"]), "windows")){
                    $this->debug .= "Operating system is Windows\n";
                    $data = fopen($this->data, "rb"); // Force binary in Windows.
                } else {
                    $this->debug .= "Operating system is not Windows\n";
                    $data = fopen($this->data, "r");
                }
                
                fwrite($stream, chr(3).filesize($this->data)." dfA".$job.$server."\n");
                $this->debug .= "Confirmation of sending receive data cmd: ".ord(fread($stream, 1))."\n";
                
                while(!feof($data)){
                    fwrite($stream, fread($data, 8192));                     
                }
				
                fwrite($stream, chr(0)); // Write null to indicate end of stream
                $this->debug .= "Confirmation of sending data: ".ord(fread($stream, 1))."\n"; 
                
                fclose($data);
            } else {
                //Send data string
                fwrite($stream, chr(3).strlen($this->data)." dfA".$job.$server."\n");           
                $this->debug .= "Confirmation of sending receive data cmd:".ord(fread($stream, 1))."\n";

                fwrite($stream, $this->data.chr(0)); //Write null to indicate end of stream
                $this->debug .= "Confirmation of sending data:".ord(fread($stream, 1))."\n";
            }
        }
		
    }
    
    //Placeholder to handle filters - this will call methods in printer driver classes 
    //instantiated by the printer_factory method..
    public function interpret(){}    
    
    private function get_job_id(){
    	return "001";
        
        //You should implement your own internal storage and incrementing of job numbers here.  
        //This might be a database value, or some file on the file system.
    }
    
    private function print_waiting($queue){
        $stream = stream_socket_client("tcp://".$this->host.":".$this->port, $this->err_no, $this->err_str, $this->timeout);
		
        if (!$stream){
            return $this->err_no." (".$this->err_str.")";
        } else {
            //Print any waiting jobs
            fwrite($stream, $queue."\n");
            $this->debug .= "Confirmation of print waiting jobs cmd:";
            
			//while(!feof($stream)){
               //$this->debug .= ord(fread($stream, 1));
            //}
			
			fclose($stream);
            $this->debug .= "\n";
        }
    }

}