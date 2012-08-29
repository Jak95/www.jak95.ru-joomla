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
(function($){$.jce.Users={select:function(){var u=[],v,s,o,h;s=window.parent.document.getElementById('users').options;$('input:checkbox:checked').each(function(){v=$(this).val();if(u=document.getElementById('username_'+v)){h=$.trim(u.innerHTML);if($.jce.Users.check(s,v)){return;}
o=new Option(h,v);s[s.length]=o;}});},check:function(s,v){var a=[];$.each(s,function(i,n){a.push(n.value);});return a.indexOf(v)!=-1;}};window.selectUsers=$.jce.Users.select;})(jQuery);