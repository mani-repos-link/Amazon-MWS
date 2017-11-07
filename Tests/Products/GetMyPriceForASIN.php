<?php

use AmazonMWS\Products;

include "../TestConfig.php";
$products = new Products($sellerId,$awsKey,$secretKey,"EU");
$result  = $products->GetMyPriceForASIN("B01K7VLD52","", "IT");

echo "\n<textarea rows='20' cols='200'>".strval($result)."</textarea>\n\n";
pp($result->getResultAsObject());
