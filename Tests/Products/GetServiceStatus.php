<?php

use AmazonMWS\Products;

include "../TestConfig.php";

$products = new Products($sellerId,$awsKey,$secretKey);
$result  = $products->GetServiceStatus();
echo "\n<textarea rows='10' cols='100'>".$result."</textarea>\n\n";
