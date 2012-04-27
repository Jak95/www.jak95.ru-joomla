/*
 * @author RocketTheme, LLC http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
var RokWeatherTip="Double click to edit location and hit enter to save.";if(window.orientation!==undefined){window.ipod=true;}var RokWeather=new Class({Implements:[Options,Events],version:"0.7",options:{defaultDegree:0},initialize:function(a){this.setOptions(a);
this.container=document.id("rokweather")||null;if(!this.container){return false;}this.input=this.container.getElement("input").setStyle("display","none");
this.form=this.container.getElement("form");this.output=this.container.getElement(".rokweather-wrapper");this.icon=this.container.getElement(".icon");this.degrees=this.container.getElements(".degrees span");
this.degree=Cookie.read("rokweather_degree")||this.options.defaultDegree;this.degree=this.degree.toInt();this.fx=new Fx.Tween(this.output,{link:"cancel",duration:300}).set("opacity",1);
this.fxicon=new Fx.Tween(this.icon,{link:"cancel",duration:300}).set("opacity",1);this.title=new Element("h5",{title:RokWeatherTip}).set("text",this.input.value).inject(this.output.getParent().setStyle("position","relative"),"top");
this.tooltip=new Tips([this.title],{onShow:function(c){var b=c.getElement(".tip");new Element("div",{"class":"tip-text tool-text"}).inject(b);c.setStyle("display","block");
}});this.attachEvent();return this;},attachEvent:function(){var a=this;this.title.addEvent((window.ipod)?"click":"dblclick",function(b){a.tooltip.hide();
this.setStyle("visibility","hidden");if(!a.board){a.board=a.createBoard(this);}else{a.board.setStyle("display","");}a.board.select();});a.degrees.removeClass("active");
a.degrees[a.degree].addClass("active");a.degEvent(a.degree);a.degrees.each(function(c,b){if(c.hasClass("active")){a.degree=b;}c.addEvent("click",function(){a.degEvent(b);
Cookie.write("rokweather_degree",b,{duration:365});});});},createBoard:function(b){var a=this;return new Element("input",{"class":"input-board",styles:{position:"absolute",outline:"none",width:b.getSize().x,height:b.getSize().y},value:(Browser.Engine.webkit)?b.innerHTML:b.get("text")}).inject(b,"before").addEvent("keyup",function(c){if(c.key!="enter"&&c.key!="esc"&&c.code!=10){return;
}if(c.key=="esc"||this.value==a.input.value){a.title.setStyle("visibility","visible");this.setStyle("display","none");}else{a.input.set("value",this.value);
a.title.set("text",this.value).setStyle("visibility","visible").addClass("loading");this.setStyle("display","none");a.form.set("send",{onComplete:a.complete.bind(a)});
a.form.send();}});},complete:function(b){var c=new Element("div").set("html",b),a=this;this.fxicon.start("opacity",0);this.fx.start("opacity",0).chain(function(){c.getElement(".icon").getChildren().inject(a.icon.empty());
c.getElement(".rokweather-wrapper").inject(a.output.empty());var d=a.icon.getElements("div");a.degEvent(a.degree);a.fxicon.start("opacity",1);a.fx.start("opacity",1);
a.title.removeClass("loading");});},degEvent:function(a){this.degree=a;this.degrees.removeClass("active");this.degrees[this.degree].addClass("active");
var b=this.output.getElements(".forecast .degf");var c=this.output.getElements(".forecast .degc");if(!a){c.each(function(d){d.setStyle("display","none");
});b.each(function(d){d.setStyle("display","block");});}else{c.each(function(d){d.setStyle("display","block");});b.each(function(d){d.setStyle("display","none");
});}this.icon.getElements("div").each(function(e,d){if(d==this.degree){e.setStyle("display","block");}else{e.setStyle("display","none");}},this);}});