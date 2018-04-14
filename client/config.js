
/**
 * 小程序配置文件
 */


//主机域名
//var wwwroot = 'https://tool.bearshop.cc'; 
var wwwroot = 'http://bdapiimg.com';
var host = wwwroot + '/index.php';

//添加用户数据
var addUser = host + '/Home/Index/addUser';
//获取配置的API
var getXcxCfg = host + '/Home/Index/getXcxCfg';
//API接口
var api = host + '/Home/Index/api';
//天气图片URL
var weathercn = wwwroot + '/Public/weathercn/';
//建议表单提交URL
var queUrl = host + '/Home/Index/queUrl'

var config = {
	wwwroot,
	addUser,
	getXcxCfg,
	api,
	weathercn,
	queUrl

};

module.exports = config