//app.js
var com = require('/pages/common/common.js');
var cfg = require('config.js');
//获取网络状态
const getNetwork = com.getNetwork();
const showMsg = com.showMsg;
App({
  onLaunch: function () {
	 	//获取小程序必要的配置
		wx.request({
			url: cfg.getXcxCfg,
			data:{'type':1},
			success:function(res){
				let reqRes = res.data.res;
				let reqData = JSON.parse(res.data.data);
				if (reqRes){
					wx.setStorageSync('xcxConfig', reqData);
				}
			},
			fail:function(){
				//showMsg('');
			}
		})
  }
  
})