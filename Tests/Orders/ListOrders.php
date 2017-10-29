<?php

use AmazonMWS\Orders;

include "../TestConfig.php";

$orders = new Orders($sellerId, $awsKey, $secretKey, "IT");

$result  = $orders->ListOrders("IT", "-2 days");

echo "\n<textarea rows='20' cols='200'>".$result."</textarea>\n\n";