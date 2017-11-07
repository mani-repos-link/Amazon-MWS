<?php

use AmazonMWS\Products;

include "../TestConfig.php";

$products = new Products($sellerId,$awsKey,$secretKey,"IT");
$result  = $products->ListMatchingProducts("Computer", "IT");

echo "\n<textarea rows='20' cols='200'>".strval($result)."</textarea>\n\n";
pp($result->getResultAsObject());
