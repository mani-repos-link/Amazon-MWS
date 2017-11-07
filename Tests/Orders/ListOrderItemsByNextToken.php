<?php

use AmazonMWS\Orders;

include "../TestConfig.php";

$orders = new Orders($sellerId, $awsKey, $secretKey, "IT");

$result  = $orders->ListOrderItemsByNextToken( "5g6wergv2xva34g64g123f13zsd54caf1321vz8c4v84fa32sd1d3v5as8f4vsdc12v3<5v4r31f32szv4sd8f42s1vc5dcas4ca.....");

echo "\n<textarea rows='20' cols='200'>".strval($result)."</textarea>\n\n";
pp($result->getResultAsObject());
