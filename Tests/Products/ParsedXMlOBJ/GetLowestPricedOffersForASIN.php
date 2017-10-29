<?php

use AmazonMWS\Core\AmazonResponseCore;

include "../../TestConfig.php";
$file =  __DIR__."/../../../Results/".strtolower("GetLowestPricedOffersForASIN").".xml";
$xml = new AmazonResponseCore($file);
$result = $xml->GetLowestPricedOffersForASIN();
echo b."Result:".hr.b;
print_r($result);
echo hr.b.b;

echo  b.hr."LOGs".hr;
print_r($xml->getLog());
