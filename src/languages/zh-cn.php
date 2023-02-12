<?php
/*
 * +----------------------------------------------------------------------
 * | Presty Framework
 * +----------------------------------------------------------------------
 * | Copyright (c) 20021~2022 Tomanday All rights reserved.
 * +----------------------------------------------------------------------
 * | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * +----------------------------------------------------------------------
 * | Email: 790455692@qq.com
 * +----------------------------------------------------------------------
 */

return[
    //系统报错警告语句
    'EC100001'                             =>                              '框架核心错误',
    'EC100002'                             =>                              '运行时错误',
    'EC100003'                             =>                              '数据库错误',
    'EC100004'                             =>                              '路由错误',
    'EC100005'                             =>                              '控制器错误',
    'EC100006'                             =>                              '模型错误',
    'EC100007'                             =>                              '视图模板错误',
    'EC100008'                             =>                              '模块错误',
    'EC100009'                             =>                              '未找到指定类',
    'EC100010'                             =>                              '未找到指定函数',
    'EC100011'                             =>                              '提供的参数过少',
    'EC100012'                             =>                              '容器错误',
    'EC100013'                             =>                              '模板引擎错误',
    'EC100014'                             =>                              '类错误',
    'EC100015'                             =>                              '函数错误',
    'EC100016'                             =>                              '资源文件错误',
    'EC100017'                             =>                              '语法错误',
    'EC100018'                             =>                              '致命错误',
    'EC100019'                             =>                              '门面错误',
    'EC100020'                             =>                              '日志错误',
    'EC100021'                             =>                              '中间件错误',
    'EC100022'                             =>                              '系统错误',
    'EC100023'                             =>                              '返回值错误',
    'EC100024'                             =>                              '未找到指定文件',
    'EC100025'                             =>                              '兼容性警告',
    'EC100026'                             =>                              '未找到指定成员函数',
    'EC100027'                             =>                              '未找到指定成员变量',
    'EC100028'                             =>                              '使用静态方法调用非静态函数',
    'EC100029'                             =>                              '不应将数组转化为字符串使用',
    'EC100030'                             =>                              '未找到指定变量',
    'EC100031'                             =>                              '传入非法参数',
    'EC100032'                             =>                              '传入空参数',
    'EC100033'                             =>                              'Url语法错误',

    //错误详细信息
    "framework_init_error"                 =>                               '框架启动错误',
    "controller_empty_return"              =>                               '控制器返回值为空',

    //开发模式信息提示
    'system_operation'                     =>                              '系统运行情况',
    'system'                               =>                              '系统',
    'running_time'                         =>                              '运行时间',
    'throughput'                           =>                              '吞吐率',
    'require_info'                         =>                              '请求信息',
    'server_info'                          =>                              '服务器信息',
    'framework_version'                    =>                              '框架版本',
    'php_version'                          =>                              'PHP版本',
    'zend_version'                         =>                              'Zend版本',
    'client_version'                       =>                              '客户端信息',
    'interface_type'                       =>                              '接口类型',
    'process_id'                           =>                              '进程ID',
    'index_node'                           =>                              '进程节点',
    'memory'                               =>                              '内存',
    'initial_memory'                       =>                              '内存',
    'current_state'                        =>                              '当前状态',
    'total_consumption'                    =>                              '共计消耗',
    'peak_occupancy'                       =>                              '峰值占用',
    'cpu_state'                            =>                              'CPU状态',
    'has_been_run'                         =>                              '框架运行状态',
    'something_wrong'                      =>                              '嗯...似乎有什么地方出错了...',
    'ru_oublock'                           =>                              '块输出操作',
    'ru_inblock'                           =>                              '块输入操作',
    'ru_msgsnd'                            =>                              '发送的message',
    'ru_msgrcv'                            =>                              '收到的message',
    'ru_maxrss'                            =>                              '最大驻留集大小',
    'ru_ixrss'                             =>                              '全部共享内存大小',
    'ru_idrss'                             =>                              '全部非共享内存大小',
    'ru_minflt'                            =>                              '页回收',
    'ru_majflt'                            =>                              '页失效',
    'ru_nsignals'                          =>                              '收到的信号',
    'ru_nvcsw'                             =>                              '主动上下文切换',
    'ru_nivcsw'                            =>                              '被动上下文切换',
    'ru_nswap'                             =>                              '交换区',
    'ru_utime.tv_usec'                     =>                              '用户态时间',
    'ru_utime.tv_sec'                      =>                              '用户态时间',
    'ru_stime.tv_usec'                     =>                              '系统内核时间',
    'ru_stime.tv_sec'                      =>                              '系统内核时间',

    //命令行信息提示
    'cache_clear_start'                    =>                              '正在清理框架缓存...',
    'cache_clear_successful'               =>                              '缓存清理成功',
    'has_been_cleared_files_prefix'        =>                              '本次共清理了',
    'has_been_cleared_files_suffix'        =>                              '个文件/文件夹：',

    //其他提示
    'unable_get_file_content'              =>                              '未知错误：无法获取到该文件内容'
    ];