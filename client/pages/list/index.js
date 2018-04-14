//index.js
//获取应用实例
const app = getApp()
const common = require('../common/common.js');
const config = require('../../config.js');
const c = common.c;
const showMsg = common.showMsg;//提示消息
var nowTime = common.getTime();//获取当前时间戳
const api = config.api;//API接口
Page({
	data: {
		thisSubclass: [],
		title: ''
	},

	onLoad: function (e) {
		var that = this;
		var id = e.id || 1;
		var title = e.title || '生活助手';
		wx.request({
			url: api,
			data: { 'pid': id, 'type': 'pid' },
			success: function (result) {
				let data = result.data;
				if (!data || 'object' !== typeof data || !data.res) {
					showMsg('获取分类失败', false);
					return false;
				}
				that.setData({
					thisSubclass: data.data,
					title: title
				})

			}, fail: function () {
				showMsg('未找到分类', false);
			}
		})










	},
	goTool: function (e) {

		var src = e.currentTarget.dataset.src;
		var types = e.currentTarget.dataset.type;
		var project = e.currentTarget.dataset.project;
		var id = e.currentTarget.dataset.id;
		wx.navigateTo({
			url: src + '?type=' + types + '&project=' + project + '&proid=' + id,
		})
	}


})
