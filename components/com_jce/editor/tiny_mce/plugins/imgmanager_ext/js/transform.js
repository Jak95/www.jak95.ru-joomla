/**
 * @version		$Id: transform.js 221 2011-06-11 17:30:33Z happy_noodle_boy $
 * @package		JCE
 * @copyright	Copyright (C) 2009 Ryan Demmer. All rights reserved.
 * @license		GNU/GPL
 * This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
(function($) {

	// Resizable aspectRatio patch
	var oldSetOption = $.ui.resizable.prototype._setOption;
    
    $.ui.resizable.prototype._setOption = function(key, value) {
        oldSetOption.apply(this, arguments);
        if (key === "aspectRatio") {
            this._aspectRatio = !!value;
        }
    };

	function getRatio(o) {
		/*// Calculate Greatest Common Diviser
		function gcd (a, b) {
			return (b == 0) ? a : gcd (b, a%b);
		}

		// get gcd for the image
		var r = gcd(o.width, o.height);
		// return ratio
		return (o.width/r) / (o.height/r);*/
		
		return o.width / o.height;
	};

	/** Depends:
	 *	jquery.ui.resizable.js
	 */
	$.widget("ui.resize", {
		options : {
			ratio : 4/3,
			width : 800,
			height: 600
		},

		_init : function() {
			var self = this;

			// store width and height
			this.width  = $(this.element).width() 	|| $(this.element).attr('width');
			this.height = $(this.element).height() 	|| $(this.element).attr('height');
			
			var pos = $(this.element).position();

			$('<div id="resize-container" class="transform-widget"/>').appendTo($(this.element).parent()).append(this.element).css(pos).resizable({
				'handles' 		: 'all',
				'aspectRatio' 	: this.options.ratio,
				containment		: 'parent',
				'start' : function() {
					self._trigger('start', null);
				},
				'resize' : function(event, ui) {
					var n = ui.element[0], w = Math.round(n.clientWidth), h = Math.round(n.clientHeight);

					$(self.element).css({
						width : w,
						height: h
					});
					
					self._trigger('resize', null, {
						width : w,
						height: h
					});
				},
				stop : function(event, ui) {
					self._trigger('stop', null, ui.size);
				}
			}).draggable({
				containment	: 'parent'
			});
			
			// remove position
			$(this.element).css({
				top : '',
				left: ''
			});

			if (!$.support.cssFloat) {
				$('#resize-container').attr('unselectable', 'on');
			}

			// remove handle
			$('div.ui-resizable-handle.ui-resizable-se', '#resize-container').removeClass('ui-icon ui-icon-gripsmall-diagonal-se');
		},
		
		_getPosition : function(width, height) {
			var $parent = $('#resize-container').parent();
			
			var width 	= width 	|| this.width;
			var height 	= height 	|| this.height;
			
			return {
				left 	: ($parent.outerWidth() - width) / 2,
				top 	: ($parent.outerHeight() - height) / 2
			}
		},
		
		setSize : function(w, h) {
			var self = this, $parent = $('#resize-container').parent(), pos = this._getPosition(w, h);
			var pw = $parent.outerWidth(), ph = $parent.outerHeight();
			
			$(this.element).animate({
				width	: w,
				height	: h
			}, {
				step : function(now, fx) {
					if (fx.prop == 'width') {
						$('#resize-container').css('left', (pw - now) / 2);
					}
					if (fx.prop == 'height') {
						$('#resize-container').css('top', (ph - now) / 2);
					}
				}, 
				
				complete : function() {
					self._trigger('stop', null);
				}
			});
		},
		
		setConstrain : function(s) {
			var ratio = s;

			if (s) {
				ratio = getRatio(s);
			}

			this.setRatio(ratio);
		},
		
		getRatio : function() {
			var o = $(this.element).get(0);

			return {
				x : this.options.width / $(o).width(),
				y : this.options.height / $(o).height()
			};
		},
		
		setRatio : function(ratio) {
			if ($.type(ratio) == 'undefined') {
				var r = this.getRatio();

				ratio = r.x / r.y;
			}

			$(this.element).resizable("option", "aspectRatio", ratio);
		},
		
		reset : function() {						
			var pos = this._getPosition();
			
			$('#resize-container').css({
				top 	: pos.top,
				left 	: pos.left,
				width 	: '',
				height	: ''
			});
			
			$(this.element).css({
				top   : ''
			});
		},
		remove : function() {
			$('#resize-container').parent().append(this.element);
			
			$('#resize-container').remove();
			this.destroy();
		},
		destroy: function() {
			$.Widget.prototype.destroy.apply( this, arguments );
		}
	});

	/** Depends:
	 *	jquery.ui.resizable.js
	 */
	$.widget("ui.rotate", {
		options : {},

		_init : function() {
			var self = this;

			var $parent = $(this.element).parent();

			$(this.element).wrap('<div id="rotate-container"/>');

			$('#rotate-container').css({
				'top' : ($parent.height() - $(this.element).height()) / 2,
				'left': ($parent.width() - $(this.element).width()) / 2
			});
			
			if (!$.support.cssFloat) {
				$('#rotate-container').attr('unselectable', 'on');
			}
		},
		
		rotate : function(angle) {
			var s;
			
			switch(angle) {
				default:
					s = 'scaleY(1) scaleX(1)'
					break;
				case '0' :
				case '90':
				case '-90':
				case '180':
					s = 'rotate(' + angle + 'deg)';
					break;
				case 'vertical':
					s = 'scaleY(-1)';
					break;
				case 'horizontal':
					s = 'scaleX(-1)';
					break;
				case 'vertical|horizontal':
					s = 'scaleX(-1) scaleY(-1)';
					break;

			}

			$(this.element).animate({
				'transform' : s
			});
		},
		
		remove : function() {
			$(this.element).unwrap();
			this.destroy();
		},
		
		destroy: function() {
			$.Widget.prototype.destroy.apply( this, arguments );
		}
	});

	/** Depends:
	 *	jquery.ui.draggable.js
	 *	jquery.ui.resizable.js
	 */
	$.widget("ui.crop", {
		options : {
			ratio : 4/3,
			width : 800,
			height: 600,
			selection : '',
			clone : null
		},

		_init : function() {
			var self = this;
			
			// store width and height
			this.width  = $(this.element).width() 	|| $(this.element).attr('width');
			this.height = $(this.element).height() 	|| $(this.element).attr('height');

			var $parent = $(this.element).parent();
			var top 	= $(this.element).css('top') || ($parent.outerHeight() - this.height) / 2;
			
			// remove position
			$(this.element).css({
				top : '',
				left: ''
			});
			
			// create clone
			var $clone 	= this.options.clone ? $(this.options.clone) : $(this.element).clone();
			// remove styles
			$clone.css('top', '');

			$('<div id="crop-container"></div>').appendTo($parent).append(this.element).append('<div id="crop-mask"/>').append(
				'<div id="crop-window"/><div id="crop-widget" class="transform-widget"/>'
			);

			var $crop  	= $('#crop-window');
			var $widget = $('#crop-widget');

			$crop.append($clone).css({
				'width' : this.width,
				'height': this.height
			});
			
			var grid;

			if ($.support.canvas) {
				grid = document.createElement('canvas');
				
				$(grid).attr({
					width : $crop.width(),
					height: $crop.height()
				});
				
				var ctx = grid.getContext('2d');
				
				for (var x = 0; x < grid.width; x += grid.width / 3) {
					ctx.moveTo(x, 0);
					ctx.lineTo(x, grid.height);
				}
				
				for (var y = 0; y < grid.height; y += grid.height / 3) {
					ctx.moveTo(0, y);
					ctx.lineTo(grid.width, y);
				}
				
				ctx.strokeStyle = "#ffffff";
				ctx.stroke();
			}

			$widget.css({
				'width' : this.width,
				'height': this.height
			}).resizable({
				'handles' 		: 'all',
				'aspectRatio' 	: this.options.ratio,
				'containment' 	: '#crop-container',
				'start'			: function(event, ui) {
					$(grid).css({
						width : $crop.width(),
						height: $crop.height()
					}).show().appendTo($crop);
					
					self._trigger('start', null);
				},
				'resize' : function(event, ui) {
					var n = ui.element[0], w = Math.round(n.clientWidth), h = Math.round(n.clientHeight);
					
					$clone.css({
						top  	: - n.offsetTop,
						left 	: - n.offsetLeft
					});

					$crop.css({
						width : w,
						height: h,
						top	  : n.offsetTop,
						left  : n.offsetLeft
					});

					$(grid).css({
						width : w,
						height: h
					});

					self._trigger('change', null, self.getArea(true));
				},
				stop : function() {
					self._trigger('stop', null, self.getArea(true));
					$(grid).hide('slow').remove();
				}
			}).append(
				'<div class="ui-resizable-handle ui-border-top"></div>' +
				'<div class="ui-resizable-handle ui-border-right"></div>' +
				'<div class="ui-resizable-handle ui-border-bottom"></div>' +
				'<div class="ui-resizable-handle ui-border-left"></div>'
			);

			$('#crop-window, #crop-widget').draggable({
				'containment' : '#crop-container',
				'start' : function(event, ui) {
					$(grid).css({
						width : $crop.width(),
						height: $crop.height()
					}).show().appendTo($crop);
				},
				'drag' : function(event, ui) {
					var top = ui.position.top, left = ui.position.left;

						$widget.css({
							top  : top,
							left : left
						});
	
						$crop.css({
							top  : top,
							left : left
						});
	
						$clone.css({
							top  : -top,
							left : -left
						});
	
						self._trigger('change', null, self.getArea(true));
				},
				stop : function() {
					self._trigger('stop', null, self.getArea(true));
					$(grid).hide('slow').remove();
				}
			});

			if (!$.support.cssFloat) {
				$widget.attr('unselectable', 'on');
			}

			// remove handle
			$('div.ui-resizable-handle.ui-resizable-se', $widget).removeClass('ui-icon ui-icon-gripsmall-diagonal-se');

			$('<div id="crop-box"/>').css({
				width 	: this.width,
				height	: this.height,
				top		: top
			}).appendTo($parent).append($('#crop-container'));
			
			
			if (this.options.selection) {
				this.setArea(this.options.selection);
			}
		},
		setConstrain : function(s) {
			var ratio = s;

			if (s) {
				ratio = getRatio(s);
				this.setArea(s);
			}

			this.setRatio(ratio);
		},
		/**
		 * Get the ratio of the crop container to the crop window
		 */
		getRatio : function() {
			return {
				x : this.width / this.options.width,
				y : this.height / this.options.height
			};
		},
		
		setRatio : function(ratio) {
			$('#crop-widget').resizable("option", "aspectRatio", ratio);
		},
		
		setArea : function(o) {
			var self = this;
			
			var s = this._calculateSelection(o, {
				width : this.width,
				height: this.height
			});

			$('#crop-widget, #crop-window').animate({
				width 	: s.width,
				height	: s.height,
				left	: s.x,
				top		: s.y
			}, {
				step : function(now, fx) {
					if (fx.elem.id == 'crop-window') {
						$(fx.elem).children(':first').css(fx.prop, 0 - now);
					}
					
					self._trigger('change', null, self.getArea(true));
				},
				complete : function() {
					self._trigger('stop', null, self.getArea(true));
				}
			});
		},
		getDimensions : function() {
			return {
				width : $('#crop-container').width(),
				height: $('#crop-container').height()
			};
		},
		_calculateSelection : function(dim, img) {
			var x = 0, y = 0;

			if (dim.width > img.width || dim.height > img.height) {
				dim = $.Plugin.sizeToFit(dim, img);
			}

			if (dim.width < img.width) {
				x = Math.floor((img.width - dim.width) / 2);
			}

			if (dim.height < img.height) {
				y = Math.floor((img.height - dim.height) / 2);
			}

			return {
				x 		: x,
				y 		: y,
				width 	: dim.width,
				height 	: dim.height
			};
		},
		getArea : function() {
			var n = $('#crop-window').get(0), c = $('#crop-container').get(0), o = this.options;

			var rx = o.width / c.clientWidth;
			var ry = o.height / c.clientHeight;

			return {
				x 		: Math.round(n.offsetLeft * rx, 1),
				y 		: Math.round(n.offsetTop * ry, 1),
				width 	: Math.round(n.clientWidth * rx, 1),
				height	: Math.round(n.clientHeight * ry, 1)
			};
		},
		reset : function() {
			$('#crop-widget, #crop-window').css({
				width 	: this.width,
				height	: this.height,
				left	: 0,
				top		: 0
			});
			
			$('#crop-window').children().css({
				left	: 0,
				top		: 0
			});
		},
		remove : function() {
			var $parent = $('#crop-container').parent();

			$(this.element).css('top', $parent.css('top')).appendTo($parent.parent());
			$parent.remove();

			this.destroy();
		},
		destroy: function() {
			$.Widget.prototype.destroy.apply( this, arguments );
		}
	});

	$.widget("ui.thumbnail", {

		options : {
			src		: '',
			values 	: {},
			width	: 400,
			height	: 300
		},

		_init : function() {
			var self = this, values = this.options.values;

			$(this.element).append(
			'<div id="transform">' +
			'<div class="form-left">' +
			//'	<fieldset>' +
			//'	<legend>' + tinyMCEPopup.getLang('imgmanager_ext_dlg.source', 'Source Image') + '</legend>' +
			'	<div id="thumbnail-create-crop"></div>' +
			//'	</fieldset>' +
			'</div>' +
			'<div class="form-right">' +
			'	<fieldset>' +
			'	<legend>' + tinyMCEPopup.getLang('imgmanager_ext_dlg.properties', 'Properties') + '</legend>' +
			'	<div class="row">' +
			'		<label for="thumbnail_width">' + tinyMCEPopup.getLang('imgmanager_ext_dlg.width', 'Width') + '</label>' +
			'		<input type="text" id="thumbnail_width" value="" class="width"  /> px' +
			'		<div id="thumbnail_constrain" class="constrain"><span class="checkbox checked" aria-checked="true" role="checkbox"></span></div>' +
			'	</div>' +
			'	<div class="row">' +
			'		<label for="thumbnail_height">' + tinyMCEPopup.getLang('imgmanager_ext_dlg.height', 'Height') + '</label>' +
			'		<input type="text" id="thumbnail_height" value="" class="height" /> px' +
			'	</div>' +
			'	<div class="row">' +
			'		<label for="thumbnail_quality">' + tinyMCEPopup.getLang('imgmanager_ext_dlg.quality', 'Quality') + '</label>' +
			'		<div id="thumbnail_quality_slider" class="slider"></div>' +
			'		<input type="text" id="thumbnail_quality" value="" class="quality" /> %' +
			'	</div>' +
			'	</fieldset>'+
			'	<fieldset>' +
			'	<legend>' + tinyMCEPopup.getLang('dlg.preview', 'Preview') + '</legend>' +
			'	<div id="thumbnail-create-preview"></div>' +
			//'	<div id="thumbnail-create-coordinates"></div>' +
			'	</fieldset>' +
			'</div>' +
			'</div>'
			);

			// create slider
			self._createSlider();

			// set defaults
			$.each(['width', 'height', 'width_type', 'height_type', 'quality', 'mode'], function(i, v) {
				v = 'thumbnail_' + v;
				$('#' + v).val(values[v]);
			});
			
			// set default width and height if none set
			if ($('#thumbnail_width').val() === '' && $('#thumbnail_height').val() === '') {
				$('#thumbnail_width').val(120);
				$('#thumbnail_height').val(90);
			}
			
			var $preview = $('#thumbnail-create-preview');

			var img = new Image();

			var thumb = {
				width 	: parseFloat($('#thumbnail_width').val()),
				height	: parseFloat($('#thumbnail_height').val())
			};
			
			// store img object
			this.image = img;

			// fire image onload
			img.onload = function() {
				var $crop = $('#thumbnail-create-crop');

				// set label
				if (img.width > 400 || img.height > 300) {
					$('legend', $crop.parents('fieldset')).html( function(i, html) {
						return html + ' (' + tinyMCEPopup.getLang('imgmanager_ext_dlg.resized', 'Resized to fit window') + ')';
					});
				}

				var size = $.Plugin.sizeToFit(img, {
					width 	: self.options.width,
					height 	: self.options.height
				});

				// set default selection
				var s = {
					x 		: 0,
					y 		: 0,
					width 	: img.width,
					height 	: img.height
				};

				$.extend(s, $.Plugin.sizeToFit(thumb, size));

				self.cropImage = $(img).clone().attr(size).appendTo('#thumbnail-create-crop').crop({
					ratio 		: getRatio(thumb),
					width 		: img.width,
					height		: img.height,
					selection	: s,
					change : function(e, s) {
						self._updatePreview(s);
					}
				});
				
				$('#thumbnail-create-crop').children().css('top', ((self.options.height - size.height)) / 2);

				// create preview
				self._createPreview(s);

				// setup constrain
				self._setConstrain();

				$preview.removeClass('loading');
			};
			$preview.addClass('loading');

			// error fallback
			img.onerror = function() {
				$preview.removeClass('loading').addClass('error');
			};
			// set image src
			img.src = this.options.src;
		},
		_createSlider : function() {
			var values = this.options.values;
			// create slider
			$('#thumbnail_quality_slider').slider({
				min : 10,
				step: 10,
				slide: function(event, ui) {
					$('#thumbnail_quality').val(ui.value);
				},
				value : values.thumbnail_quality || 80
			});
		},
		_setConstrain : function() {
			var self = this;

			// shortcut
			var img = this.image;

			$('#thumbnail_constrain span.checkbox').click( function() {
				$(this).toggleClass('checked');
			});
			// store values
			$('#thumbnail_width, #thumbnail_height').each( function() {
				$(this).data('tmp', $(this).val());
			});
			
			$('#thumbnail_width').change( function() {
				var w = $(this).val(), $height = $('#thumbnail_height');

				// cannot be greater than image size
				if (w > img.width) {
					w = img.width;
					$(this).val(w);
				}

				// if constrain is on
				if (w && $('span.checkbox', '#thumbnail_constrain').hasClass('checked')) {
					var tw = $(this).data('tmp'), h = $height.val();
					
					if (h !== '') {
						var temp = ((h / tw) * w).toFixed(0);
						$height.val(temp).data('tmp', temp);	
					}
				}
				// store new tmp value
				$(this).data('tmp', w);

				self._resizeMarquee(w, $height.val());
			});
			$('#thumbnail_height').change( function() {
				var h = $(this).val(), $width = $('#thumbnail_width');

				if (h > img.height) {
					h = img.height;
					$(this).val(h);
				}

				// if constrain is on
				if (h && $('#thumbnail_constrain span.checkbox').hasClass('checked')) {
					var th = $(this).data('tmp'), w = $width.val();
					
					if (w !== '') {
						var temp = ((w / th) * h).toFixed(0);
						$width.val(temp).data('tmp', temp);
					}
				}
				// store new tmp value
				$(this).data('tmp', h);

				self._resizeMarquee($width.val(), h);
			});
		},
		
		/**
		 * Create the Thumbnail Preview
		 */
		_createPreview : function(s) {
			var img = this.image;

			var thumb = {
				width 	: parseFloat($('#thumbnail_width').val()),
				height	: parseFloat($('#thumbnail_height').val())
			};
			
			var $parent = $('#thumbnail-create-preview').parent();
			var ph = $parent.height() - ($parent.outerHeight() - $parent.height());

			$('#thumbnail-create-preview').css({
				width : thumb.width,
				height: thumb.height
			}).append($(img).clone()).css({
				top : (ph - thumb.height) / 2
			});

			// update thumbnail preview
			this._updatePreview(s);
		},
		
		/**
		 * Resize the Thumbnail Crop Marquee
		 */		
		_resizeMarquee : function(width, height) {
			$preview = $('#thumbnail-create-preview');
			
			var $parent = $preview.parent();
			var ph = $parent.height() - ($parent.outerHeight() - $parent.height());
			
			// must have at least one value
			if (!width && !height) {
				return;
			}
			
			width 	= parseInt(width);
			height 	= parseInt(height);
			
			if (!width) {
				width = Math.round(height / this.options.height * this.options.width);
			}
			
			if (!height) {
				height = Math.round(width / this.options.width * this.options.height);
			}
			
			var size = $.Plugin.sizeToFit({
				width 	: width,
				height 	: height
			}, {
				width 	: this.options.width,
				height 	: this.options.height
			});

			// set default selection
			var s = {
				x 		: 0,
				y 		: 0,
				width 	: size.width,
				height 	: size.height
			};
			
			if (width > 200 || height > 150) {
				$preview.css($.Plugin.sizeToFit({
					width 	: width,
					height 	: height
				}, {
					width 	: 200,
					height 	: 150
				}));
			} else {
				$preview.css({
					width 	: width,
					height 	: height
				});
			}
			
			$preview.css({
				top : (ph - $preview.height()) / 2
			});

			var ratio = getRatio({
				width 	: width,
				height 	: height
			});

			$(this.cropImage).crop('setRatio', ratio);
			$(this.cropImage).crop('setArea', s);
		},
		
		/**
		 * Update Thumbnail Preview and Crop Marquee
		 */
		_updatePreview : function(s) {
			var w, h, $preview = $('#thumbnail-create-preview'), tw = $('#thumbnail_width').val(), th = $('#thumbnail_height').val();

			var img = this.image;

			var iw = img.width;
			var ih = img.height;
			
			if (!tw && !th) {
				return;
			}
			
			if (!tw) {
				tw = Math.round(th / ih * iw);
			}
			
			if (!th) {
				th = Math.round(tw / iw * ih);
			}

			var rx = (tw / s.width) * ($preview.width() / tw);
			var ry = (th / s.height) * ($preview.height() / th);

			// Landscape
			if (tw > th) {
				w = 'auto';
				h = Math.round(ry * ih);
				// Portrait
			} else {
				w = Math.round(rx * iw);
				h = 'auto';
			}

			$('img', '#thumbnail-create-preview').css({
				width		: w,
				height		: h,
				marginLeft	: 0 - Math.round(rx * s.x),
				marginTop	: 0 - Math.round(ry * s.y)
			});
		},
		
		getMime : function(s) {
			var mime 	= 'image/jpeg';
			var ext 	= $.String.getExt(name);
			
			switch (ext) {
				case 'jpg':
				case 'jpeg':
					mime = 'image/jpeg';
					break;
				case 'png':
					mime = 'image/png';
					break;
				case 'bmp':
					mime = 'image/bmp';
					break;
			}
			
			return mime;
		},
		
		save : function() {
			var data = $(this.cropImage).crop('getArea', true);
			
			var w = $('#thumbnail_width').val(), h = $('#thumbnail_height').val();
			
			// need a width and height
			if (!w && !h) {
				return;
			}
			
			if (!w) {
				w = Math.round(h / this.options.height * this.options.width);
			}
			
			if (!h) {
				h = Math.round(w / this.options.width * this.options.height);
			}
			
			var quality = parseFloat($('#thumbnail_quality').val());
			quality = Math.max(Math.min(quality, 100), 10);
			
			if ($.support.canvas) {
				var canvas = document.createElement('canvas');
				$(canvas).attr({
					width : w,
					height: h
				}).appendTo('body').hide();
				
				canvas.getContext('2d').drawImage(this.image, data.x, data.y, data.width, data.height, 0, 0, w, h);
				var mime = this.getMime(this.image.src);

				// divide by 100 to give value between 0.1 and 1
				quality = quality / 100;
				
				try {
					data = canvas.toDataURL(mime, quality);	
				} catch (e) {
					data = canvas.toDataURL(mime);	
				}
				
				$(canvas).remove();				
			} else {
				// get quality
				$.extend(data, {
					'sx'		: data.x,
					'sy'		: data.y,
					'sw'		: data.width,
					'sh'		: data.height,
					'width'		: w,
					'height'	: h,
					'quality' 	: quality
				});
			}
			
			return data;
		},
		destroy: function() {
			$.Widget.prototype.destroy.apply( this, arguments );
		}
	});

	$.extend($.ui.thumbnail, {
		version: "2.0.7"
	});
})(jQuery);