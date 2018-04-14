// pages/user/index.js

var common = require('../common/common.js');

var showMsg = common.showMsg;


var cfg = require('../../config.js');
//添加用户数据URL
var addUser = cfg.addUser;
//换取code的URL
var codeUrl = cfg.onLogin;



Page({

	/**
	 * 页面的初始数据
	 */
	data: {
		"userImg": '/images/userImg.png',
		'userName': '点击登录'
	},

	/**
	 * 生命周期函数--监听页面加载
	 */
	onLoad: function (options) {

		var that = this

		// 用户登录
		var loginInfo = wx.getStorageSync('loginInfo')
		if (!loginInfo.authOpenid) {
			showMsg('请登录', false)
			setTimeout(function () {
				common.login(true, that);
			}, 2000)
			return false;
		}

		//页面载入获取用户信息
		wx.getUserInfo({
			withCredentials: true,
			success: function (res) {
				// console.info(res)
				that.setData({
					userImg: res.userInfo.avatarUrl,
					userName: res.userInfo.nickName
				})
			}		
		})
	

		//onLoad END
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
	phoneCall: common.phoneCall,
	//点击交易记录
	goOrder: function () {
		wx.navigateTo({
			url: '../order/index',
		})
	},
	goSp: common.goSp,
	goQue: common.goQue
	
})