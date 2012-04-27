/**
 * @version		$Id: helper.js 20828 2011-02-22 04:22:21Z dextercowley $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */


function  redim_helper_send(iddiv,valemail,valtext){

	$(iddiv).innerHTML='<div style="background: url(/plugins/system/imagesizer/formfields/loading.gif) no-repeat center center; width: 100%; height:100%;">&nbsp;Loading</div>';
	//$(idform).submit();

		var myRequest = new Request({url: 'index.php?code=redim-helper', method: 'post',async: false,data: { 'email' : valemail, 'text' : valtext, 'code' : 'redim-helper' }, onSuccess: function(responseText){}});
		myRequest.send();

		$(iddiv).innerHTML=	myRequest.response.text;

		return true
}