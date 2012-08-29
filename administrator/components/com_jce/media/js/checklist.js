/*  
 * JCE Editor                 2.2.4
 * @package                 JCE
 * @url                     http://www.joomlacontenteditor.net
 * @copyright               Copyright (C) 2006 - 2012 Ryan Demmer. All rights reserved
 * @license                 GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html
 * @date                    16 July 2012
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * NOTE : Javascript files have been compressed for speed and can be uncompressed using http://jsbeautifier.org/
 */
(function($){$.fn.checkList=function(options){this.each(function(){return $.CheckList.init(this,options);});};$.CheckList={options:{valueAsClassName:false},init:function(el,options){var self=this;$.extend(this.options,options);var ul=document.createElement('ul');var elms=[];if(el.nodeName=='SELECT'){$.each($('option',el),function(){elms.push({name:$(this).html(),value:$(this).val(),selected:$(this).prop('selected')});});}else{$.each(el.value.split(','),function(){elms.push({name:this,value:this});});}
$(el).hide();$(ul).addClass('widget-checklist').insertBefore(el);$.each(elms,function(){self.createElement(el,ul,this);});if($(el).hasClass('sortable')){$(ul).addClass('sortable').sortable({axis:'y',tolerance:'intersect',update:function(event,ui){self.setValue(el,$(ui.item).parent());},placeholder:"ui-state-highlight"}).disableSelection();}},createElement:function(el,ul,n){var self=this,d=document,li=d.createElement('li'),check=d.createElement('span');$(li).attr({title:n.value}).addClass('ui-widget-content ui-corner-all').appendTo(ul);$(check).addClass('checkbox').addClass(function(){return n.selected?'checked':'';}).click(function(){$(this).toggleClass('checked');self.setValue(el,ul);}).appendTo(li);$(li).append('<span class="widget-checklist-'+n.value+'">'+n.name+'</span>');},setValue:function(el,ul){$list=$('li',ul);var x=$.map($('span.checked',$list),function(n){return $(n).parent('li').attr('title');});if(el.nodeName=='SELECT'){$(el).empty();$.each($list,function(i,item){var v=$(item).attr('title');var o=document.createElement('option');$(o).attr({'value':v}).prop('selected',!($.inArray(v,x)==-1)).appendTo(el);});}else{el.value=x.join(',');}}};})(jQuery);