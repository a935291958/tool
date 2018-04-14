// pages/tool/cxtq/index.js

const common = require('../../common/common.js');

const config = require('../../../config.js');
const c = common.c;
const showMsg = common.showMsg;//提示消息
var nowTime = common.getTime();//获取当前时间戳
const api = config.api;//API接口
const weathercn = config.weathercn;//天气图片URL



Page({

	/**
	 * 页面的初始数据
	 */
	data: {
		result: {},
		weathercn: '/images/0',
		searchMainHiden:true
	},
	requestApi:function(api,getData){
		var that = this;
		wx.request({
			url: api,
			data: getData,
			success: function (res) {
				if (res.data.res) {
					var result = res.data.data;
					//c(result)
					//去掉2018 S
					let daily = result.daily;
					for (var i in daily) {
						daily[i].date = daily[i].date.replace('2018-', '');
					}
					//去掉2018 E
					that.setData({
						result: result,
						weathercn: weathercn
					})
				} else {
					showMsg('请求失败', false);
				}
			},
			fail: function () {
				showMsg('请求失败', false);
			}
		})
	},
	/**
	 * 生命周期函数--监听页面加载
	 */
	onLoad: function (options) {
		//有这个东东的话表示已经登录了,就不用再请求服务器了。
		var loginInfo = wx.getStorageSync('loginInfo');
		//c(loginInfo)
		if (!loginInfo) {
			common.login(true, that);
			return false;
		}
		if (options) {
			wx.setStorageSync('options', options)
		}
		var proid = options.proid;

		var that = this;
		//c(111)
		//获取位置
		wx.getLocation({
			type: 'wgs84',
			success: function (res) {
				res.type = 'cxtq';
				res.authOpenid = loginInfo.authOpenid;
				that.requestApi(api,res);
			

			},
			fail: function () {
				//用户拒绝授权位置
				wx.openSetting({
					success: function (res) {
						if (res.authSetting["scope.userLocation"]) {
							that.onLoad();
						} else {
							wx.showToast({
								title: '请授权位置',
								time: 5000,
								image: '/images/error.png'
							})
						}
						//console.log(res)

					}, fail: function () {
						wx.showToast({
							title: '拉取权限失败',
							time: 3000,
							image: '/images/error.png'
						})
					}
				});
			}
		})

		that.setData({
			proid: proid
		})
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
		//获取页面参数
		var options = wx.getStorageSync('options')
		var that = this;
		//加载评论
		common.getMsg(api, options.proid, that)
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
	//用户点击下面的空调运动等指数
	showContent: function (e) {
		//有这个东东的话表示已经登录了,就不用再请求服务器了。
		var loginInfo = wx.getStorageSync('loginInfo');
		var content = e.currentTarget.dataset.content;
		var title = e.currentTarget.dataset.title;
		if (content && title) {
			wx.showModal({
				title: title,
				content: content,
				showCancel: false,
				success: function (res) {
					if (res.confirm) {
						//console.log('用户点击确定')
					} else if (res.cancel) {
						//  console.log('用户点击取消')
					}
				}
			})
		}
	},
	//用户点击搜索天气按钮
	searchShow:function(e) {
		var that = this;
		that.setData({
			searchMainHiden:false
		})
	},
	//用户提交搜索表单
	searchSubmit:function(e){
		var loginInfo = wx.getStorageSync('loginInfo');
		var that = this;
		var keyword = e.detail.value.keyword;
		if(!keyword){
			showMsg('请输入城市',false);
			return false;
		}
		var getData = { 'keyword': keyword};
		getData.type = 'cxtq';
		getData.authOpenid = loginInfo.authOpenid;
		that.requestApi(api, getData);
		that.setData({
			searchMainHiden:true
		})
	}

})