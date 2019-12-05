<?php
/*
 * Class Print_send - Abstract printing class.
 * Models the actual sending of data to the printer.
 */
 
abstract class Print_send{

    protected $data;    
    protected $debug;

    abstract protected function print_job($queue);
    
    abstract protected function interpret();
    
    public function __construct(){}

    public function set_data($data){ 
        //This can be a filename or some ASCII.  A file check should be made in derived classes.
        $this->data = $data;
        $this->debug .= "Data set\n";
    }
    
    //Future functionality (?) Could create filters with this for different languages like PCL or ESC/P
    public static function printer_factory($type){ 
        if (include_once 'drivers/' . $type . '.php') {
            return new $type; // Return a new instance of a printer driver of type $type.
        } else {
            throw new Exception('Driver not found: $type');
        }        
    }

    public function get_debug(){
		return $this->debug;
    }

}