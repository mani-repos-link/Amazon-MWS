<?php

use AmazonMWS\Feed;

include "../TestConfig.php";

$orders = new Feed($sellerId, $awsKey, $secretKey, "IT");

//parameter can be array also or just a string
$result  = $orders->GetFeedSubmissionCount();

echo "\n<textarea rows='20' cols='200'>".$result."</textarea>\n\n";
