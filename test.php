<?php  
error_reporting(E_ALL);

include_once("print_send.php");
include_once("print_send_lpr.php");

$text = "Hello world!"; // zpl/epl/ipl/gpl formatted label view

$lpr = new Print_send_lpr();
$lpr->set_port("6001");
$lpr->set_host("192.168.1.1");
$lpr->set_data($text);
$lpr->print_job("Queue 1");
$lpr->get_debug();