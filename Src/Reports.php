<?php
/**
 * Created by PhpStorm.
 * User: WahaGuru
 * Date: 22/10/2017
 * Time: 18:00
 */

namespace AmazonMWS;
use AmazonMWS\core\AmazonMWSCore;


class Reports extends AmazonMWSCore
{
    private $options = array();
    function __construct($sellerId = "", $awsKey = "", $secretKey = "", $endpoint = "EU")
    {
        parent::__construct($sellerId, $awsKey, $secretKey);
        $this->setAPIName("Reports");
        $this->setAPIVersion("2009-01-01");
        $this->setSignatureMethod();
        $this->setTimestamp();
        $this->setSignatureVersion(2);
        $this->setEndPoint($endpoint);
        $this->setAPIRequestType("POST");
    }

    public function init()
    {
        parent::__construct($this->options["sellerId"], $this->options["awsKey"], $this->options["secretKey"]);
        parent::init();
        $this->setAPIName("Reports");
        $this->setAPIVersion("2009-01-01");
        $this->setSignatureMethod();
        $this->setTimestamp();
        $this->setSignatureVersion(2);
        $this->setEndPoint($this->options["endpoint"]);
        $this->setAPIRequestType("POST");
    }

    private function __setMarketPlaceID($marketplaceId)
    {
        if(empty($marketplaceId))
            return $marketplaceId;
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

    public function GetReport($reportID)
    {
        $this->setAPIAction("GetReport");
        $this->setOperationField("ReportId", $reportID);
        $this->_invoke();
        return $this;
    }

    public function GetReportCount($ReportTypeList="", $Acknowledged=false, $AvailableFromDate="", $AvailableToDate="")
    {
        $this->setAPIAction("GetReportCount");
        if(!empty($ReportTypeList)) $this->_setList($ReportTypeList,"ReportTypeList.Type.");
        if(!empty($Acknowledged)) $this->setOperationField("Acknowledged", $Acknowledged);

        if(!empty($AvailableFromDate))
        {
            $date = $this->__fixDateTime($AvailableFromDate);
            $this->setOperationField("AvailableFromDate", $date);
        }

        if(!empty($AvailableToDate))
        {
            $date = $this->__fixDateTime($AvailableToDate);
            $this->setOperationField("AvailableToDate", $date);
        }
        $this->_invoke();
        return $this;

    }

    public function GetReportList($MaxCount="", $ReportTypeList="", $Acknowledged=false,
                                  $AvailableFromDate="", $AvailableToDate="", $ReportRequestIdList="")
    {

        $this->setAPIAction("GetReportList");
        if(!empty($MaxCount)) $this->setOperationField("MaxCount", $MaxCount);
        if(!empty($ReportTypeList)) $this->_setList($ReportTypeList,"ReportTypeList.Type.");
        if(!empty($ReportRequestIdList)) $this->_setList($ReportRequestIdList,"ReportRequestIdList.Id.");
        if(!empty($Acknowledged)) $this->setOperationField("Acknowledged", $Acknowledged);

        if(!empty($AvailableFromDate))
        {
            $date = $this->__fixDateTime($AvailableFromDate);
            $this->setOperationField("AvailableFromDate", $date);
        }

        if(!empty($AvailableToDate))
        {
            $date = $this->__fixDateTime($AvailableToDate);
            $this->setOperationField("AvailableToDate", $date);
        }
        $this->_invoke();
        return $this;

    }

    public function GetReportListByNextToken($token)
    {
        $this->setAPIAction("GetReportListByNextToken");
        $this->setOperationField("NextToken", $token);
        $this->_invoke();
        return $this;
    }

    public function GetReportRequestCount($RequestedFromDate="", $RequestedToDate="", $ReportTypeList="", $ReportProcessingStatusList="")
    {
        $this->setAPIAction("GetReportRequestCount");

        if(!empty($RequestedFromDate))
        {
            $date = $this->__fixDateTime($RequestedFromDate);
            $this->setOperationField("RequestedFromDate", $date);
        }

        if(!empty($RequestedToDate))
        {
            $date = $this->__fixDateTime($RequestedToDate);
            $this->setOperationField("RequestedToDate", $date);
        }

        if(!empty($RequestedToDate)) $this->_setList($ReportTypeList, "ReportTypeList.Type.");
        if(!empty($ReportProcessingStatusList)) $this->_setList($ReportProcessingStatusList, "ReportProcessingStatusList.Status.");

        $this->_invoke();
        return $this;

    }

    public function GetReportRequestList($MaxCount="", $RequestedFromDate="", $RequestedToDate="",
                                         $ReportRequestIdList="", $ReportTypeList="", $ReportProcessingStatusList="")
    {

        $this->setAPIAction("GetReportRequestList");
        if(!empty($MaxCount)) $this->setOperationField("MaxCount", $MaxCount);

        if(!empty($ReportRequestIdList)) $this->_setList($ReportRequestIdList,"ReportRequestIdList.Id.");
        if(!empty($ReportTypeList)) $this->_setList($ReportTypeList,"ReportTypeList.Type.");
        if(!empty($ReportProcessingStatusList)) $this->_setList($ReportProcessingStatusList,"ReportProcessingStatusList.Status.");

        if(!empty($RequestedFromDate))
        {
            $date = $this->__fixDateTime($RequestedFromDate);
            $this->setOperationField("RequestedFromDate", $date);
        }

        if(!empty($RequestedToDate))
        {
            $date = $this->__fixDateTime($RequestedToDate);
            $this->setOperationField("RequestedToDate", $date);
        }
        $this->_invoke();
        return $this;
    }

    public function GetReportRequestListByNextToken($token)
    {
        $this->setAPIAction("GetReportRequestListByNextToken");
        $this->setOperationField("NextToken", $token);

        $this->_invoke();
        return $this;
    }

    public function CancelReportRequests($RequestedFromDate="", $RequestedToDate="",
                                         $ReportRequestIdList="", $ReportTypeList="", $ReportProcessingStatusList="")
    {

        $this->setAPIAction("CancelReportRequests");
        if(!empty($ReportRequestIdList)) $this->_setList($ReportRequestIdList,"ReportRequestIdList.Id.");
        if(!empty($ReportTypeList)) $this->_setList($ReportTypeList,"ReportTypeList.Type.");
        if(!empty($ReportProcessingStatusList)) $this->_setList($ReportProcessingStatusList,"ReportProcessingStatusList.Status.");

        if(!empty($RequestedFromDate))
        {
            $date = $this->__fixDateTime($RequestedFromDate);
            $this->setOperationField("RequestedFromDate", $date);
        }

        if(!empty($RequestedToDate))
        {
            $date = $this->__fixDateTime($RequestedToDate);
            $this->setOperationField("RequestedToDate", $date);
        }
        $this->_invoke();
        return $this;
    }

    public function RequestReport($ReportType, $MarketplaceIdList="", $StartDate="", $EndDate="", $ReportOptions="")
    {
        $this->setAPIAction("RequestReport");
        if (!empty($StartDate)) {
            $date = $this->__fixDateTime($StartDate);
            $this->setOperationField("StartDate", $date);
        }

        if (!empty($EndDate)) {
            $date = $this->__fixDateTime($EndDate);
            $this->setOperationField("EndDate", $date);
        }

        $MarketplaceIdList = $this->__setMarketPlaceID($MarketplaceIdList);
        if (!empty($ReportOptions)) $this->setOperationField("ReportOptions", $ReportOptions);
        if (!empty($ReportType)) $this->setOperationField("ReportType", $ReportType);
        if (!empty($MarketplaceIdList)) $this->_setMarketPlaceId($MarketplaceIdList, true);

        $this->_invoke();
        return $this;
    }

    public function ManageReportSchedule($ReportType, $Schedule, $ScheduleDate="")
    {
        $this->setAPIAction("ManageReportSchedule");
        $this->setOperationField("ReportType", $ReportType);
        $this->setOperationField("Schedule", $Schedule);
        if(!empty($ScheduleDate))
        {
            $date = $this->__fixDateTime($ScheduleDate);
            $this->setOperationField("ScheduleDate", $date);
        }
        $this->_invoke();
        return $this;
    }

    public function GetReportScheduleList($ReportTypeList)
    {
        $this->setAPIAction("GetReportScheduleList");
        $this->_setList($ReportTypeList, "ReportTypeList.Type.");

        $this->_invoke();
        return $this;
    }

    public function GetReportScheduleListByNextToken($token)
    {
        $this->setAPIAction("GetReportScheduleListByNextToken");
        $this->setOperationField("NextToken.-", $token);

        $this->_invoke();
        return $this;
    }

    public function GetReportScheduleCount($ReportTypeList="")
    {
        $this->setAPIAction("GetReportScheduleCount");
        if(!empty($ReportTypeList)) $this->_setList($ReportTypeList, "ReportTypeList.Type.");

        $this->_invoke();
        return $this;
    }

    public function UpdateReportAcknowledgements($ReportIdList, $Acknowledged="")
    {
        $this->setAPIAction("UpdateReportAcknowledgements");
        $this->_setList($ReportIdList, "ReportIdList.Id.");
        if(!empty($Acknowledged)) $this->setOperationField("Acknowledged", $Acknowledged);

        $this->_invoke();
        return $this;
    }

}