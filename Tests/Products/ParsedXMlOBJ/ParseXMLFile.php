<?php

use AmazonMWS\Core\AmazonResponseCore;


include "../../TestConfig.php";


$file =  __DIR__."/../../../Results/".strtolower("GetLowestPricedOffersForASIN").".xml";
$xml = new AmazonResponseCore($file);
$xml->parseXML($file);
$result = $xml->getParsedXML();
printResult($result);

$file =  __DIR__."/../../../Results/".strtolower("getcompetitivepricingforsku").".xml";
$result = $xml->parseXML($file);
$result = $xml->getParsedXML();
printResult($result);

$file =  __DIR__."/../../../Results/".strtolower("getmatchingproductforid").".xml";
$result = $xml->parseXML($file);
$result = $xml->getParsedXML();
printResult($result);

$file =  __DIR__."/../../../Results/".strtolower("getlowestofferlistingsforsku").".xml";
$result = $xml->parseXML($file);
$result = $xml->getParsedXML();
printResult($result);

$file =  __DIR__."/../../../Results/".strtolower("getmatchingproductforid").".xml";
$result = $xml->parseXML($file);
$result = $xml->getParsedXML();
printResult($result);


echo  b.hr."LOGs".hr;
print_r($xml->getLog());

function printResult($result="")
{
    echo b."Result:".hr.b;
    print_r($result);
    echo hr.b.b;
}
