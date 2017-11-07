<?php

namespace AmazonMWS;
use AmazonMWS\core\AmazonMWSCore;

class Orders extends AmazonMWSCore
{
    function __construct($sellerId = "", $awsKey = "", $secretKey = "", $endpoint="EU")
    {
        parent::__construct($sellerId, $awsKey, $secretKey);
        $this->setAPIName("Orders");
        $this->setAPIVersion("2013-09-01");
        $this->setSignatureMethod();
        $this->setTimestamp();
        $this->setSignatureVersion(2);
        $this->setEndPoint($endpoint);
        $this->setAPIRequestType("POST");
    }

    private function __dateReplacement($str)
    {
        //$str = str_replace("%3A",":",$str);
        return rawurldecode($str);
    }

    private function __fixDateTime($date, $format=DATE_ISO8601)
    {
        $timeDate = strtotime($date);
        $date = empty($timeDate)?$this->__dateReplacement($date):$timeDate;
        $date = date($format, $date);
        return $this->_getTimeStamp($date);
    }

    private function __setMarketPlaceID($marketplaceId)
    {
        if(is_string($marketplaceId))
        {
            if(strlen($marketplaceId) == 2)
                $marketplaceId = $this->_getMarketPlaceID($marketplaceId);

        }elseif(is_array($marketplaceId)){
            $market = [];
            foreach ($marketplaceId as $key => $id){

                if( is_array($id))
                    continue; // i can't do anything >:((

                if(strlen($id) === 2){
                    $market[] = $this->_getMarketPlaceID($id);
                }else{
                    $market[] = $id;
                }
            }
            $marketplaceId = $market;
        }
        return $marketplaceId;
    }

    protected function _setOrderStatus($status, $listName="OrderStatus.Status.")
    {
        $statuses = ["Pending","Unshipped","PartiallyShipped","Shipped","Canceled","Unfulfillable","PendingAvailability"];

        if(is_array($status)){
            foreach ($status as $s) $this->_setOrderStatus($s,$listName);
            return $this;
        }

        if(in_array(strtolower($status), array_map("strtolower", $statuses)))
        {
            $this->_setList($status,$listName);
        }else{
            $this->log("It seems order status in not correct but still added.", "OrderStatus", "Warning");
            $this->_setList($status,$listName);
        }

        return $this;

    }

    protected function _createAfterDate($date)
    {
        $date = $this->__fixDateTime($date);
        $this->setOperationField("CreatedAfter", $date);
        return $this;
    }

    protected function _createdBeforeDate($date)
    {
        $date = $this->__fixDateTime($date);
        $this->setOperationField("CreatedBefore", $date);
        return $this;
    }

    protected function _LastUpdatedAfter($date)
    {
        $date = $this->__fixDateTime($date);
        $this->setOperationField("LastUpdatedAfter", $date);
        return $this;
    }

    protected function _LastUpdatedBefore($date)
    {
        $date = $this->__fixDateTime($date);
        $this->setOperationField("LastUpdatedAfter", $date);
        return $this;
    }

    protected function _setFulfillmentChannel($ch, $listName="FulfillmentChannel.Channel.")
    {
        $channel = ["AFN", "MFN"];
        if(in_array(strtolower($ch), array_map("strtolower", $channel)))
        {
            $this->_setList($ch, $listName);
            return $this;
        }
        return $this;
    }

    protected function _setPaymentMethod($method, $listName="PaymentMethod.Method.")
    {
        $this->_setList($method, $listName);
        return $this;
    }

    protected function _setTFMShipmentStatus($method, $listName="TFMShipmentStatus.Status.")
    {
        $this->_setList($method, $listName);
        return $this;
    }

    public function GetServiceStatus()
    {
        $this->setAPIAction("GetServiceStatus");
        $this->_invoke();
        return $this;
    }

    public function ListOrders($marketplaceId, $CreatedAfter="", $CreatedBefore="",
                               $LastUpdatedAfter="",$LastUpdatedBefore="",$OrderStatus="",
                               $FulfillmentChannel="",$SellerOrderId="",$BuyerEmail="",
                               $PaymentMethod="", $TFMShipmentStatus="", $MaxResultsPerPage="")
    {
        $marketplaceId = $this->__setMarketPlaceID($marketplaceId);

        $this->setAPIAction("ListOrders");
        $this->_setMarketPlaceId($marketplaceId, true, "IT", "MarketplaceId.Id.");

        if(!empty($CreatedAfter)) $this->_createAfterDate($CreatedAfter);
        if(!empty($CreatedBefore)) $this->_createdBeforeDate($CreatedBefore);
        if(!empty($LastUpdatedAfter)) $this->_LastUpdatedAfter($LastUpdatedAfter);
        if(!empty($LastUpdatedBefore)) $this->_LastUpdatedBefore($LastUpdatedBefore);
        if(!empty($OrderStatus)) $this->_setOrderStatus($OrderStatus);
        if(!empty($FulfillmentChannel)) $this->_setFulfillmentChannel($FulfillmentChannel);
        if(!empty($SellerOrderId)) $this->setOperationField("SellerOrderId", $SellerOrderId);
        if(!empty($BuyerEmail)) $this->setOperationField("BuyerEmail", $BuyerEmail);
        if(!empty($PaymentMethod)) $this->setOperationField("PaymentMethod", $PaymentMethod);
        if(!empty($TFMShipmentStatus)) $this->setOperationField("TFMShipmentStatus", $TFMShipmentStatus);
        if(!empty($MaxResultsPerPage)) $this->setOperationField("MaxResultsPerPage", $MaxResultsPerPage);

        $this->_invoke();
        return $this;
    }

    public function ListOrdersByNextToken($token)
    {
        $this->setAPIAction("ListOrdersByNextToken");
        $this->setOperationField("NextToken", $token);
        $this->_invoke();
        return $this;
    }

    public function GetOrder($amazonOrderID)
    {
        $this->setAPIAction("GetOrder");
        $this->_setList($amazonOrderID,"AmazonOrderId.Id.");
        $this->_invoke();
        return $this;
    }

    public function ListOrderItems($amazonOrderID)
    {
        $this->setAPIAction("ListOrderItems");
        $this->setOperationField("AmazonOrderId", $amazonOrderID);
        $this->_invoke();
        return $this;
    }

    public function ListOrderItemsByNextToken($token)
    {
        $this->setAPIAction("ListOrderItemsByNextToken");
        $this->setOperationField("NextToken", $token);
        $this->_invoke();
        return $this;
    }

}