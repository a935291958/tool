const config = require('../../config.js');

var common = {



	//获取当前时间戳
	getTime() {
		var t = new Date().getTime().toString();
		return t.substr(0, 10);
	},
	//提示信息，icon默认类型为success，image 默认为/images/error.png 默认为延迟时间3秒
	//title bool 
	//types obj 
	//duration number
	showMsg(title, types, duration) {

		//设置默认参数
		duration = duration || 3000;
		

		if (types) {
			wx.showToast({
				title: title,
				icon: 'success',
				duration: duration
			})
		} else {
			wx.showToast({
				title: title,
				image: '/images/error.png',
				duration: duration
			})
		}
	},

	//输出所有到控制台
	c(voids) {
		console.log(voids);
	},
	//打开设置页面，重新拉取页面
	/*
		scope.userInfo 	wx.getUserInfo 	用户信息
		scope.userLocation 	wx.getLocation, wx.chooseLocation 	地理位置
		scope.address 	wx.chooseAddress 	通讯地址
		scope.invoiceTitle 	wx.chooseInvoiceTitle 	发票抬头
		scope.werun 	wx.getWeRunData 	微信运动步数
		scope.record 	wx.startRecord 	录音功能
		scope.writePhotosAlbum 	wx.saveImageToPhotosAlbum, wx.saveVideoToPhotosAlbum 	保存到相册
		scope.camera 		摄像头
	*/
	opSet(scope, suMsg, failMsg) {
		if (!scope) {
			return false;
		}
		//用户拒绝获取，调用设置再次请求登录
		wx.openSetting({
			success: function (res) {
				if (res.authSetting[scope]) {
					wx.showToast({
						title: suMsg,
					})
				} else {
					wx.showToast({
						title: failMsg,
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
	},

	//获取网络状态
	getNetwork() {
		wx.getNetworkType({
			success: function (res) {
				// 返回网络类型, 有效值：
				// wifi/2g/3g/4g/unknown(Android下不常见的网络类型)/none(无网络)
				wx.setStorageSync('networkType', { 'networkType': res.networkType })
			},
			fail: function (e) {
				//console.log('获取网络状态失败');
			}
		})

	},
	/*
	* 登陆获取用户信息
	* isShowMsg bool 是否显示提示信息,默认不显示
	* appThis object 调用页面的this对象
	*/

	login: function (isShowMsg, appThis) {
		var that = this;


		//页面载入获取用户信息
		wx.getUserInfo({
			withCredentials: true,
			success: function (res) {
				//console.log('用户同意获取信息')
				var userInfo = res.userInfo;
				//console.log(typeof userInfo)
				//获取code存到本地
				wx.login({
					success: function (res) {
						if (res.code) {
							var code = res.code;
							//获取设备信息
							var systemInfo = wx.getSystemInfoSync();

							//获取当前时间戳
							var timestamp = that.getTime();

							//获取网络状态
							var networkType = wx.getStorageSync('networkType');

							//合并所有要发送的数据
							var postData = Object.assign(userInfo, systemInfo, { 'timestamp': timestamp, 'code': code }, networkType);

							//console.log(postData)

							if (isShowMsg) {
								wx.showLoading({
									title: '登录中...',
									mask: true
								});
							}


							//把用户基本信息和code传服务器，会返回authopenid用户唯一标识符
							wx.request({
								url: config.addUser,
								method: 'POST',
								data: postData,
								dataType: 'json',
								header: {
									"Content-Type": "application/x-www-form-urlencoded"
								},
								success: function (requestRes) {
									if (requestRes.data.res) {
										wx.setStorageSync('loginInfo', { 'authOpenid': requestRes.data.authOpenid, 'timestamp': timestamp, 'userSession': requestRes.data.userSession });
										if (isShowMsg) {
											wx.hideLoading();
											setTimeout(function () {
												wx.showToast({
													title: '登录成功',
													time: 3000
												})
											}, 800)
										}


									}
								}
							})
						} else {
							if (isShowMsg) {
								wx.showToast({
									title: '登录失败:' + res.errMsg,
									time: 3000
								})
							}


						}
					}
				})
			},
			fail: function () {
				//用户拒绝获取，调用设置再次请求登录
				wx.openSetting({
					success: function (res) {
						if (res.authSetting["scope.userInfo"]) {
							appThis.onLoad();
						} else {
							wx.showToast({
								title: '请授权',
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


	},

	/*
	*获取某一项目的评论
	*/
	getMsg: function (api, proid, that) {
		// 用户登录
		var loginInfo = wx.getStorageSync('loginInfo')
		if (!loginInfo.authOpenid) {
			showMsg('请登录', false)
			setTimeout(function () {
				common.login(true, that);
			}, 2000)
		}
		//页面载入显示评论
		wx.request({
			url: api,
			data: { 'type': 'getComments', 'proid': proid, 'authOpenid': loginInfo.authOpenid },
			success: function (res) {
				var data = res.data.data;

				if (data.length && data.length > 0) {
					for (let i = 0; i < data.length; i++) {
						let score = data[i]['score']
						let len = [];

						data[i]['score'] = Array.apply(23, Array(parseInt(score)))

					}

					that.setData({
						comList: data
					})
				}

			},
			fail: function () {
				showMsg('请求评论失败', false)
			}
		})

	},
	/*
	*保存图片
	*需在控件设置data-src="a.jpg" 属性
	*/
	saveImg: function (e) {
		//注意这里的this是调用common.js的父文件的this
		var that = this;
		//console.log(that)
		var src = e.currentTarget.dataset.src;
		if (!src) {
			return false;
		}
		//下载远程图片到本地
		wx.downloadFile({
			url: src, //图片地址
			success: function (res) {
				var tmpImg = res.tempFilePath;
				// 只要服务器有响应数据，就会把响应内容写入文件并进入 success 回调，业务需要自行判断是否下载到了想要的内容
				if (res.statusCode === 200) {
					wx.saveImageToPhotosAlbum({
						filePath: tmpImg,
						success(res) {
							wx.showToast({
								title: '保存成功',
								time: 3000
							})
						},
						fail() {
							//用户拒绝授权权限，重新拉取权限
							that.showMsg('请授权权限', false)
							setTimeout(function () {
								that.opSet('scope.writePhotosAlbum', '再次点击保存', '已拒绝保存')
							}, 3000)
						}
					})
				}
			},
			fail: function () {

				that.showMsg('保存图片失败',false)
			}
		})
	},
	/*
	*去赞赏页面
	*/
	goSp:function(){
		wx.navigateTo({
			url: '/pages/sponsorship/index',
		})
	},
	/*
	*去使用建议
	*/
	goQue: function () {
		wx.navigateTo({
			url: '/pages/question/question',
		})
	},
	
}



module.exports = common;