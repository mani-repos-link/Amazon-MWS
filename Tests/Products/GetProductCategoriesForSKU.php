<?php

use AmazonMWS\Products;

include "../TestConfig.php";
$products = new Products($sellerId,$awsKey,$secretKey,"EU");
$result  = $products->GetProductCategoriesForSKU("38511", "IT");

echo "\n<textarea rows='20' cols='90%'>".$result."</textarea>\n\n";
