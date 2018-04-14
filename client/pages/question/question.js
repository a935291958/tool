// pages/question/question.js
var common = require('../common/common.js');

var config = require('../../config.js');
var c = common.c
var url = config.host;
const showMsg = common.showMsg;//提示消息
const queUrl = config.queUrl;//表单提交地址
const getTime = common.getTime;//获取时间戳
Page({
	data: {},
	onLoad: function (options) {
		// 页面初始化 options为页面跳转所带来的参数
	},
	formSubmit(e) {
		var that = this;
		//获取上次提交的时间，看看有没有频繁提交
		var subQue = wx.getStorageSync('subQue');
		if (getTime() - subQue < 10) {
			showMsg('请勿频繁提交', false);
			return false;
		}
		//获取表单值
		var formData = e.detail.value;
		//判断是否填写正确
		if (!formData.content || formData.content.length < 5 || formData.content.length > 254) {
			wx.showModal({
				title: '提示',
				content: '建议内容应在5~254个字之间',
				showCancel: false,
				success: function (res) {

				}
			})
			return false;
		}
		
		//获取用户信息
		var loginInfo = wx.getStorageSync('loginInfo');
		if (!loginInfo.authOpenid){
			showMsg('请登录',false);
			setTimeout(function(){
				common.login(true,that);
			},2000)
			return false;
		}

		wx.request({
			url: queUrl,
			data: formData,
			success(res) {
				var data = res.data;
				if (data.res) {
					//设置时间戳
					wx.setStorageSync('subQue', getTime())
					wx.showModal({
						title: '提交结果',
						content: '提交成功',
						showCancel:false,
						success: function (res) {
							if (res.confirm) {
								//console.log('用户点击确定')
							} else if (res.cancel) {
								//console.log('用户点击取消')
							}
						}
					})

				}
			},
			fail() {
				showMsg('提交失败', false);
			}
		})

	}




})