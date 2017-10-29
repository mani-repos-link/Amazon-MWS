<?php

use AmazonMWS\Core\AmazonResponseCore;

include "../../TestConfig.php";

$file =  __DIR__."/../../../Results/".strtolower("GetMatchingProductForId").".xml";
$xml = new AmazonResponseCore($file);
$result = $xml->GetMatchingProductForId($file);
echo b."Result: ".hr.b;
print_r($result);
echo hr.b.b;

echo  b.hr."LOGs".hr;
print_r($xml->getLog());
