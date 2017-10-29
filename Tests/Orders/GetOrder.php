<?php

use AmazonMWS\Orders;

include "../TestConfig.php";

$orders = new Orders($sellerId, $awsKey, $secretKey, "IT");

//parameter can be array also or just a string
$result  = $orders->GetOrder( "407-874512-895");

echo "\n<textarea rows='20' cols='200'>".$result."</textarea>\n\n";