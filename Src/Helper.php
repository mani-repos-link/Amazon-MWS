<?php

if(!function_exists("is_cli"))
{
    function is_cli()
    {
        static $mode;
        if($mode == null)
            $mode 	= php_sapi_name()==="cli";
        return $mode;
    }
}

if(!function_exists("pp"))
{
    function pp($data="", $cmnt="", $varDump=false)
    {
        $br 	= is_cli()?"\n":"<br/>";
        if($cmnt!="") echo pp($cmnt);
        if($varDump){
            print_array($data, $varDump);
        }elseif(is_string($data)){
            if(strlen($data)>0)
                echo $br.trim($data);
            else pp($br."P--EMPTY STRING--P".$br);
        }elseif(is_bool($data)){
            pp($data?"true":"false");
        }elseif(is_int($data)){
            echo $br.$data;
        }elseif(is_resource($data)){
            pp("Given Value is resource : ");
            print_r($data);
        }elseif(is_array($data)|| is_object($data)){
            print_array($data, $varDump);
        }elseif(is_null($data)){
            pp("NULL");
        }else{
            pp("Unknown Data :( WTH/F?!?@# \ns0rry \nLets try with simple print_r\n");
            print_r($data);
        }
    }
}

if(!function_exists("print_array"))
{
    function print_array($data, $varDump=false)
    {
        echo is_cli()?"\n":"<br/><pre>";
        if($varDump) var_dump($data);
        else print_r($data);
        echo (!is_cli())?"</pre>":"";
    }
}