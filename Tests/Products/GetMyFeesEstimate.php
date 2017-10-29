<?php
use AmazonMWS\Products;

include "../TestConfig.php";
$products = new Products($sellerId,$awsKey,$secretKey,"EU");
$result  = $products->GetMyFeesEstimate("IT","ASIN","B01K7VLD52","false", "request1", "250","EUR","10.90", "EUR");
echo "\n<textarea rows='20' cols='90%'>".$result."</textarea>\n\n";
