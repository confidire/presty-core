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

namespace presty;

/**
 * 验证器类
 */
class Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [];

    /**
     * 未经处理的验证规则
     * @var array
     */
    protected $ruleString = [];

    /**
     * 表达式别名
     * @var string[]
     */
    protected $expressionAlias = [
        '>' => 'greaterThan',
        '>=' => 'equalGreaterThan',
        '<' => 'lessThan',
        '<=' => 'equalLessThan',
        '=' => 'equal',
        'same' => 'equal',
        'eq' => 'equal',
        'gt' => 'greaterThan',
        'egt' => 'equalGreaterThan',
        'lt' => 'lessThan',
        'elt' => 'equalLessThan',
        'num' => 'number',
        'bool' => 'boolean'
        ];

    /**
     * 内置正则验证式
     * @var string[]
     */
    protected $defaultRegex = [
        'letter'       => '/^[A-Za-z]+$/',
        'number'    => '/^[0-9]+$/',
        'letterNum'    => '/^[A-Za-z0-9]+$/',
        'letterDash'   => '/^[A-Za-z0-9\-\_]+$/',
        'zn'         => '/^[\x{4e00}-\x{9fa5}\x{9fa6}-\x{9fef}\x{3400}-\x{4db5}\x{20000}-\x{2ebe0}]+$/u',
        'znLetter'    => '/^[\x{4e00}-\x{9fa5}\x{9fa6}-\x{9fef}\x{3400}-\x{4db5}\x{20000}-\x{2ebe0}a-zA-Z]+$/u',
        'znLetterNum' => '/^[\x{4e00}-\x{9fa5}\x{9fa6}-\x{9fef}\x{3400}-\x{4db5}\x{20000}-\x{2ebe0}a-zA-Z0-9]+$/u',
        'znDash'     => '/^[\x{4e00}-\x{9fa5}\x{9fa6}-\x{9fef}\x{3400}-\x{4db5}\x{20000}-\x{2ebe0}a-zA-Z0-9\_\-]+$/u',
        'phone'      => '/^1[3-9]\d{9}$/',
        'idCard'      => '/(^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$)|(^[1-9]\d{5}\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}$)/',
        'postalCode'         => '/\d{6}/',
    ];

    /**
     * 内置filter验证常量
     * @var array
     */
    protected $filter = [
        'email'   => FILTER_VALIDATE_EMAIL,
        'ip'      => [FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6],
        'int'     => FILTER_VALIDATE_INT,
        'url'     => FILTER_VALIDATE_URL,
        'macCode' => FILTER_VALIDATE_MAC,
        'float'   => FILTER_VALIDATE_FLOAT,
    ];

    /**
     * 附加验证规则列表
     * @var array
     */
    protected $append = [];

    /**
     * 移除的验证规则列表
     * @var array
     */
    protected $remove = [];

    /**
     * 构造器
     */
    public function __construct () {
        if(!empty($this->ruleString)) $this->rule = $this->parseRule ($this->ruleString);
    }

    /**
     * 检查字段
     * @param $data
     * @param array $rule
     * @param bool $clearAppendRule
     * @return bool|mixed
     */
    public function check ($data, array $rule = [], bool $clearAppendRule = false)
    {
        $result = true;

        if(!empty($rule)) $this->rule($rule);

        $data = $this->arrayDimensionalityReduction ($data); // 多维数组降维

        foreach ($this->append as $key => $v) {
            if (!isset($rule[$key])) {
                $rule[$key] = $v;
                if($clearAppendRule) unset($this->append[$key]);
            }
        }

        foreach ($this->remove as $key => $v) {
            if (!isset($rule[$key])) {
                unset($rule[$key]);
                if($clearAppendRule) unset($this->remove[$key]);
            }
        }

        foreach ($this->rule as $key => $value) {
            foreach ($value as $item) {
                if(is_string ($item)) $result = call_user_func_array ([$this,$item],[$data,[$key => $item]]);
                elseif(is_array ($item)) $result = call_user_func_array ([$this,array_search (end($item),$item)],[$data,[$key => end ($item)]]);
                if(!$result) return $result;
            }
        }
        return $result;
    }

    /**
     * 验证后清空附加/删除的规则
     * @param $data
     * @param $rule
     * @return bool|mixed
     */
    public function clearCheck ($data, $rule = [])
    {
        return $this->check ($data,$rule,true);
    }

    /**
     * 定义验证规则
     * @param $rule
     * @return $this
     */
    public function rule ($rule): Validate
    {
        $this->rule = $this->parseRule ($rule);
        return $this;
    }

    /**
     * 获取验证规则
     * @return array
     */
    public function getRule (): array
    {
        return $this->rule;
    }

    /**
     * 解析验证规则
     * @param $rule
     * @return array
     */
    protected function parseRule($rule): array
    {
        $globalRule = [];
        foreach ($rule as $key => $item) {
            $item = explode ("|",$item);
            foreach ($item as $value) {
                if(is_bool (stripos ($value,":"))){
                    if(isset ($this->expressionAlias[$value])) $value = $this->expressionAlias[$value];
                    $globalRule[$key][] = $value;
                }
                else{
                    $value = explode (":",$value);
                    if(isset ($this->expressionAlias[$value[0]])) $value[0] = $this->expressionAlias[$value[0]];
                    if(is_bool (stripos ($value[1],","))) $globalRule[$key][] = [$value[0] => $value[1]];
                    else {
                        $value[1] = explode (",",$value[1]);
                        $globalRule[$key][] = [$value[0] => $value[1]];
                    }
                }
            }
        }
        return $globalRule;
    }

    /**
     * 附加规则
     * @param $rule
     * @return $this
     */
    public function append ($rule): Validate
    {
        $this->append = $this->parseRule ($rule);
        return $this;
    }

    /**
     * 删除规则
     * @param $name
     * @param $rule
     * @return $this
     */
    public function remove($name, $rule = null): Validate
    {
        if (is_array($name)) {
            foreach ($name as $key => $rule) {
                if (is_int($key)) {
                    $this->remove($rule);
                } else {
                    $this->remove($key, $rule);
                }
            }
        } else {
            if (is_string($rule)) {
                $rule = $this->parseRule ($rule);
            }

            $this->remove[$name] = $rule;
        }
        return $this;
    }

    /**
     * 字段需要值
     * @param $data
     * @param $rule
     * @return bool
     */
    public function require ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        return isset($data[$name]);
    }

    /**
     * 字段不需要值
     * @param $data
     * @param $rule
     * @return bool
     */
    public function notRequire ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        return !isset($data[$name]);
    }

    /**
     * 字段等于值
     * @param $data
     * @param $rule
     * @return bool
     */
    public function equal ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        if(!isset($data[$name])) return true;
        return $data[$name] == $rule[$name];
    }

    /**
     * 字段大于值
     * @param $data
     * @param $rule
     * @return bool
     */
    public function greaterThan ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        if(!isset($data[$name])) return true;
        return $data[$name] > $rule[$name];
    }

    /**
     * 字段大于等于值
     * @param $data
     * @param $rule
     * @return bool
     */
    public function equalGreaterThan ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        if(!isset($data[$name])) return true;
        return $data[$name] >= $rule[$name];
    }

    /**
     * 字段小于值
     * @param $data
     * @param $rule
     * @return bool
     */
    public function lessThan ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        if(!isset($data[$name])) return true;
        return $data[$name] < $rule[$name];
    }

    /**
     * 字段小于等于值
     * @param $data
     * @param $rule
     * @return bool
     */
    public function equalLessThan ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        if(!isset($data[$name])) return true;
        return $data[$name] >= $rule[$name];
    }

    /**
     * 数字字段在某区间
     * @param $data
     * @param $rule
     * @return bool
     */
    public function between ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        if(!isset($data[$name])) return true;
        if(is_numeric ($data[$name])) {
            $data[$name] = intval ($data[$name]);
            if($data[$name] >= $rule[$name][0] && $data[$name] <= $rule[$name][1]) return true;
        }
        return false;
    }

    /**
     * 数字字段不在某区间
     * @param $data
     * @param $rule
     * @return bool
     */
    public function notBetween ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        if(!isset($data[$name])) return true;
        if(is_numeric ($data[$name])) {
            $data[$name] = intval ($data[$name]);
            if($data[$name] >= $rule[$name][0] && $data[$name] <= $rule[$name][1]) return false;
        }
        return true;
    }

    /**
     * 字段不存在或存在且有值
     * @param $data
     * @param $rule
     * @return bool
     */
    public function have ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        $have = isset($data[$name]) && !empty($data[$name]);
        return !isset($data[$name]) || $have;
    }

    /**
     * 字段不存在或存在且无值
     * @param $data
     * @param $rule
     * @return bool
     */
    public function notHave ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        $have = isset($data[$name]) && !empty($data[$name]);
        return !isset($data[$name]) || !$have;
    }

    /**
     * 字段为整数
     * @param $data
     * @param $rule
     * @return bool
     */
    public function int ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        return filter_var($data[$name],$this->filter["int"]);
    }

    /**
     * 字段为浮点数
     * @param $data
     * @param $rule
     * @return bool
     */
    public function float ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        return filter_var($data[$name],$this->filter["float"]);
    }

    /**
     * 字段为布尔类型
     * @param $data
     * @param $rule
     * @return bool
     */
    public function boolean ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        return is_bool($data[$name]);
    }

    /**
     * 字段为字符串
     * @param $data
     * @param $rule
     * @return bool
     */
    public function string ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        return is_string($data[$name]);
    }

    /**
     * 字段为数字（数字类型或字符串类型的数字）
     * @param $data
     * @param $rule
     * @return bool
     */
    public function numeric ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        return is_numeric($data[$name]);
    }

    /**
     * 字段全为小写
     * @param $data
     * @param $rule
     * @return bool
     */
    public function lower ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        if(!isset($data[$name])) return true;
        return ctype_lower($data);
    }

    /**
     * 字段全为大写
     * @param $data
     * @param $rule
     * @return bool
     */
    public function upper ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        if(!isset($data[$name])) return true;
        return ctype_upper($data);
    }

    /**
     * 字段全为空白字符
     * @param $data
     * @param $rule
     * @return bool
     */
    public function space ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        if(!isset($data[$name])) return true;
        return ctype_space($data);
    }

    /**
     * 字段全为除空格外的可打印字符
     * @param $data
     * @param $rule
     * @return bool
     */
    public function graph ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        if(!isset($data[$name])) return true;
        return ctype_graph($data);
    }

    /**
     * 字段全为控制字符
     * @param $data
     * @param $rule
     * @return bool
     */
    public function cntrl ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        if(!isset($data[$name])) return true;
        return ctype_cntrl($data);
    }

    /**
     * 字段全为可打印字符
     * @param $data
     * @param $rule
     * @return bool
     */
    public function printable ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        if(!isset($data[$name])) return true;
        return ctype_print($data);
    }

    /**
     * 字段全为标点符号
     * @param $data
     * @param $rule
     * @return bool
     */
    public function punctuation ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        if(!isset($data[$name])) return true;
        return ctype_punct($data);
    }

    /**
     * 字段全为十六进制字符
     * @param $data
     * @param $rule
     * @return bool
     */
    public function hex ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        if(!isset($data[$name])) return true;
        return ctype_xdigit($data);
    }

    /**
     * 字段全为二进制字符
     * @param $data
     * @param $rule
     * @return bool
     */
    public function binary ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        if(!isset($data[$name])) return true;
        $dictionary = [1,0];
        $data = array_values (array_unique(str_split ($data[$name],1)));
        return array_intersect($data,$dictionary) == $dictionary;
    }

    /**
     * 数字字段最大值
     * @param $data
     * @param $rule
     * @return bool
     */
    public function max ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        if(!isset($data[$name])) return true;
        if(is_numeric ($data[$name])) {
            $data[$name] = intval ($data[$name]);
            if($data[$name] <= $rule[$name][0]) return true;
        }
        return false;
    }

    /**
     * 数字字段最小值
     * @param $data
     * @param $rule
     * @return bool
     */
    public function min ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        if(!isset($data[$name])) return true;
        if(is_numeric ($data[$name])) {
            $data[$name] = intval ($data[$name]);
            if($data[$name] >= $rule[$name][0]) return true;
        }
        return false;
    }

    /**
     * 日期字段等于值
     * @param $data
     * @param $rule
     * @return bool
     */
    public function date ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        if(!isset($data[$name])) return true;
        return strtotime($data[$name] !== false);
    }

    /**
     * 字段为邮箱
     * @param $data
     * @param $rule
     * @return bool
     */
    public function email ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        if(!isset($data[$name])) return true;
        return filter_var ($data[$name],$this->filter["email"]);
    }

    /**
     * 字段为IP地址
     * @param $data
     * @param $rule
     * @return bool
     */
    public function ip ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        if(!isset($data[$name])) return true;
        return filter_var ($data[$name],$this->filter["ip"]);
    }

    /**
     * 字段为URL地址
     * @param $data
     * @param $rule
     * @return bool
     */
    public function url ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        if(!isset($data[$name])) return true;
        return filter_var ($data[$name],$this->filter["url"]);
    }

    /**
     * 字段为Mac地址
     * @param $data
     * @param $rule
     * @return bool
     */
    public function macCode ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        if(!isset($data[$name])) return true;
        return filter_var ($data[$name],$this->filter["macCode"]);
    }

    /**
     * 字段为有效的URL地址
     * @param $data
     * @param $rule
     * @return bool
     */
    public function activeUrl ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        if(!isset($data[$name])) return true;
        return checkdnsrr ($data[$name]);
    }

    /**
     * 字段在指定范围内
     * @param $data
     * @param $rule
     * @return bool
     */
    public function in ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        if(!isset($data[$name])) return true;
        if(is_numeric ($data[$name])) {
            $data[$name] = intval ($data[$name]);
            if(in_array ($data[$name],$rule[$name])) return true;
        }
        return false;
    }

    /**
     * 字段不在指定范围内
     * @param $data
     * @param $rule
     * @return bool
     */
    public function notIn ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        if(!isset($data[$name])) return true;
        if(!in_array ($data[$name],$rule[$name])) return true;
        return false;
    }

    /**
     * 字符串字段长度等于值
     * @param $data
     * @param $rule
     * @return bool
     */
    public function length ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        if(!isset($data[$name])) return true;
        if(is_string ($data[$name])) {
            if(strlen ($data[$name]) == $rule[$name]) return true;
        }
        return false;
    }

    /**
     * 字符串字段在指定区间内
     * @param $data
     * @param $rule
     * @return bool
     */
    public function lengthBetween ($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        if(!isset($data[$name])) return true;
        if(is_numeric ($data[$name])) {
            $data[$name] = intval ($data[$name]);
            if(strlen ($data[$name]) >= $rule[$name][0] && strlen ($data[$name]) <= $rule[$name][1]) return true;
        }
        return false;
    }

    /**
     * 日期字段小于值
     * @param $data
     * @param $rule
     * @return bool
     */
    public function before($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        if(!isset($data[$name])) return true;
        if(strtotime($data[$name]) <= strtotime($rule[$name])) return true;
        return false;
    }

    /**
     * 日期字段大于值
     * @param $data
     * @param $rule
     * @return bool
     */
    public function after($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        if(!isset($data[$name])) return true;
        if(strtotime($data[$name]) >= strtotime($rule[$name])) return true;
        return false;
    }

    /**
     * 日期字段在指定范围内
     * @param $data
     * @param $rule
     * @return bool
     */
    public function dateBetween($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        if(!isset($data[$name])) return true;
        if(strtotime($data[$name]) >= strtotime($rule[$name][0]) && strtotime($data[$name]) <= strtotime($rule[$name][1])) return true;
        return false;
    }

    /**
     * 字段为指定许可IP
     * @param $data
     * @param $rule
     * @return bool
     */
    public function allowIp($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        if(!isset($data[$name])) return true;
        if(in_array($data[$name], $rule[$name])) return true;
        return false;
    }

    /**
     * 字段为指定禁止IP
     * @param $data
     * @param $rule
     * @return bool
     */
    public function banedIp($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        if(!isset($data[$name])) return true;
        if(in_array($data[$name], $rule[$name])) return true;
        return false;
    }

    /**
     * 字段与指定正则表达式匹配
     * @param $data
     * @param $rule
     * @return bool
     */
    public function regex($data, $rule = []): bool
    {
        $name = array_search (end ($rule),$rule);
        if(!isset($data[$name])) return true;
        if (isset($this->regex[$rule[$name]])) {
            $rule = $this->regex[$rule[$name]];
        } elseif (isset($this->defaultRegex[$rule[$name]])) {
            $rule = $this->defaultRegex[$rule[$name]];
        }

        if (is_string($rule[$name]) && 0 !== strpos($rule[$name], '/') && !preg_match('/\/[imsU]{0,4}$/', $rule[$name])) {
            // 不是正则表达式则两端补上/
            $rule[$name] = '/^' . $rule[$name] . '$/';
        }
        return is_scalar($data[$name]) && 1 === preg_match($rule[$name], (string) $data[$name]);
    }

    /**
     * 多维数组降维
     * @param $array
     * @return mixed
     */
    public function arrayDimensionalityReduction ($array)
    {
        if(count($array) == 1 && is_array (end($array))) return $this->arrayDimensionalityReduction (end($array));
        return $array;
    }

    /**
     * __call()
     * @param $name
     * @param $arguments
     * @return bool
     */
    public function __call ($name, $arguments)
    {
        $data = $arguments[0];
        $rule = $arguments[1];
        $name = array_search (end ($rule),$rule);
        if(!isset($data[$name])) return true;
        if(isset($this->defaultRegex[$rule[$name]])) return preg_match($this->defaultRegex[$rule[$name]],$data[$name]) != 0;
        return true;
    }
}