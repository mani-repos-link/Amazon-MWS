<?php

use AmazonMWS\Feed;

include "../TestConfig.php";

$orders = new Feed($sellerId, $awsKey, $secretKey, "IT");


$feed = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<AmazonEnvelope xsi:noNamespaceSchemaLocation=\"amzn-envelope.xsd\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\">
    <Header>
        <DocumentVersion>1.01</DocumentVersion>
        <MerchantIdentifier>12345678974</MerchantIdentifier>
    </Header>
    <MessageType>OrderFulfillment</MessageType>
    <Message>
        <MessageID>1</MessageID>
        <OperationType>Update</OperationType>
        <OrderFulfillment>
            <AmazonOrderID>002-3275191-2204215</AmazonOrderID>
            <FulfillmentDate>2009-07-22T23:59:59-07:00</FulfillmentDate>
            <FulfillmentData>
                <CarrierName>Contact Us for Details</CarrierName>
                <ShippingMethod>Standard</ShippingMethod>
            </FulfillmentData>
            <Item>
                <AmazonOrderItemCode>42197908407194</AmazonOrderItemCode>
                <Quantity>1</Quantity>
            </Item>
        </OrderFulfillment>
    </Message>
</AmazonEnvelope>";


//parameter can be array also or just a string
$result  = $orders->SubmitFeed( $feed, "_POST_FLAT_FILE_ORDER_ACKNOWLEDGEMENT_DATA_", "IT", false);

echo "\n<textarea rows='20' cols='200'>".strval($result)."</textarea>\n\n";
pp($result->getResultAsObject());