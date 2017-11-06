<?php

namespace AmazonMWS\core;

use cURLRequester\cURLRequester;

class AmazonMWSCore
{

    protected $options = array();

    protected $log = array();

    protected $headers = array();

    protected $curl = "";

    protected $saveResultPath = "";

    function __construct($sellerId = "", $awsKey = "", $secretKey = "")
    {
        $this->options["required_params"] = array();
        $this->options["required_params"]["SellerId"] = $sellerId;
        $this->options["required_params"]["AWSAccessKeyId"] = $awsKey;
        $this->options["AWS_SECRET_ACCESS_KEY"] = $secretKey;
        $this->requestAble(true);
        $this->initAPIOperationFields();
        $this->curl = new cURLRequester();
        if(!empty($this->curl->getERRORS())){
            $this->log("Curl Error : ".json_encode($this->curl->getERRORS()), "CURL", "Error");
            $this->requestAble(false);
        }
        $ds = DIRECTORY_SEPARATOR;
        $path = __DIR__ .$ds."..".$ds."..".$ds."Results".$ds;
        $this->setResultPath($path);
        return $this;
    }

    public function init()
    {
        $this->options = array();
        $this->log = array();
        $this->options["required_params"] = array();
        $this->requestAble(true);
        $this->setAPIRequestType("POST");
        $this->initAPIOperationFields();
        $this->curl = new cURLRequester();
    }

    protected function setResultPath($path)
    {
        $this->options["XMLResultPath"] = $path;
        return $this;
    }

    protected function getResultPath()
    {
        return $this->_isSet($this->options, "XMLResultPath");
    }

    protected function getResultFile()
    {
        return $this->getResultPath().strtolower($this->getAPIAction()).".xml";
    }

    public function initAPIOperationFields()
    {
        $this->options["operation"] = array();
        return $this;
    }

    public function setOperationField($key, $value)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) $this->setOperationField($k, $v);
            return $this;
        }

        $this->options["operation"][$key] = $value;
        return $this;
    }

    public function isOperationFieldAlreadySet($field)
    {
        return isset($this->options["operation"][$field]);
    }

    public function getOperationFields()
    {
        return $this->options["operation"];
    }

    /**
     * Check if index is set or not if set it will return value of it.
     * @param $var
     * @param $index
     * @return string
     */
    private function _isSet($var, $index)
    {
        if (is_array($var)) {
            return isset($var[$index]) ? $var[$index] : "";
        }

        if (is_object($var)) {
            return property_exists($var, $index) ? $var->$index : "";
        }

        if (is_string($var)) return $var;

        return isset($var) ? $var : "";
    }

    protected function invoke($useParams = false, $debug = false)
    {

        $this->options["result"] = "";

        if (!$this->options["RequestAble"]){
            $this->options["result"] = "";
            if($debug){
                echo "<pre>\nDebug:\n\tRequest has been Cancelled. Take a look at log.";
                print_R($this->getLog());
                echo "\n</pre>";
            }
            return false;
        }

        if (!is_object($this->curl)) $this->curl = new cURLRequester();

        // set userAgents
        if (isset($this->options["useragent"])) {
            if($debug){
                echo "<pre>\nDebug:\n\tSetting Useragent";
                print_R($this->options["useragent"]);
                echo "\n</pre>";
            }
            $this->curl->setUserAgent($this->options["useragent"]);
            $this->options["useragent"] = $this->curl->getUserAgent();
        }

        if (!empty($this->headers)) {
            if($debug){
                echo "<pre>\nDebug:\n\tSetting headers";
                print_R($this->headers);
                echo "\n</pre>";
            }
            $this->curl->setHeaders($this->headers);
        }

        $this->getRequestUrl($useParams);

        $url = $this->options["url"];
        $params = "";

        if ($useParams) {
            $params = $this->options["urlQuery"];
        }

        if($debug){
            echo "<pre>\nDebug:\n\tRequest Type is ".$this->getAPIRequestType()."\n\tUrl is ".$url."\n\tParam string is ".(empty($params)?" empty.":$params);
            echo "\n</pre>";
        }

        if ($this->getAPIRequestType() === "POST") {
            $this->curl->post($url, $params);
        } else {
            $this->curl->get($url, $params);
        }

        $this->options["result"] = $this->curl->getResult();
        $this->options["curlOptUsed"] = $this->curl->getCurlSetOptions();
        if($debug)
        {
            echo "\nCurl opt set : \n";
            print_r($this->options["curlOptUsed"]);
            echo "\n";
        }

        $resultSaveFile = $this->getResultFile();
        $file = realpath($this->getResultFile());
        if(empty($file))
            $file = $resultSaveFile;

        if($debug)
            echo "<pre>\nDebug:\n\tXMl Save File :  ".$file."\n</pre>";

        file_put_contents($file, $this->options["result"]);
        
        $this->curl->closeCurl();
        return $this;
    }

    protected function getUrlParameters()
    {
        return $this->options["queryParams"];
    }

    public function getResult()
    {
        return $this->_isSet($this->options, "result");
    }

    public function setAPIRequestType($http = "POST")
    {
        $this->options["HTTPAction"] = strtoupper($http);
        return $this;
    }

    public function getAPIRequestType()
    {
        return $this->_isSet($this->options, "HTTPAction") == "" ? "POST" : $this->_isSet($this->options, "HTTPAction");
    }

    /**
     * Set Useragent.
     * If this method is called and no useragent is passed in parameter, it will set random useragent using by curl lib.
     * Give custom useragent.
     * @param string $useragent
     * @return $this
     */
    public function setUseragent($useragent = "")
    {
        if (!empty($useragent)) {
            $this->options["useragent"] = $useragent;
        } else {
            $this->options["useragent"] = $this->createCustomUserAgent();
        }
        return $this;
    }

    //Amazon Methods -----------------------------

    /**
     * Create custom user agent
     * @param string $applicationName
     * @param string $applicationVersion
     * @param array $attributes
     * @return string
     */
    private function createCustomUserAgent($applicationName = "AmazonMWS", $applicationVersion = "1.0.0", $attributes = [])
    {

        $userAgent = $this->quoteApplicationName($applicationName) . '/' . $this->quoteApplicationVersion($applicationVersion);
        $userAgent .= ' (';
        $userAgent .= 'Language=PHP/' . phpversion();
        $userAgent .= '; ';
        $userAgent .= 'Platform=' . php_uname('s') . '/' . php_uname('m') . '/' . php_uname('r');
        $userAgent .= '; ';
        $userAgent .= 'MWSClientVersion=' . $this->getMWSClientVersion();

        foreach ($attributes as $key => $value) {
            $userAgent .= '; ' . $this->quoteAttributeName($key) . '=' . $this->quoteAttributeValue($value);
        }
        $userAgent .= ')';

        return $userAgent;
    }

    /**
     * Collapse multiple whitespace characters into a single ' ' character.
     * @param $s
     * @return string
     */
    private function collapseWhitespace($s)
    {
        return preg_replace('/ {2,}|\s/', ' ', $s);
    }

    /**
     * Collapse multiple whitespace characters into a single ' ' and backslash escape '\',
     * and '/' characters from a string.
     * @param $s
     * @return string
     */
    private function quoteApplicationName($s)
    {
        $quotedString = $this->collapseWhitespace($s);
        $quotedString = preg_replace('/\\\\/', '\\\\\\\\', $quotedString);
        $quotedString = preg_replace('/\//', '\\/', $quotedString);

        return $quotedString;
    }

    /**
     * Collapse multiple whitespace characters into a single ' ' and backslash escape '\',
     * and '(' characters from a string.
     *
     * @param $s
     * @return string
     */
    private function quoteApplicationVersion($s)
    {
        $quotedString = $this->collapseWhitespace($s);
        $quotedString = preg_replace('/\\\\/', '\\\\\\\\', $quotedString);
        $quotedString = preg_replace('/\\(/', '\\(', $quotedString);

        return $quotedString;
    }

    /**
     * Collapse multiple whitespace characters into a single ' ' and backslash escape '\',
     * and '=' characters from a string.
     *
     * @param $s
     * @return mixed|string
     */
    private function quoteAttributeName($s)
    {
        $quotedString = $this->collapseWhitespace($s);
        $quotedString = preg_replace('/\\\\/', '\\\\\\\\', $quotedString);
        $quotedString = preg_replace('/\\=/', '\\=', $quotedString);

        return $quotedString;
    }

    /**
     * Collapse multiple whitespace characters into a single ' ' and backslash escape ';', '\',
     * and ')' characters from a string.
     * @param $s
     * @return mixed|string
     */
    private function quoteAttributeValue($s)
    {
        $quotedString = $this->collapseWhitespace($s);
        $quotedString = preg_replace('/\\\\/', '\\\\\\\\', $quotedString);
        $quotedString = preg_replace('/\\;/', '\\;', $quotedString);
        $quotedString = preg_replace('/\\)/', '\\)', $quotedString);

        return $quotedString;
    }

    // Finished Amazon Methods----------------------

    public function setSellerId($id)
    {
        $this->options["required_params"]["SellerId"] = $id;
        return $this;
    }

    public function setAWSAccessKeyId($id)
    {
        $this->options["required_params"]["AWSAccessKeyId"] = $id;
        return $this;
    }

    public function setSecretAccessKey($id)
    {
        $this->options["AWS_SECRET_ACCESS_KEY"] = $id;
        return $this;
    }

    public function getSecretAccessKey()
    {
        return $this->_isSet($this->options, "AWS_SECRET_ACCESS_KEY");
    }

    public function setMWSClientVersion($version)
    {
        $this->options["MWSClientVersion"] = $version;
        return $this;
    }

    public function getMWSClientVersion()
    {
        $val = $this->_isSet($this->options, "MWSClientVersion");
        if (!empty($val)) return $val;
        $this->setMWSClientVersion("1.0.0");
        return "1.0.0";
    }

    public function setAPIName($api)
    {
        $this->options["api"] = $api;
        return $this;
    }

    public function getAPIName()
    {
        return $this->_isSet($this->options, "api");
    }

    /**
     * set API version 2018-10-17...
     * @param $version
     * @return $this
     */
    public function setAPIVersion($version)
    {
        $this->options["required_params"]["Version"] = $version;
        return $this;
    }

    public function getAPIVersion()
    {
        return $this->_isSet($this->options["required_params"], "Version");
    }

    /**
     * Set MWS auth token
     * @param $token
     * @return $this
     */
    public function setMWSAuthToken($token)
    {
        $this->options["required_params"]["MWSAuthToken"] = $token;
        return $this;
    }

    public function setAPIAction($apiOP)
    {
        $this->options["required_params"]["Action"] = $apiOP;
        return $this;
    }

    public function getAPIAction()
    {
        return $this->_isSet($this->options["required_params"], "Action");
    }

    /**
     * Alias of setAPIAction
     * @param $apiOP
     * @return $this
     */
    public function setAPIOperation($apiOP)
    {
        $this->options["required_params"]["Action"] = $apiOP;
        return $this;
    }

    public function getEndPointUrl($code = "")
    {
        if (strlen($code) >= 4) return $code;

        $code = mb_strtoupper($code);
        $endpoints = array("US" => "https://mws.amazonservices.com", "UK" => "https://mws.amazonservices.co.uk", "DE" => "https://mws.amazonservices.de", "FR" => "https://mws.amazonservices.fr", "IT" => "https://mws.amazonservices.it", "EU" => "https://mws-eu.amazonservices.com", "CN" => "https://mws.amazonservices.com.cn", "CA" => "https://mws.amazonservices.ca", "IN" => "https://mws.amazonservices.in", "JP" => "https://mws.amazonservices.jp");

        if (isset($endpoints[$code])) {
            return $endpoints[$code];
        }

        $this->log("Endpoint provided does not exists! Currently following endpoints are supported US, CA, DE, ES, FR, IT, UK, IN, CN, JP. By default EU(https://mws.amazonservices.it ) endpoints has been set.", "", "WARNING");

        return $endpoints["EU"];
    }

    /**
     * Set End Point of API by providing country code.
     * Currently US, CA, DE, ES, FR, IT, UK, IN, CN, JP codes are supported by MWS.
     * @param $code
     * @return mixed|string
     */
    public function setEndPoint($code = "")
    {
        $this->options["endPoint"] = $this->getEndPointUrl($code);
        return $this;
    }

    public function getEndPoint()
    {
        return $this->_isSet($this->options, "endPoint");
    }

    public function _getMarketPlaceID($code = "")
    {
        $code = mb_strtoupper($code);
        $markets = array("IT" => "APJ6JRA9NG5V4",
            "ES" => "A1RKKUPIHCS9HS",
            "UK" => "A1F83G8C2ARO7P",
            "DE" => "A1PA6795UKMFR9",
            "FR" => "A13V1IB3VIYZZH",
            "US" => "ATVPDKIKX0DER",
            "MX" => "A1AM78C64UM0Y8",
            "CA" => "A2EUQ1WTGCTBG2",
            "IN" => "A21TJRUUN4KGV",
            "BR" => "A2Q3Y263D00KWC",
            "AU" => "A39IBJ37TRP1C6",
            "CN" => "AAHKV2X7AFYLW",
            "JP" => "A1VC38T7YXB528");

        if($code === "ALL")
        {
            return $markets;
        }

        if (isset($markets[$code])) return $markets[$code];

        $marketValues = array_values($markets);
        if(isset($marketValues[$code])
        {
        	return $code;
        }


        $this->log("MarketplaceId provided does not exists! Currently following endpoints are supported US, BR, AU, CA, MX, DE, ES, FR, IT, UK, IN, CN, JP.");
        $this->log("By default IT(APJ6JRA9NG5V4) endpoints has been set. ", "", "WARNING");

        return $markets["IT"];
    }

    public function setMarketPlaceID($code)
    {
        $this->options["MarketplaceId"] = $this->_getMarketPlaceID($code);
        return $this;
    }

    public function getMarketPlaceID()
    {
        return $this->_isSet($this->options, "MarketplaceId");
    }

    public function setTimestamp($dateTime = "", $format = DATE_ISO8601)
    {
        if (empty($dateTime))
            $dateTime = Date("Y-m-d H:i:s", time());

        //$this->options["required_params"]["Timestamp"] = gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", $dateTime);

        $this->options["required_params"]["Timestamp"] = $this->_getTimeStamp($dateTime, $format);
        return $this;
    }

    protected function _getTimeStamp($dateTime, $format = DATE_ISO8601){

        if (!is_object($dateTime)) {
            if (is_string($dateTime) || is_int($dateTime)) {
                $dateTime = new \DateTime($dateTime);
            } else {
                $this->log("Time provided by you is not valid. Set current time by default.", "Timestamp", "ERROR");
                $dateTime = new \DateTime(time());
            }
        } else {
            if (!($dateTime instanceof \DateTime)) {
                $this->log("Time object provided by you is not instance of DateTime object. Set current time by default.", "Timestamp", "ERROR");
                $dateTime = new \DateTime(time());
            }
        }
        return $dateTime->format($format);

    }

    protected function setSignatureVersion($version = "2")
    {
        $this->options["required_params"]["SignatureVersion"] = $version;
        return $this;
    }

    protected function setSignatureMethod($method = "HmacSHA256")
    {
        $this->options["required_params"]["SignatureMethod"] = $method;
        return $this;
    }

    /**
     * Set headers.
     * @param $key
     * @param $value
     * @return $this
     */
    protected function setHeaders($key, $value="")
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->setHeaders($k, $v);
            }
            return $this;
        }
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * Check whether required parameters are set or not.
     * if not then it will set some parameters as default.
     * @return mixed
     */
    protected function getRequiredParams()
    {
        $required = array("SellerId" => false, "AWSAccessKeyId" => false, "SignatureVersion" => true, "Action" => false, "Timestamp" => true, "Version" => false, "SignatureMethod" => true);
        $requiredKeys = array_keys($required);

        foreach ($requiredKeys as $requiredParam) {
            if (!isset($this->options["required_params"][$requiredParam])) {

                //check if we can some default values
                if ($required[$requiredParam]) {
                    $methodName = "Set" . $requiredParam;
                    $this->$methodName();
                    $this->log("$requiredParam parameter is required for Amazon MWS request. But It has been set to default value.", "SkipAble", "WARNING");
                } else {
                    $this->log("$requiredParam param is required for Amazon MWS request.");
                    $this->log("Request has been cancelled due to lack of required parameters.", "Error", "ERROR");
                    $this->requestAble(false);
                    continue;
                }

            }

            if (empty($this->options["required_params"][$requiredParam])) {
                $this->log("$requiredParam param cannot be empty!");
                $this->requestAble(false);
                continue;
            }

        }
        return $this->options["required_params"];
    }

    /**
     * Make url query and returns.
     * @return array
     */
    protected function getQueryParams()
    {
        $primaryRequiredParams = $this->getRequiredParams();
        $secondaryRequiredParams = $this->getOperationFields();
        if (!is_array($secondaryRequiredParams)) {
            $secondaryRequiredParams = array();
        }

        $urlParams = array_merge($primaryRequiredParams, $secondaryRequiredParams);
        $urlParams["Action"] = $this->getAPIAction();

        uksort($urlParams, 'strcmp');

        return $this->options["queryParams"] = $urlParams;
    }

    /**
     * Make a string sign able and returns.
     * @return bool|string
     */
    protected function makeStringSignAble()
    {
        $url_parts = array();
        $queryParams = $this->getQueryParams();

        foreach ($queryParams as $key => $value) {
            $value = $this->_urlEncode($value);
            $url_parts[] = $key . "=" . $value;
        }

        if (!isset($this->options["HTTPAction"])) $this->SetAPIRequestType();
        if (!isset($this->options["endPoint"])) $this->setEndPoint();

        $stringToSign[] = $this->getAPIRequestType(); //POST or GET
        $stringToSign[] = str_replace("https://", "", $this->getEndPoint());
        if ($this->getAPIName() === "" || $this->getAPIVersion() === "") {
            $this->requestAble(false);
            $this->log("API name(products,feed,reports..) or version(YYYY-mm-dd) is invalid", "Error", "ERROR");
            return false;
        }
        $stringToSign[] = "/" . $this->getAPIName()."/" . $this->getAPIVersion();
        $url_parts = implode("&", $url_parts);
        $stringToSign[] = $url_parts;
        $this->options["queryString"] = $url_parts;
        return $this->options["signedString"] = implode("\n", $stringToSign);
    }

    /**
     * Returns the signature of string.
     * @param string $stringToSign
     * @return mixed
     */
    protected function getSignature($stringToSign = "", $key="")
    {
        if (empty($stringToSign)) {
            $this->log("Request has been cancelled due to signature problem. Sign String is empty.");
            $this->requestAble(false);
            return false;
        };

        if(empty($key))
        {
            $key = $this->getSecretAccessKey();
            if($key === "")
            {
                $this->log("Please Provide Secret access key. Request has been cancelled to server.");
                $this->requestAble(false);
                return false;
            }
        }

        $hash   = hash_hmac("sha256", $stringToSign, $key, true);
        $this->options["queryParams"]["Signature"] = urlencode(base64_encode($hash));
        return $this->options["queryParams"]["Signature"];
    }

    /**
     * Returns the request url with or without query.
     * @param bool $urlWithoutQuery
     * @return bool|string
     */
    public function getRequestUrl($urlWithoutQuery = true)
    {
        $signAbleString = $this->makeStringSignAble();
        if ($signAbleString === false) {
            $this->requestAble(false);
            return false;
        }


        $url = $this->getEndPoint();

        if (!(substr($url, strlen($url) - 1) === '/')) {
            $url .= '/';
        }

        $this->options["url"] = $url . $this->getAPIName() . "/" . $this->getAPIVersion();

        $signature = $this->getSignature($signAbleString);

        $this->options["urlQuery"] = $this->options["queryString"] . "&Signature=" . $signature;

        if ($urlWithoutQuery === false) {
            $this->options["url"] = $this->options["url"] . "?" . $this->options["urlQuery"];
        }

        return $url;
    }

    /**
     * Encode the url as amazon mentioned.
     * @param $value
     * @return mixed
     */
    public function _urlEncode($value)
    {
        return str_replace('%7E', '~', rawurlencode($value));
    }

    /**
     * Set false to cancel the request to server MWS.
     * @param $val
     * @return $this
     */
    protected function requestAble($val)
    {
        $this->options["RequestAble"] = $val;
        return $this;
    }

    protected function log($msg, $msgName = "", $type = "ERROR")
    {
        $type = strtoupper($type);
        if (!empty($msgName)) {
            //avoid errors
            if (!isset($this->log[$type][$msgName])) $this->log[$type][$msgName] = array();

            $this->log[$type][$msgName] = $msg;

        } elseif (is_array($msg)) {

        } else {
            $this->log[$type][] = $msg;
        }
    }

    public function getLog()
    {
        return $this->log;
    }

    protected function _setList($list, $prefix, $suffix = "")
    {
        if (!is_array($list)) {
            $list = array($list);
        }

        $counter = 0;
        foreach ($list as $key => $value) {
            $prefixName = $prefix . (++$counter).$suffix;
            if($this->isOperationFieldAlreadySet($prefixName))
                continue;
            $this->setOperationField($prefixName, $value);
        }
        return $this;
    }

    protected function _setMarketPlaceId($marketPlaceId, $setAsList=false, $defaultID="IT", $prefixForList="MarketplaceIdList.Id.")
    {
        if(is_array($marketPlaceId) ||  $setAsList === true)
        {
            $this->_setList($marketPlaceId, $prefixForList);
            return $this;
        }

        if(!empty($marketPlaceId) && strlen($marketPlaceId)>3) {
            $this->setOperationField("MarketplaceId", $marketPlaceId);
        }
        elseif(strlen($marketPlaceId)==2)
        {
            //if user is lazy and he just pass only country code
            $this->setMarketPlaceID($marketPlaceId);
            $this->setOperationField("MarketplaceId", $this->getMarketPlaceID());
        }
        else{
            //check if marketplace id already set or not
            $marketPlaceId = $this->getMarketPlaceID();
            if($marketPlaceId !== "")
            {
                $this->setOperationField("MarketplaceId", $marketPlaceId);
            }else{
                $this->setMarketPlaceID($defaultID);
                $this->setOperationField("MarketplaceId", $this->getMarketPlaceID());
            }
        }
        return $this;
    }

    public function _invoke($useParams = false, $debug = false)
    {
        $this->invoke($useParams, $debug);
        return $this->getResult() == "" ? false : $this->getResult();
    }

    protected function _isValidXML($xmlContent, $version = '1.0', $encoding = 'utf-8')
    {
        /*if(trim($xmlContent) == '')
            return false;
        if(is_array($xmlContent))
            return false;
        $doc = new \DOMDocument($version, $encoding);
        $doc->loadXML($xmlContent);
        $errors = libxml_get_errors();
        libxml_clear_errors();
        return !empty($errors);*/

        libxml_use_internal_errors(true);
        $doc = simplexml_load_string($xmlContent);
        if (!$doc) {
            $this->log("XMl is not valid. Errors : ".json_encode(libxml_get_errors())."","XML", "ERROR");
            libxml_clear_errors();
            return false;
        }
        return true;
    }

    protected function uploadFeedFile($feed)
    {

        if(!is_resource($this->curl))
            $this->curl = new cURLRequester();

        if(!is_file($feed))
        {
            $file = __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."Others".DIRECTORY_SEPARATOR."feed.xml";
            file_put_contents($file, $feed);
            $feed = $file;
        }

        $feedFile = $feed;
        $this->options["fopen"] = fopen($feedFile, "r");
        $feed = file_get_contents($feedFile);
        $md5 = base64_encode(md5($feed,true));
        $this->setOperationField("ContentMD5Value", $md5);
        $this->options["ContentMD5Value"] = $md5;

        $this->curl->setOpt("CURLOPT_VERBOSE", 1);
        $this->curl->setOpt("CURLOPT_CUSTOMREQUEST", "POST");
        $this->curl->setOpt("CURLOPT_UPLOAD", true);
        $this->curl->setOpt("CURLOPT_INFILE", $this->options["fopen"]);

        //$this->curl->uploadFile($feed, true);
        return $this;
    }

    protected function isArrayNumeric($array)
    {
        if (empty($array)) return false;
        return count(array_filter(array_keys($array), 'is_numeric'))>0?true:false;
    }

    protected function getCurlOptionsUsed()
    {
        return $this->_isSet($this->options, "curlOptUsed" );
    }

    protected function getContentMD5()
    {
        return $this->_isSet($this->options, "ContentMD5Value");
    }

    protected function closeOpenedFileInstance()
    {
        if(isset($this->options["fopen"])){
            fclose($this->options["fopen"]);
        }
    }

}
