<?php
/**
 * Created by PhpStorm.
 * User: WahaGuru
 * Date: 22/10/2017
 * Time: 19:22
 */

namespace AmazonMWS;
use AmazonMWS\core\AmazonMWSCore;


class Feed extends AmazonMWSCore
{
    function __construct($sellerId = "", $awsKey = "", $secretKey = "", $endpoint = "EU")
    {
        parent::__construct($sellerId, $awsKey, $secretKey);
        $this->setAPIName("Feeds");
        $this->setAPIVersion("2009-01-01");
        $this->setSignatureMethod();
        $this->setTimestamp();
        $this->setSignatureVersion(2);
        $this->setEndPoint($endpoint);
        $this->setAPIRequestType("POST");
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

    public function CancelFeedSubmissions($FeedSubmissionIdList="", $FeedTypeList="", $SubmittedFromDate="", $SubmittedToDate="")
    {
        $this->setAPIAction("CancelFeedSubmissions");
        if(!empty($FeedSubmissionIdList)) $this->_setList($FeedSubmissionIdList,"FeedSubmissionIdList.Id.");
        if(!empty($FeedTypeList)) $this->_setList($FeedTypeList,"FeedTypeList.Type.");

        if(!empty($SubmittedToDate))
        {
            $date = $this->__fixDateTime($SubmittedToDate);
            $this->setOperationField("SubmittedToDate", $date);
        }

        if(!empty($SubmittedFromDate))
        {
            $date = $this->__fixDateTime($SubmittedFromDate);
            $this->setOperationField("SubmittedFromDate", $date);
        }
        $result = $this->_invoke();
        return $this;
    }

    public function GetFeedSubmissionList($FeedSubmissionIdList="", $MaxCount="", $FeedTypeList="",
                                          $FeedProcessingStatusList="", $SubmittedFromDate="", $SubmittedToDate="")
    {
        $this->setAPIAction("GetFeedSubmissionList");

        if(!empty($FeedSubmissionIdList)) $this->_setList($FeedSubmissionIdList,"FeedSubmissionIdList.Id.");
        if(!empty($MaxCount)) $this->setOperationField("MaxCount", $MaxCount);
        if(!empty($FeedTypeList)) $this->_setList($FeedTypeList,"FeedTypeList.Type.");
        if(!empty($FeedProcessingStatusList)) $this->_setList($FeedProcessingStatusList,"FeedProcessingStatusList.Status.");

        if(!empty($SubmittedToDate))
        {
            $date = $this->__fixDateTime($SubmittedToDate);
            $this->setOperationField("SubmittedToDate", $date);
        }

        if(!empty($SubmittedFromDate))
        {
            $date = $this->__fixDateTime($SubmittedFromDate);
            $this->setOperationField("SubmittedFromDate", $date);
        }
        $result = $this->_invoke();
        return $this;

    }

    public function GetFeedSubmissionListByNextToken($token)
    {
        $this->setAPIAction("GetFeedSubmissionListByNextToken");
        $this->setOperationField("NextToken", $token);
        $result = $this->_invoke();
        return $this;
    }

    public function GetFeedSubmissionCount($FeedTypeList="", $FeedProcessingStatusList="", $SubmittedFromDate="", $SubmittedToDate="")
    {
        $this->setAPIAction("GetFeedSubmissionCount");
        if(!empty($FeedTypeList)) $this->_setList($FeedTypeList,"FeedTypeList.Type.");
        if(!empty($FeedProcessingStatusList)) $this->_setList($FeedProcessingStatusList,"FeedProcessingStatusList.Status.");

        if(!empty($SubmittedToDate))
        {
            $date = $this->__fixDateTime($SubmittedToDate);
            $this->setOperationField("SubmittedToDate", $date);
        }

        if(!empty($SubmittedFromDate))
        {
            $date = $this->__fixDateTime($SubmittedFromDate);
            $this->setOperationField("SubmittedFromDate", $date);
        }
        $result = $this->_invoke();
        return $this;
    }

    public function GetFeedSubmissionResult($FeedSubmissionId)
    {
        $this->setAPIAction("GetFeedSubmissionResult");
        $this->setOperationField("FeedSubmissionId", $FeedSubmissionId);
        $this->_invoke();
        return $this;
    }

    public function SubmitFeed($feed, $FeedType="", $MarketplaceIdList="", $PurgeAndReplace=false)
    {
        $this->setAPIAction("SubmitFeed");

        if(empty($feed))
        {
            $this->log("Feed is empty. Request is cancelled.", "Feederror", "ERROR");
            $this->requestAble(false);
            return $this;
        }

        if(is_file($feed)) $feedContent = file_get_contents($feed);
        else $feedContent = $feed;

        $feedFile = __DIR__.DIRECTORY_SEPARATOR."Others".DIRECTORY_SEPARATOR."feed.xml";
        file_put_contents($feedFile, $feedContent);

        if(!$this->_isValidXML($feed))
        {
            $this->requestAble(false);
            $this->log("Request is cancelled due to invalid xml feed.");
        }


        if(!empty($FeedType)) $this->setOperationField("FeedType", $FeedType);

        if(!empty($MarketplaceIdList)) {
            $markets = $this->__setMarketPlaceID($MarketplaceIdList);
            $this->_setMarketPlaceId($markets, true);
        }

        if($PurgeAndReplace === true) $PurgeAndReplace = "true";
        else $PurgeAndReplace = "false";

        $this->setOperationField("PurgeAndReplace", $PurgeAndReplace);
        $this->uploadFeedFile($feedFile);
        $this->setUseragent();

        //Headers
        $header["Expect"] = ' ';
        $header["Accept"] = ' ';
        $header["Transfer-Encoding"] = 'chunked';
        $header["Content-Type"] = 'text/xml';
        //$header[] = 'Content-MD5: '.$this->getContentMD5();
        $this->setHeaders($header);
        $result =  $this->_invoke();
        $this->closeOpenedFileInstance();
        return $this;
    }



}