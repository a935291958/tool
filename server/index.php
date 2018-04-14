<?php
//标量类型的声明,严格模式，当数据类型不对的时候会报错
declare(strict_types=1);


// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------



// 应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG', TRUE); 

//项目名称
define('APP_NAME','index');

// 定义应用目录
define('APP_PATH','./app/index/');

#define('DIR_SECURE_FILENAME', 'index.html');//默认是index.html

#define('DIR_SECURE_CONTENT', '禁止访问');//索引文件里面的内容

#禁止生成安全文件
define('BUILD_DIR_SECURE', false);

// 引入ThinkPHP入口文件
require './tp3.2/ThinkPHP.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单