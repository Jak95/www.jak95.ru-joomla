/* Mobile Joomla! 1.0.3 | mobilejoomla.com/license.html */
var jQT = new $.jQTouch({
	addGlossToIcon: true,
	statusBar: 'black'
});

$(function()
{
	if(isFrontPage)
	{
		$("a.back").hide();
		$("a.home").hide();
	}

	$("ul#mainlevel li").each(function()
	{
		$(this).addClass("forward");
	});
});