//获取应用实例

const common = require('../common/common.js');
const validation = require('../common/validation.js');
const config = require('../../config.js');
const c = common.c;
const showMsg = common.showMsg;//提示消息
const api = config.api;//API接口
Component({
	properties: {
		// 这里定义了innerText属性，属性值可以在组件使用时指定
		proid: Number,
		comList: Object
	},
	data: {
		array: [5, 4, 3, 2, 1],
		index: 0,
		proid: -1,
		
	},
	
	methods: {
		
		bindPickerChange: function (e) {
			//console.log('picker发送选择改变，携带值为', e.detail.value)
			this.setData({
				index: e.detail.value
			})
		},
		cmdSubmit: function (e) {
			var that = this;
			//获取上次评论的时间，用于判断是否频繁提交
			var cmdSubmitTimt = wx.getStorageSync('cmdSubmitTimt');
			//当前时间戳
			var times = common.getTime();

			if (!cmdSubmitTimt) {
				wx.setStorageSync('cmdSubmitTimt', times)
			}
			//每次评论需间隔5秒
			if (times-cmdSubmitTimt<5){
				showMsg('5秒后再试',false)
				return false;
			}




			//获取表单值
			var formData = e.detail.value;
			for (var i in formData) {
				if (!formData[i]) {
					showMsg('请填写完整', false);
					return false;
				}
			}
			//获取用户信息
			var loginInfo = wx.getStorageSync('loginInfo')

			if (!loginInfo.authOpenid) {
				showMsg('请登录', false)
				setTimeout(function () {
					common.login(true, that);
				}, 2000)
			}
			//拼接要提交的数据
			var postData = Object.assign(formData, loginInfo, { 'type': 'comment', 't': times })
			wx.request({
				url: api,
				data: postData,
				success: function (res) {
					var reqData = res.data;
					//c(reqData)
					if (!reqData.res) {
						showMsg(res.msg, false)
						return false;
					}
					//再次设置时间戳
					wx.setStorageSync('cmdSubmitTimt', times)
					wx.showModal({
						title: '提示',
						showCancel:false,
						content: '评论成功，等待审核中',
						success: function (res) {
							
						}
					})
				},
				fail: function () {
					showMsg('请求失败')
				}
			})

		}
	}

})