<?php
/*
 * +----------------------------------------------------------------------
 * | Presty Framework
 * +----------------------------------------------------------------------
 * | Copyright (c) 20021~2022 Confidire All rights reserved.
 * +----------------------------------------------------------------------
 * | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * +----------------------------------------------------------------------
 * | Email: 790455692@qq.com
 * +----------------------------------------------------------------------
 */

namespace presty\View\Driver;

use PhpParser\Node\Expr\Isset_;
use presty\Exception\NotFoundException;

class View
{
    public $fileContent = "";
    protected $url = [];
    protected $data = [];
    protected $replaceText;
    protected $outputOpinion = "";
    protected $output = false;
    protected $saveResult = false;
    protected $subscript = [];
    protected $templateEnginePrefix = "{{";
    protected $templateEngineSuffix = "}}";
    protected $variablePrefix = "$";
    protected $constantPrefix = "%";

    public function parse ($fileContent, $data)
    {
        if(empty($fileContent)) return "";
        $this->data = $data;
        \presty\Container::getInstance ()->set("hasBeenRun","tpl"," - [".(new \DateTime())->format("Y-m-d H:i:s:u")."] => Template_Engine_Init");
        $this->url = \presty\Container::getInstance ()->makeAndSave("request")->siteUrl();
        \presty\Container::getInstance ()->makeAndSave("middleWare")->getClassName ('beforeParseFile')->listening ([$this->fileContent]);
        $this->fileContent = $fileContent;
        $this->templateEnginePrefix = \presty\Container::getInstance ()->makeAndSave("config")->get("View.template_engine_prefix","{{");
        $this->templateEngineSuffix = \presty\Container::getInstance ()->makeAndSave("config")->get("View.template_engine_suffix","}}");
        $this->variablePrefix = \presty\Container::getInstance ()->makeAndSave("config")->get("View.variable_prefix","$");
        $this->constantPrefix = \presty\Container::getInstance ()->makeAndSave("config")->get("View.constant_prefix","%");
        $this->parseNotes ();
        $this->parseVars ();
        $this->parseConstant ();
        $this->parseInclude ();
        $this->parseController ();
        $this->parseModel ();
        $this->parseFunction();
        $this->parseIf ();
        $this->parseForeach ();
        \presty\Container::getInstance ()->makeAndSave("middleWare")->getClassName ('afterParseFile')->listening ([$this->fileContent]);
        return $this->fileContent;
    }

    protected function parseNotes(){
        $this->fileContent = preg_replace("/<!--.*-->/","",$this->fileContent);
    }

    protected function parseInclude ()
    {
        $isMatched = preg_match_all ('/'.$this->templateEnginePrefix.'include(.*?)'.$this->templateEngineSuffix.'/', $this->fileContent, $matches);
        if($isMatched != 0) {
            if(is_dir (\presty\Container::getInstance ()->getAppPath."config")) $viewMapping = require \presty\Container::getInstance ()->getAppPath."config".DS.\presty\Container::getInstance ()->makeAndSave("config")->get ("view.view_mapping_config_file_name","ViewMapping").".php";
            else $viewMapping = [];
            foreach ($matches[0] as $key => $value) {
                $args = $matches[1][$key];
                $args = array_values (array_filter (explode (" ",$args)));
                foreach ($args as $k => $v){
                    $v = explode ("=",$v);
                    $args[$v[0]] = $v[1];
                    unset($args[$k]);
                }
                $content = "";
                if(isset($viewMapping[$args["name"]])){
                    if(substr ($viewMapping[$args["name"]],0,1) == "/") $viewMapping[$args["name"]] = substr ($viewMapping[$args["name"]],1);
                    $content = file_get_contents (\presty\Container::getInstance ()->getAppPath.$viewMapping[$args["name"]].\presty\Container::getInstance ()->makeAndSave("config")->get ("view.file_suffix",".html"));
                }
                else if(file_exists (\presty\Container::getInstance ()->getAppPath.$args["name"].\presty\Container::getInstance ()->makeAndSave("config")->get ("view.file_suffix",".html"))){
                    $content = file_get_contents (\presty\Container::getInstance ()->getAppPath.$args["name"].\presty\Container::getInstance ()->makeAndSave("config")->get ("view.file_suffix",".html"));
                }
                else new NotFoundException($args["name"]."，文件不存在于根目录或映射配置文件中未定义",__FILE__,__LINE__,"EC100024");
                $this->fileContent = str_replace ($value,$content,$this->fileContent);
            }
        }
    }

    protected function parseController ()
    {
        $isMatched = preg_match_all ('/'.$this->templateEnginePrefix.'controller=.*?'.$this->templateEngineSuffix.'/', trim ($this->fileContent), $matches);
        if ($isMatched != 0) {
            for ($i = 0; $i < count ($matches, 1) - 1; $i++) {
                $funcName = preg_replace ('/('.$this->templateEnginePrefix.'controller=)(.*?)('.$this->templateEngineSuffix.')/', '$2', $matches[0][$i]);
                $this->fileContent = str_replace ($matches[0], "", $this->fileContent);
                $className = "\\app\\" . $this->url['app'] . "\\" . ucfirst ($funcName) . "\\" . ucfirst ($funcName);
                $class = new $className;
                call_user_func_array ([$class, $funcName], [$this->url['vars']]);
            }
        }
    }

    protected function parseModel ()
    {
        $isMatched = preg_match_all ('/'.$this->templateEnginePrefix.'model=.*?'.$this->templateEngineSuffix.'/', trim ($this->fileContent), $matches);
        if ($isMatched != 0) {
            for ($i = 0; $i < count ($matches, 1) - 1; $i++) {
                $funcName = preg_replace ('/('.$this->templateEnginePrefix.'model=)(.*?)('.$this->templateEngineSuffix.')/', '$2', $matches[0][$i]);
                $this->fileContent = str_replace ($matches[0], "", $this->fileContent);
                $className = "\\model" . "\\" . $funcName . "\\" . ucfirst ($funcName);
                $class = new $className;
                call_user_func_array ([$class, $funcName], [$this->url['vars']]);
            }
        }
    }

    protected function parseFunction ()
    {
        $isMatched = preg_match_all ('/'.$this->templateEnginePrefix.'function=.*?'.$this->templateEngineSuffix.'/', trim ($this->fileContent), $matches);
        $matched = $matches[0];
        foreach ($matched as $v) {
            $this->fileContent = str_replace ($v,"",$this->fileContent);
            $valid = preg_replace ('/'.$this->templateEnginePrefix.'function=(.*?)'.$this->templateEngineSuffix.'/', '$1', $v);
            $origin = $this->templateEnginePrefix.$valid.$this->templateEngineSuffix;
            $data = explode ("||",$valid);
            $info = explode ("\\",$data[0]);
            $func = array_pop ($info);
            $class = !empty($info) ? implode ("\\",$info) : "";
            $args = [];

            $result = "";
            $argsInfo = array_slice ($data,1);
            foreach ($argsInfo as $value) {
                $argName = explode ("=",$value);
                if (method_exists ($this,$argName[0])) {
                    $this->callFunction ($argsInfo);

                }
                else {
                    $args[$argName[0]] = $argName[1];
                }
            }
            if (!empty($class)) {
                if (method_exists (new $class,$func)) $result = call_user_func_array ([new $class,$func],$args);
            }
            else{
                if(function_exists ($func)) $result = call_user_func_array ($func, $args);
            }
            if(!empty($this->outputOpinion) && $this->saveResult) $this->data[$this->outputOpinion] = $result;
            if($this->output) echo $result;
        }
    }

    protected function parseIf ()
    {
        if(preg_match_all ('/'.$this->templateEnginePrefix.'if(.*)'.$this->templateEngineSuffix.'([\s\S]+?)'.$this->templateEnginePrefix.'\/if'.$this->templateEngineSuffix.'/', trim ($this->fileContent), $matches)) {
            $matches = $matches[0];
            foreach ($matches as $item) {
                $isMatched = preg_match_all ('/'.$this->templateEnginePrefix.'\/else.*'.$this->templateEngineSuffix.'/', $item);
                if($isMatched == 0) {
                    preg_match_all ('/'.$this->templateEnginePrefix.'if(.*)'.$this->templateEngineSuffix.'([\s\S]+?)'.$this->templateEnginePrefix.'\/if'.$this->templateEngineSuffix.'/', $item, $i);
                    $condition = $i[1][0];
                    $content = $i[2][0];
                    $result = $this->parseIfByManual ($condition,$content,$item,true);
                    if($result) break;
                }
                else{
                    $isMatched = preg_match_all ('/'.$this->templateEnginePrefix.'if(.*)'.$this->templateEngineSuffix.'([\s\S]+?).*'.$this->templateEnginePrefix.'\//', trim ($this->fileContent), $i);
                    if($isMatched != 0) {
                        $condition = $i[1][0];
                        $content = $i[2][0];
                        $result = $this->parseIfByManual ($condition, $content, $item);
                        if($result) break;
                    }

                    $isMatched = preg_match_all ('/elseif(.*)'.$this->templateEngineSuffix.'([\s\S]+?).*'.$this->templateEnginePrefix.'\//', trim ($this->fileContent), $k);
                    if($isMatched != 0){
                        for ($i=0;$i<count($k)-1;$i++){
                            $condition = $k[1][$i];
                            $content = $k[2][$i];
                            $this->parseIfByManual ($condition,$content,$item);
                        }
                    }

                    $isMatched = preg_match_all ('/else'.$this->templateEngineSuffix.'([\s\S]+?).*'.$this->templateEnginePrefix.'\//', trim ($this->fileContent), $i);
                    if($isMatched != 0){
                        $this->parseIfByManual ("",$i[1][0],$item);
                    }
                }
            }
        }
    }
    
    protected function parseForeach ()
    {
        if(preg_match_all ('/'.$this->templateEnginePrefix.'foreach(\s\S*)*'.$this->templateEngineSuffix.'([\s\S]+?)'.$this->templateEnginePrefix.'\/foreach'.$this->templateEngineSuffix.'/',  ($this->fileContent), $matches)) {
                $execute = trim(str_replace(array("\r","\n","\r\n"),"",end($matches)[0]));
            $matches = $matches[0];
            foreach ($matches as $item){
                $this->fileContent = str_replace($item,"",$this->fileContent);
                $conditionNum = preg_match_all ('/'.'\s\S*=[^}\s]*'.'/', $item, $conditions);
                $conditions = $conditions[0];
                $conditionsList = [];
                foreach ($conditions as $condition){
                    $condition = explode("=",$condition);
                    $conditionsList[trim($condition[0])] = trim($condition[1]);
                }
                $loop = \presty\Container::getInstance ()->has($conditionsList["loop"],"",true);
                if(array_key_exists("key",$conditionsList)) $execute = str_replace("$".$conditionsList["key"],"\$key",$execute);
                $execute = str_replace("$".$conditionsList["value"],"\$value",$execute);
                if(array_key_exists("key",$conditionsList)){
                    foreach ($loop as $key => $value){
                        eval($execute);
                    }
                }else{
                    foreach ($loop as $value){
                        eval($execute);
                    }
                }
            }
        }
    }

    protected function parseIfByManual ($condition,$content,$replace,$replaceEmpty = false): bool
    {
            $res = "";
            if($condition == "") {
                $this->fileContent = str_replace ($replace,$content,$this->fileContent);
                return true;
            }
            else {
                eval("\$res = ".$condition.";");
                if ($res) {
                    $this->fileContent = str_replace ($replace, $content, $this->fileContent);
                    return true;
                } elseif ($replaceEmpty) {
                    $this->fileContent = str_replace ($replace, "", $this->fileContent);
                    return false;
                }
            }
            return false;

    }

    protected function parseVars ()
    {
        preg_match_all ('/'.$this->templateEnginePrefix.'\\'.$this->variablePrefix.'.+?'.$this->templateEngineSuffix.'/', trim ($this->fileContent), $matches);
        if(empty($matches[0])) return $this->fileContent;
        $matched = $matches[0];
        foreach ($matched as $v) {
            $valid = preg_replace ('/'.$this->templateEnginePrefix.'\\'.$this->variablePrefix.'(.+?)'.$this->templateEngineSuffix.'/', '$1', $v);
            $origin = "$this->templateEnginePrefix\$".$valid."$this->templateEngineSuffix";
            $data = explode ("||",$valid);
            $args = array_slice ($data,1);
            $v = $data[0];
            preg_match_all ('/\[.+?\]/', $v, $matches);
            preg_match_all ('/\.[^.|}]*/', $v, $matches2);
            $matches = array_merge ($matches[0],$matches2[0]);
            if(count($matches) > 0) {
                foreach ($matches as $value) {
                    if(!is_bool (stripos ($value,"."))) {
                        $tempSubscript = str_replace ("'.$this->templateEngineSuffix.'", "", str_replace ("'.$this->templateEnginePrefix.'\$", "", $value));
                        $tempSubscript = str_replace (".","",$tempSubscript);
                        $this->subscript[] = $tempSubscript;
                        $v = str_replace ($value,"",$v);
                        continue;
                    }
                    $this->subscript[] = preg_replace ('/\[(.+?)\]/', '$1', $value);
                    $v = str_replace ($value,"",$v);
                }
            }
                if (isset($this->data[$v])) {
                    $this->replaceText = $this->data[$v];
                } else {
                    eval("global \$$v;");
                    eval("\$this->replaceText = \"\$$v\";");
                }
            if (count($args) > 0) {
                $this->callFunction ($args);
            }
            if(!empty($this->subscript)){
                foreach ($this->subscript as $item) {
                    $this->replaceText = $this->replaceText[$item];
                }
            }
            $this->subscript = [];
            $this->fileContent = str_replace ($origin, $this->replaceText, $this->fileContent);
        }
    }

    protected function parseConstant()
    {
        preg_match_all ('/'.$this->templateEnginePrefix.'\\'.$this->constantPrefix.'.+?'.$this->templateEngineSuffix.'/', trim ($this->fileContent), $matches);
        if(empty($matches[0])) return $this->fileContent;
        $matched = $matches[0];
        foreach ($matched as $item) {
            $originalText = $item;
            $item = strtoupper (str_replace ($this->templateEnginePrefix.$this->constantPrefix,"",str_replace ($this->templateEngineSuffix,"",$item)));
            $this->fileContent = str_replace ($originalText,constant ($item),$this->fileContent);
        }
    }

    protected function callFunction($args)
    {
        foreach ($args as $key => $value) {
            $detail = explode ("=",$value);
            is_array ($detail[1]) ? $arg = $detail[1] : $arg = [$detail[1]];
            if($detail[0] == "output") call_user_func_array ([$this,$detail[0]], $arg);
            else if (method_exists ($this,$detail[0])) call_user_func_array ([$this,$detail[0]], $arg);
        }
    }

    protected function default($value)
    {
        if (empty($this->replaceText)) {
            $this->replaceText = $value;
        }
    }

    protected function output($output)
    {
        if($output == "true") $this->output = true;
        elseif($output == "false") $this->output = false;
        elseif($output == "both") $this->output = $this->saveResult = true;
        else {
            $this->saveResult = true;
            $this->outputOpinion = $output;
        }
    }
}