<?php

use AmazonMWS\Core\AmazonResponseCore;

include "../../TestConfig.php";
$file =  __DIR__."/../../../Results/getservicestatus.xml";
$xml = new AmazonResponseCore($file);
$result = $xml->GetServiceStatus($file, true);
echo b."Result(1):".hr.b;
print_r($result);
echo hr.b.b;

$result = $xml->GetServiceStatus($file, false);
echo b."Result(2):".hr;
print_r($result);
echo hr.b.b.b.b;




echo  b.hr."LOGs".hr;
print_r($xml->getLog());
