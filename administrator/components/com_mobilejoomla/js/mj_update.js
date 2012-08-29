/* Mobile Joomla! 1.1.0 | mobilejoomla.com/license.html */
function version_compare(v1, v2){
	var vm = {'dev': -4, 'alpha': -3, 'beta': -2, 'rc': -1},
		vprep = function(v){return ('' + v).toLowerCase().replace(/([^.\d]+)/g, '.$1.').replace(/\s+/g, '').replace(/\.{2,}/g, '.').split('.');},
		vnum = function(v){return !v ? 0 : (isNaN(v) ? vm[v] || -5 : parseInt(v, 10));};
	v1 = vprep(v1);
	v2 = vprep(v2);
	var i = 0,
		x = Math.max(v1.length, v2.length);
	for(; i < x; i++){
		if(v1[i]==v2[i]) continue;
		v1[i] = vnum(v1[i]);
		v2[i] = vnum(v2[i]);
		return (v1[i] < v2[i]) ? 1 : -1;
	}
	return 0;
}

window.addEvent('domready',function(){
	function checkUpdate(){
		if(typeof Request == "function"){
			new Request.HTML( {
				url: 'http://www.mobilejoomla.com/getver.php?v=' + escape('1.1.0'),
				method: 'get',
				update: 'mjlatestver',
				onSuccess : function(tree, elements, response){
					if(version_compare('1.1.0', response)>0){
						$('mjlatestverurl').setStyle('display', 'block');
					}
				}
			}).send();
		} else if(typeof Ajax == "function"){
			new Ajax( 'http://www.mobilejoomla.com/getver.php?v=' + escape('1.1.0'), {
				method: 'get',
				update: $('mjlatestver'),
				onComplete: function(response){
					if(version_compare('1.1.0', response)>0){
						$('mjlatestverurl').setStyle('display', 'block');
					}
				}
			}).request();
		}
	}

	try{
		checkUpdate();
	}catch(e){}
});
