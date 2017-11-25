<?php

use AmazonMWS\Reports;

include "../TestConfig.php";

$products = new Reports($sellerId,$awsKey,$secretKey,"IT");
$result  = $products->GetReportRequestList();
echo "\n<textarea rows='20' cols='200'>".strval($result)."</textarea>\n\n";
print_r($result->getResultAsObject());
