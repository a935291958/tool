// pages/tool/phone.js
//获取应用实例
const app = getApp()
const common = require('../../common/common.js');
const validation = require('../../common/validation.js');
const config = require('../../../config.js');
const c = common.c;
const showMsg = common.showMsg;//提示消息
var nowTime = common.getTime();//获取当前时间戳
const api = config.api;//API接口




Page({

	/**
	 * 页面的初始数据
	 */
	data: {
		result: {},
		project: '',
		placeholder: '',
		mapData: {
			longitude: 116.174283, latitude: 23.775566, markers: [{
				id: 0,
				latitude: 23.775566,
				longitude: 116.174283,
				width: 50,
				height: 50,
				color: "#f8f8f8",
				callout: {
					content: "萌萌熊商城",
					fontSize: 16,
					display: 'ALWAYS',
					padding: 10,
					color: '#000000',
					bgColor: '#ffffff'
				},
				title: '萌萌熊商城'
			}]
		}
	},
	getInfo: function (types, value) {
		var returnData = {};
		switch (types) {
			case 'gsd':
				returnData.placeholder = '请输入手机号码';
				returnData.key = 'phone';
				if (value) {
					returnData.validation = validation.isPhone(value);
				}
				break;
			case 'ipdz':
				returnData.placeholder = '请输入IP地址';
				returnData.key = 'ip';
				returnData.validation = validation.isIP(value);
				break;
			case 'ipdw':
				returnData.placeholder = '请输入IP地址';
				returnData.key = 'ip';
				returnData.validation = validation.isIP(value);
				break;
			default:
				returnData.placeholder = '必填项';
				returnData.key = false;
				returnData.validation = false;
		}
		return returnData;
	},
	/**
	 * 生命周期函数--监听页面加载
	 */
	onLoad: function (options) {
		var that = this;
		if (options){
			wx.setStorageSync('options', options)
		}
		// 用户登录
		var loginInfo = wx.getStorageSync('loginInfo')
		if (!loginInfo.authOpenid) {
			showMsg('请登录', false)
			setTimeout(function () {
				common.login(true, that);
			}, 2000)
		}

		


		wx.setNavigationBarColor({
			frontColor: '#ffffff',
			backgroundColor: '#1A0000',
			animation: {
				duration: 400,
				timingFunc: 'easeIn'
			}
		})


		//有调用到该页的列表，默认隐藏
		wx.setStorageSync('isHide', { 'gsd': true, 'ipdz': true, 'ipdw': true, 'ipdwMap': true, 'localBut': true });

		var that = this;
		var onLoadIsHide = wx.getStorageSync('isHide');


		//获取当前载入的类型
		var types = options.type;
		var project = options.project;
		var proid = options.proid;
		var thisInfo = that.getInfo(types, '');

		//动态设置当前页面的标题
		wx.setNavigationBarTitle({
			title: project
		})

		//如果是IP地址查询就显示查询本机
		if (validation.isInArray(['ipdz'], types)) {
			onLoadIsHide.localBut = false;

		}


		

		that.setData({
			project: project,
			types: types,
			placeholder: thisInfo.placeholder,
			isHide: onLoadIsHide,
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
	formSubmit: function (e) {
		var that = this;
		var loginInfo = wx.getStorageSync('loginInfo');
		var types = e.detail.value.types;//查询的类型
		var mixvalue = e.detail.value.mixvalue;//获取表单值
		var butType = e.detail.target.dataset.but;//获取按钮类型

		var thisInfo = that.getInfo(types, mixvalue);

		var nowIsHide = wx.getStorageSync('isHide');

		if ((!mixvalue || !thisInfo.validation) && (butType !== 'local')) {
			showMsg('输入有误', false, 2000);
			return false;
		}




		wx.showLoading({
			title: '查询中',
			mask: true
		})
		//公共请求参数
		var getData = { 'type': types, 'authOpenid': loginInfo.authOpenid };
		//添加查询值[不是点击获取本机才是]
		if (butType !== 'local') {
			getData[thisInfo.key] = mixvalue;
		} else {
			getData[thisInfo.key] = '';
		}





		wx.request({
			url: api,
			data: getData,
			success: function (res) {
				wx.hideLoading();

				if (res.data.res) {
					nowIsHide[types] = false;
					var result = res.data.data;

					switch (types) {
						case 'ipdw':
							if (result.rectangle.length < 1) {
								showMsg('暂无数据', false);
								return false;
							}
							var rectangle = result.rectangle;

							var rectangleList = rectangle.split(';');
							result.rectangleList = rectangleList;
							break;
					}
					wx.setStorageSync('isHide', nowIsHide);
					that.setData({
						result: result,
						isHide: nowIsHide,
					})

				} else {
					showMsg('暂无数据', false);
				}
			},
			fail: function () {
				showMsg('请求失败', false);
			}
		})

	},
	ipdwMap: function (e) {
		var that = this;
		var ipdw = e.currentTarget.dataset.value.split(',');//获取经纬度
		var i = e.currentTarget.dataset.i;//获取下标

		//地图组件所需的参数
		var mapData = that.data.mapData;
		//c(mapData)
		mapData.markers["0"].longitude = ipdw[0];
		mapData.markers["0"].latitude = ipdw[1];
		mapData.longitude = ipdw[0];
		mapData.latitude = ipdw[1];
		mapData.markers["0"].callout.content = '第' + (i + 1) + '个坐标';
		//c(mapData)
		if (!ipdw) {
			showMsg('缺少经纬度', false);
			return false;
		}
		//显示地图组件
		var ipdwMapIsHide = wx.getStorageSync('isHide');
		ipdwMapIsHide.ipdwMap = false;


		that.setData({
			isHide: ipdwMapIsHide,
			mapData: mapData
		})
	}
})