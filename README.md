# StartPHP v0.7-Beta

欢迎使用 StartPHP v0.7,相比于以往的版本，此次版本更新更加注重框架整体结构和规范化，我们重构了大部分底层结构，并将在未来的几个版本中持续优化框架运行逻辑。

[框架官网](https://startphp.catcatalpa.com)
[开发文档](https://doc.startphp.catcatalpa.com)
[开源地址](https://github.com/catcatalpa/startphp)
[开源协议](https://github.com/catcatalpa/StartPHP/blob/master/LICENSE)

## 在下载本次更新前，您需要了解以下重要事项：

- StartPHP v0.7 对于 PHP 运行环境要求是 7.3 或以上。
- 若您单独更新过框架核心文件版本，请注意您当前项目根目录下的`README.md`文件可能并不是适合您框架版本的最新版，请以`vendor/catcatalpa/core`目录下的`README.md`文件为主。
- 我们在v0.7版本中对StartPHP的框架结构和运行逻辑进行了**完全**的重构和完善，相较于v0.6版本，不论是框架目录结构、启动引导项、底层运行逻辑、浏览器本地缓存技术（Session、Cookie、LocalStorage）、自动加载机制、系统函数操作逻辑、应用请求流程（请求与响应）、命名规范等重要机制都有了非常大的变化，因此，我们并不推荐您在v0.6版本的基础上通过替换系统文件的方法更新框架版本，建议您在备份好网站数据后，清空或重建项目目录，并将v0.7版本资源解压进目录中，再手动还原备份，避免出现数据丢失、网站无法访问等重大损失。
---

## StartPHP v0.7-220901-Beta 更新日志如下

### *[ 说明 ]*

- 由于v0.7更新内容过多，为合理安排更新进程，部分原计划将于本版本上线的功能将不会上线正式版，不便之处敬请谅解。

### *[ 系统 ]*

- 新增`Cookie`机制
- 重写了入口文件部分功能
- 重构了框架底层运行逻辑
- 重构了框架初始化流程
- ~~移除原核心文件目录`core`，核心文件调整至`startphp`根目录中~~（该项已在v0.7-220901-Beta的更新中失效）
- 完善了`Facade`操作机制
- 完善了`容器`机制
- 将框架主体与核心部分分离，以更好的支持使用composer无缝更新框架
- 使用composer下载框架主体资源时将自动补齐框架核心文件
- 将框架核心部分与composer包结合，移除了`startphp`目录
- 重构了`Session`操作机制
- 重构部分系统文件的操作逻辑
- 重构了环境变量机制，将系统环境变量与部分次要配置项分离
- 实现了惰性加载，降低了运行占用消耗
- 部分非必需系统文件将不会在初始化时全部载入
- 修复了部分系统变量命名中的拼写错误
- 统一了文件版权声明格式
- 修复了部分已知Bug

### *[ 视图 & 模板引擎 ]*

- 新增`响应`机制
- 新增了`Html`渲染格式
- 新增了`Json`渲染格式
- 新增了`Jsonp`渲染格式
- 新增自定义模板引擎的功能
- 新增`页面锁定`机制，在锁定后的第一次渲染前，页面内容都无法被更改
- 新增反XSS注入机制
- 视图基类下的`assign`方法可批量赋值
- 视图与模板引擎解耦合
- 重构了模板引擎部分已有操作逻辑
- 完善了模板引擎部分机制
- 修复了无法输出空页面的问题
- 修复了控制器返回的字符串无法被正常输出的bug

### *[ 路由解析 ]*

- 新增`请求`机制
- 新增全局路由规则操作对象
- 引进`路由解析引擎`概念
- 将路由解析引擎与路由处理模块解耦合
- 支持自定义路由解析引擎
- 重构了框架自动加载机制
- 重构了路由别名机制
- 优化了静态资源访问反馈
- 修复了路由别名定义全静态Url时无法被正常解析的Bug
- 修复了无法定义首页路由别名的Bug

### *[ 控制器 ]*

- 控制器方法不存在时系统将优先尝试调用`__call()`方法
- 完善了控制器基类

### *[ 模型 ]*

- 新增`模型`基类
- 通过模型基类可直接调用指定Class的模型文件

### *[ 调试模式 ]*

- 调试模式关闭时报错页面将不会显示错误详情信息
- 调整了部分内容的输出样式
- `CPU信息`一项的输出信息已支持多语言显示
- 新增输出`客户端信息`项信息

### *[ Facade (门面) ]*

- 完善了Facade机制的运行逻辑
- 多个系统机制正式接入Facade，将支持使用Facade格式调用
- 修复了部分因机制错误导致的遗留问题

### *[ 自动加载 & 命名规范 ]*

- 完善了对模型文件Class的解析机制
- 重构了控制器与模型的命名空间命名规范
- `controller`控制器文件的命名规范更新为`顶级空间\...\应用名\controller`
- `model`模型文件的命名规范更新为`顶级空间\...\应用名\model`
- 自动加载将根据类名引入文件，请在命名时将文件中主要类的类名与文件名保持完全一致

> 以下内容预计将在后续版本中完善。
> 
- 更加安全的验证机制
- 符合更多`PSR`系列规范
- 自定义插件拓展系统
- 更多待开发功能将伴随此日志的更新不断补充

## 特别感谢

| 项目名 | 作者  | 开源协议 | 开源地址 | 代码用途 | 使用程度 |
|-----|:---:|:----:|:----:|:----:|:----:|

