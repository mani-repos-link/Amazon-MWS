<?php

use AmazonMWS\Reports;

include "../TestConfig.php";

$report = new Reports($sellerId,$awsKey,$secretKey,"IT");

$r = new Reports($sellerId,$awsKey,$secretKey,"IT");
/*
//$res = $r->RequestReport("_GET_ORDERS_DATA_");
$res = $r->RequestReport("_GET_FLAT_FILE_ORDERS_DATA_");
$obj = $res->getResultAsObject();
print_r($obj);
print_r($obj["ReportRequestId"]);*/


//$result  = $report->GetReportRequestList("","","",$obj["ReportRequestId"]);
$result  = $report->GetReportRequestList("","","","68617017495");
echo "\n<textarea rows='20' cols='200'>".strval($result)."</textarea>\n\n";
print_r($result->getResultAsObject());