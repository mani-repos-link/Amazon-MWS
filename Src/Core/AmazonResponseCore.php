<?php

namespace AmazonMWS\Core;

include __DIR__."/../Helper.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class AmazonResponseCore
{

    private $options = array();
    protected $response = "";
    private $logs = array();
    protected $xml = "";

    function __construct($result="")
    {
        //make something unique
        $ds = DIRECTORY_SEPARATOR;
        $this->options["XMLDefaultPath"] = realpath(__DIR__ .$ds."..".$ds."..".$ds."Results".$ds).$ds;
        $this->setXML($result);
        return $this;
    }

    public function setXML($result)
    {
        //make something unique
        if(is_file($result)) {
            $result = realpath($result);
            $this->setResultPath(dirname($result));
            $this->setResultFile($result);
            $this->xml = file_get_contents($result);
            $result = $this->xml;
        }

        if($this->_isValidXML($result)){
            $this->xml = $result;
        }else{
            $this->xml = "";
        }

        if(empty($this->xml)) {
            $this->log("Invalid XMl Feed Provided.", "invalidXML", "ERROR");
            return false;
        }

        return true;
    }

    public function setResultPath($path)
    {
        $this->options["XMLResultPath"] = $path;
        return $this;
    }

    public function getXMLDefaultPath()
    {
        return $this->options["XMLDefaultPath"];
    }

    public function getResultPath()
    {
        return $this->_isSet($this->options, "XMLResultPath");
    }

    public function setResultFile($path)
    {
        $this->options["XMLResultFile"] = $path;
        return $this;
    }

    public function getResultFile()
    {
        return $this->_isSet($this->options, "XMLResultFile");
    }

    protected function _isValidXML($xmlContent, $version = '1.0', $encoding = 'utf-8')
    {
        libxml_use_internal_errors(true);

        if(is_file($xmlContent)){
            $xmlContent = file_get_contents($xmlContent);
        }

        $doc = simplexml_load_string($xmlContent);
        if (!$doc) {
            $this->log("XMl is invalid. Errors : ".json_encode(libxml_get_errors())."","invalidXML", "ERROR");
            libxml_clear_errors();
            return false;
        }
        return true;
    }

    protected function log($msg, $msgType="", $level="Error")
    {
        $level = strtoupper($level);
        if (!empty($msgType)) {
            //avoid errors
            if (!isset($this->logs[$level][$msgType]))
                $this->logs[$level][$msgType] = array();
            $this->logs[$level][$msgType][] = $msg;
        } elseif (is_array($msg)) {
            reset($msg);
            $this->log(key($msg), $msgType, $level);
        } else {
            $this->logs[$level][] = $msg;
        }
    }

    public function getLog()
    {
        return $this->logs;
    }

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

    private function setupXMlSetting($xml="")
    {
        if(empty($xml)) {
            $file = $this->_isSet($this->options, "XMLResultFile");
            if($file === "")
                return false;
            $xml = file_get_contents($file);
        }

        if(!is_string($xml))
        {
            $this->log("XML is invalid.", "invalidXML", "ERROR");
            return false;
        }

        $xml  = str_replace("\n", "", $xml);
        $xml  = str_replace("\t", "", $xml);
        $xml  = str_replace("\r", "", $xml);

        return $this->xml = $this->setXML($xml) ? simplexml_load_string($this->xml) : false;
    }

    public function serializeXMlIntoArray($xml)
    {
        $arr = [];
        if(is_string($xml))
            $xml = simplexml_load_string($xml);

        if(!is_object($xml) || !array($xml)){
            $this->log("Parameter given in serializeXMlIntoArray is not object of SimpleXMLElement.", "Serialization","Error");
            return false;
        }

        if(!isset($this->options["xmlParsed"]))
            $this->options["xmlParsed"] = array();

        foreach ($xml as $key => $value) {
            $this->options["xmlParsed"][$key] = array();

            $this->options["xmlParsed"][$key] = json_decode(json_encode($value),1);
            if(is_object($value)){
                $this->serializeXMlIntoArray($value);
            }

            $attr = $this->setXMLAttributes($value, null, true);
            if(!empty($attr)) $this->options["xmlParsed"][$key]["attributes"] = $attr;

            $children = $this->setXMLChildren($value, true, $key);
            if(!empty($children)) $this->options["xmlParsed"][$key] = $children;
        }
        return $this->options["xmlParsed"];
    }

    private function setXMLAttributes($value, $ns = null, $is_prefix= false)
    {
        $arr = [];
        foreach ($value->attributes($ns, $is_prefix) as $attKey => $attrValue)
        {
            $arr[$attKey] = $attrValue;
        }
        return $arr;
    }

    private function setXMLChildren($value, $prefix=true)
    {
        $nameSpaces = $value->getNamespaces(true);
        $children = [];
        foreach ($nameSpaces as $key_ => $value_){
            if (empty($key_)) continue;
            foreach ($value->children($key_, $prefix) as $attKey => $attrValue) {
                $children[$attKey] = $attrValue;
            }
        }
        return $children;
    }

    public function GetServiceStatus($xml = "", $returnParserArr=true)
    {
        if(!$this->setupXMlSetting($xml))
        {
            $this->log("Failed to set up the xml.", "invalidXML", "ERROR");
            return false;
        }

        $xml = json_decode(json_encode($this->xml),1);
        if($returnParserArr) return $xml;
        return $xml["GetServiceStatusResult"]["Status"];
    }

    public function GetMatchingProductForId($xml = "")
    {
        if(!$this->setupXMlSetting($xml))
        {
            $this->log("Failed to set up the xml.", "invalidXML", "ERROR");
            return false;
        }

        $xmlData = $this->serializeXMlIntoArray($this->xml);
        $this->options["xmlParsed"] = json_decode(json_encode($xmlData),1);
        return true;
    }

    public function GetLowestPricedOffersForASIN($xml = "")
    {
        if(!$this->setupXMlSetting($xml))
        {
            $this->log("Failed to set up the xml.", "invalidXML", "ERROR");
            return false;
        }
        $xmlData = $this->serializeXMlIntoArray($this->xml);
        $this->options["xmlParsed"] = json_decode(json_encode($xmlData),1);
        return true;
    }

    public function parseXML($xml)
    {
        if(!$this->setupXMlSetting($xml))
        {
            $this->log("Failed to set up the xml.", "invalidXML", "ERROR");
            return false;
        }
        $xmlData = $this->serializeXMlIntoArray($this->xml);
        $this->options["xmlParsed"] = json_decode(json_encode($xmlData),1);
        return true;
    }

    public function getParsedXML()
    {
        return $this->_isSet($this->options, "xmlParsed");
    }
}