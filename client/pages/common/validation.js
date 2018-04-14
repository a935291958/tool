var validation = {
	//验证手机号码
	isPhone(str) {
		var myreg = /^[1][3,4,5,6,7,8][0-9]{9}$/;
		if (!myreg.test(str)) {
			return false;
		} else {
			return true;
		}
	},

	//IP

	isIP(ip) {
		var reg = /^(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$/
		return reg.test(ip);
	},

/**
 * 使用循环的方式判断一个元素是否存在于一个数组中
 * @param {Object} arr 数组
 * @param {Object} value 元素值
 */
 isInArray(arr, value){
		for(var i = 0; i < arr.length; i++){
	if (value === arr[i]) {
		return true;
	}
}
return false;
}

}



module.exports = validation;