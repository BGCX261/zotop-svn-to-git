 
/* >>>>>Begin utils.js */
/**工具类
 * @tiexg
 */
Utils = {
    offsetHost: function(host, elem, position) {//这个函数依赖jquery
        var offset = $(host).offset();
        var left = offset.left;
        var top = offset.top;
        switch (position) {
            case "top": 
                {
                    elem.style.left = left + "px";
                    elem.style.top = top - elem.offsetHeight + "px";
                    break;
                }
            case "left": 
                {
                    elem.style.left = left - elem.offsetWidth + "px";
                    elem.style.top = top + "px";
                    break;
                }
            case "right": 
                {
                    elem.style.left = left + host.offsetWidth + "px";
                    elem.style.top = top + "px";
                    break;
                }
            default: 
                {
                    elem.style.left = left + "px";
                    elem.style.top = top + host.offsetHeight + "px";
                    break;
                }
        }
    },
    htmlEncode: function(str) {
        if (typeof str == "undefined") return "";
        str = str.replace(/&/g, "&amp;");
        str = str.replace(/</g, "&lt;");
        str = str.replace(/>/g, "&gt;");
        str = str.replace(/\"/g, "&quot;");
        str = str.replace(/ /g, "&nbsp;");
        return str;
    },
    htmlDecode: function(str) {
        if (typeof str == "undefined") return "";
        str = str.replace(/&amp;/g, "&");
        str = str.replace(/&lt;/g, "<");
        str = str.replace(/&gt;/g, ">");
        str = str.replace(/&quot;/g, "\"");
        str = str.replace(/&nbsp;/g, " ");
        return str;
    },
 
    getFileNameByPath: function(path) {
        var fileName = "";
        var charIndex = path.lastIndexOf("\\");
        if (charIndex != -1) {
            fileName = path.substr(charIndex + 1);
        }
        return fileName;
    },
    requestByScript: function(scriptId, dataHref, callback, charset, retry) {
        var isReady = false;
        if (callback) {
            if (typeof (callback) == "string") {
                charset = callback;
                callback = null;
            }
        }
        var head = document.getElementsByTagName("head")[0];
        var objScript = document.getElementById(scriptId);
        if (objScript && !document.all) {
            objScript.src = "";
            objScript.parentNode.removeChild(objScript);
            objScript = null;
        }
        if (objScript != null) {
            if (dataHref.indexOf("?") == -1) dataHref += "?";
            dataHref += "&" + Math.random();
            objScript.src = dataHref;
            var dataScript = objScript;
        } else {
            var dataScript = document.createElement("script");
            dataScript.id = scriptId;
            if (charset) {
                dataScript.charset = charset;
            } else {
                dataScript.charset = "GB2312";
            }
            dataScript.src = dataHref;
            dataScript.defer = true;
            dataScript.type = "text/javascript";
            head.appendChild(dataScript);
        }
        if (document.all) {
            dataScript.onreadystatechange = function() {
                if (dataScript.readyState == "loaded" || dataScript.readyState == "complete") {
                    isReady = true;
                    if (callback) callback();
                }
            }
        } else {
            dataScript.onload = function() {
                isReady = true;
                if (callback) callback();
            }
        }
 
        if (retry) {
            setTimeout(function() {
                if (retry.times > 0 && !isReady) {
                    retry.times--;
                    if (dataHref.indexOf("?") == -1) dataHref += "?";
                    dataHref += "&" + Math.random();
                    Utils.requestByScript(scriptId, dataHref, callback, charset, retry);
                }
            }, retry.timeout);
        }
    }
}
//异步等待对象可用，然后执行回调
Utils.waitForReady=function(query,callback,win){
	var tryTimes=0;
	var done=false;
	checkReady();
	if(!done){
	    var intervalId=setInterval(checkReady,300);
	}
	function checkReady(){
		tryTimes++;
		try{
			var result;
			if(win!=undefined){
				result=win.document.getElementById(query);
			}else{
				result=eval(query);
			}
			
			if(result || tryTimes>200){
			    done=true;
				if(intervalId)clearInterval(intervalId);
				callback();
			}
		}catch(ex){
			//对象尚不可用
		}
	}
	
}
Utils.queryString = function(param, url) {
    if (!url) {
        url = location.search;
    }
    var reg = new RegExp("[?#&]" + param + "=([^&]*)", "i");
    var svalue = url.match(reg);
    var result = svalue ? unescape(svalue[1]) : null;
    if (!result && location.hash) {
        svalue = location.hash.match(reg);
        result = svalue ? unescape(svalue[1]) : null;
    }
    return result;
}
//获取event对象,主要用于兼容firefox
Utils.getEvent=function(A){
  var evt=A||window.event;
  if(!evt){
    var arr=[],C=this.getEvent.caller;
    while(C){
      evt=C.arguments[0];
      if(evt && (evt.constructor.target || evt.srcElement)){
	  //if(evt && evt.constructor==Event){
        break ;
      }
      var B=false;
      for(var D=0;D<arr.length;D++){
        if(C==arr[D]){
          B=true;
          break ;
        }
      }
      if(B){
        break ;
      }else {
        arr.push(C);
      }
      C=C.caller;
    }
  }
  return evt;
}
//停止事件冒泡
Utils.stopEvent=function(e){
  if(!e){
    e=this.getEvent();
  }
  if (e) {
  	if (e.stopPropagation) {
  		e.stopPropagation();
  		e.preventDefault();
  	}
  	else {
  		e.cancelBubble = true;
  		e.returnValue = false;
  	}
  }
}
//添加事件
Utils.addEvent=function(obj,eventName,func){
	if(obj.attachEvent){
		obj.attachEvent(eventName,func)
	}else{
		obj.addEventListener(eventName.substring(2),func,false);
	}
}
//删除事件
Utils.removeEvent=function(obj,eventName,func){
	if(obj.detachEvent){
		obj.detachEvent(eventName,func)
	}else{
		obj.removeEventListener(eventName.substring(2),func,false);
	}
}
 
//获取object对象的长度
Utils.getLength=function(obj){
var i=0;
for(elem in obj){
	i++;
}
return i;
}
//将object转换为数组
Utils.toArray=function(obj,nameFlag){
var arr=new Array();
for(elem in obj){
	if(nameFlag){
		arr.push(elem);
	}else{
		arr.push(obj[elem]);
	}
	
}
return arr;
}
//查找父节点
Utils.findParent=function(obj,tagName){
	while(obj.parentNode){
		if(obj.tagName.toLowerCase()==tagName.toLowerCase()){
			return obj;
		}
		obj=obj.parentNode;
	}
}
//得到元素的绝对坐标
Utils.findPosition=function(obj) {
	var curleft = 0;
	var curtop = 0;
	if (obj.offsetParent) {
		do {
			curleft += obj.offsetLeft;
			curtop += obj.offsetTop;
		} while (obj = obj.offsetParent);
	}
	/*while(obj.offsetParent){
		obj=obj.offsetParent;
		curleft += obj.offsetLeft;
		curtop += obj.offsetTop;
	}*/
	return [curleft,curtop];
}
//递归遍历查找属性值等于特定值的子节点
Utils.findChild=function(node,tagName,attrName,attrValue){
	var result=null;
	for (var i = 0; i < node.childNodes.length; i++) {
		var n = node.childNodes[i];
		if (n.nodeType == 1) {
			if(n.tagName.toLowerCase()==tagName.toLowerCase()){
				if(attrName){
					if (n.getAttribute(attrName) == attrValue) {
						return n;
					}
				}else{
					return n;	
				}
				
			}
			
			result=this.findChild(n,tagName,attrName, attrValue);
		}
	}
	return result;
}
//预加载图片
Utils.preloadImages=function(){
	var imgs=[];
	for(var i=0;i<arguments.length;i++){
	    imgs[i]=new Image();
	    imgs[i].src=arguments[i];
	}
}
 
//格式化日期，如yyyy-MM-dd
Date.prototype.format = function(format){
    var o = {
        "M+" : this.getMonth()+1, //month
        "d+" : this.getDate(),    //day
        "h+" : this.getHours(),   //hour
        "m+" : this.getMinutes(), //minute
        "s+" : this.getSeconds(), //second
        "q+" : Math.floor((this.getMonth()+3)/3), //quarter
        "S" : this.getMilliseconds() //millisecond
    }
    if(/(y+)/.test(format)) {
        format=format.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length));
    }
 
    for(var k in o) {
        if(new RegExp("("+ k +")").test(format)) {
            format = format.replace(RegExp.$1, RegExp.$1.length==1 ? o[k] : ("00"+ o[k]).substr((""+ o[k]).length));
        }
    }
    return format;
}
//判断天数的差值
Utils.dayDiff=function(date1,date2){
		var t = date2.getTime() - date1.getTime(); 	//相差毫秒
		var day=Math.round(t/1000/60/60/24);
		if(day==0 || day==1){
			day=date1.getDate()==date2.getDate()?0:1;
		}
		return day;
}
//将coremail的字符串表示的日期转化为Date类型，
Utils.parseDate=function(str){
		var tmpArr		= str.split(" ");
		var dateStr		= tmpArr[0];
		var tmpDateArr	= dateStr.split(".");
		var iYear		= tmpDateArr[0];
		var iMonth		= tmpDateArr[1];
		var iDate		= tmpDateArr[2];
		var timeStr		= tmpArr[1];
		var	tmpTimeArr	= timeStr.split(":");
		var iHour		= tmpTimeArr[0];
		var iMinute		= tmpTimeArr[1];
		return new Date(iYear,iMonth - 1,iDate,iHour,iMinute);
}
Utils.getCookie=function(name) {
 var arr = document.cookie.match(new RegExp("(^|\\W)"+name+"=([^;]*)(;|$)"));
 if(arr != null)return unescape(arr[2]);
 return "";
}
Utils.setCookie=function(name,value)
{
document.cookie = name + " = " + escape ( value ) + "; path=/; "
//+window.location.host.match(/[^.]+\.[^.]+$/);
 +  " expires=" +    ( new Date ( 2099 , 12 , 31 ) ) . toGMTString();
 
 
}
 
//格式化字符串,支持array和object两种数据源
String.format = function(str, arr) {
    var tmp;
    if (arr.constructor == Array) {
        for (var i = 0; i < arr.length; i++) {
            var re = new RegExp('\\{' + (i) + '\\}', 'gm');
            tmp = String(arr[i]).replace(/\$/g, "$$$$");
            str = str.replace(re, tmp);
        }
    } else {
        for (var elem in arr) {
            var re = new RegExp('\\{' + elem + '\\}', 'gm');
            tmp = String(arr[elem]).replace(/\$/g, "$$$$");
            str = str.replace(re, tmp);
        }
    }
    return str;
}
 
String.prototype.getBytes = function() {   
    var cArr = this.match(/[^\x00-\xff]/ig);   
    return this.length + (cArr == null ? 0 : cArr.length);   
}    
 
String.prototype.trim = function(){
	return this.replace(/^\s+|\s+$/g, "");
}
String.prototype.format = function() {
    var str = this;
    var tmp;
    for (var i = 0; i < arguments.length; i++) {
        tmp = String(arguments[i]).replace(/\$/g, "$$$$");
        str = str.replace(eval("/\\{" + i + "\\}/g"), tmp);
    }
    return str;
}
 
//得到字符串的前n个字符（1个汉字相当于两个字符，一个英文字母相当于1个字符）
String.prototype.getLeftStr = function(len,showSymbol){
	var leftStr = this;
	var curLen  = 0;
	for(var i=0;i<this.length;i++){
		curLen += this.charCodeAt(i)>255 ? 2 : 1;
		if(curLen > len){
			leftStr = this.substring(0,i);
			break;
		}else if(curLen == len){
			leftStr = this.substring(0,i + 1);
			break;
		}
	}
	if(showSymbol){
		if(leftStr != this){
			leftStr += "..."; 
		}
	}
	return leftStr;
}
String.prototype.$=function(from,to){
    if(!to){
        to=from;
        from=0;
    }
    var arr=[];
    for(var i=from;i<=to;i++){
        arr.push(this.replace(/\$i/g,i));
    }
    return arr;
}
 
 
Utils.checkEmail=function(email){
	var m=email.match(/^[\w+\-.]+@[\w+\-.]+[a-z]{2,3}$/i);
	if(m){
		return true;
	}else{
		return false;
	}
}
 
Utils.getEmail=function(email){//从发件人中提取邮件地址和姓名
	var result=[];
	email=this.htmlDecode(email);
	var m=email.match(/"(.+?)"\W+<(\w+([.-]?\w)*@\w+([.-]?\w)*\.\w+([.-]?\w)*)>/i);
	if(m){
		result[0]=m[1];
		result[1]=m[2];
	}else{
		var idx=email.indexOf("@");
		result[0]=email.substr(0,idx);
		result[1]=email;
	}
	return result;
} 
 
Utils.loadSkinCss=function(path,doc,prefix,dir){
	if(!path){
		var skinCookie=Utils.getCookie("SkinPath");
		path=skinCookie||top.UserConfig["skinPath"]||"skin_xmas";
	}
	if(prefix){
		path=path.replace("skin",prefix+"_skin");
	}
	if(!doc){
		doc=document;
	}
	if(doc==top.document){
	    if(!window.cssTag){
	        document.write('<link id="skinLink" rel="stylesheet" type="text/css" href="{0}" />'.format(resourcePath+"/css/"+path+".css"));
	        window.cssTag=document.getElementById("skinLink");
	    }else{
	        window.cssTag.href=top.resourcePath+"/css/"+path+".css";
	    }
	}else{
	    var links=doc.getElementsByTagName("link");
	    for(var i=0;i<links.length;i++){
		    var l=links[i];
			if(!l.href){
				if(dir){
					l.href=dir+path+".css";
				}else{
					l.href=top.resourcePath+"/css/"+path+".css";
				}
				
			}else if(l.href.match(/skin_\w+.css$/)){
				l.href=l.href.replace(/skin_\w+/,path);
			}
			/*
		    if(!l.href||/\w+_\w+.css$/.test(l.href)){
		        if(window==window.top){
		            l.href=top.resourcePath+"/css/"+path+".css";
		        }else{
					if(l.href)
		            l.href=top.cssTag.href;
		        }
			    break;
		    }*/
	    }
	}
}
 
Utils.setDomain=function(){
	document.domain=window.location.host.match(/[^.]+\.[^.]+$/)[0];
}
 
 
//文本框获得焦点并定位光标到末尾
Utils.focusTextBox=function(objTextBox){
    try{
        if(document.all){
            var r =objTextBox.createTextRange();
            r.moveStart("character",objTextBox.value.length);
            r.collapse(true);
            r.select();
        }else{
            objTextBox.setSelectionRange(objTextBox.value.length,objTextBox.value.length);
            objTextBox.focus();
        }
    }catch(e){}
}
 
//解析email地址
Utils.parseEmail=function(text){
    var reg=/(?:[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}|(?:"[^"]*")?\s?<[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}>)(?=;|,|，|；|$)/gi;
    var regName=/^"([^"]+)"|^([^<]+)</;
    var regAddr=/<?([A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4})>?/i;
    var matches=text.match(reg);
    var result=[];
    if(matches){
        for(var i=0,len=matches.length;i<len;i++){
            var item={};
            item.all=matches[i];
            var m=matches[i].match(regName);
            if(m)item.name=m[1];
            m=matches[i].match(regAddr);
            if(m)item.addr=m[1];
            if(item.addr){
                item.account=item.addr.split("@")[0];
                item.domain=item.addr.split("@")[1];
                if(!item.name)item.name=item.account;
                result.push(item);
            }
        }
    }
    return result;
}
Utils.testEmail=function(txt){
    var mailReg=/^[A-Z0-9._%+-]+@(?:[A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
    var mailRegExt=/^<[A-Z0-9._%+-]+@(?:[A-Z0-9-]+\.)+[A-Z]{2,4}>$/i;
    txt=txt.replace(/"[^"]*"(?=\s*<)/g,"");
    var arr=txt.split(/[;,；，]/);
    for(var i=0;i<arr.length;i++){
        arr[i]=arr[i].trim();
        if(arr[i]=="")continue;
        if(mailReg.test(arr[i])||mailRegExt.test(arr[i])){
            continue;
        }else{
            Utils.testEmail.error=arr[i];
            return false;
        }
    }
    return true;
}
Utils.toogle=function(element,btn,cls1,cls2){
	element.style.display=element.style.display=="none"?"":"none";
	btn.className=btn.className==cls1?cls2:cls1;
}
Utils.setBodyFixToFrame=function(){
    document.body.style.height=window.frameElement.offsetHeight+"px";
}
 
String.prototype.encode=function(){
    return Utils.htmlEncode(this);
}
String.prototype.decode=function(){
    return Utils.htmlDecode(this);
}
Utils.getGB2312=function(str,callback){
	var i,c,ret="",strSpecial="!\"#$%&'()*+,/:;<=>?@[\]^`{|}~%";
	var url=resourcePath+"/js/gb2312.js"
	this.requestByScript("script_gb2312data",url,function(){
		for (i = 0; i < str.length; i++) {
			if (str.charCodeAt(i) >= 0x4e00) {
				c = Arr_GB2312[str.charCodeAt(i) - 0x4e00];
				ret += "%" + c.slice(0, 2) + "%" + c.slice(-2);
			}
			else {
				c = str.charAt(i);
				if (c == " ") 
					ret += "%20";
				else 
					if (strSpecial.indexOf(c) != -1) 
						ret += "%" + str.charCodeAt(i).toString(16);
					else 
						ret += c;
			}
		}
		callback(ret);
	})
 
 
}
 
 
Utils.getUserDataFromCookie=function(){
	var cookiesString=Utils.getCookie("UserData");
    if(cookiesString!=""){
 	    try{
		    UserData=eval("("+cookiesString+")");
		    UserData_bak=UserData;
	    }catch(ex){
 
	    }
     }
}
Utils.getXmlDoc=function(xml){
    if(document.all){
        var ax=new ActiveXObject("Microsoft.XMLDOM");
        ax.loadXML(xml);
        return ax;
    }
	var parser = new DOMParser();
	return parser.parseFromString(xml, "text/xml");
}
 
 
//2008-12-4 行为统计(首页和欢迎页)
var behaviorList=[];
var behaviorTimer;
Utils.addEvent(document,"onclick",function (e){
    e=e||event;
    var target=e.srcElement || e.target;
    try{
        behaviorClick(target);
    }catch(e){}
});
//首页加载代理页面
if(Utils.queryString("funcid")=="main"){
    Utils.setDomain();
    var htmlCode="<iframe id='webmailProxy' name='webmailProxy' onload='this.isReady=true' \
    style='display:none' src='{0}/proxy.htm' ></iframe>\
    <iframe id='ucProxy' name='ucProxy' onload='this.isReady=true' \
    style='display:none' src='{1}/proxy.htm' ></iframe>".format(webmailDomain,ucDomain);
    document.write(htmlCode);
}
function behaviorClick(target,win){
    if(window!=top){
        top.behaviorClick(target,window);
        return;
    }
    var behavior;
    var ext;
    if(target.getAttribute("behavior")){
        behavior=target.getAttribute("behavior");
        ext=target.getAttribute("ext");
    }else if(target.parentNode && target.parentNode.getAttribute && target.parentNode.getAttribute("behavior")){
        target=target.parentNode;
        behavior=target.getAttribute("behavior");
        ext=target.getAttribute("ext");
    }else if(target.tagName=="A" || (target.parentNode && target.parentNode.tagName=="A")){
        while(target){
            if(target.id=="adHolder1" || target.id=="adHolder2"){
                behavior=target.id=="adHolder1"?"最新活动":"邮箱公告";
                break;
            }
            if(win && win.document.title=="写信" && (target.id=="btnSMS" || target.id=="btnMMS")){
                behavior=target.id=="btnSMS"?"短信发送":"彩信发送";
                break;
            }
            target=target.parentNode;
        }
    }else{
        while(target){
            if(target.tagName=="A"){
                behavior=target.getAttribute("behavior");
                ext=target.getAttribute("ext");
                break;
            }
            target = target.parentNode;
        }
    }
    if(behavior){
        top.addBehavior(behavior,ext);
    }
}
 
function addBehavior(behaviorKey,extendKey){
    var bid,mid,extendID,behaviorNode,moduleKey;
    if(!window.behaviorCongfigDoc){
        behaviorCongfigDoc=$(Utils.getXmlDoc(BehaviorConfig));//先把配置xml转化成jquery托管的xml文档
    }
    //behavior节点
    var behaviorNode=behaviorCongfigDoc.find("behavior[@key='{0}']".format(behaviorKey));
    
    if(behaviorNode.length==0){
        Debug.write("[行为统计]捕获点击:【{0}】,找不到该元素的配置编号,可能已移除".format(behaviorKey),"blue");
        return;
    }
    extendID=behaviorNode.attr("extendID");
    bid=behaviorNode.attr("id");
    moduleKey=behaviorNode.attr("module");
    if(bid && extendKey && /^\d+$/.test(extendKey)){
        extendID = extendKey;
    }else if(extendKey){
        //extend节点
        var extendNode=behaviorNode.find("extend[@key='{0}']".format(extendKey));
        if(extendNode.length==0){
            extendNode=behaviorCongfigDoc.find("behavior[@key='{0}']".format(extendKey));
            if(extendNode.length==0){
                extendNode=behaviorCongfigDoc.find("extend[@key='{0}']".format(extendKey));
                if(extendNode.length==0){
                    Debug.write("[行为统计]捕获点击:【{0}】,找不到该元素的配置编号,可能已移除".format(extendKey),"blue");
                    return;
                }
            }
        }
        extendID=extendNode.attr("extendID");
        bid=extendNode.attr("id");
        moduleKey=extendNode.attr("module");
    }
    mid=behaviorCongfigDoc.find("module[@key='{0}']".format(moduleKey)).attr("id");
    if(!extendID){
        extendID=parseInt(behaviorCongfigDoc.find("behaviorConfig").attr("baseExtendID"));
    }else{
        extendID=parseInt(extendID)+parseInt(behaviorCongfigDoc.find("behaviorConfig").attr("baseExtendID"));
    }
 
    for(var i=0,len=behaviorList.length;i<len;i++){
        var item=behaviorList[i];
        if(item.id==bid && item.extendID==extendID && item.moduleID==mid){
            Debug.write("[行为统计]捕获点击:【{0}】,该元素此前已被点击".format(extendKey==null?behaviorKey:extendKey));
            return;//存在重复则不加入数组
        }
    }
    Debug.write("[行为统计]捕获点击:【{0} id:{1},功能划分：{2}】,进入列队,index:{3}".format(extendKey==null?behaviorKey:extendKey,bid,moduleKey,behaviorList.length+1));
    var behaviorItem={id:bid,extendID:extendID,moduleID:mid};
    behaviorList.push(behaviorItem);
    
    if(!behaviorTimer){
        behaviorTimer=setInterval(sendBehavior,behaviorCongfigDoc.find("behaviorConfig").attr("timespan"));
        sendBehavior();
    }else{
        if(!window.businessBehaviorSended && behaviorItem.moduleID!="14"){
            sendBehavior();
        }
    }
    
}
function sendBehavior(){
    if(document.getElementById("ucProxy").isReady && frames["ucProxy"].$ && behaviorList.length>0){
        var tmpList=[];
        var xml="<reports>";
        for(var i=0,len=behaviorList.length;i<len;i++){
            var item=behaviorList[i];
            if(!item.isSended){
                xml+="<behavior id='{0}' extendID='{1}' mid='{2}' index='{3}' />".format(item.id,item.extendID,item.moduleID,i+1);
                tmpList.push(item);
                if(item.moduleID!="14"){
                    businessBehaviorSended=true;
                }
            }
        }
        xml+="</reports>";
        if(tmpList.length==0)return;
        postData();
    }
    
    function postData(){
        Debug.write("[行为统计]即将发送数据,请查看抓包信息:"+xml,"red");
        window.frames["ucProxy"].$.ajax({
            type:"POST",
            url:top.ucDomain+"/Behavior/BehaviorGather.ashx?sid="+top.UserData.ssoSid+"&rnd="+Math.random(),
            data:{reports:xml},
            success:function(response){
                if(response=="200"){
                    for(var i=0,len=tmpList.length;i<len;i++){
                        tmpList[i].isSended=true;
                    }
                    Debug.write("[行为统计]上报结果：返回成功","green");
                }else{
                    Debug.write("[行为统计]上报结果：后台程序提示写入失败","red");
                }
            },
            error:function(){
                Debug.write("[行为统计]上报结果：后台程序出错","red");
            }
        })
    }
}
BehaviorConfig='<behaviorConfig timespan="60000" baseExtendID="8000">\
  <modules>\
    <module key="成功阅读邮件" id="11" />\
    <module key="成功变更邮箱设置" id="12" />\
    <module key="点击邮箱营销链接等行为" id="13" />\
    <module key="基础邮箱其它行为" id="14" />\
    <module key="使用邮箱增值服务功能" id="25" />\
    <module key="短信超人" id="33" />\
  </modules>\
  <behaviors>\
    <behavior id="1101" module="成功变更邮箱设置" key="发件人姓名" />\
    <behavior id="1102" module="点击邮箱营销链接等行为" key="积分等级" />\
    <behavior id="1103" module="点击邮箱营销链接等行为" key="积分" />\
    <behavior id="1104" module="成功变更邮箱设置" key="登录短信通知" />\
    <behavior id="1118" module="成功变更邮箱设置" key="欢迎页-邮件到达通知" />\
    <behavior id="1120" module="成功变更邮箱设置" key="获取wap地址" />\
    <behavior id="1115" module="成功阅读邮件" key="未读邮件" />\
    <behavior id="1105" module="成功变更邮箱设置" key="一键搬家" />\
    <behavior id="1106" module="成功变更邮箱设置" key="一键搬家-下一步" />\
    <behavior id="1174" key="家庭邮箱" module="点击邮箱营销链接等行为" />\
    <behavior id="1107" module="点击邮箱营销链接等行为" key="最新活动"/>\
    <behavior id="1108" module="点击邮箱营销链接等行为" key="邮箱公告"/>\
    <behavior id="1109" module="成功变更邮箱设置" key="天气城市" />\
    <behavior id="1110" module="基础邮箱其它行为" key="日期" />\
    <behavior id="1111" module="基础邮箱其它行为" key="农历黄历" />\
    <behavior id="1112" module="基础邮箱其它行为" key="邮箱容量" />\
    <behavior id="1113" module="点击邮箱营销链接等行为" key="推荐好友" />\
    <behavior id="1114" module="点击邮箱营销链接等行为" key="中国移动" />\
    <behavior id="1206" module="基础邮箱其它行为" key="反馈" />\
    <behavior id="1131" module="点击邮箱营销链接等行为" key="邮箱伴侣" />\
    <behavior id="1201" module="成功变更邮箱设置" key="设置" />\
    <behavior id="1202" module="成功变更邮箱设置" key="换肤" />\
    <behavior id="1203" module="基础邮箱其它行为" key="邮件搜索" />\
    <behavior id="1204" module="基础邮箱其它行为" key="高级搜索" />\
    <behavior id="1205" extendID="1" module="点击邮箱营销链接等行为" key="帮助" />\
    <behavior id="1230" module="成功阅读邮件" key="收信" />\
    <behavior id="1005" module="基础邮箱其它行为" key="写信" />\
    <behavior id="1003" module="成功阅读邮件" key="收件箱" />\
    <behavior id="1231" module="基础邮箱其它行为" key="草稿箱" />\
    <behavior id="1008" module="基础邮箱其它行为" key="已发送" />\
    <behavior id="1007" module="基础邮箱其它行为" key="已删除" />\
    <behavior id="1232" module="基础邮箱其它行为" key="已删除-清空" />\
    <behavior id="1233" module="基础邮箱其它行为" key="垃圾邮件" />\
    <behavior id="1234" module="基础邮箱其它行为" key="垃圾邮件-清空" />\
    <behavior id="1235" module="基础邮箱其它行为" key="病毒邮件" />\
    <behavior id="1236" module="基础邮箱其它行为" key="病毒邮件-清空" />\
    <behavior id="1237" module="基础邮箱其它行为" key="定时邮件" />\
    <behavior id="1244" module="基础邮箱其它行为" key="账单投递" />\
    <behavior id="1245" module="使用邮箱增值服务功能" key="精品订阅" />\
    <behavior id="1012" module="基础邮箱其它行为" key="我的文件夹" />\
    <behavior id="1238" module="成功变更邮箱设置" key="新建文件夹" />\
    <behavior id="1025" module="成功阅读邮件" key="代收邮件" />\
    <behavior id="1006" module="成功变更邮箱设置" key="通讯录" />\
    <behavior id="1019" module="成功变更邮箱设置" key="PushEmail" />\
    <behavior id="1010" module="使用邮箱增值服务功能" key="发短信" />\
    <behavior id="1010" module="使用邮箱增值服务功能" key="自写短信" />\
    <behavior id="1009" module="使用邮箱增值服务功能" key="发彩信" />\
    <behavior id="1009" module="使用邮箱增值服务功能" key="自写彩信" />\
    <behavior id="1016" module="使用邮箱增值服务功能" key="手机网盘" />\
    <behavior id="1240" module="使用邮箱增值服务功能" key="移动助理" />\
    <behavior id="1239" module="使用邮箱增值服务功能" key="飞信操作" />\
    <behavior id="1241" module="使用邮箱增值服务功能" key="发传真" />\
    <behavior id="1462" module="使用邮箱增值服务功能" key="短信发送" />\
    <behavior id="1464" module="使用邮箱增值服务功能" key="彩信发送" />\
    <behavior id="1468" module="成功变更邮箱设置" key="写信页通讯录" />\
    <behavior id="1205" extendID="2" module="基础邮箱其它行为" key="传真-帮助中心链接" />\
    <behavior id="1243" module="基础邮箱其它行为" key="传真-预览发送" />\
    <behavior id="1173" extendID="2" module="基础邮箱其它行为" key="娱乐专区-一起玩吧" />\
    <behavior id="1182" extendID="2" module="基础邮箱其它行为" key="娱乐专区-商务社区" />\
    <behavior id="1172" extendID="2" module="基础邮箱其它行为" key="娱乐专区-民生家园" />\
    <behavior id="1184" extendID="2" module="基础邮箱其它行为" key="娱乐专区-动感舞台" />\
    <behavior id="1175" extendID="2" module="基础邮箱其它行为" key="娱乐专区-魔信" />\
    <behavior id="2100" module="使用邮箱增值服务功能" key="文件快递" />\
    <behavior id="1246" module="使用邮箱增值服务功能" key="明信片" />\
	<behavior id="3100" module="基础邮箱其它行为" key="视频邮件-录制" />\
	<behavior id="3101" module="基础邮箱其它行为" key="视频邮件-插入到邮件" />\
	<behavior id="3102" module="基础邮箱其它行为" key="视频邮件-作为附件发送" />\
	<behavior id="3103" module="基础邮箱其它行为" key="视频邮件-下载" />\
	<behavior id="3104" module="基础邮箱其它行为" key="视频邮件-播放" />\
	<behavior id="2350" module="短信超人" key="短信超人-文件快递" />\
	<behavior id="2351" module="短信超人" key="短信超人-免费短信专区" />\
	<behavior id="2354" module="短信超人" key="短信超人-复制支持" />\
	<behavior id="2101" module="基础邮箱其它行为" key="文件快递-提醒设置" />\
	<behavior id="2102" module="基础邮箱其它行为" key="文件快递-客户端下载" />\
	<behavior id="3150" module="基础邮箱其它行为" key="在线杀毒-按钮" />\
	<behavior id="2010" module="基础邮箱其它行为" key="发短信提醒好友收邮件" />\
	<behavior id="2021" module="基础邮箱其它行为" key="发信完成-添加到通讯录" />\
	<behavior id="2014" module="基础邮箱其它行为" key="发信完成-邀请好友使用139邮箱" />\
	<behavior id="2015" module="基础邮箱其它行为" key="发信完成-文件快递" />\
	<behavior id="2016" module="基础邮箱其它行为" key="发信完成-视频邮件" />\
	<behavior id="2017" module="基础邮箱其它行为" key="发信完成-明信片" />\
	<behavior id="2018" module="基础邮箱其它行为" key="发信完成-贺卡" />\
	<behavior id="2019" module="基础邮箱其它行为" key="发信完成-精品订阅" />\
	<behavior id="2022" module="基础邮箱其它行为" key="发信完成-手机网盘" />\
	<behavior id="1422" module="基础邮箱其它行为" key="读信页-缩放正文字号" />\
	<behavior id="1423" module="基础邮箱其它行为" key="读信页-winmail.dat邮件阅读" />\
	<behavior id="1424" module="基础邮箱其它行为" key="读信页-winmail.dat帮助链接" />\
	<behavior id="1264" module="基础邮箱其它行为" key="邮件列表-搜索结果彻底删除" />\
	<behavior id="1280" module="基础邮箱其它行为" key="邮件列表-删除所有未读邮件" />\
	<behavior id="1281" module="基础邮箱其它行为" key="邮件列表-标记所有未读邮件" />\
	<behavior id="1269" module="基础邮箱其它行为" key="邮件列表-标记邮件置顶" />\
	<behavior id="1993" module="基础邮箱其它行为" key="写信-粘贴附件" />\
	<behavior id="1994" module="基础邮箱其它行为" key="写信-短信发送正文" />\
	<behavior id="1995" module="基础邮箱其它行为" key="写信-彩信发送正文" />\
	<behavior id="3120" module="基础邮箱其它行为" key="属性卡-添加到通讯录" />\
	<behavior id="3121" module="基础邮箱其它行为" key="属性卡-添加手机号码" />\
	<behavior id="3122" module="基础邮箱其它行为" key="属性卡-发邮件" />\
	<behavior id="3123" module="基础邮箱其它行为" key="属性卡-发短信" />\
	<behavior id="3124" module="基础邮箱其它行为" key="属性卡-添加到手机邮件白名单" />\
	<behavior id="41" module="基础邮箱其它行为" key="新建文件夹同时创建过滤器" />\
	<behavior id="1600" module="基础邮箱其它行为" key="代收文件夹-收信" />\
	<behavior id="1117" module="基础邮箱其它行为" key="欢迎页-未读邮件夹" />\
	<behavior id="1119" module="基础邮箱其它行为" key="欢迎页-自定义快捷设置" />\
	<behavior id="1121" module="基础邮箱其它行为" key="欢迎页-更新告知" />\
	<behavior id="1122" module="基础邮箱其它行为" key="欢迎页-超大附件" />\
	<behavior id="1068" module="基础邮箱其它行为" key="欢迎页-自定义我的应用" />\
	<behavior id="1993" module="基础邮箱其它行为" key="粘贴附件" />\
	<behavior id="1264" module="基础邮箱其它行为" key="搜索结果全量删除" />\
	<behavior id="3200" module="基础邮箱其它行为" key="功能引导-关闭tips" />\
	<behavior id="3201" module="基础邮箱其它行为" key="功能引导-看下一个功能" />\
	<behavior id="3202" module="基础邮箱其它行为" key="功能引导-立即使用邮箱" />\
	<behavior id="3220" module="基础邮箱其它行为" key="新用户向导-点击提交，进入下一步" />\
	<behavior id="3221" module="基础邮箱其它行为" key="新用户向导-点击完成，进入邮箱" />\
	<behavior id="3222" module="基础邮箱其它行为" key="新用户向导-不填了跳过" />\
	<behavior id="1403" extendID="6" module="基础邮箱其它行为" key="通讯录-谁加了我入口" />\
    <behavior id="8001" module="基础邮箱其它行为" key="飞信登录Tip广告显示" />\
	<behavior id="8002" module="基础邮箱其它行为" key="飞信登录Tip点击关闭" />\
  </behaviors>\
</behaviorConfig>';
 
 
 
//调试器
Debug={
    write:function(message,color){
        if(!top.Debug.isDebugging)return;
        if(window!=window.top){
            window.top.Debug.write(message,color);
            return;
        }
        try{
            this.content.find("li").css("background-color","white");
            var li=$("<li></li>").text(message).css("background-color","silver").appendTo(this.content);
            if(color)li.css("color",color);
            this.content.append("<hr />");
            this.content[0].scrollTop=1000000;
        }catch(e){
            alert("调试器错误");
        }
    },
    init:function(){
        if(this.inited)return;
        this.inited=true;
        this.container=$("<div style='position:absolute;left:10px;top:10px;\
        width:300px;background:white;z-index:99999;\
        border:1px solid black;'><p style='margin:0;padding:3px;\
        width:100%;background:skyblue;color:white'>调试窗口</p><ul style='height:300px;overflow-x:hidden;overflow-y:auto;'></ul></div>")
        .appendTo(document.body);
        this.content=this.container.find("ul").eq(0);
        new DragManager(this.container[0],this.container.find("p")[0]);
    },
    start:function(){
        this.init();
        this.isDebugging=true;
    }
}
Utils.isUploadControlSetupExt=function(){
    if (/firefox/i.test(navigator.userAgent.toString())) {
       var mimetype = navigator.mimeTypes["application/x-shockwave-flash"];
       if(mimetype && mimetype.enabledPlugin){
           return true;
       }
       return false;
    } else {
       return false;
    }
}
Utils.isUploadControlSetup = function(showTip) {
    if (!document.all) {
        return Utils.isUploadControlSetupExt();
    }
    if (top.isUploadControlSetup) return true;
    var setup = false;
    Utils.checkUploadControlResult = 0;
    try {
        var obj = new ActiveXObject("Cxdndctrl.Upload");
        if (obj) setup = true;
    } catch (e) {
 
    }
    if (!setup && showTip) {
        if (window.confirm("上传文件必须安装139邮箱控件,是否安装?")) {
            Utils.openControlDownload(true);
            Utils.checkUploadControlResult = 1;
        }
 
    } else if (setup && obj.getversion() < 65536) {//装了上线前的旧版本
        if (showTip && window.confirm("您安装的上传控件已经不能使用,是否更新?")) {
            Utils.openControlDownload(true);
            Utils.checkUploadControlResult = 2;
        }
        return false;
    } else if (setup && showTip && top.SiteConfig.uploadControlVersion) {
        var version = obj.getversion();
        if (version < top.SiteConfig.uploadControlVersion && !top.UserData.donotAnswerControlUpdate) {
            if (window.confirm("上传控件有更新的版本,是否更新?")) {
                Utils.checkUploadControlResult = 3;
                Utils.openControlDownload();
                return false;
            } else {
                top.UserData.donotAnswerControlUpdate = true;
            }
        }
    }
    top.isUploadControlSetup = setup;
    return setup;
}
Utils.isScreenControlSetup = function(showTip,cacheResult) {
	if(top.isScreenControlSetup!=undefined && cacheResult){
		return top.isScreenControlSetup;
	}
    if (!document.all) {
        if(showTip)alert("截屏功能仅能在IE浏览器下使用");
        return false;
    }
    var setup = false;
    try {
        var obj = new ActiveXObject("ScreenSnapshotCtrl.ScreenSnapshot.1");
        if (obj) setup = true;
    } catch (e) {
 
    }
    if (!setup && showTip) {
        if (window.confirm("使用截屏功能必须安装139邮箱控件,是否安装?")) {
            Utils.openControlDownload();
        }
    } else if (setup && showTip && top.SiteConfig.screenControlVersion) {
        var version = obj.GetVersion();
        if (version < top.SiteConfig.screenControlVersion && !top.UserData.donotAnswerControlUpdate) {
            if (window.confirm("邮箱截屏控件有更新的版本,是否更新?")) {
                Utils.openControlDownload();
				top.isScreenControlSetup=false;
                return false;
            } else {
                top.UserData.donotAnswerControlUpdate = true;
            }
        }
    }
    delete obj;
	top.isScreenControlSetup=setup;
    return setup;
}
Utils.openControlDownload = function(removeUploadproxy) {
    window.open(top.ucDomain + "/LargeAttachments/html/control139.htm");
    try {
        top.addBehavior("文件快递-客户端下载");
        if (removeUploadproxy) {
            top.$("#uploadproxy").attr("src", "about:blank");
            top.$("#uploadproxy").remove();
        }
    } catch (e) { }
}
 
 
Utils.addEvent(document, "onkeydown", function(e) {
    e = e || event;
    try {
        if (window.top.globalKeyDownEvent) {
            window.top.globalKeyDownEvent(e);
        }
    } catch (e) { }
});
Utils.addEvent(document, "onclick", function(e) {
    e = e || event;
    try {
        if (window.top.globalClickEvent) {
            window.top.globalClickEvent(e);
        }
    } catch (e) { }
});
 
Utils.isChinaMobileNumber = function(num) {
    if (num.length != 13 && num.length != 11) return false;
    if (num.length == 11) {
        num = "86" + num;
    }
    var reg = new RegExp(top.UserData.regex);
    return reg.test(num);
}
 
 
ScriptErrorLog = {
    sendTimes: 0,
    timesLimit: 20,
    addLog: function(log) {
        if (window != window.top) {
            top.ScriptErrorLog.addLog(log);
        } else {
            if (this.sendTimes < this.timesLimit) {
                try {
                    this.sendLog(log);
                } catch (e) { }
            }
        }
    },
    sendLog: function(log) {
        var url = top.SiteConfig.scriptLog || "http://scriptlog.n20svrg.139.com/ScriptLog/addlog.ashx";
        scriptLogImage = new Image();
        if (log.length > 500) {
            log = log.substring(0, 500) + ">>>>";
        }
        scriptLogImage.src = url + "?log=" + encodeURIComponent(log.replace(/[\r\n]/g, ""));
        this.sendTimes++;
    }
}
function window_onerror(msg,fileName,lineNumber){
    var stack = [];
    var userNumber = "";
    try{
        userNumber = top.UserData.userNumber.replace(/^86/, "");
    }catch(e){}
    var log = userNumber + "｜file:" + fileName.replace(/sid=[^&]+/i,"") + "｜lines:" + lineNumber + "｜msg:" + msg + "\n";
Uncaught TypeError: Cannot call method 'replace' of undefined
    var caller = arguments.callee.caller;
    var reg_getFunName=/function (\w*\([^(]*\))/;
    while(caller){
        var funCode = caller.toString();
        var match = funCode.match(reg_getFunName);
        stack.push( (match && match[1]) || funCode);
        caller = caller.caller;
    }
    log += stack.join("->");
    ScriptErrorLog.addLog(log);
    if(document.all){
        if (window.location.href.indexOf(".139.com") == -1){
            debugger;
        }else{
            try{
                if (window.top.UserData.IsTestUserNumber) debugger;
            }catch(e){}
        }
    }
}
if(window.location.href.indexOf(".139.com")>0){
    window.onerror=window_onerror;
}
/*随机排序数组*/
function randomSortArray(arr){   
    var B,C;   
    var X = [];   
  var j=0;   
  var A=[];
  for(i=0;i<arr.length;i++){   
    A[i]=arr[i];
  }
  
  for(i=A.length;i>=1;i--){   
   C=Math.floor(Math.random() * A.length);   
    X[j] = A[C];   
     A.splice(C,1)   
     j++;   
   }   
    return X   
} 
 
/* >>>>>End   utils.js */
 
 
/* >>>>>Begin balloon.js */
/**
 * @author Administrator
 */
var Balloon = {
    show: function(text, direction, elem, width, offset) {
        var div = document.createElement("div");
        div.className = "FTUTip";
        var template = "<BLOCKQUOTE style='WIDTH: {width}px'>{text}</BLOCKQUOTE><BUTTON class='wsSmallCoolCloseButton' onclick='Balloon.close(this)'></BUTTON><DIV class='balloonArrow {direction}'></DIV></DIV>";
 
        var s = String.format(template, {
            text: text,
            direction: direction,
            width: width
        });
 
        div.innerHTML = s;
 
        var pos = Utils.findPosition(elem);
        document.body.appendChild(div);
        if (!offset) {
            offset = { x: 0, y: 0 };
        }
 
        switch (direction) {
            case "left":
                div.style.left = (pos[0] + Number(elem.offsetWidth) + offset["x"]) + "px";
                div.style.top = (pos[1] + offset["y"] - 15) + "px";
                break;
            case "right":
                div.style.left = (pos[0] - div.offsetWidth + offset["x"]) + "px";
                div.style.top = (pos[1] + offset["y"] - 15) + "px";
                break;
            case "top":
                div.style.left = pos[0] + "px";
                div.style.top = (pos[1] + elem.offsetHeight + 10 + offset["y"]) + "px";
                break;
            case "bottom":
                div.style.left = pos[0] + "px";
                div.style.top = (pos[1] - div.offsetHeight - 10 + offset["y"]) + "px";
                break;
 
        }
 
 
 
    },
    close: function(sender) {
        document.body.removeChild(sender.parentNode);
    }
 
};
var Tooltip={
	tip:null,
	register:function(target,win){
		if(!win){
			win=window;
		}
	    var title=target.title;
	    target.title="";
	    var div=win.$("<div style='display:none' class='tooltip'></div>");
	    div.html(title);
	    $(target).hover(showTip,hideTip);
	    $(target).click(hideTip);
	    function showTip(){
	        Tooltip.show(div,target,win);
	    }
	    function hideTip(){
	        Tooltip.hide(div);
	    }
	},
	show:function(div,target,win){
		if(!win){
			win=window;
		}
		div.appendTo(win.document.body);
	        var offset=win.$(target).offset();
	        div.show();
			var left=offset.left;
			var top=offset.top-div.height()-win.$(target).height();
			if(offset.top<300){
				top=offset.top+win.$(target).height();
			}
			if(offset.left>400){
				left=offset.left-div.width()
			}
	        div.css({
	            left:left,
	            top:top
	        });
	},
	hide: function(div){
		div.hide();
	},
	guide:function(target,text,win){
		if(!win){
			win=window;
		}
		var div=win.$("<div style='display:none' class='tooltip'></div>");
	    div.html(text);
		Tooltip.show(div,target,win);
	}
};
 
/* >>>>>End   balloon.js */
 
 
/* >>>>>Begin dragmanager.js */
/*本类实现对元素的拖放，通用代码,依赖于工具类Utils.js(为了兼容firefox的event)*/
	function DragManager(o,handleObj){
		this.onDragStart=null;
		this.onDragMove=null;
		this.onDragEnd=null;
		this.orignX=0;
		this.orignY=0;
		var min_x=0,min_y=0,
		max_x=$(document.body).width()-$(o).width(),
		max_y=$(document.body).height()-$(o).height();
		var manager=this;
		var offset=[];
		//o.attachEvent("onmousedown",drag_mouseDown);
		if(handleObj){
		    handleObj.onmousedown=drag_mouseDown;
		}else{
		    o.onmousedown=drag_mouseDown;
		}
		this.startDrag=function(e){
			var x,y;
			e=Utils.getEvent();
			if(window.event){
				x=event.clientX+document.body.scrollLeft;
				y=event.clientY+document.body.scrollTop;
			}else{
				x=e.pageX;
				y=e.pageY;
			}
	
	
			if (o.setCapture) {	//在窗口以外也能响应鼠标事件
				o.setCapture();
			}else if (window.captureEvents) {
				window.captureEvents(Event.MOUSEDOWN | Event.MOUSEMOVE | Event.MOUSEUP);
			}
					
			var postion=Utils.findPosition(o);
			if(postion[0]==0){
				offset=[0,0];
			}else{
				offset=[x-postion[0],y-postion[1]];
			}
 
			//window.status=x+","+y;
			if(manager.onDragStart){
				manager.onDragStart({x:x,y:y});
			}
			Utils.addEvent(document,"onmousemove",drag_mouseMove);
			Utils.addEvent(document,"onmouseup",drag_mouseUp);
			Utils.stopEvent(e);//阻止事件泡冒
		}
		this.stopDrag=function(){
			if (o.releaseCapture){
				o.releaseCapture();
			}
			else if (window.captureEvents) {
				window.captureEvents(Event.MOUSEMOVE | Event.MOUSEUP);
			}
 
			if(manager.onDragEnd){
				manager.onDragEnd();
			}
			
			Utils.removeEvent(document,"onmousemove",drag_mouseMove);
			Utils.removeEvent(document,"onmouseup",drag_mouseUp);
 
		}
		
		function drag_mouseMove(e){
			var newX,newY;
			if(window.event){
				newX=event.clientX+document.body.scrollLeft;
				newY=event.clientY+document.body.scrollTop;
			}else{
				newX=e.pageX;
				newY=e.pageY;
			}
			var _x=newX-offset[0];
			var _y=newY-offset[1];
			if(_x<0){
			    _x=0;
			}else if(_x>max_x){
			    _x=max_x;
			}
			if(_y<0){
			    _y=0;
			}else if(_y>max_y){
			    _y=max_y;
			}
			o.style.left = _x+"px";
			o.style.top = _y+"px";
			
			if(manager.onDragMove){
				manager.onDragMove({x:newX,y:newY});
			}
		}
		function drag_mouseDown(e){
			manager.startDrag(e);
		}
		function drag_mouseUp(e){
			manager.stopDrag(e);
		}
		//碰撞检测
		this.hitTest=function(o, l){
			function getOffset(o){
				for(var r = {l: o.offsetLeft, t: o.offsetTop, r: o.offsetWidth, b: o.offsetHeight};
				o = o.offsetParent; r.l += o.offsetLeft, r.t += o.offsetTop);
				return r.r += r.l, r.b += r.t, r;
			}
			for(var b, s, r = [], a = getOffset(o), j = isNaN(l.length), i = (j ? l = [l] : l).length; i;
			b = getOffset(l[--i]), (a.l == b.l || (a.l > b.l ? a.l <= b.r : b.l <= a.r))
			&& (a.t == b.t || (a.t > b.t ? a.t <= b.b : b.t <= a.b)) && (r[r.length] = l[i]));
			return j ? !!r.length : r;
		};
		
	};
/* >>>>>End   dragmanager.js */
 
 
/* >>>>>Begin FloatingFrame.js */
function FloatingFrame(){
    var This=this;
    var htmlCode="";
    htmlCode+='<div class="winTip" style="z-index:1024;position:absolute;left:0px;top:0px;">';
    htmlCode+=  '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
    htmlCode+=      '<tr style="cursor:move">';
    htmlCode+=          '<td class="wtTL">&nbsp;&nbsp;<span class="do"></span><b class="doN">#title#</b> <a hideFocus="1" href="javascript:;" class="clR CloseButton"></a></td>';
    htmlCode+=          '<td class="wtTC">&nbsp;</td>';
    htmlCode+=          '<td class="wtTR">&nbsp;</td>';
    htmlCode+=      '</tr>';
    htmlCode+=      '<tr>';
    htmlCode+=          '<td colspan="3" style="padding-right:2px;">';
    htmlCode+=              '<div class="winTipC">';
    htmlCode+=                  '<div class="wTipCont">';
    htmlCode+=                  '</div>';
    htmlCode+=              '</div>';
    htmlCode+=          '</td>';
    htmlCode+=      '</tr>';
    htmlCode+=  '</table>';
    htmlCode+='</div>';
    var jContainer=$(htmlCode).appendTo(document.body);
    var jTitle=jContainer.find("tr:eq(0)");
    var jContent=jContainer.find(".wTipCont");
    var tmpPoint;
    this.jContainer=jContainer;
    this.jTitle=jTitle;
    this.jContent=jContent;
    this.closeButton=jContainer.find(".CloseButton");
    this.setTitle = function(text) {
        jContainer.find(".doN").text(text);
    };
 
    this.setContent = function(html) {
        this.jContent.html("");
        if (typeof (html) == "string") {
            this.jContent.html(html);
        } else if (typeof (html) == "object") {
            this.jContent.append(html);
        }
    };
    this.show = function(fix) {
        jContainer.show();
        //jContainer.css({ top: 0, left: 0 });
        //重设高度
        var top = ($(document.body).height() - jContainer.height()) / 2;
        var left = ($(document.body).width() - jContainer.width()) / 2;
        if (window != window.top) {
            try {
                var offset = $(frameElement).offset();
                top -= offset.top / 2;
                top -= offset.left / 2;
            } catch (e) { }
        }
        if (top < 0) top = 0;
        if (left < 0) left = 0;
        jContainer.css({ top: top, left: left });
        Glass.show();
        FloatingFrame.lastCurrent = FloatingFrame.current;
        FloatingFrame.current = this;
        if (this.onshow && !fix) { this.onshow() };
    };
    this.hide = function() {
        Glass.hide();
        jContainer.hide();
    };
    this.close = function() {
        this.hide();
        dispose();
        this.isDisposed = true;
        if (FloatingFrame.lastCurrent && FloatingFrame.current != FloatingFrame.lastCurrent && !FloatingFrame.lastCurrent.isDisposed) {
            FloatingFrame.current = FloatingFrame.lastCurrent;
            FloatingFrame.lastCurrent = null;
        }
        try {
            $("input:text:eq(0)").focus().blur();
        } catch (e) { }
    };
 
    init();
    var isMousedown=false;
    function init(){
        DM = new DragManager(jContainer[0], jTitle[0]);
    }
    function dispose(){
        jContainer.remove();
    }
}
FloatingFrame.alert = function(message, callback) {
    if (window != window.top) return window.top.FloatingFrame.alert(message, callback);
    var ff = new FloatingFrame();
    message = message.toString().replace(/(?:\\r)?\\n/g, "<br>");
    var htmlCode = "";
    htmlCode += '<ul>';
    htmlCode += '<li class="wTc"><b class="wcDot"></b><span class="wcDot1">' + message + '</span></li>';
    htmlCode += '<li class="wTcBut"><a href="javascript:;" class="but YesButton">确&nbsp;定</a></li>';
    htmlCode += '</ul>';
    ff.setTitle("系统提示");
    ff.setContent(htmlCode);
    setTimeout(function() {
        $(".YesButton", ff.jContainer).focus();
    }, 0);
    $(".CloseButton,.YesButton", ff.jContainer).click(
        function() {
            ff.close();
            try {
                if (callback) callback();
            } catch (e) { }
            return false;
        }
    );
    ff.show();
};
FloatingFrame.confirm = function(message, callback, cancelCallback, isYesAndNo) {
    if (window != window.top) return window.top.FloatingFrame.confirm(message, callback, cancelCallback, isYesAndNo);
    var ff = new FloatingFrame();
    var htmlCode = "";
    htmlCode += '<ul>';
    htmlCode += '<li class="wTc"><b class="wcDot"></b><span class="wcDot1">' + message + '</span></li>';
    if (isYesAndNo) {
        htmlCode += '<li class="wTcBut"><a href="javascript:;" class="but YesButton">是</a> <a href="javascript:;" class="but CancelButton">否</a></li>';
    } else {
        htmlCode += '<li class="wTcBut"><a href="javascript:;" class="but YesButton">确&nbsp;定</a> <a href="javascript:;" class="but CancelButton">取&nbsp;消</a></li>';
    }
    htmlCode += '</ul>';
    ff.setTitle("系统提示");
    ff.setContent(htmlCode);
    setTimeout(function() {
        $(".YesButton", ff.jContainer).focus();
    }, 0);
    $(".CloseButton,.YesButton,.CancelButton", ff.jContainer).click(
        function() {
            ff.close();
            try {
                if ($(this).hasClass("YesButton")) {
                    if (callback) callback();
                } else {
                    if (cancelCallback) cancelCallback(!$(this).hasClass("CancelButton"));
                }
            } catch (e) { }
            return false;
        }
    );
    ff.show();
};
FloatingFrame.prompt = function(title, message, defaultValue, callback, maxLength) {
    if (window != window.top) return window.top.FloatingFrame.prompt(title, message, defaultValue, callback, maxLength);
    try {
        if (callback && callback.constructor == Number) {
            maxLength = callback;
        }
        if (defaultValue.constructor == Function) {
            callback = defaultValue;
            defaultValue = "";
        }
    } catch (e) { }
    var ff = new FloatingFrame();
    var htmlCode = "";
    htmlCode += '<ul>';
    htmlCode += '<li class="wTc">' + message + '</li>';
    htmlCode += '<li class="wTcN"><input type="text" style="width:220px" class="inp" maxLength="{0}" /></li>';
    htmlCode += '<li class="wTcBut"><a href="javascript:;" class="but YesButton">确&nbsp;定</a> <a href="javascript:;" class="but CancelButton">取&nbsp;消</a></li>';
    htmlCode += '</ul>';
    htmlCode = htmlCode.format(maxLength ? maxLength : "");
    ff.setTitle(title);
    ff.setContent(htmlCode);
    setTimeout(function() {
        try {
            $(".inp", ff.jContainer).focus();
        } catch (e) { }
    }, 0);
 
    var $textbox = $("input", ff.jContainer);
    $textbox.val(defaultValue);
    $textbox.keypress(
        function(evt) {
            evt = evt || event;
            if (evt.keyCode == 13) {
                ff.jContainer.find(".YesButton").click();
            }
        }
    );
    $(".CloseButton,.YesButton,.CancelButton", ff.jContainer).click(
        function() {
            try {
                var text = ff.jContainer.find("input").val();
                ff.close();
                if ($(this).hasClass("YesButton") && callback) callback(text);
            } catch (e) { }
            return false;
        }
    );
    ff.show();
    $textbox[0].select();
};
//玻璃层
var Glass = {
    _glass: null,
    isShow: false,
    show: function() {
        if (!this._glass) {
            this._glass = $(
                "<div class='glass'><iframe></iframe><div class='glass' style='z-index:1000'></div></div>"
            ).appendTo(document.body);
        }
        this._glass.show();
        this.isShow = true;
    },
    hide: function() {
        if (this._glass) this._glass.hide();
        this.isShow = false;
    }
};
FloatingFrame.current=null;
FloatingFrame.show = function(html, title, width, height) {
    if (window != window.top) return window.top.FloatingFrame.show(html, title, width, height);
    var ff = new FloatingFrame();
    FloatingFrame.current = ff;
    ff.setContent(html);
    $(".CloseButton",ff.jContainer).focus();
    ff.setTitle(title || "");
    ff.jContainer.find(".CloseButton").click(
        function() {
            ff.close();
            return false;
        }
    );
    ff.show();
    if (width) {
        ff.jContainer.width(width + "px");
    }
    return ff;
};
 
FloatingFrame.open = function(title, src, width, height, fixSize, miniIcon, hideIcon,hideTitle) {
    if (window != window.top) return window.top.FloatingFrame.open(title, src, width, height, fixSize, miniIcon, hideIcon,hideTitle);
    var ff = new FloatingFrame();
    if (hideIcon) {
        if (hideIcon) {
            ff.jContainer.find("span.do:eq(0)").hide();
            ff.jContainer.find("b.doN:eq(0)").css("left", "12px");
        }
    }
    FloatingFrame.current = ff;
    if (fixSize) {
        ff.jContent = ff.jContent.parent();
        ff.jContent.css("padding", "0px");
        ff.jContent.html("");
    }
    if(hideTitle){
        ff.jContainer.find("tr:eq(0)").remove();
    }
    var html = String.format("<iframe src={0} frameBorder='0' scrolling=no style='width:100%;height:{2}px;'></iframe>",
			[src, width, height]);
    ff.setContent(html);
    $(".CloseButton", ff.jContainer).focus();
    ff.setTitle(title || "");
    ff.jContainer.find(".CloseButton").click(
        function() {
            if (ff.onclose) {
                var tobeClosed = true;
                try {
                    tobeClosed = ff.onclose();
                } catch (e) { }
                if (tobeClosed != false) {
                    ff.close();
                }
            } else {
                var theIframe = ff.jContainer.find("iframe")[0];
                if (theIframe) {
                    try {
                        if (theIframe.contentWindow.onFloatingFrameClose) {
                            var tobeClosed = true;
                            tobeClosed = theIframe.contentWindow.onFloatingFrameClose();
                            if (tobeClosed != false) {
                                ff.close();
                            }
                        } else {
                            ff.close();
                        }
                    } catch (e) {
                        ff.close();
                    }
                } else {
                    ff.close();
                }
            }
            return false;
        }
    );
    if (miniIcon) {
        ff.jContainer.find(".CloseButton").before('<a style="display:none" title="最小化窗口" href="javascript:;" class="clR1"></a>');
        ff.jContainer.find(".clR1").click(function() {
            FloatingFrame.minimize();
            return false;
        });
        //onload后才显示最小化按钮
        ff.jContainer.find("iframe").load(function() {
            ff.jContainer.find(".clR1").show();
        });
    }
    ff.show();
    if (width) {
        ff.jContainer.width(width + "px");
    }
    return ff;
};
FloatingFrame.setWidth = function(width,donotposition) {
    if (window != window.top) return window.top.FloatingFrame.setWidth(width);
    try {
        FloatingFrame.current.jContainer.width(width);
        if(!donotposition)FloatingFrame.current.show();
    } catch (e) { }
};
FloatingFrame.setHeight = function(height,donotposition) {
    if (window != window.top) return window.top.FloatingFrame.setHeight(height);
    try{
        FloatingFrame.current.jContent.find("iframe").css("height", height);
        if(!donotposition)FloatingFrame.current.show();
    }catch(e){}
};
FloatingFrame.close = function() {
    try{
        top.FloatingFrame.current.close();
    }catch(e){}
};
FloatingFrame.clearContainer = function() {
    if (window != window.top) return window.top.FloatingFrame.clearContainer();
    FloatingFrame.current.jContainer.find("tr:eq(0)").remove();
    FloatingFrame.current.jContainer.find(".winTipC").css("border", "0px").css("margin", "0px").css("padding", "0px");
    FloatingFrame.current.jContainer.find(".wTipCont").css("margin", "0px").css("padding", "0px");
};
//20090826 最小化窗口
FloatingFrame.minimize = function() {
    if (window != window.top) return window.top.FloatingFrame.minimize();
    var ff = FloatingFrame.current;
    /*
    ff.hide();
    */
    try{
        if (ff.onminimize) {
            ff.onminimize();
        }
    }catch(e){}
    Glass.hide();
    ff.jContainer.css({ left: -1000 });
    return ff;
};
 
if(!window.FF){
    FF = FloatingFrame;
}
/* >>>>>End   FloatingFrame.js */
 
 
/* >>>>>Begin menu.js */
/**
 * @tiexg 下拉菜单组件
 */
Menu = {
    MENU: null,
    lastMenu: null,
    createMenu: function(data, styles) {
        MENU = this;
        if (!styles) {
            styles = {		//样式表
                button: "tlBtn",
                menu: "dMenu",
                icon: "tlBtn3"
            };
        }
        var container = document.createElement("li");
        if (data["name"]) {
            container.id = data["name"];
        }
 
        container.innerHTML = "<a hideFocus='1' href='javascript:void(0)' class='" + styles["button"] + "'><b class='"
		+ styles["icon"] + "'></b><b class='mFont139'>" + data["text"] + "</b><b class=\"mDot139\"/></b><span/></a>";
 
        var ul = document.createElement("ul");
        ul.style.display = "none";
        ul.style.position = "absolute";
        if (data["width"]) {
            ul.style.width = data["width"];
        }
        ul.className = styles["menu"];
        for (var i = 0; i < data["items"].length; i++) {
            var item = data["items"][i];
            var li = document.createElement("li");
            if (data["width"]) {
                li.style.width = data["width"];
            }
            li.innerHTML = "<a href='javascript:;' hideFocus='1'>" + item.text + "</a>";
            ul.appendChild(li);
            if (item.click) {
                Utils.addEvent(li, "onclick", item.click);
            } else {	//触发统一的itemClick
                (function(data, item) {
                    Utils.addEvent(li, "onclick", function() {
                        data["itemClick"](item["data"])
                    })
                })(data, item);
            }
 
            Utils.addEvent(li, "onclick", this.hideMenu);
            Utils.addEvent(li, "onmouseover", function(e) {
                var target = e.srcElement || e.target;
            });
            Utils.addEvent(li, "onmouseout", function(e) {
                var target = e.srcElement || e.target;
            });
        }
        container.appendChild(ul);
        if (data["click"]) {
			$(container.firstChild.childNodes[1]).addClass("mLine139");
            Utils.addEvent(container.firstChild, "onclick", data["click"]);
            Utils.addEvent(container.firstChild.childNodes[2], "onclick", this.showMenu);
			Utils.addEvent(container.firstChild.childNodes[3], "onclick", this.showMenu);
        } else {
            Utils.addEvent(container.firstChild, "onclick", this.showMenu);
            Utils.addEvent(container.firstChild.childNodes[2], "onclick", this.showMenu);
        }
        return container;
    },
    showMenu: function(e) {
        if (MENU.lastMenu) {
            MENU.lastMenu.style.display = "none";
        }
        var target = e.srcElement || e.target;
        var root = Utils.findParent(target, "li");
        var btn = root.childNodes[0];
        var f = root.childNodes[1];
        f.style.position = "absolute";
        f.style.display = "block";
        var px = 0; var py = 0;
        px = root.offsetLeft;//$(btn).offset().left;//btn.offsetLeft;
        if(px==0){
			px=root.parentNode.offsetLeft;
		}
        py = root.offsetTop;//$(btn).offset().top;//btn.offsetTop;
        f.style.left = px + "px";
        f.style.top = (py + btn.offsetHeight).toString() + "px";
 
        MENU.lastMenu = f;
        Utils.stopEvent();
        Utils.addEvent(document, "onclick", MENU.docClick);
    },
    hideMenu: function(e) {
        if (e) {
            var target = e.srcElement || e.target;
            var u = Utils.findParent(target, "ul");
            u.style.display = "none";
        } else if (MENU.lastMenu) {
            MENU.lastMenu.style.display = "none";
        }
        MENU.lastMenu = null;
        Utils.stopEvent();
        Utils.removeEvent(document, "onclick", MENU.docClick);
    },
    docClick: function() {
        MENU.hideMenu();
    }
 
}
 
/* >>>>>End   menu.js */
 
 
/* >>>>>Begin PageTurnner.js */
//PageTurnner是一个不包含任何UI的逻辑对象
function PageTurnner(pageCount,pageIndex){
    var thePageTurnner=this;
    this.pageIndex=pageIndex;
 
    this.fristPage = function() {
        this.turnPage(1);
    };
    this.lastPage = function() {
        this.turnPage(pageCount);
    };
    this.nextPage = function() {
        this.turnPage(thePageTurnner.pageIndex + 1);
    };
    this.previousPage = function() {
        this.turnPage(thePageTurnner.pageIndex - 1);
    };
    this.turnPage = function(index) {
        if (index < 1 || index > pageCount || index == this.pageIndex) return;
        this.pageIndex = index;
        this.callPageChangeHandler(index);
    };
    this.pageChangeHandlers=[];
    this.addPageChangeListener = function(handler) {
        this.pageChangeHandlers.push(handler);
    };
    this.callPageChangeHandler = function(pageIndex) {
        for (var i = 0; i < this.pageChangeHandlers.length; i++) {
            this.pageChangeHandlers[i](pageIndex);
        }
    };
}
PageTurnner.createStyle = function(pageCount, pageIndex, containerId, callback) {
    var thePageTurnner = new PageTurnner(pageCount, pageIndex);
    var btnNext = createLink("下一页");
    var btnPrevious = createLink("上一页");
    var btnFrist = createLink("首页");
    var btnLast = createLink("末页");
    function createLink(text) {
        var a = document.createElement("a");
        a.innerHTML = text;
        a.href = "javascript:void(0)";
        return a;
    }
    btnFrist.onclick = function() { thePageTurnner.fristPage(); this.blur(); return false; };
    btnPrevious.onclick = function() { thePageTurnner.previousPage(); this.blur(); return false; };
    btnNext.onclick = function() { thePageTurnner.nextPage(); this.blur(); return false; };
    btnLast.onclick = function() { thePageTurnner.lastPage(); this.blur(); return false; };
 
    var select = document.createElement("select");
    for (var i = 1; i <= pageCount; i++) {
        var item = new Option(i.toString() + "/" + pageCount + "页", i);
        select.options.add(item);
        if (i == pageIndex) {
            item.selected = true;
        }
    }
    select.onchange = function() { thePageTurnner.turnPage(this.selectedIndex + 1); };
    thePageTurnner.addPageChangeListener(
        function(index) {
            select.options[index - 1].selected = true;
        }
    );
    setLinkDisabled(btnFrist, true);
    setLinkDisabled(btnPrevious, true);
    thePageTurnner.addPageChangeListener(disabledButton);
    thePageTurnner.addPageChangeListener(callback);
    var container;
    if (typeof (containerId) == "string") {
        container = document.getElementById(containerId);
    } else {
        container = containerId;
    }
    //container.appendChild(document.createTextNode("[ "));
    container.appendChild(btnPrevious);
    container.appendChild(document.createTextNode(" "));
    container.appendChild(btnNext);
    container.appendChild(document.createTextNode(" "));
    container.appendChild(select);
 
    disabledButton(pageIndex);
    function disabledButton(index) {
        setLinkDisabled(btnFrist, false);
        setLinkDisabled(btnPrevious, false);
        setLinkDisabled(btnNext, false);
        setLinkDisabled(btnLast, false);
        if (index == 1) {
            setLinkDisabled(btnFrist, true);
            setLinkDisabled(btnPrevious, true);
        }
        if (index == pageCount) {
            setLinkDisabled(btnNext, true);
            setLinkDisabled(btnLast, true);
        }
    }
    function setLinkDisabled(link, value) {
        if (value) {
            //link.style.color="silver";
            link.style.display = "none";
        } else {
            //link.style.color="";
            link.style.display = "";
        }
    }
};
/* >>>>>End   PageTurnner.js */
 
 
/* >>>>>Begin poptip.js */
/**
 * @tiexg PopTip,用于右下角弹出式的提醒
 */
var PopTip={
	element:null,
	show:function(title,text){
		PopTip.isClosed=false;
		if(!this.element){
			this.element=document.createElement("div");
			this.element.id="popTip";
			this.element.className="popTip";
			this.element.style.position="absolute";
			this.element.style.right="1px";
			this.element.style.bottom="0px";
			this.element.style.display="none";
 
			document.body.appendChild(this.element);
		}
		this.element.innerHTML=this.getHtml(title,text);
		var tip=this;
		var	maxY=0;
		var offsetY=0;
		var intervalId=window.setInterval(anmiate,100);
		function anmiate(){
			if (PopTip.isClosed) {	//在弹出的过程中点了关闭
				tip.element.style.display = "none";
				PopTip.isClosed=false;
				window.clearInterval(intervalId);
			}
			else {
				tip.element.style.display = "block";
				maxY = tip.element.offsetHeight;
				if (offsetY <= maxY + 1) {
					tip.element.style.bottom = (-maxY + offsetY).toString() + "px";
				}
				else {
					window.clearInterval(intervalId);
					offsetY = 0;
					if(!tip.isOver){
						tip.timerClose=setTimeout(tip.close,5000);
					}
					
				}
				var m = 30 * (maxY - offsetY) / maxY;//缓冲系数
				offsetY += m < 2 ? 2 : m; //偏移量,要注意极限值,否则无法停下来.
			}
			
		}
		
	},
	timerClose:0,
	isClosed:false,
	isOver:false,
	close:function(){
		var t=PopTip;
		t.element.style.bottom=(-t.element.offsetHeight).toString()+"px";
		t.element.style.display="none";
		t.isClosed=true;
	},
	mouseMove:function(){
		isOver=true;
		//alert(PopTip.timerClose);
		clearTimeout(PopTip.timerClose);
	},
	mouseOut:function(){
		isOver=false;
		this.timerClose=setTimeout(PopTip.close,5000);
	},
	getHtml:function(title,text){
		var s="<div class=\"newTip\" onmousemove=\"PopTip.mouseMove()\" onmouseout=\"PopTip.mouseOut()\"><ul><li><span class=\"doN\">{title}</span> <a id=\"aClosePopTip\" href=\"javascript:;\" hidefocus=\"1\" class=\"clR\" onclick=\"PopTip.close();return false;\"></a></li></ul><span class=\"nTc\">{text}</span></div>";
		return String.format(s,{title:title,text:text});
	}
};
 
/* >>>>>End   poptip.js */
 
 
/* >>>>>Begin Repeater.js */
/**
 * @author tiexg
 * Repeater，实现类似于asp.net的数据绑定，模板列机制。
 */
function Repeater(obj){	
	this.HtmlTemplate=null;
	this.HeaderTemplate=null;
	this.FooterTemplate=null;
	this.ItemTemplate;
	this.ItemTemplateOrign;
	this.SeparateTemplate;
	this.Functions=null;
	this.DataSource=null;
	this.ItemContainer;
	this.ItemDataBound=null;
	this.RenderMode=0;	//0，同步渲染，界面一次性组装，1.异步渲染，50毫秒生成一行
	this.RenderCallback=null;
	this.Element;	
	RP=this;
	this.Instance=null;
	this.DataRow=null;	//当前行数据
	if (typeof(obj) != undefined) {
		if (typeof(obj) == "string") {
			this.Element = document.getElementById(obj);
		}
		else {
			this.Element = obj;
		}
		//n=findChild(obj,"name","item");
		
 
	}
 
	this.DataBind = function() {
	    if (this.DataSource.length == 0) {
	        return;
	    }
	    if (this.HtmlTemplate == null) {
	        this.HtmlTemplate = this.Element.innerHTML;
	    }
	    //this.ItemTemplate=this.HtmlTemplate.match(/(<!--item\s+start-->)([\r\n\w\W]+)(<!--item\s+end-->)/ig)[0];
	    var re = /(<!--item\s+start-->)([\r\n\w\W]+)(<!--item\s+end-->)/i;
	    //re.exec(this.HtmlTemplate);
	    var match = this.HtmlTemplate.match(re);
	    this.ItemTemplateOrign = match[0];
	    this.ItemTemplate = match[2];
 
	    reg1 = /\$\w+\s?/ig;
	    reg2 = /\@(\w+)\s?\((.*?)\)/ig;
	    var result = new Array();
	    for (var i = 0; i < this.DataSource.length; i++) {
	        var dataRow = this.DataSource[i];
	        dataRow["index"] = i; 	//追加索引
	        this.DataRow = dataRow; //设置当前行数据
	        var row = this.ItemTemplate;
 
	        row = row.replace(reg2, function($0, $1, $2) { //替换函数
	            var name = $1.trim();
	            var paramList = $2.split(",");
	            var param = new Array();
	            for (var i = 0; i < paramList.length; i++) {
	                param.push(dataRow[paramList[i]]);
	            }
	            if (RP.Functions[name]) {
	                //return RP.Functions[name](param);
	                var context = RP;
	                if (RP.Instance) {
	                    RP.Instance.DataRow = dataRow;
	                    context = RP.Instance;
	                }
	                return RP.Functions[name].apply(context, param)
	            }
 
 
	        });
	        row = row.replace(reg1, function($0) { //替换变量
	            m = $0.substr(1).trim();
	            return dataRow[m];
 
	        });
 
	        var itemArgs = {	//事件参数
	            index: i,
	            data: dataRow,
	            html: row
	        };
	        if (this.ItemDataBound) {	//是否设置了行绑定事件
	            var itemRet = this.ItemDataBound(itemArgs);
	            if (itemRet) {
	                row = itemRet;
	            }
	        }
	        result.push(row);
	    }
	    this.Render(result);
 
 
 
	};
 
	this.Render = function(result) {
	    if (!this.RenderCallback) {
	        var str = result.join("");
	        var html = "";
	        if (this.HtmlTemplate) {
	            html = this.HtmlTemplate.replace(this.ItemTemplateOrign, str);
	        } else {
	            html = this.ItemTemplate.replace(this.ItemTemplateOrign, str);
	        }
	        if (this.HeaderTemplate)
	            html = this.HeaderTemplate + html;
	        if (this.FooterTemplate) {
	            html = html + this.FooterTemplate;
	        }
	        this.Element.innerHTML = html;
	    } else {
	        var n = 0;
	        var el = this.Element;
	        var rowObj = null;
	        var args = { index: 0, element: el, html: "", rowCount: result.length };
	        var intervalId = setInterval(function() {
	            if (n < result.length) {
	                //el.innerHTML=RP.HtmlTemplate.replace(RP.ItemTemplate,result[0]);
	                args.index = n;
	                args.element = el;
	                args.html = result[n];
	                RP.RenderCallback(args);
	                n++;
	            } else {
	                clearInterval(intervalId);
	            }
	        }, 50);
	    }
	}
 
		
}
 
 
String.prototype.trim = function() { return this.replace(/^\s+|\s+$/, ''); };
Object.extend = function(A, $) {
    for (var _ in $) {
        A[_] = $[_];
    }
    return A;
};
//Object.extend(Repeater.prototype,DataList)
/**
 * @author tiexg
 * DataList控件，依赖于Repeater
 */
function DataList(obj){
	this.Layout=1;	// 0为使用div布局方式, 1为使用table布局方式。
	this.RepeatColumns=5;
	this.ItemTemplate;
	this.id="table_list";
	this.Style_Cell;
	var rp=new Repeater(obj);
 
	var DL=this;
	this.DataSource=null;
	this.Functions=null;
	this.DataRow;	//当前行数据
 
 
	var table=document.createElement("table");
	var tr;
	rp.RenderCallback = function(arg) {
 
	    var td = document.createElement("td");
	    td.innerHTML = arg.html;
	    if (DL.Style_Cell) {
	        td.className = DL.Style_Cell;
	    }
 
	    if (arg.index == 0) {	//第一个数据
	        var table = document.createElement("table");
	        var tbody = document.createElement("tbody");
	        tr = document.createElement("tr");
	        table.appendChild(tbody);
	        tbody.appendChild(tr);
	        rp.Element.appendChild(table);
	        table.id = DL.id;
	        tr.appendChild(td);
	    } else if (arg.index == arg.rowCount - 1) {	//最后一个数据
	        tr.appendChild(td);
	    } else if (arg.index % DL.RepeatColumns == 0) {	//换行
	        tbody = tr.parentNode;
	        tr = document.createElement("tr");
	        tbody.appendChild(tr);
	        tr.appendChild(td);
	    } else {
	        tr.appendChild(td);
	    }
 
 
	};
	this.DataBind = function() {
	    var arr = new Array();
	    //arr.push("<table>");
	    arr.push("<!--item start-->");
	    arr.push(this.ItemTemplate);
	    arr.push("<!--item end-->");
	    //arr.push("</table>");
	    rp.HtmlTemplate = arr.join("");
 
	    rp.DataSource = this.DataSource;
	    rp.Functions = DL.Functions;
	    rp.Instance = DL;
	    rp.DataBind();
 
	};
	
	
}
 
 
/* >>>>>End   Repeater.js */
 
 
/* >>>>>Begin UI.AutoCompleteMenu.js */
function AutoCompleteMenu(/*文本框*/host, /*输入回调*/inputCallback, /*子项选中回调*/itemClickHandler) {
    var This = this;
    var key = {
        up: 38,
        down: 40,
        enter: 13,
        space: 32,
        tab: 9,
        left: 37,
        right: 39
    };
    var isShow = false;
    var doc = host.ownerDocument;
    var itemFocusColor = "#3399FE";
    var menuCSSText = "position:absolute;z-index:101;display:none;border:1px solid #99ba9f;height:200px;overflow:auto;overflow-x:hidden;background:white";
    var itemCSSText = "width:100%;line-height:20px;text-indent:3px;cursor:pointer;display:block;";
    var bgIframe = doc.createElement("iframe");
    with (bgIframe.style) {
        position = "absolute";
        zIndex = 100;
        display = "none";
    }
    var items = [];
    var container = doc.createElement("div");
    container.onclick = function(e) {
        Utils.stopEvent(e);
    }
    container.onmousedown = function(e) {
        Utils.stopEvent(e);
    }
    if (document.all) {
        $(document).click(hide);
    }
    function clear() {
        items.length = 0;
        container.innerHTML = "";
    }
    this.addItem = function(value, title) {
        if (typeof value == "object") {
            var span = value;
        } else {
            var span = doc.createElement("span");
            span.value = value;
            span.innerHTML = title;
        }
        if (document.all) {
            span.style.cssText = itemCSSText;
        } else {
            span.setAttribute("style", itemCSSText);
        }
 
        span.onmousedown = function() {
            itemClickHandler(this);
            hide();
        }
        span.onmouseover = function() { selectItem(this); }
        span.menu = this;
        span.selected = false;
        items.push(span);
        container.appendChild(span);
    }
    function getSelectedItem() {
        var index = getSelectedIndex();
        if (index >= 0) return items[index];
        return null;
    }
    function getSelectedIndex() {
        for (var i = 0; i < items.length; i++) {
            if (items[i].selected) return i;
        }
        return -1;
    }
    //设置选中行
    function selectItem(item) {
        var last = getSelectedItem();
        if (last != null) blurItem(last);
        item.selected = true;
        item.style.backgroundColor = itemFocusColor;
        item.style.color = "white";
        menuScroll(item, container); //如果选中的项被遮挡的话则滚动滚动条
        function menuScroll(element, container) {
            var elementView = {
                //top:      element.offsetTop,这样写ff居然跟ie的值不一样
                top: getSelectedIndex() * element.offsetHeight,
                bottom: element.offsetTop + element.offsetHeight
            };
            var containerView = {
                top: container.scrollTop,
                bottom: container.scrollTop + container.offsetHeight
            };
            if (containerView.top > elementView.top) {
                container.scrollTop -= containerView.top - elementView.top;
 
            } else if (containerView.bottom < elementView.bottom) {
                container.scrollTop += elementView.bottom - containerView.bottom;
            }
        }
    }
    //子项失去焦点
    function blurItem(item) {
        item.selected = false;
        item.style.backgroundColor = "";
        item.style.color = "";
    }
    function show() {
        if (isShow) return;
        if (container.parentNode != doc.body) {
            doc.body.appendChild(container);
            doc.body.appendChild(bgIframe);
        }
        with (container.style) {
            Utils.offsetHost(host, container);
            display = "block";
            width = Math.max(host.offsetWidth,300) + "px";
            if (items.length < 7) {
                height = items[0].offsetHeight * items.length + 10 + "px";
            } else {
                height = items[0].offsetHeight * 7 + "px";
            }
        }
        with (bgIframe.style) {
            left = container.style.left;
            top = container.style.top;
            width = container.offsetWidth;
            height = container.offsetHeight;
            if (document.all) display = "";
        }
        selectItem(items[0]); //显示的时候选中第一项
        isShow = true;
    }
    function hide() {
        if (!isShow) return;
        container.style.display = "none";
        bgIframe.style.display = "none";
        clear();
        isShow = false;
    }
    if (document.all) {
        container.style.cssText = menuCSSText;
        host.attachEvent("onkeyup", host_onkeyup);
        host.attachEvent("onblur", host_onblur);
        host.attachEvent("onkeydown", host_onkeydown);
    } else {
        container.setAttribute("style", menuCSSText);
        host.addEventListener("keyup", host_onkeyup, true);
        host.addEventListener("blur", host_onblur, true);
        host.addEventListener("keydown", host_onkeydown, true);
    }
    function host_onkeyup(evt) {
        switch ((evt || event).keyCode) {
            case key.enter:
            case key.tab:
            case key.up:
            case key.down:
            case key.left:
            case key.right: return;
        }
        hide();
        inputCallback(This, evt || event);
        if (items.length > 0) show();
    }
    function host_onblur() {
        if (!document.all) hide();
    }
    function host_onkeydown(evt) {
        evt = evt || event;
        switch (evt.keyCode) {
            case key.space:
            case key.enter: doEnter(); break;
            case key.up: doUp(); break;
            case key.down: doDown(); break;
            case key.right:
            case key.left: hide(); break;
            default: return;
        }
        function doEnter() {
            var item = getSelectedItem();
            if (item != null) item.onmousedown();
            if (evt.keyCode == key.enter) {
                Utils.stopEvent(evt);
            }
        }
        function doUp() {
            var index = getSelectedIndex();
            if (index >= 0) {
                index--;
                index = index < 0 ? index + items.length : index;
                selectItem(items[index]);
            }
        }
        function doDown() {
            var index = getSelectedIndex();
            if (index >= 0) {
                index = (index + 1) % items.length;
                selectItem(items[index]);
            }
        }
    }
}
AutoCompleteMenu.createAddrMenu_compose = function(host, userAllEmailText) {}
AutoCompleteMenu.createAddrMenu = function(host, userAllEmailText, dataSource, splitLetter) {
    if (typeof userAllEmailText == "undefined") {
        userAllEmailText = true;
    }
    splitLetter = splitLetter || ";";
    var getMailReg = /^([^@]+)@(.+)$/
    var getInput = /(?:[;,；，]|^)\s*([^;,；，\s]+)$/;
    var allLinkMan = [];
    if (!top.LinkManList) return;
    if (!dataSource) {
        var GroupList = top.GroupList;
        var LinkManList = top.LinkManList;
        var LastLinkList = top.LastLinkList;
        var CloseLinkList = top.CloseLinkList;
    } else {
        var GroupList = dataSource.GroupList;
        var LinkManList = dataSource.LinkManList;
        var LastLinkList = dataSource.LastLinkList;
        var CloseLinkList = dataSource.CloseLinkList;
    }
    if (LastLinkList) {
        allLinkMan = LastLinkList.concat(LinkManList);
    } else {
        allLinkMan = LinkManList;
    }
    if (CloseLinkList) {
        allLinkMan = allLinkMan.concat(CloseLinkList);
    }
 
    function autoLinkMan(menu) {
        var match = host.value.match(getInput);
        if (!match) return false;
        var txt = match[1].trim().toLowerCase();
        if (txt == "") return false;
        try{
            if(Utils.isChinaMobileNumber(txt) && txt.length==11){
                host.value=host.value.replace(/([;,；，]|^)\s*([^;,；，\s]+)$/,"$1"+txt+"@139.com;");
                return;
            }
        }catch(e){}
        var tmp_arr = [];
        var count = 0;
        for (var i = 0, j = allLinkMan.length; i < j; i++) {
            try {
                var obj = allLinkMan[i];
                if (!obj.title) {
                    obj.title = "\"" + obj.name + "\"<" + obj.addr + ">";
                    var match = obj.addr.match(getMailReg);
                    if (match) {
                        obj.mailID = match[1];
                        obj.mailDomain = match[2];
                    }
                }
                if (host.value.indexOf(obj.title) >= 0) continue;
                var title = "";
                var theIndex;
                if (txt.indexOf("@") > 0 && (theIndex = obj.addr.toLowerCase().indexOf(txt)) >= 0) {//如果用户输入了@,则按整个邮件地址mailID@domain.com去搜索
                    txt = obj.addr.substring(theIndex, theIndex + txt.length);
                    title = obj.title.replace(txt, "[b]" + txt + "[/b]");
                } else if ((theIndex = obj.name.toLowerCase().indexOf(txt)) >= 0) {//如果没有输入@,则先搜索好友名称部分,即["好友名称"<mailID@domain.com>]
                    txt = obj.name.substring(theIndex, theIndex + txt.length);
                    title = "\"" + obj.name.replace(txt, "[b]" + txt + "[/b]") + "\"<" + obj.addr + ">";
                } else if ((theIndex = obj.mailID.toLowerCase().indexOf(txt)) >= 0) {//如果名称没有匹配,再搜索邮件地址的mailID部分
                    txt = obj.mailID.substring(theIndex, theIndex + txt.length);
                    title = "\"" + obj.name + "\"<" + obj.mailID.replace(txt, "[b]" + txt + "[/b]") + "@" + obj.mailDomain + ">";
                } else if ((obj.quanpin && obj.quanpin.indexOf(txt) >= 0) || (obj.jianpin && obj.jianpin.indexOf(txt) >= 0)) {
                    title = obj.title;
                }
                if (title != "") {
                    if (!isRepeat(tmp_arr, obj)) {
                        tmp_arr.push(obj);
                        var _value = userAllEmailText == true ? obj.title : obj.addr;
                        menu.addItem(_value, Utils.htmlEncode(title).replace(/\[/g, "<").replace(/\]/g, ">"));
                        count++;
                    }
                }
                if (count >= 50) break;
            } catch (e) { }
        }
    }
    $(host).keydown(function(e) {
        if (e.keyCode == 8 && !e.ctrlKey && !e.shiftKey) {
            var p = getTextBoxPos(this);
            if (!p || p.start != p.end || p.start == 0 || p.start < this.value.length) return;
            var lastValue = this.value;
            var deleteChar = lastValue.charAt(p.start - 1);
            if (/[;,；，]/.test(deleteChar)) {
                var leftText = lastValue.substring(0, p.start);
                var rightText = lastValue.substring(p.start, lastValue.length);
                var cutLeft = leftText.replace(/(^|[;,；，])[^;,；，]+[;,；，]$/, "$1$1");
                this.value = cutLeft + rightText;
            }
        }
    });
    function isRepeat(arr, item) {
        for (var i = arr.length - 1; i >= 0; i--) {
            if (item.id && item.id == arr[i].id) return true;
        }
        return false;
    }
    function linkManItemClickHandler(item) {
        host.value = host.value.replace(/；/g,";").replace(/，/g,",");
        host.value = host.value.replace(/([;,]|^)\s*([^;,\s]+)$/, "$1" + item.value + splitLetter);
    }
    init();
    function init() {
        if (LinkManList) {
            new AutoCompleteMenu(host, autoLinkMan, linkManItemClickHandler);
        }
    }
}
 
function getTextBoxPos(textBox) {
    var start = 0;
    var end = 0;
    if (typeof (textBox.selectionStart) == "number") {
        start = textBox.selectionStart;
        end = textBox.selectionEnd;
    }
    else if (document.selection) {
        textBox.focus();
        var workRange = document.selection.createRange();
        var selectLen = workRange.text.length;
        if (selectLen > 0) return null;
        textBox.select();
        var allRange = document.selection.createRange();
        workRange.setEndPoint("StartToStart", allRange);
        var len = workRange.text.length;
        workRange.collapse(false);
        workRange.select();
        start = len;
        end = start + selectLen;
    }
    return { start: start, end: end };
}
AutoCompleteMenu.createPostfix = function(host) {
    new AutoCompleteMenu(
		host,
		function(menu) {
		    var arr = ["@sina.com", "@sohu.com", "@21cn.com", "@tom.com", "@yahoo.com.cn", "@yahoo.cn"];
		    var txt = host.value;
		    if ($.trim(txt) == "") return;
		    var match = txt.match(/\w+(@[\w.]*)/);
		    for (var i = 0; i < arr.length; i++) {
		        if (match) {
		            if (arr[i].indexOf(match[1]) == 0 && arr[i] != match[1]) {
		                var value = txt.match(/^([^@]*)@/)[1];
		                menu.addItem(value + arr[i], value + arr[i]);
		            }
		        } else {
		            menu.addItem(txt + arr[i], txt + arr[i]);
		        }
		    }
		},
		function(item) {
		    host.value = item.value;
		}
	)
}
/* 包装一个使用实例,可以根据输入提示手机号码 */
AutoCompleteMenu.createPhoneNumberMenuFromLinkManList = function(host, withAddrName, data) {
    var regMatchPhoneNumber = /(?:^|[;,])\s*([^;,]+)$/;
    var LinkManList = window.LinkManList;
    if (data) {
        LinkManList = data.LinkManList || data;
    }
    function textChanged(menu) {
        var match = host.value.match(regMatchPhoneNumber);
        var inputNumber = "";
        if (match) {
            inputNumber = match[1].toLowerCase();
        } else {
            return false;
        }
        var matchedCount = 0;
        for (var i = 0, j = LinkManList.length; i < j; i++) {
            if (!LinkManList[i].addr) continue;
            var theinfo = LinkManList[i];
            var num = theinfo.addr.replace(/\D/g, "");
            var pname = theinfo.name.replace(/[<>"']/g, "");
            var nameIndex;
            if (host.value.indexOf(num) >= 0) continue;
            if (num.indexOf(inputNumber) >= 0) {
                var str = num.replace(inputNumber, "<span style='color:Red'>" + inputNumber + "</span>")
                if (pname) str = "\"" + pname + "\"<" + str + ">";
                if (withAddrName) {
                    menu.addItem("\"" + pname + "\"<" + num + ">", str);
                } else {
                    menu.addItem(num, str);
                }
                matchedCount++;
            } else if ((nameIndex = pname.toLowerCase().indexOf(inputNumber)) >= 0) {
                var _inputNumber = pname.substring(nameIndex, nameIndex + inputNumber.length);
                var str = pname.replace(_inputNumber, "<span style='color:Red'>" + _inputNumber + "</span>")
                if (pname) str = "\"" + str + "\"<" + num + ">";
                if (withAddrName) {
                    menu.addItem("\"" + pname + "\"<" + num + ">", str);
                } else {
                    menu.addItem(num, str);
                }
                matchedCount++;
            } else if ((theinfo.quanpin && theinfo.quanpin.indexOf(inputNumber) >= 0) || (theinfo.jianpin && theinfo.jianpin.indexOf(inputNumber) >= 0)) {
                var str = "\"" + pname + "\"<" + num + ">";
                if (withAddrName) {
                    menu.addItem(str, str);
                } else {
                    menu.addItem(num, str);
                }
                matchedCount++;
            }
            if (matchedCount >= 50) break;
        }
        return !(matchedCount == 0);
    }
    function itemClick(item) {
        host.value = host.value.replace(/([;,]|^)\s*([^;,\s]+)$/, "$1" + item.value + ",");
    }
    window.top.Contacts.ready(function() {
        if (!window.LinkManList) {
            top.Contacts.init("mobile", window);
        }
        new AutoCompleteMenu(host, textChanged, itemClick, withAddrName);
    });
}
 
/* 包装一个使用实例,可以根据输入提示手机号码 */
AutoCompleteMenu.createPhoneNumberMenuForSearchByMobile = function(host) {
    var regMatchPhoneNumber = /(?:^|[;,])\s*(\d+)$/;
    function textChanged(menu) {
        var match = host.value.match(regMatchPhoneNumber);
        var inputNumber = "";
        if (match) {
            inputNumber = match[1];
        } else {
            return false;
        }
        var matchedCount = 0;
        for (var i = 0, j = LinkManList.length; i < j; i++) {
            if (!LinkManList[i].addr) continue;
            var num = LinkManList[i].addr.toString();
            var pname = LinkManList[i].name;
            if (host.value.indexOf(num) >= 0) continue;
            if (num.indexOf(inputNumber) == 0) {
                var str = num.replace(inputNumber, "<span style='color:Red'>" + inputNumber + "</span>")
                if (pname) str = "(" + pname + ")" + str;
                menu.addItem(num, str);
                matchedCount++;
            }
            if (matchedCount >= 50) break;
        }
        return !(matchedCount == 0);
    }
    function itemClick(item) {
        host.value = host.value.replace(/([;,]|^)\s*([^;,\s]+)$/, "$1" + item.value);
    }
    new AutoCompleteMenu(host, textChanged, itemClick);
}
 
/* 包装一个使用实例,可以根据输入提示手机号码 */
AutoCompleteMenu.createPhoneNumberMenu = function(/*文本框*/host, /*手机号码数组:Array*/numbers) {
    var regMatchPhoneNumber = /(?:^|[;,])\s*(\d+)$/;
    function textChanged(menu) {
        var match = host.value.match(regMatchPhoneNumber);
        var inputNumber = "";
        if (match) {
            inputNumber = match[1];
        } else {
            return false;
        }
        var matchedCount = 0;
        for (var i = 0, j = numbers.length; i < j; i++) {
            if (!numbers[i].number) continue;
            var num = numbers[i].number.toString();
            if (host.value.indexOf(num) >= 0) continue;
            if (num.indexOf(inputNumber) == 0) {
                var str = num.replace(inputNumber, "<span style='color:Red'>" + inputNumber + "</span>")
                if (numbers[i].name) str = "(" + numbers[i].name + ")" + str;
                menu.addItem(num, str);
                matchedCount++;
            }
            if (matchedCount >= 50) break;
        }
        return !(matchedCount == 0);
    }
    function itemClick(item) {
        host.value = host.value.replace(/([;,]|^)\s*([^;,\s]+)$/, "$1" + item.value + ",");
    }
    new AutoCompleteMenu(host, textChanged, itemClick);
}
 
/* >>>>>End   UI.AutoCompleteMenu.js */
 
 
/* >>>>>Begin waitpannel.js */
/**
 * 显示正在加载中.......
 */
var WaitPannel=new Object();
WaitPannel.show = function(msg,delay) {
    div = document.getElementById("contextWaitPannel");
    if (!div) {
        div = document.createElement("div");
        div.id = "contextWaitPannel";
        div.className = "loadingInfo";
        div.style.position = "absolute";
        div.style.zIndex = "99999999";
        if (!msg) {
            msg = "加载中，请稍候........";
        }
        div.innerHTML = msg.encode();
        div.style.left = document.body.clientWidth / 2 - 20;
        div.style.top = document.body.clientHeight / 2 - 30;
        document.body.appendChild(div);
    }
 
    div.style.left = document.body.clientWidth / 2 - 20;
    div.style.top = document.body.clientHeight / 2 - 30;
    //div.display="block";
	if(delay){
		setTimeout(WaitPannel.hide,delay);
	}
};
 
WaitPannel.hide = function() {
    div = document.getElementById("contextWaitPannel");
    if (div) {
        document.body.removeChild(div);
        //div.style.display="none";
    }
 
 
};
 
/* >>>>>End   waitpannel.js */
 
 
/* >>>>>Begin popmenu.js */
//PopMenu是一个独立的对象
function PopMenu(containerClass){
    Utils.stopEvent();
    var theMenu=this;
    var container=document.createElement("div");
    this.container=container;
    container.id="editorSelect";
    container.className=containerClass||"editorSelect";
    container.style.display="none";
    var documentClick=null;
    this.show = function(host) {
        if (PopMenu.current) PopMenu.current.hide();
        PopMenu.current = theMenu;
        document.body.appendChild(container);
        var offset = $(host).offset();
        container.style.left = offset.left + "px";
        container.style.top = offset.top + $(host).height() + "px";
        container.style.display = "block";
        container.style.position = "absolute";
        documentClick = function() {
            $(this).unbind("click", arguments.callee);
            if (PopMenu.current) PopMenu.current.hide();
        };
        $(document).click(documentClick);
    };
    container.onclick = function(e) {
        Utils.stopEvent();
    };
    this.hide = function() {
        if (!PopMenu.current) return;
        if (container.parentNode) container.parentNode.removeChild(container);
        if (theMenu.onHide) theMenu.onHide();
        $(document).unbind("click", documentClick);
        PopMenu.current = null;
    };
    this.addItem = function(title, clickHandler) {
        var item;
        if (typeof (title) == "string") {
            item = document.createElement("a");
            item.innerHTML = title;
        } else {
            item = title;
        }
        item.href = "javascript:void(0)";
        item.onclick = function(evt) {
            if (clickHandler) clickHandler(this);
            theMenu.hide();
        };
        container.appendChild(item);
    };
    this.setContent = function(obj) {
        if (typeof obj == "string") {
            container.innerHTML = obj;
        } else {
            container.innerHTML = "";
            container.appendChild(obj);
        }
    };
} 
 
/* >>>>>End   popmenu.js */
 
 
/* >>>>>Begin tabpage.js */
function TabPage(container,list,callback){
	TheTab=this;
	this.TabList=list;
	this.Container=container;
	this.SelectedIndex=0;	//当前选中项索引
	this.OnTabChange=callback;	//选中tab回调事件
	this.tabControl=null;		//tab栏的容器
	this.tabContent=null;		//内容栏的容器
	this.contentList=new Object();	//内容栏的缓存列表
	if(this.tabControl==null){	
		this.tabControl=document.createElement("div");
		this.Container.appendChild(this.tabControl);
		this.tabContent=document.createElement("div");
		this.Container.appendChild(this.tabContent);
		this.tabContent.className="tab_content";
	}
	
	//设置tab页的内容，可以传字符串也可以传dom element，实现了对内容节点的缓存
	this.SetPageContent = function(content) {
	    var key = this.SelectedIndex;
	    if (this.tabContent.childNodes.length > 0) {	//先删除原节点
	        this.tabContent.removeChild(this.tabContent.childNodes[0]);
	    }
 
	    if (this.contentList[key]) {	//本tab页已打开过
	        this.tabContent.appendChild(this.contentList[key]);
	    }
	    else {	//本tab页第一次加载
	        var c = document.createElement("div");
	        this.tabContent.appendChild(c);
	        if (typeof (content) == "string") {
	            c.innerHTML = content;
	        } else {
	            c.appendChild(content);
	        }
	        this.contentList[key] = c;
	    }
 
 
	};
	//创建和重新显示tab栏
	this.RenderTab = function(isInit) {
	    var idx = 0;
	    for (elem in this.TabList) {
	        obj = this.TabList[elem];
	        var tab;
	        if (isInit) {	//首次调用时创建tab
	            tab = document.createElement("div");
	            tab.style.display = "inline";
	            tab.innerHTML = obj;
	            this.tabControl.appendChild(tab);
	        } else {
	            tab = this.tabControl.childNodes[idx];
	        }
	        if (this.SelectedIndex == idx) {
	            tab.className = "tab_on";
	        } else {
	            tab.className = "tab";
	        }
	        (function() {
	            var i = idx;
	            tab.onclick = function() {
	                TheTab.ChangeTab(i);
	            };
	        })();
	        idx++;
 
	    }
	};
	//切换tab栏
	this.ChangeTab = function(targetIndex) {
	    this.SelectedIndex = targetIndex;
	    this.RenderTab(false);
	    this.OnTabChange(targetIndex);
	};
	
	this.RenderTab(true);
	this.ChangeTab(0);
	
}
 
/* >>>>>End   tabpage.js */
 
 
/* >>>>>Begin dialogBoxGuide.js */
 // JavaScript Document
// jim Liang
/* 该组件主要是用来实现功能向导：在指定区域显示指定的对话框，当用户点击类似“下一步“按钮“
该组建依赖于jquery框架，在使用前先加载该框架
为了重用之前的梦版本层，在这里直接调用FloatingFrame.js里面的蒙板层，因此在如果蒙板，则需要调用FloatingFrame.js
同时引用的样式必须包含有下面样式，否则蒙板效果出不来：
.glass, .glass iframe{width : 100 % ; height : 100 % ; position : absolute; z - index : 999; left : 0; top : 0; background - color : black; opacity : 0.1; - ms - filter : "progid:DXImageTransform.Microsoft.Alpha(Opacity=10)"; #filter : alpha(opacity = 10); }
 */
/* comm function */
// UserData.showPwdSetGuid == 1，有这个就代表是新用户
// 你在main_ext.js里搜索一下UserData.showPwdSetGuid == 1
 
if(typeof caixun == "undefined")
{
   caixun = cx =
   {
   }
   ;
}
;
/* *
 * 根据elementId获取元素
 * @param {Object} elementId
 */
cx.$ = function(elementId)
{
   if(typeof elementId == "undefined")
   {
      return null;
   }
   try
   {
      var obj=document.getElementById(elementId);
      if(obj==null){
            obj=window.top.document.getElementById("welcome").contentWindow.document.getElementById(elementId);
                           
      } 
      return obj;
   }
   catch(e)
   {  
      return null;
   }
}
;
/* *
 * 把指定id的元素展示或隐藏起来
 * @param {Object} id
 * @param {Boolean} isShow true or false 标识该元素是否显示
 */
cx.show = function(id, isShow)
{
 
   var obj = document.getElementById(id)
   if(typeof(obj) != "object")
   return ;
 
   try
   {
      if(isShow)
      obj.style.display = "";
      else
      obj.style.display = "none";
   }
   catch(e)
   {
   }
}
;
 
 
/* oop function */
var Class =
{
   create : function()
   {
      return function()
      {
         this.initialize.apply(this, arguments);
         // initialize 方法必须要实现
      }
   }
   ,
   extend : function(destination, source)
   {
      for (property in source)
      {
         destination[property] = source[property];
      }
      return destination;
   }
}
;
 
var Extend = function(destination, source)
{
   for (var property in source)
   {
      destination[property] = source[property];
   }
}
 
var Extend = function(destination, source)
{
   for (var property in source)
   {
      destination[property] = source[property];
   }
}
 
var ARROW_HEIGHT=20;//小三角形高度
var ARROW_WIDTH=20;//小三角形高度
var dialogBoxGuide = Class.create();
 
dialogBoxGuide.prototype =
{
   // 接受参数可以是数组类型，或者是对象类型
   initialize : function(arrOptions,iframeLeft,iframeTop)
   {
      var defaultOption = this.getDefaultOption();
      this.arrOptions = new Array();
      
      if(typeof(iframeLeft)=="undefined"){
           iframeLeft=0;
      }
      if(typeof(iframeTop)=="undefined"){
           iframeTop=0;
      }
      this.iframeLeft=iframeLeft; 
      this.iframeTop=iframeTop;
      this.index = 0;
      // 当前显示序列号
      for(var i = 0; i < arrOptions.length; i ++ )
      {
         this.arrOptions[i] = this.getDefaultOption();
         this.setOptions(arrOptions[i], i);
         this.arrOptions[i].isShowBackDiv = ! ! this.arrOptions[i].isShowBackDiv;
         this.arrOptions[i].isUserHTML = ! ! this.arrOptions[i].isUserHTML;
         this.repair(this.arrOptions[i]);
 
      }
   }
   ,
   // 设置默认属性
   setOptions : function(options, i)
   {
      Extend(this.arrOptions[i], options || {});
   }
   ,
   // 获取默认属性
   getDefaultOption : function()
   {
      return{
      isShowBackDiv:false,// 是否显示蒙板 
      isUserHTML:false,// 是否全部使用HTML填充
      title:'',// 对话框标题
      titleCss: '',// 标题样式
      description:'',// 描述
      descriptionCss : '',// 描述部分样式
      OKBttonHTML : '',// 确定按钮HTML
      onOKEvent : '',// 确定按钮点击事件函数
      onCloseEvnet : '',// 关闭按钮函数
       target :{id : '', direction : 'center'},// up down left right center,last 显示方向 目标ID可以是元素ID或者是DOM 元素
       HTML :'',
       dialogCss : '',// 对话框样式，主要是控制长度和宽度
       isFrameDom:true, //该元素是否是在ifame里面嵌套的页面
	   dialogOffset:{left:0,top:0},//偏差调整
	   arrowOffset:{left:0,top:0}
	   
	   
       }
   }
   ,
   // 对提供的属性进行修正
   repair : function(options)
   {
      var target = options.target;
      if(typeof(target.id) != "object")
      {
         //target.id = cx.$(target.id) // 根据ID
      }
      // 纠正提供的方向是否在指定范围内
      var direction = target.direction.toLowerCase() + ",";
      var directionContent = "top,bottom,left,right,center,last,";
      if(directionContent.indexOf(direction) == - 1)
      {
         target.direction = "center";
      }
   }
   ,
   // 显示对话框
   show : function()
   {
	   
      if(this.index >= this.arrOptions.length)
      return;
 
      // 创建dom元素
      var doc = document;
      var elem = doc.createElement("div");
      elem.id = "dialogBox";
 
      elem.className = "intoTips";
      var dialogCss = this.arrOptions[this.index].dialogCss;
      if( dialogCss != "")
      {
         elem.className = elem.className + " " + dialogCss;
      }
      if(this.arrOptions[this.index].isUserHTML)
      {
         elem.innerHTML = this.arrOptions[this.index].HTML;
      }
      else
      {
         var html = new Array();
         var direction = this.arrOptions[this.index].target.direction.toLowerCase();
         var forThisCss = "";
         var tag = "";
 
         switch(direction)
         {
            case 'center' :
               tag = "h1";
               break;
            case 'top' :
               tag = "h5";
               forThisCss = "forTop";
               break;
            case 'right' :
               tag = "h5";
               forThisCss = "forRight";
               break;
            case 'bottom' :
               tag = "h5";
               forThisCss = "forBottom";
               break;
            case 'left' :
               tag = "h5";
               forThisCss = "forLeft";
               break;
            case 'last' :
               tag = "h2";
               break;
            default :
               break;
         }
         // title
         
         html.push('<' + tag );
         if(this.arrOptions[this.index].titleCss != "")
         {
            html.push(' class="'+this.arrOptions[this.index].titleCss+'"');
         }
         html.push('>');
         html.push(this.arrOptions[this.index].title);
         html.push('</' + tag + '>');
 
         // description
         if(direction != "center")
         {
            html.push('<p id="desc"')
            if(this.arrOptions[this.index].descriptionCss != "")
            {
               html.push(' class="'+this.arrOptions[this.index].descriptionCss+'"');
            }
            html.push('>');
            html.push(this.arrOptions[this.index].description);
            html.push('</p>');
         }
 
         if(this.arrOptions[this.index].OKBttonHTML != "")
         {
            html.push(this.arrOptions[this.index].OKBttonHTML);
         }
         else
         {
            html.push('<a href="#"   id="btnIknow">看下一个功能 >></a>');
         }
         html.push('<div class="closeWindow" id="btnCloseDialogBox" title="关闭">关闭</div>');
          
         if(forThisCss != "")
         {
            html.push('<div class="'+forThisCss+'" id="arrow" >forThis</div>');
         }
         elem.innerHTML = html.join("");   
      }
      doc.body.appendChild(elem);
	 
      elem.style.position = "absolute";
      elem.style.display = "none";
      elem.style.zIndex = "99999";
      
      
 
      // 设置显示位置
      var target = this.arrOptions[this.index].target;
	  var dialogOffset=this.arrOptions[this.index].dialogOffset;
	  var arrowOffset=this.arrOptions[this.index].arrowOffset;
	  	 
      if(target.id == null)
      {
         // 如果给定的元素为空，则直接在中间显示
         target.direction = "center";
      }
             
      // 如果用户自定义确定按钮，则可能该按钮对象取不到
       try
      {
         (function(obj)
         {
            var _this = obj;
            cx.$("btnCloseDialogBox").onclick = function()
            {
               _this.closeDialog(); 
			  window.event.stopPropagation=true;
            }
            
         }
         )(this);
         (function(obj)
         {
            var _this = obj;
            cx.$("btnIknow").onclick = function()
            {
               _this.nextDialog(); 
            }
         }
         )(this);
      }
      catch(e)
      {
      }  
      if(target.direction=="last"){
		 target.direction="center"; 	
	  }
      this.setPosition(target.direction, elem, target.id,dialogOffset,arrowOffset);
	 
      if(this.arrOptions[this.index].isShowBackDiv)
      {
         Glass.show();
      }
      else
      {
         Glass.hide();
      }
	   
      elem.style.display = "block";
	
   }
   ,
   // obj : 要靠近的dom对象
   setPosition : function(type, dialog, obj,dialogOffset,arrowOffset)
   {
	  obj=cx.$(obj); 
      var left = 0, top = 0;
      if(obj != null)
      {  
         if(this.arrOptions[this.index].isFrameDom){
             left = $(obj).offset().left+this.iframeLeft; //iframe页面的left+父窗口左侧宽度
             top = $(obj).offset().top+this.iframeTop; //iframe页面top +header高度
         }else{
             left = $(obj).offset().left;
             top = $(obj).offset().top;
         }
      }
      var body = document.body;
      var arrow=cx.$("arrow");
      
      switch(type.toLowerCase())
      {
         case 'left' :
             var dialogLeft=left + $(obj).outerWidth();
             var ht=$(obj).outerHeight() / 2;
             var dialogTop=top - parseInt($(dialog).outerHeight() / 2) + $(obj).outerHeight() / 2;
             //处理最上面被遮挡情况
             if(dialogTop<0){
                dialogTop=0;
             }
             //处理最下面被遮挡情况
            // var maxTop=$(document).scrollTop()+$(document).outerHeight()-$(dialog).outerHeight();
             if(dialogTop>maxTop){
                dialogTop=maxTop;
             }
             dialog.style.left = (dialogLeft+ARROW_WIDTH) + "px";
             dialog.style.top = dialogTop+ "px";
			 
            
            //设置箭头靠近对象的中间位置         
             if(arrow!=null){
                arrow.style.top=(top-dialogTop-ARROW_HEIGHT/2)+ arrowOffset.top+ "px";                
             }
            break;
         case 'right' :
             var dialogLeft=left -$(dialog).outerWidth();
             var ht=$(obj).outerHeight() / 2;
             var dialogTop=top - parseInt($(dialog).outerHeight() / 2) + $(obj).outerHeight() / 2 ;
             //处理最上面被遮挡情况
             if(dialogTop<0){
                dialogTop=0;
             }
             //处理最下面被遮挡情况
             var maxTop=$(body).scrollTop()+$(body).outerHeight()-$(dialog).outerHeight();
             if(dialogTop>maxTop){
                dialogTop=maxTop;
             }
             
             dialog.style.left = (dialogLeft-ARROW_WIDTH)+ "px";
             // 设置对话框居中
             dialog.style.top = dialogTop+ "px";
             
            //设置箭头靠近对象的中间位置         
             if(arrow!=null){
                arrow.style.top=(top-dialogTop)+ arrowOffset.top+"px";
             }
            break;
         case 'center' :
             // 设置对话框居中
             dialog.style.left = ($(body).outerWidth() / 2 - parseInt($(dialog).outerWidth()) / 2) + "px";
             dialog.style.top = ($(body).outerHeight() / 2 - parseInt($(dialog).outerHeight()) / 2) + "px";
            break;
         case 'top' :
             var wd=$(obj).outerWidth() / 2;
             var dialogLeft=left - $(dialog).outerWidth() / 2 + wd;
             //纠正对话框被遮挡情况
             if(dialogLeft<0){//最左边
                dialogLeft=10; //纠正靠左边的情况
             }
              
             //处理对话框在最右边的情况         
             var maxLeft=$(body).scrollLeft()+$(body).outerWidth()-($(dialog).outerWidth());
             if(dialogLeft>maxLeft){
               dialogLeft=maxLeft-80;   //纠正靠右边的情况        
             }
             dialog.style.left = dialogLeft+"px";
              
             var height=0;          
             dialog.style.top = (top + $(obj).outerHeight()) + ARROW_HEIGHT+ "px" 
             
             //设置箭头靠近对象的中间位置         
             if(arrow!=null){
               arrow.style.left=((left-dialogLeft)+wd/2)+ arrowOffset.left+"px"; 
             }
            break;
         case 'bottom' :
             var wd=$(obj).outerWidth() / 2;
             var dialogLeft=left - $(dialog).outerWidth() / 2 + wd;
             //纠正对话框被遮挡情况
             if(dialogLeft<0){//最左边
                dialogLeft=10; //纠正靠左边的情况
             }
              
             //处理对话框在最右边的情况         
             var maxLeft=$(body).scrollLeft()+$(body).outerWidth()-($(dialog).outerWidth());
             if(dialogLeft>maxLeft){
               dialogLeft=maxLeft-80;   //纠正靠右边的情况        
             }
             dialog.style.left = dialogLeft + "px";
             dialog.style.top = (top-ARROW_HEIGHT-$(dialog).outerHeight()-$(obj).outerHeight()/2)+ "px";
             
             //设置箭头靠近对象的中间位置         
             if(arrow!=null){
                arrow.style.left=(left-dialogLeft)+wd/2+ arrowOffset.left+"px";
             }
            break;
         default :
 
      }
   }
   ,
   closeDialog : function()
   {
      var obj = cx.$("dialogBox");
      if(obj != null)
      {
         obj.style.display = "none";
         Glass.hide();
      }
      // 执行用户定义的关闭函数
      if(typeof(this.arrOptions[this.index].onCloseEvnet) == "function")
      {
         this.arrOptions[this.index].onCloseEvnet();
      }
      // 移除对应的对话框
      document.body.removeChild(obj);
 
   }
   ,
   nextDialog : function()
   {
      // 执行用户定义的关闭函数
      if(typeof(this.arrOptions[this.index].onOKEvent) == "function")
      {
         this.arrOptions[this.index].onOKEvent();
      }
      var obj = cx.$("dialogBox"); 
      document.body.removeChild(obj);
      this.index ++ ; 
      this.show();
 
   }
 
 
 
 
}
 
 
 
 
 
 
 
 
/* >>>>>End   dialogBoxGuide.js */
 
 
/* >>>>>Begin jquery.xml.js */
if (window.$) {
 
	$.loadXML = function(_url, _data, _callback){
		$.ajax({
			type: "get",
			url: _url,
			data: _data,
			async: true,
			dataType: "html",
			success: function(xml){
				var retObj = parseXML(xml);
				_callback(retObj);
			}
		});
		
	}
	$.postForm = function(_url, form, _callback){
		var formData = getForm(form);
		$.post(_url, formData, _callback, null);
	}
}
function parseXML(xml){
		var oXmlDom,oXmlElement;
			/*去除非法的xml注释部分
			 *当xml包含头部声明时<?xml version="1.0" encoding="UTF-8"?>需要下面这行代码
			 *xml = xml.replace(/\<\!\-\-[\s\S]+\-\-\>/,"");*/	
			//xml=xml.replace(/＜/ig,"<").replace(/＞/ig,">");//为适应coremail模板我不得不把尖括号<>转换为全角字符，在这里要替换回来成为xml格式
			xml=xml.replace(/[^\]]\]><!--a-->/ig,"]]><!--a-->");//解决coremail截断字符的bug
			
			if(jQuery.browser.msie){
				//var oXmlDom = new ActiveXObject("Microsoft.XMLDOM");
				var oXmlDom = new ActiveXObject("MSXML.DOMDocument")
				oXmlDom.loadXML(xml);
			}else{	//firefox,opera,safari
				//xml = xml.replace(/\<\!\-\-\sCoreMail[\s\S]+?\-\-\>/,"");
				//xml="<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n"+xml;
				//xml=xml.replace(/[\u001b-\u001f]/ig,"-");//替换打印字符
				var oParser = new DOMParser();
				var oXmlDom = oParser.parseFromString(xml,"text/xml");
			}
			oXmlElement= oXmlDom.documentElement;
			if(oXmlElement!=null){
				var retObj;
				if(oXmlElement.nodeName=="parsererror" || oXmlElement.nodeName=="html"){
					retObj=new Object;
					retObj.error=xml;
					if(checkLogout(xml)){return null};
				}else{
					retObj=xml2array(oXmlElement);
				}
				return retObj;
			}else{
				var retObj=new Object;
				retObj.error=xml;
				if(checkLogout(xml)){return null};
				return retObj;
			}
}
function checkLogout(page){
	if(page.indexOf("登录超时")>0 || page.indexOf("重新登录")>0){
		window.top.document.write(page);
		window.top.document.close();
		return true;
	}else if(page.indexOf("非法请求")>0){
		//alert("您似乎闲置太久，请刷新页面或重新登录");
		//return true;
	}
}
function xml2array(xmlDoc){
	var resultObj=new Array();	//dataset
	for(var i=0; i<xmlDoc.childNodes.length; i++) {
		var tableNode=xmlDoc.childNodes[i];
		if(tableNode.nodeName.charAt(0)  != "#" ){
			var arr=new Array();	//用于存放datatable
			resultObj[tableNode.nodeName]=arr;
			for(var j=0; j<tableNode.childNodes.length; j++) {
				var rowNode=tableNode.childNodes[j];	
				if(rowNode.nodeName.charAt(0)  != "#" ){
					var arr_row=new Array();	//存放datarow
					arr.push(arr_row);
					for(var k=0; k<rowNode.childNodes.length; k++) {
						var colNode=rowNode.childNodes[k];
						if(colNode.nodeName.charAt(0)  != "#" ){
							if(colNode.childNodes.length>0){
								var secName=colNode.childNodes[0].nodeName;
								var colValue;
								if(secName=="#text" || secName=="#cdata-section"){
									colValue=colNode.childNodes[0].nodeValue;
									arr_row[colNode.nodeName]=colValue;
								}else if(secName="#comment"){
									colValue=colNode.childNodes[0].nodeValue.replace(/\[CDATA\[/ig,"").replace(/\]\]/ig,"")
									arr_row[colNode.nodeName]=colValue;
								}
							}else{
								arr_row[colNode.nodeName]=null;
							}
							
						}
					}
				}
			}
			
		}
	}
	return resultObj;
}
 
function postByFrame(url,data,callback){
    var form=$("<form method='post' action='{0}' target='iframe_post'></form>".format(url)).appendTo(document.body);
    var iframe=$("#iframe_post");
    if(iframe.length==0){
	    iframe=$("<iframe style='display:none' name='iframe_post' id='iframe_post'></iframe>").appendTo(document.body);
	}
	for(var elem in data){
		var obj=document.createElement("input");
		obj.name=elem;
		obj.type="hidden";
		obj.value=data[elem];
		form.append(obj)
	}
	iframe.load(doCallback);
	try{
	    form[0].submit();//可能出现"拒绝访问"
	}catch(e){
	    iframe.unbind("load",doCallback);
	    iframe.load(function(){
	        iframe.unbind("load",arguments.callee);
	        iframe.load(doCallback);
	        form[0].submit();
	    });
	    iframe.attr("src",top.stylePath+"/empty.htm");
	}
	function doCallback(){
	    iframe.unbind("load",arguments.callee);
	    var response="";
	    try{
	        //目标页未必是同域的
	        response=iframe[0].contentWindow.document.documentElement.innerHTML;
	    }catch(e){}
	    if(callback)callback(response);
	    form.remove();
	}
}
 
 
 
 
//获取表单数据，目前只支持text hidden checkbox
function getForm(form){
var arr=form.getElementsByTagName("input");
var arr2=form.getElementsByTagName("textarea");
var formData=new Object;
formData=getTagValue(formData,arr);
formData=getTagValue(formData,arr2);
return formData;
};
function getTagValue(formData,arr){
	for(elem in arr){
		var obj=arr[elem];
		if(obj.tagName==undefined){
			continue;
		}
		
		if(obj.tagName.toLowerCase()=="input"){
			var key=obj.name;
			if(obj.type=="text" ||  obj.type=="hidden"){
				formData[key]=obj.value;
			}else if(obj.type=="checkbox"){
				if(obj.checked){
					formData[key]=obj.value;
				}
			}
		}else if(obj.tagName.toLowerCase()=="textarea"){
			formData[key]=obj.value;
		}
	}
	return formData;
 
}
/* >>>>>End   jquery.xml.js */
 
 
/* >>>>>Begin ContactsAttrCard.js */
ContactsAttrCard = {};
var ContactsAttrCardHtmlCode = '<div id="divContactsAttrCard" style="z-index:20;display:none;position: absolute; left: 100px; top: 100px;" class="menuWrap w">\
  <div class="mPop">\
    <table class="attrCard">\
	    <tr>\
    	    <td>\
        	    <h2 rel="name">黄三升</h2>\
                <div><a title="写邮件" rel="email" href="javascript:;" command="gotoCompose" class="fe">huangzl@139.com</a></div>\
                <div style="display:none" rel="addContacts"><a behavior="属性卡-添加到通讯录" class="fe" href="javascript:;" command="addContacts">添加到通讯录</a></div>\
                <div style="display:none" rel="addMobile"><a class="fe" href="javascript:;" rel="clickDonotHide" command="addMobile">添加手机号码</a></div>\
                <div style="display:none" rel="mobileEdit"><label>手机：<input maxLength="20" rel="clickDonotHide" type="text" class="text" style="width:90px;" /></label> <input behavior="属性卡-添加手机号码" command="saveMobileNumber" type="button" value="保存" /></div>\
                <div style="display:none" rel="mobile"><span style="display:inline-block;width:135px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" class="pl5px" rel="mobileNumber"></span><span rel="clickDonotHide" command="addMobile" class="unLine">编辑</span></div>\
                <div style="display:none" rel="mailNotify_whiteList"><a behavior="属性卡-添加到手机邮件白名单" href="javascript:;" command="addWhiteList" class="fe">添加到手机邮件白名单</a></div>\
                <div style="display:none" rel="mailNotify_blackList"><a href="javascript:;" command="addBlackList" class="fe">添加到手机邮件黑名单</a></div>\
                <div style="display:none" rel="mailNotify_setting"><a href="javascript:;" command="gotoMailNotify" class="fe">设置手机邮件过滤</a></div>\
                <div><span style="padding-left:8px" class="unLine" command="searchLetters">查看往来邮件</span><span style="display:none" class="unLine" command="searchSMS">|查看往来短信</span></div>\
                <div class="getMail">\
            	    <table>\
                	    <tr style="cursor:pointer">\
                    	    <td><em class="fcI" command="gotoMMS">写彩信</em></td>\
                            <td><em behavior="属性卡-发短信" class="fcI" command="gotoSMS">写短信</em></td>\
                            <td><em behavior="属性卡-发邮件" class="fcI" command="gotoCompose">写邮件</em></td>\
                        </tr>\
                    </table>\
                </div>\
            </td>\
        </tr>\
    </table>\
  </div>\
</div>';
ContactsAttrCard.show = function(param) {
    clearTimeout(ContactsAttrCard.hidTimer);
    ContactsAttrCard.hide();
    //属性卡依赖通讯录数据
    if (!top.Contacts.data.contacts || !ContactsAttrCard.isSettingDataReady()) return;
    var elementTag;
    if (elementTag = document.getElementById("divContactsAttrCard")) {
        elementTag.parentNode.removeChild(elementTag);
        ContactsAttrCard.element = null;
    }
 
 
    if (!ContactsAttrCard.element || !document.getElementById("divContactsAttrCard")) {
        ContactsAttrCard.element = $(ContactsAttrCardHtmlCode).appendTo(document.body);
        ContactsAttrCard.element.mouseout(function(e) {
            if (e.target.id == "divContactsAttrCard") {
                ContactsAttrCard.hide(true);
            }
        }).mouseover(function(e) {
            clearTimeout(ContactsAttrCard.hidTimer);
        }).click(ContactsAttrCard.click);
    }
    if (param.type == "email") {
        var email = param.email;
        var name = "";
        if (/["<>]/.test(email)) {
            var arr = Utils.parseEmail(email);
            if (arr.length == 0) return;
            email = arr[0].addr;
            name = arr[0].name;
        } else {
            name = email.split("@")[0];
        }
        var contacts = top.Contacts.getContactsByEmail(email);
        if (contacts) contacts = contacts[0];
        if (!contacts) {
            ContactsAttrCard.currentContacts = {
                email: email,
                name: name
            };
        } else {
            ContactsAttrCard.currentContacts = {
                email: email,
                name: contacts.name,
                id: contacts.SerialId,
                mobile: contacts.mobiles[0]
            };
        }
        try{
            ContactsAttrCard.showHTML(param.host);
        }catch(e){}
    }
}
ContactsAttrCard.canShow = function() {
    return Boolean(top.Contacts.isReady && top.mailNotifySetting);
}
ContactsAttrCard.showHTML = function(host) {
    var container = ContactsAttrCard.element;
    var addrObj = ContactsAttrCard.currentContacts;
    var items = container.find("*[rel]");
    if(!ContactsAttrCard.isMailNotifyOn()){
        items.filter("*[rel='mailNotify_whiteList']").show();
    }else if (ContactsAttrCard.isMailNotifyWhiteListOn()) {
        if (top.mailNotifySetting.whiteList.length == 0) {
            items.filter("*[rel='mailNotify_setting']").show();
        } else {
            items.filter("*[rel='mailNotify_whiteList']").show();
        }
    } else if (!ContactsAttrCard.isMailNotifyFilterListOn()) {
        items.filter("*[rel='mailNotify_setting']").show();
    } else if (ContactsAttrCard.isMailNotifyBlackListOn()) {
        if (top.mailNotifySetting.blackList.length == 0) {
            items.filter("*[rel='mailNotify_setting']").show();
        } else {
            items.filter("*[rel='mailNotify_blackList']").show();
        }
    }else{
        items.filter("*[rel='mailNotify_setting']").show();
    }
    items.filter("*[rel='name']").text(addrObj.name).show();
    items.filter("*[rel='email']").text(addrObj.email).show();
    if (addrObj.id) {
        if (addrObj.mobile) {
            items.filter("*[rel='mobile']").show();
            items.filter("*[rel='mobileNumber']").text("手机:" + addrObj.mobile);
        } else {
            items.filter("*[rel='addMobile']").show();
        }
    } else {
        items.filter("*[rel='addContacts']").show();
    }
    if (host.title) {
        host.setAttribute("addr", host.title);
        host.title = "";
    }
    host = $(host);
    var offset = host.offset();
    container.css({
        top: offset.top + ((offset.top + container.height() > $(document.body).height()) ? -container.height() : host.height()),
        left: offset.left + 50,
        display: "block"
    });
}
ContactsAttrCard.click = function(e) {
    var command = e.target.getAttribute("command");
    if (command && ContactsAttrCard[command]) {
        if (e.target.getAttribute("rel") != "clickDonotHide") ContactsAttrCard.hide();
        ContactsAttrCard[command]();
    }
    top.behaviorClick(e.target, window);
    return false;
}
ContactsAttrCard.addMobile = function() {
    var addrObj = ContactsAttrCard.currentContacts;
    var mobileEdit = ContactsAttrCard.element.find("div[rel='mobileEdit']");
    mobileEdit.show();
    ContactsAttrCard.element.find("div[rel='addMobile'],div[rel='mobile']").hide();
    var txtMobile = mobileEdit.find("input:text");
    if (addrObj.mobile) {
        txtMobile.val(addrObj.mobile);
    }
    Utils.focusTextBox(txtMobile[0]);
}
ContactsAttrCard.isSettingDataReady = function() {
    return Boolean(top.mailNotifySetting);
}
ContactsAttrCard.loadMailNotifySetting = function(callback) {
    var url = top.ucDomain + "/ServiceAPI/GetMailNotifyInfo.ashx?sid=" + top.UserData.ssoSid + "&rnd=" + Math.random();
    top.frames["ucProxy"].$.get(url, function(json) {
        try {
            var mailNotifySetting = eval(json);
            mailNotifySetting.whiteList = mailNotifySetting.whiteList == "" ? [] : mailNotifySetting.whiteList.split(",");
            mailNotifySetting.blackList = mailNotifySetting.blackList == "" ? [] : mailNotifySetting.blackList.split(",");
            top.mailNotifySetting = mailNotifySetting;
            if (callback) callback();
        } catch (e) {
 
        }
    });
}
ContactsAttrCard.hide = function(delay) {
    if (ContactsAttrCard.element) {
        if (delay) {
            ContactsAttrCard.hidTimer = setTimeout(function(){
                ContactsAttrCard.element.hide();
            },300);
        } else {
            ContactsAttrCard.element.hide();
        }
    }
}
ContactsAttrCard.gotoCompose = function() {
    var addrObj = ContactsAttrCard.currentContacts;
    var userAccount;
    try {
        var fid = Utils.queryString("fid") || top.MB.folderId;
        userAccount = top.MB.getPopAccount(fid);
    } catch (e) { }
    top.CM.show({
        userAccount: userAccount,
        receiver: "\"" + addrObj.name + "\"<" + addrObj.email + ">"
    });
}
ContactsAttrCard.gotoSMS=function(){
    var addrObj = ContactsAttrCard.currentContacts;
    var mobileText = "\"" + addrObj.name + "\"<" + addrObj.mobile + ">";
    top.Links.show("sms", "&mobile=" + (addrObj.mobile ? escape(mobileText) : ""));
}
ContactsAttrCard.gotoMMS = function() {
    var addrObj = ContactsAttrCard.currentContacts;
    var mobileText = "\"" + addrObj.name + "\"<" + addrObj.mobile + ">";
    top.Links.show("mms", "&mobile=" + (addrObj.mobile ? escape(mobileText) : ""));
}
//
ContactsAttrCard.addContacts = function() {
    var addrObj = ContactsAttrCard.currentContacts;
    top.Links.show("addrContacts", "&type=add&email=" + addrObj.email + "&name=" + escape(addrObj.name));
}
//添加到白名单
ContactsAttrCard.addWhiteList = function() {
    var addrObj = ContactsAttrCard.currentContacts;
    if (ContactsAttrCard.existWhiteList(addrObj.email,true)) {
        FF.alert("该账号已经在白名单中");
        return;
    }
    FF.confirm("<div style='font-size:12px'>添加到手机邮件<span style='color:rgb(246,110,33)'>白名单</span>，此联系人来信通知<br/><span style='color:rgb(246,110,33)'>发送到手机</span><br />\
    您可以<a href='javascript:;' onclick='top.Links.show(\"mailnotify\",\"&whiteList=true\");FF.close();return false;'>添加多个地址到白名单</a></div>", function() {
        ContactsAttrCard.addEmailFilterList("white", addrObj.email, function(result) {
            if (result.success) {
                FF.alert(result.msg);
            } else {
                FF.alert(result.msg || "服务器繁忙,请稍后再试");
            }
        });
    });
}
ContactsAttrCard.addEmailFilterList = function(type, addr, callback) {
    var result = {};
    var url = top.ucDomain + "/ServiceAPI/GetMailNotifyInfo.ashx?sid=" + top.UserData.ssoSid
    + "&open=true&maillist=" + encodeURIComponent(addr)
    + "&rnd=" + Math.random()
    + "&filter=" + (type == "white" ? 1 : 2);
    top.WaitPannel.show("加载中...");
    top.frames["ucProxy"].$.ajax({
        type: "GET",
        url: url,
        success: function(json) {
            if (json.indexOf("whiteList") > 0) {
                top.WaitPannel.hide();
                result.success = true;
                result.msg = "设置成功";
                var mailNotifySetting = eval(json);
                mailNotifySetting.whiteList = mailNotifySetting.whiteList == "" ? [] : mailNotifySetting.whiteList.split(",");
                mailNotifySetting.blackList = mailNotifySetting.blackList == "" ? [] : mailNotifySetting.blackList.split(",");
                top.mailNotifySetting = mailNotifySetting;
                if (callback) callback(result);
            } else {
                fail();
            }
        },
        error: fail,
        timeout: 10000
    });
    function fail() {
        top.WaitPannel.hide();
        result.success = false;
        result.msg = "服务器繁忙，请稍后再试";
        if (callback) callback(result);
    }
}
//添加到黑名单
ContactsAttrCard.addBlackList=function(){
    var addrObj = ContactsAttrCard.currentContacts;
    if (ContactsAttrCard.existBlackList(addrObj.email,true)) {
        FF.alert("该账号已经在黑名单中");
        return;
    }
    FF.confirm("<div style='font-size:12px'>添加到手机邮件<span style='color:rgb(246,110,33)'>黑名单</span>，此联系人来信通知<br/><span style='color:rgb(246,110,33)'>不发送到手机</span><br />\
    您可以<a href='javascript:;' onclick='top.Links.show(\"mailnotify\",\"&whiteList=true\");FF.close();return false;'>添加多个地址到黑名单</a></div>", function() {
        ContactsAttrCard.addEmailFilterList("black",addrObj.email, function(result) {
            if (result.success) {
                FF.alert("添加成功!");
            } else {
                FF.alert(result.msg||"服务器繁忙,请稍后再试");
            }
        });
    });
}
//往来邮件
ContactsAttrCard.searchLetters = function() {
    top.MB.facileSearch(ContactsAttrCard.currentContacts.email);
}
//查看短信记录
ContactsAttrCard.searchSMS=function(){
 
}
ContactsAttrCard.saveMobileNumber = function() {
    var addrObj = ContactsAttrCard.currentContacts;
    var num = ContactsAttrCard.element.find("input:text").val().trim();
    if (num != "") {
        top.Contacts.addContactsMobile(addrObj.id, num, function(result) {
            if (result.success) {
                FF.alert("保存成功");
            } else {
                FF.alert(result.msg);
            }
        });
    } else {
        FF.alert("请输入手机号码");
    }
}
//编辑资料
ContactsAttrCard.editContacts = function() {
    top.Links.show("addrContacts", "&type=edit&id=" + ContactsAttrCard.currentContacts.id);
}
//设置过滤条件
ContactsAttrCard.gotoMailNotify = function() {
    top.Links.show('mailnotify',"&type=setFilter");
}
//是否开启到达通知
ContactsAttrCard.isMailNotifyOn = function() {
    return top.mailNotifySetting.open;
}
//是否开启黑白名单
ContactsAttrCard.isMailNotifyFilterListOn = function() {
    return top.mailNotifySetting.filterType != 0;
}
//是否开启白名单
ContactsAttrCard.isMailNotifyWhiteListOn = function() {
    return top.mailNotifySetting.filterType == 1;
}
//是否开启黑名单
ContactsAttrCard.isMailNotifyBlackListOn = function() {
    return top.mailNotifySetting.filterType == 2;
}
//是否在白名单
ContactsAttrCard.existWhiteList = function(addr,real) {
    var whiteList = top.mailNotifySetting.whiteList;
    addr = addr.toLowerCase();
    for (var i = 0; i < whiteList.length; i++) {
        var item = whiteList[i].toLowerCase();
        if (!real && item.indexOf("@") == 0 && addr.indexOf(item) >= 0) {
            return true;
        } else if (item == addr) {
            return true;
        }
    }
    return false;
}
//是否在黑名单
ContactsAttrCard.existBlackList = function(addr,real) {
    var blackList = top.mailNotifySetting.blackList;
    addr = addr.toLowerCase();
    for (var i = 0; i < blackList.length; i++) {
        var item = blackList[i].toLowerCase();
        if (!real && item.indexOf("@") == 0 && addr.indexOf(item) >= 0) {
            return true;
        } else if (item == addr) {
            return true;
        }
    }
    return false;
}
 
if(window.$){
    $(document).mouseout(ContactsAttrCard_mouseout)
    .mouseover(ContactsAttrCard_mouseover)
    .click(ContactsAttrCard_click);
}
function ContactsAttrCard_click(e){
    var tag = (e && e.target) || window.event.srcElement;
    if (tag.getAttribute("rel") != "clickDonotHide") {
        ContactsAttrCard.hide();
    }
}
function ContactsAttrCard_mouseout(e) {
    var tag = e.target;
    if (!tag || !tag.getAttribute) return;
    if (tag.getAttribute("rel") == "showAddrCard") {
        clearTimeout(ContactsAttrCard.showTimer);
        ContactsAttrCard.hide(true);
    }
}
function ContactsAttrCard_mouseover(e){
    var tag = e.target;
    if (!tag || !tag.getAttribute) return;
    if (tag.getAttribute("rel") == "showAddrCard") {
        var email = tag.title || tag.getAttribute("addr");
        if (!email) email = tag.innerText || tag.textContent;
        if (!email || Utils.parseEmail(email).length==0) return;
        
        clearTimeout(ContactsAttrCard.showTimer);
        ContactsAttrCard.hide(true);
        ContactsAttrCard.showTimer = setTimeout(function() {
            ContactsAttrCard.show({
                type: "email",
                email: email,
                host: tag
            });
        }, 800);
    }
};
 
/*
mailNotifySetting={
	open:true,//是否开启邮件到达通知
	filterType:0, //1白 2黑
	whiteList:["123@123.com","abc@123.com"],//黑名单 不管是否开启过滤 如果黑白名单有填写,都返回数据
	blackList:[]//白名单
}
mailbox.js main_ext.js onepageread.js
top.GlobalEvent.add("mailNotifySettingChanged", function() {
    ContactsAttrCard.loadMailNotifySetting();
});
*/
/* >>>>>End   ContactsAttrCard.js */
 
 
/* >>>>>Begin editorManager.js */
EditorManager = {};
EditorManager.create = function(param) {
    if (!param.container) return;
    var editorUrl = "http://" + window.top.location.host + "/" + top.stylePath + "/editor.htm?";
    if (param.hidToolBar) {
        if (param.hidToolBar) editorUrl += "&hidToolBar=true";
    }
    var htmlCode = "<iframe name='theEditorFrame' id='theEditorFrame' style='width:100%;height:100%' frameBorder='0' scrolling='no' src='" + editorUrl + "'></iframe>";
    if (param.width) {
        htmlCode = htmlCode.replace("width:100%", "width:" + param.width.toString().replace(/(\d+)$/, "$1px"));
    }
    if (param.height) {
        htmlCode = htmlCode.replace("height:100%", "height:" + param.height.toString().replace(/(\d+)$/, "$1px"));
    }
    param.container.innerHTML = htmlCode;
}
EditorManager.getHtmlContent = function() {
    return window.frames["theEditorFrame"].theEditorBox.getHtmlContent();
}
EditorManager.setHtmlContent = function(content) {
    return window.frames["theEditorFrame"].theEditorBox.setHtmlContent(content);
}
EditorManager.insertImage = function(url) {
    window.frames["theEditorFrame"].theEditorBox.insertImage(url);
}
EditorManager.toggleToolBar = function() {
    window.frames["theEditorFrame"].$("#toolbar").toggle();
    window.frames["theEditorFrame"].resizeAll();
}
EditorManager.getHtmlToTextContent = function() {
    return window.frames["theEditorFrame"].theEditorBox.getHtmlToTextContent();
}
EditorManager.onload=function(){
    
}
/* >>>>>End   editorManager.js */
 