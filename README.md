# digicode-web-print-sdk
Web print SDK from DigiCode Kft. The code makes easier the web print from server side to a local printer. It works mainly for label printers from [Zebra](https://www.digicode.hu/zebra-m1/cimke-vonalkod-nyomtato-c1), [Honeywell](https://www.digicode.hu/honeywell-m4/cimke-vonalkod-nyomtato-c1), [Intermec](https://www.digicode.hu/intermec-m3/cimke-vonalkod-nyomtato-c1), [TSC](https://www.digicode.hu/tsc-m20/cimke-vonalkod-nyomtato-c1) but it can be used with classic printers, like HP, Konica, etc..

## Usage

First of all you must check the available methods for printing, mostly the set_ functions. The driver files not required for the direct socket connection, but you must send the manufacturer created script language to the printer. For example: with Zebra printer you must user ZPL program language.

> If your website out of localhost or the printer is not published to the web, you must set firewall **port forwarding** in router. 

```php
error_reporting(E_ALL);

include_once("print_send.php");
include_once("print_send_lpr.php");

$text = "Hello world!"; // zpl/epl/ipl formatted label view

$lpr = new Print_send_lpr();
$lpr->set_port("6001");
$lpr->set_host("192.168.1.1");
$lpr->set_data($text);
$lpr->print_job("Queue 1");
$lpr->get_debug();
```
> EPL the oldest label print language, its a acronym for Eltron Program Language, the company bought by Zebra. The ZPL acronym for Zebra Program Language, the current version is [ZPL v.2.0](https://files.digicode.hu/zebra-zpl-zbi2-programming-manual-en.pdf). [IPL](https://files.digicode.hu/intermec-ipl-programming-manual-en.pdf) mean Intermec Program Language.

## Label printers

If you need any device for testing the code feel free to [contact us](https://www.digicode.hu/kapcsolat). You can choose any [barcode label printer](https://www.digicode.hu/cimke-vonalkod-nyomtato-c1) on our website.
