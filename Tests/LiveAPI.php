<?php

use AmazonMWS\Products;

include "./TestConfig.php";

$amazon = new Products($sellerId,$awsKey,$secretKey);
$result = $amazon->GetServiceStatus();

