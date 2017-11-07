<?php

use AmazonMWS\Feed;

include "../TestConfig.php";

$orders = new Feed($sellerId, $awsKey, $secretKey, "IT");

//parameter can be array also or just a string
$result  = $orders->GetFeedSubmissionResult("66787017465");

echo "\n<textarea rows='20' cols='200'>".strval($result)."</textarea>\n\n";
pp($result->getResultAsObject());