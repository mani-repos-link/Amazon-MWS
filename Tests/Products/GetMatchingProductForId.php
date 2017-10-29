<?php

use AmazonMWS\Products;

include "../TestConfig.php";

$products = new Products($sellerId,$awsKey,$secretKey,"IT");
$result  = $products->GetMatchingProductForId("ASIN","B01K7VLD52", "IT");
echo "\n<textarea rows='20' cols='200'>".$result."</textarea>\n\n";