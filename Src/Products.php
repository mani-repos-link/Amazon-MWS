<?php

namespace AmazonMWS;

use AmazonMWS\Core\AmazonMWSCore;

class Products extends AmazonMWSCore
{

    function __construct($sellerId = "", $awsKey = "", $secretKey = "", $endpoint="EU")
    {
        parent::__construct($sellerId, $awsKey, $secretKey);
        $this->setAPIName("Products");
        $this->setAPIVersion("2011-10-01");
        $this->setSignatureMethod();
        $this->setTimestamp();
        $this->setSignatureVersion(2);
        $this->setEndPoint($endpoint);
        $this->setAPIRequestType("POST");
    }

    private function _setAsin($asin, $setAsList=false)
    {
        if(is_array($asin) || $setAsList===true){
            $this->_setList($asin, "ASINList.ASIN.");
            return $this;
        }

        $this->setOperationField("ASIN", $asin);
        return $this;
    }

    private function _setIdType($idType)
    {
        $this->setOperationField("IdType", $idType);
        return $this;
    }

    private function _setIdList($idListValues)
    {
        $this->_setList($idListValues,"IdList.Id.");
        return $this;
    }

    private function _setSellerSKU($sku, $setAsList=false)
    {
        if(is_array($sku) || $setAsList===true){
            $this->_setList($sku, "SellerSKUList.SellerSKU.");
            return $this;
        }

        $this->setOperationField("SellerSKU", $sku);
        return $this;
    }

    private function _setFeesEstimateRequestMarketplaceId($marketPlaceId)
    {
        $this->_setList($marketPlaceId,"FeesEstimateRequestList.FeesEstimateRequest.", ".MarketplaceId");
        return $this;
    }

    private function _setFeesEstimateRequestIdType($idType)
    {
        $this->_setList($idType,"FeesEstimateRequestList.FeesEstimateRequest.", ".IdType");
        return $this;
    }

    private function _setFeesEstimateRequestIdValue($idValue)
    {
        $this->_setList($idValue,"FeesEstimateRequestList.FeesEstimateRequest.", ".IdValue");
        return $this;
    }

    private function _setFeesEstimateRequestIsAmazonFulfilled($isAmazonFulfilled)
    {
        echo "\n$isAmazonFulfilled\n";
        if(!is_bool($isAmazonFulfilled))
        {
            if(strtolower($isAmazonFulfilled) !== "true" && strtolower($isAmazonFulfilled ) !== "false")
                $this->log("_setFeesEstimateRequestIsAmazonFulfilled method accepts only boolean value. ".
                    gettype($isAmazonFulfilled)." is given. (".
                    json_encode($isAmazonFulfilled).")","InvalidValue","Warning");
        }

        if($isAmazonFulfilled === true) $isAmazonFulfilled = "true";
        if($isAmazonFulfilled === false) $isAmazonFulfilled = "false";

        $this->_setList($isAmazonFulfilled,"FeesEstimateRequestList.FeesEstimateRequest.", ".IsAmazonFulfilled");
        return $this;
    }

    private function _setFeesEstimateRequestIdentifier($Identifier)
    {
        $this->_setList($Identifier,"FeesEstimateRequestList.FeesEstimateRequest.", ".Identifier");
        return $this;
    }

    private function _setFeesEstimateRequestPriceToEstimateFeesListingPriceAmount($val)
    {
        $this->_setList($val,"FeesEstimateRequestList.FeesEstimateRequest.", ".PriceToEstimateFees.ListingPrice.Amount");
        return $this;
    }

    private function _setFeesEstimateRequestPriceToEstimateFeesListingPriceCurrencyCode($val)
    {
        $this->_setList($val,"FeesEstimateRequestList.FeesEstimateRequest.", ".PriceToEstimateFees.ListingPrice.CurrencyCode");
        return $this;
    }

    private function _setFeesEstimateRequestPriceToEstimateFeesShippingAmount($val)
    {
        $this->_setList($val,"FeesEstimateRequestList.FeesEstimateRequest.", ".PriceToEstimateFees.Shipping.Amount");
        return $this;
    }

    private function _setFeesEstimateRequestPriceToEstimateFeesShippingCurrencyCode($val)
    {
        $this->_setList($val,"FeesEstimateRequestList.FeesEstimateRequest.", ".PriceToEstimateFees.Shipping.CurrencyCode");
        return $this;
    }

    private function _setFeesEstimateRequestPriceToEstimateFeesPointsPointsNumber($val)
    {
        $this->_setList($val,"FeesEstimateRequestList.FeesEstimateRequest.", ".PriceToEstimateFees.Points.PointsNumber");
        return $this;
    }

    private function _setFeesEstimateRequestPriceToEstimateFeesPointsPointsMonetaryValueAmount($val)
    {
        $this->_setList($val,"FeesEstimateRequestList.FeesEstimateRequest.", ".PriceToEstimateFees.Points.PointsMonetaryValue.Amount");
        return $this;
    }

    private function _setFeesEstimateRequestPriceToEstimateFeesPointsPointsMonetaryValueCurrencyCode($val)
    {
        $this->_setList($val,"FeesEstimateRequestList.FeesEstimateRequest.", ".PriceToEstimateFees.Points.PointsMonetaryValue.CurrencyCode");
        return $this;
    }

    public function GetServiceStatus()
    {
        $this->setAPIAction("GetServiceStatus");
        $result = $this->_invoke();
        return $this;
    }

    public function ListMatchingProducts($query, $marketPlaceId="")
    {
        $this->setAPIAction("ListMatchingProducts");
        $this->setOperationField("Query", $query);
        $this->_setMarketPlaceId($marketPlaceId);
        $result = $this->_invoke();
        return $this;
    }

    public function GetMatchingProduct($asin, $marketPlaceId="")
    {
        $this->setAPIAction("GetMatchingProduct");
        $this->_setAsin($asin, true);
        $this->_setMarketPlaceId($marketPlaceId);
        $this->_invoke();
        return $this;
    }
    
    public function GetMatchingProductForId($idType, $idValue, $marketPlaceId="")
    {
        $this->setAPIAction("GetMatchingProductForId");
        $this->_setIdType($idType);
        $this->_setIdList($idValue);
        $this->_setMarketPlaceId($marketPlaceId);
        $this->_invoke();
        return $this;
    }

    public function GetCompetitivePricingForSKU($SellerSKU, $marketPlaceId="")
    {
        $this->setAPIAction("GetCompetitivePricingForSKU");
        $this->_setSellerSKU($SellerSKU,true);
        $this->_setMarketPlaceId($marketPlaceId);
        $this->_invoke();
        return $this;
    }

    public function GetCompetitivePricingForASIN($asin, $marketPlaceId="")
    {
        $this->setAPIAction("GetCompetitivePricingForASIN");
        $this->_setAsin($asin,true);
        $this->_setMarketPlaceId($marketPlaceId);
        $this->_invoke();
        return $this;
    }

    /**
     * Why all operation are not equals ?
     * I waste 2 days to solve jboss processing error!! >:(
     *
     * Solution :
     *          Other operation can accept query with urls. host/api/version?query
     *          But this type of op doesn't accept query in params. So we need to send query string by using curl_postfields
     * @param $SellerSKU
     * @param $ItemCondition
     * @param string $marketPlaceId
     * @return bool|string
     */
    public function GetLowestPricedOffersForSKU($SellerSKU, $ItemCondition, $marketPlaceId="")
    {
        $this->setAPIAction("GetLowestPricedOffersForSKU");
        $this->_setSellerSKU($SellerSKU);
        $this->_setMarketPlaceId($marketPlaceId);
        $this->setOperationField("ItemCondition", $ItemCondition);
        $this->_invoke(true);
        return $this;
    }

    public function GetLowestPricedOffersForASIN($asin, $ItemCondition, $marketPlaceId="")
    {
        $this->setAPIAction("GetLowestPricedOffersForASIN");
        $this->_setAsin($asin);
        $this->_setMarketPlaceId($marketPlaceId);
        $this->setOperationField("ItemCondition", $ItemCondition);
        $this->_invoke(true);
        return $this;
    }

    /**
     * @param array|string $SellerSKU
     * @param string $ItemCondition
     * @param bool $excludeMe
     * @param string $marketPlaceId
     * @return bool|string
     */
    public function GetLowestOfferListingsForSKU($SellerSKU, $ItemCondition="", $excludeMe=false, $marketPlaceId="")
    {
        $this->setAPIAction("GetLowestOfferListingsForSKU");
        $this->_setSellerSKU($SellerSKU, true);
        $this->_setMarketPlaceId($marketPlaceId);
        if(!empty($ItemCondition))  $this->setOperationField("ItemCondition", $ItemCondition);
        if(!empty($excludeMe))  $this->setOperationField("ExcludeMe", $excludeMe);

        $this->_invoke(true);
        return $this;
    }

    public function GetLowestOfferListingsForASIN($asin, $ItemCondition="", $excludeMe=false, $marketPlaceId="")
    {
        $this->setAPIAction("GetLowestOfferListingsForASIN");
        $this->_setAsin($asin, true);
        $this->_setMarketPlaceId($marketPlaceId);

        if(!empty($ItemCondition))  $this->setOperationField("ItemCondition", $ItemCondition);
        if(!empty($excludeMe))  $this->setOperationField("ExcludeMe", $excludeMe);


        $this->_invoke(true);
        return $this;
    }

    public function GetMyFeesEstimate($marketplaceId, $IdType, $IdValue, $IsAmazonFulfilled, $Identifier="request1",
                                      $ListingPriceAmount, $ListingPriceCurrencyCode="EUR", $ShippingAmount, $ShippingCurrencyCode="EUR",
                                      $PointsPointsNumber=0, $PointsMonetaryValueAmount="", $PointsPointsMonetaryValueCurrencyCode="")
    {

        $this->setAPIAction("GetMyFeesEstimate");
        if(is_string($marketplaceId))
        {
            if(strlen($marketplaceId)<=2)
            {
                $marketplaceId = $this->_getMarketPlaceID($marketplaceId);
            }
        }

        $this->_setFeesEstimateRequestMarketplaceId($marketplaceId);
        $this->_setFeesEstimateRequestIdType($IdType);
        $this->_setFeesEstimateRequestIdValue($IdValue);
        $this->_setFeesEstimateRequestIsAmazonFulfilled($IsAmazonFulfilled);
        $this->_setFeesEstimateRequestIdentifier($Identifier);
        $this->_setFeesEstimateRequestPriceToEstimateFeesListingPriceAmount($ListingPriceAmount);
        $this->_setFeesEstimateRequestPriceToEstimateFeesListingPriceCurrencyCode($ListingPriceCurrencyCode);
        $this->_setFeesEstimateRequestPriceToEstimateFeesShippingAmount($ShippingAmount);
        $this->_setFeesEstimateRequestPriceToEstimateFeesShippingCurrencyCode($ShippingCurrencyCode);
        $this->_setFeesEstimateRequestPriceToEstimateFeesPointsPointsNumber($PointsPointsNumber);
        if(!empty($PointsMonetaryValueAmount)) $this->_setFeesEstimateRequestPriceToEstimateFeesPointsPointsMonetaryValueAmount($IdType);
        if(!empty($PointsPointsMonetaryValueCurrencyCode)) $this->_setFeesEstimateRequestPriceToEstimateFeesPointsPointsMonetaryValueCurrencyCode($IdType);
        $this->_invoke(true);
        return $this;
    }

    public function GetMyPriceForSKU($SellerSKU, $ItemCondition="", $marketPlaceId="")
    {
        $this->setAPIAction("GetMyPriceForSKU");
        $this->_setSellerSKU($SellerSKU, true);
        $this->_setMarketPlaceId($marketPlaceId);
        if(!empty($ItemCondition)) $this->setOperationField("ItemCondition", $ItemCondition);
        $this->_invoke(true);
        return $this;
    }

    public function GetMyPriceForASIN($asin, $ItemCondition="", $marketPlaceId="")
    {
        $this->setAPIAction("GetMyPriceForASIN");
        $this->_setAsin($asin, true);
        $this->_setMarketPlaceId($marketPlaceId);
        if(!empty($ItemCondition)) $this->setOperationField("ItemCondition", $ItemCondition);
        $this->_invoke(true);
        return $this;
    }

    public function GetProductCategoriesForSKU($sellerSKU, $marketPlaceId="")
    {
        $this->setAPIAction("GetProductCategoriesForSKU");
        $this->_setSellerSKU($sellerSKU);
        $this->_setMarketPlaceId($marketPlaceId);
        if(!empty($ItemCondition)) $this->setOperationField("ItemCondition", $ItemCondition);
        $this->_invoke(true);
        return $this;
    }

    public function GetProductCategoriesForASIN($asin, $marketPlaceId="")
    {
        $this->setAPIAction("GetProductCategoriesForASIN");
        $this->_setAsin($asin);
        $this->_setMarketPlaceId($marketPlaceId);
        if(!empty($ItemCondition)) $this->setOperationField("ItemCondition", $ItemCondition);
        $this->_invoke(true);
        return $this;
    }


    
}