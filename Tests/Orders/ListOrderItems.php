<?php

use AmazonMWS\Orders;

include "../TestConfig.php";

$orders = new Orders($sellerId, $awsKey, $secretKey, "IT");

$result  = $orders->ListOrderItems( "407-874512-895");

echo "\n<textarea rows='20' cols='200'>".strval($result)."</textarea>\n\n";
pp($result->getResultAsObject());
