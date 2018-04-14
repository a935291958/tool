// pages/sponsorship/index.js

const common = require('../common/common.js');
const config = require('../../config.js');
const c = common.c;
const showMsg = common.showMsg;//提示消息
const api = config.api;//API接口
Page({

  /**
   * 页面的初始数据
   */
  data: {
	  imgSrc:'https://tool.bearshop.cc/Public/youzan.png'
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
  
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
  saveImg: common.saveImg,
  showMsg: showMsg,
  opSet: common.opSet
})