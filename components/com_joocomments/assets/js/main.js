var component_path="";
function initialize(urlComponent){
	component_path=urlComponent;
}
function refreshCaptcha(){
	var captcha_path=component_path+"&task=showcaptcha";
	var imgObj=document.getElementById("captcha_image");
	if(imgObj!=null){
		imgObj.src=captcha_path+'&random='+Math.random();
	}
}
function validateCaptcha(captchaText){
	var isValid=false;
	var parameters=
		"&userCaptcha="+captchaText.value;
	var url=component_path+"&task=checkCaptcha";
	var myRequest = new Request({method: 'post', async:false,url: url,onSuccess: function(responseText){
		if(responseText=="1"){isValid=true;}
    }
});
	myRequest.send(parameters);
	
	return isValid;
}
