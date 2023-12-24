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

use presty\Facade\Template;

class DebugMode
{

    public function format_bytes($size, $delimiter = ''): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        if (is_numeric($size))
            $size = (int) $size;
        else
            return "0KB";
        for ($i = 0; $size >= 1024 && $i < 5; $i++)
            $size /= 1024;
        return round((float) $size, 2) . $delimiter . " " . $units[$i];
    }

    public function output()
    {
        $data = $this->print();
        echo ('<div id="presty_trace_open" style="height: 30px;float: right;text-align: right;overflow: hidden;position: fixed;bottom: 0px;right: 10px;color: rgb(0, 0, 0);line-height: 30px;cursor: pointer;display: block;border-radius: 5px 5px 0px 0px;box-shadow: #0000002e -1px -1px 4px 2px;background-color: #fff;">
    <div style="color: #262626db;padding:0 6px;float:right;line-height:30px;font-size:14px;">' . round(number_format(microtime(true) - SYSTEM_START_TIME, 10, '.', ''), 6) . 's </div>
    <img width="30" style="" title="ShowPageTrace" src="/imgs/favicon.ico">
</div>

<div id="presty_trace" style="position: fixed;bottom:0;right:0;font-size:14px;width:100%;z-index: 999999;color: #000;text-align:left;font-family:"微软雅黑";">
    <div id="presty_trace_tab" style="display: none; background: white; margin: 0px; height: 250px;">
        <div id="presty_trace_tab_tit" style="height:30px;padding: 6px 12px 0;border-bottom:1px solid #ececec;border-top:1px solid #ececec;font-size:16px">
            <span style="color: rgb(0, 0, 0); padding-right: 12px; height: 30px; line-height: 30px; display: inline-block; margin-right: 3px; cursor: pointer; font-weight: 700;">基本</span>
            <span style="color: rgb(153, 153, 153); padding-right: 12px; height: 30px; line-height: 30px; display: inline-block; margin-right: 3px; cursor: pointer; font-weight: 700;">文件</span>
            <span style="color: rgb(153, 153, 153); padding-right: 12px; height: 30px; line-height: 30px; display: inline-block; margin-right: 3px; cursor: pointer; font-weight: 700;">状态</span>
            <span style="color: rgb(153, 153, 153); padding-right: 12px; height: 30px; line-height: 30px; display: inline-block; margin-right: 3px; cursor: pointer; font-weight: 700;">错误</span>
            <span style="color: rgb(153, 153, 153); padding-right: 12px; height: 30px; line-height: 30px; display: inline-block; margin-right: 3px; cursor: pointer; font-weight: 700;">SQL</span>
            <span style="color: rgb(153, 153, 153); padding-right: 12px; height: 30px; line-height: 30px; display: inline-block; margin-right: 3px; cursor: pointer; font-weight: 700;">系统</span>
        </div>
        <div id="presty_trace_tab_cont" style="overflow:auto;height:212px;padding:0;line-height: 24px">
            <div style="display: block;">
                <ol style="padding: 0; margin:0">');
        echo '<li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px;font-weight:600;">本项展示了系统重要信息</li>';
        if(!empty($data['total'])){
            foreach ($data['total'] as $value) {
                echo "<li style=\"border-bottom:1px solid #EEE;font-size:14px;padding:0 12px\">$value</li>";
            }
        }
        else{
            echo '<li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px;font-weight:600;color:red;">运行时错误：未获取到任何系统信息，请检查框架资源完整性！</li>';
        }
        echo ('</ol>
            </div>
            <div style="display: none;">
                <ol style="padding: 0; margin:0">
                    ');
        echo '<li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px;font-weight:600;">本项展示了页面渲染前所有被引入的PHP文件，按引入时间从先到后排序</li>';
        if (!empty(get_included_files())) {
            foreach (get_included_files() as $file) {
                echo '<li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">' . $file . '  ( ' . $this->format_bytes(filesize($file)) . ' )  </li>';
            }
        }
        else{
            echo '<li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px;font-weight:600;color:red;">致命错误：已引入文件列表为空，请检查PHP运行状态和日志！</li>';
        }
        echo ('</ol>
            </div>
            <div style="display: none;">
                <ol style="padding: 0; margin:0">');
        echo '<li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px;font-weight:600;">本项展示了框架各机制的运行状态和启动时间</li>';
        if (!empty($data['flow'])) {
            foreach ($data['flow'] as $flow) {
                echo '<li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">'. $flow .'</li>';
            }
        } else {
            echo '<li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px;font-weight:600;color:red;">运行时错误：未记录到任何运行状态，请检查框架资源完整性！</li>';
        }
        echo ('
                </ol>
            </div>
            <div style="display: none;">
                <ol style="padding: 0; margin:0">
                <li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px;font-weight:600;">本项记录了框架运行时产生的所有错误与异常</li>
                <li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px;color:grey;">功能正在开发中，敬请期待</li>
                </ol>
            </div>
            <div style="display: none;">
                <ol style="padding: 0; margin:0">
                    ');
        echo '<li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px;font-weight:600;">本项记录了所有的数据库操作</li>';
        if (!empty($data['sql'])) {
            foreach ($data['sql'] as $sql) {
                echo '<li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">' . $sql . '</li>';
            }
        } else {
            echo '<li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">无任何SQL操作</li>';
        }
        echo ('</ol>
            </div>
            <div style="display: none;">
                <ol style="padding: 0; margin:0">');
        echo '<li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px;font-weight:600;">本项展示了系统环境配置</li>';
        if(!empty($data['system'])){
            foreach ($data['system'] as $system) {
                echo '<li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">' . $system . '</li>';
            }
        }
        else{
            echo '<li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px;font-weight:600;color:red;">致命错误：无法获取系统信息，请检查PHP与操作系统的运行状态和日志！</li>';
        }
        echo ('
                </ol>
            </div>
        </div>
    </div>
    <div id="presty_trace_close" style="display: block; text-align: right; height: 15px; position: absolute; top: 10px; right: 12px; cursor: pointer;"><img style="vertical-align:top;" src="data:image/gif;base64,R0lGODlhDwAPAJEAAAAAAAMDA////wAAACH/C1hNUCBEYXRhWE1QPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS4wLWMwNjAgNjEuMTM0Nzc3LCAyMDEwLzAyLzEyLTE3OjMyOjAwICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M1IFdpbmRvd3MiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MUQxMjc1MUJCQUJDMTFFMTk0OUVGRjc3QzU4RURFNkEiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MUQxMjc1MUNCQUJDMTFFMTk0OUVGRjc3QzU4RURFNkEiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDoxRDEyNzUxOUJBQkMxMUUxOTQ5RUZGNzdDNThFREU2QSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDoxRDEyNzUxQUJBQkMxMUUxOTQ5RUZGNzdDNThFREU2QSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PgH//v38+/r5+Pf29fTz8vHw7+7t7Ovq6ejn5uXk4+Lh4N/e3dzb2tnY19bV1NPS0dDPzs3My8rJyMfGxcTDwsHAv769vLu6ubi3trW0s7KxsK+urayrqqmop6alpKOioaCfnp2cm5qZmJeWlZSTkpGQj46NjIuKiYiHhoWEg4KBgH9+fXx7enl4d3Z1dHNycXBvbm1sa2ppaGdmZWRjYmFgX15dXFtaWVhXVlVUU1JRUE9OTUxLSklIR0ZFRENCQUA/Pj08Ozo5ODc2NTQzMjEwLy4tLCsqKSgnJiUkIyIhIB8eHRwbGhkYFxYVFBMSERAPDg0MCwoJCAcGBQQDAgEAACH5BAAAAAAALAAAAAAPAA8AAAIdjI6JZqotoJPR1fnsgRR3C2jZl3Ai9aWZZooV+RQAOw=="></div>
</div>
<script type="text/javascript">
    (function(){
        var tab_tit  = document.getElementById("presty_trace_tab_tit").getElementsByTagName("span");
        var tab_cont = document.getElementById("presty_trace_tab_cont").getElementsByTagName("div");
        var open     = document.getElementById("presty_trace_open");
        var close    = document.getElementById("presty_trace_close").children[0];
        var trace    = document.getElementById("presty_trace_tab");
        var cookie   = document.cookie.match(/presty_show_running_trace=(\d\|\d)/);
        var history  = (cookie && typeof cookie[1] != "undefined" && cookie[1].split("|")) || [0,0];
        open.onclick = function(){
            trace.style.display = "block";
            this.style.display = "none";
            close.parentNode.style.display = "block";
            history[0] = 1;
            document.cookie = "presty_show_running_trace="+history.join("|")
        }
        close.onclick = function(){
            trace.style.display = "none";
            this.parentNode.style.display = "none";
            open.style.display = "block";
            history[0] = 0;
            document.cookie = "presty_show_running_trace="+history.join("|")
        }
        for(var i = 0; i < tab_tit.length; i++){
            tab_tit[i].onclick = (function(i){
                return function(){
                    for(var j = 0; j < tab_cont.length; j++){
                        tab_cont[j].style.display = "none";
                        tab_tit[j].style.color = "#999";
                    }
tab_cont[i].style.display = "block";
tab_tit[i].style.color = "#000";
history[1] = i;
document.cookie = "presty_show_running_trace="+history.join("|")
}
})(i)
        }
        parseInt(history[0]) && open.click();
        tab_tit[history[1]].click();
    })();
</script>');
        // echo Template::getTemplateContent(\presty\Container::getInstance ()->newInstance("config")->get("env.running_trace_template","RunningTrace"));
    }

    public static function getRenderContent($path, $data = [])
    {
        ob_start();
        extract($data);
        include $path;
        return ob_get_clean();
    }

    public function print()
    {
        $hasBeenRun = \presty\Container::getInstance()->get("hasBeenRun");
        $data = [];
        $runtime = number_format(microtime(true) - SYSTEM_START_TIME, 10, '.', '') * 1000;
        $reqs = $runtime > 0 ? number_format(1 / $runtime, 2) : '∞';
        $mem = number_format((memory_get_usage() - SYSTEM_START_MEMORY) / 1024, 2);
        if (isset($_SERVER['HTTP_HOST'])) {
            $uri = $_SERVER['SERVER_PROTOCOL'] . ' ' . $_SERVER['REQUEST_METHOD'] . ' : ' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        } else {
            $uri = 'cmd:' . implode(' ', $_SERVER['argv']);
        }
        $data['total'] = [
            \presty\Container::getInstance()->makeAndSave("lang")->lang()['running_time'] . "：$runtime ms " . \presty\Container::getInstance()->makeAndSave("lang")->lang()['throughput'] . "：" . $reqs . " req/s",
            \presty\Container::getInstance()->makeAndSave("lang")->lang()['require_info'] . "：" . date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']) . ' ' . $uri,
            \presty\Container::getInstance()->makeAndSave("lang")->lang()['server_info'] . "：" . php_uname("a"),
            \presty\Container::getInstance()->makeAndSave("lang")->lang()['framework_version'] . "：" . VERSION,
            \presty\Container::getInstance()->makeAndSave("lang")->lang()['php_version'] . "：" . phpversion(),
            \presty\Container::getInstance()->makeAndSave("lang")->lang()['zend_version'] . "：" . zend_version(),
            \presty\Container::getInstance()->makeAndSave("lang")->lang()['client_version'] . "：" . ($_SERVER['HTTP_USER_AGENT'] ?? ""),
            \presty\Container::getInstance()->makeAndSave("lang")->lang()['interface_type'] . "：" . php_sapi_name(),
            \presty\Container::getInstance()->makeAndSave("lang")->lang()['process_id'] . "：" . getmypid(),
            \presty\Container::getInstance()->makeAndSave("lang")->lang()['index_node'] . "：" . getmyinode(),
        ];
        if (session_id()) {
            $data['total'][] = "SessionID：" . session_id();
        } else {
            $data['total'][] = "SessionID：NULL";
        }
        $data['system'] = ["-----------" . \presty\Container::getInstance()->makeAndSave("lang")->lang()['memory'] . "-----------",
            \presty\Container::getInstance()->makeAndSave("lang")->lang()['initial_memory'] . "：" . $this->format_bytes(SYSTEM_START_MEMORY),
            \presty\Container::getInstance()->makeAndSave("lang")->lang()['current_state'] . "：" . $this->format_bytes(memory_get_usage()),
            \presty\Container::getInstance()->makeAndSave("lang")->lang()['total_consumption'] . "：" . $this->format_bytes($mem),
            \presty\Container::getInstance()->makeAndSave("lang")->lang()['peak_occupancy'] . "：" . $this->format_bytes(memory_get_peak_usage()),
            "------------CPU-----------"];
        foreach (getrusage() as $key => $value) {
            $data['system'][] = \presty\Container::getInstance()->makeAndSave("lang")->lang()[$key] . " $key ：$value";
        }
        $data['flow'] = $hasBeenRun;
        $data['sql'] = \presty\Container::getInstance()->make('database')->getQueryRecords();
        return $data;
    }
}