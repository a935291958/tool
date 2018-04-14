// pages/index /index.js
//获取应用实例
const app = getApp()
const common = require('../common/common.js');
const config = require('../../config.js');
const c = common.c;
var nowTime = common.getTime();//获取当前时间戳
const api = config.api;//API接口

const showMsg = common.showMsg;
Page({

	/**
	 * 页面的初始数据
	 */
	data: {
		classification: [],
		hotProList:[]
	},

	/**
	 * 生命周期函数--监听页面加载
	 */
	onLoad: function (options) {
		var that = this;
		//获取分类
		wx.request({
			url: api,
			data: { 'pid': 0 ,'type':'pid'},
			success: function (result) {
				let data = result.data;
				if (!data || 'object' !== typeof data || !data.res) {
					showMsg('获取分类失败', false);
					return false;
				}
				
				that.setData({
					classification:data.data
				})

			},
			fail: function () {
				showMsg('获取分类失败', false);
			}
		})

		//获取热门项目
		wx.request({
			url: api,
			data: { 'type': 'getHot' },
			success: function (result) {
				let data = result.data;
				if (!data || 'object' !== typeof data || !data.res) {
					showMsg('获取热门失败', false);
					return false;
				}

				that.setData({
					hotProList: data.data
				})

			},
			fail: function () {
				showMsg('获取分类失败', false);
			}
		})


		

		//有这个东东的话表示已经登录了,就不用再请求服务器了。
		var loginInfo = wx.getStorageSync('loginInfo');
		if (!loginInfo) {
			common.login(true, that);

		}

		//如果上次登录的时间长超过1天[3600秒]，就再次登录更新数据
		if (loginInfo.timestamp && nowTime - loginInfo.timestamp >= 3600) {
			// c('登录已过期');	
			common.login(false, that);
		}
	},

	/**
	 * 生命周期函数--监听页面初次渲染完成
	 */
	onReady: function () {

	},

	/**
	 * 生命周期函数--监听页面显示
	 */
	onShow: function () {

	},

	/**
	 * 生命周期函数--监听页面隐藏
	 */
	onHide: function () {

	},

	/**
	 * 生命周期函数--监听页面卸载
	 */
	onUnload: function () {

	},

	/**
	 * 页面相关事件处理函数--监听用户下拉动作
	 */
	onPullDownRefresh: function () {

	},

	/**
	 * 页面上拉触底事件的处理函数
	 */
	onReachBottom: function () {

	},

	/**
	 * 用户点击右上角分享
	 */
	onShareAppMessage: function () {

	},
	/*
	* 用户点击分类
	*/
	onList: function (e) {
		let id = e.currentTarget.dataset.id;
		let title = e.currentTarget.dataset.title;
		wx.navigateTo({
			url: '/pages/list/index?id=' + id + '&title=' + title,
		})
	},
	/*
	用户点击热门项目
	*/
	goPro:function(e){
		var src = e.currentTarget.dataset.src;
		var types = e.currentTarget.dataset.type;
		var project = e.currentTarget.dataset.name;
		var id = e.currentTarget.dataset.id;
		wx.navigateTo({
			url: src + '?type=' + types + '&project=' + project + '&proid=' + id
		})
	}
})