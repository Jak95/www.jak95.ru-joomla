(function($) {

	function getRatio(o) {
		// Calculate Greatest Common Diviser
		function gcd(a, b) {
			return (b == 0) ? a : gcd(b, a % b);
		}

		// get gcd for the image
		var r = gcd(o.width, o.height);
		// return ratio
		return (o.width / r) / (o.height / r);
	};

	var EditorDialog = {

		stack : [],

		settings : {
			resize_quality : 80,
			save 	: $.noop
		},

		_setLoader : function() {
			$('<div/>').appendTo('#editor-image').addClass('loading');
		},

		init : function() {
			var self = this;
			
			// run init
			$.Plugin.init();
			
			$('#editor').removeClass('offleft');

			// window resize
			$(window).bind('resize', function() {
				self._resizeWin();
			});

			this.src = tinyMCEPopup.getWindowArg('src');

			$.extend(this.settings, {
				width 	: tinyMCEPopup.getWindowArg('width'),
				height 	: tinyMCEPopup.getWindowArg('height'),
				save	: tinyMCEPopup.getWindowArg('save')
			});

			// set container dimensions
			$('#editor-image').css({
				height 	:  $('body').height() -  $('#transform-tools').outerHeight(true) - 40,
				width 	: Math.min(Math.max(640, Math.min(this.settings.width, 1024)), $('body').width() - 335)
			});

			// set toolbox panel dimensions
			$('div.ui-tabs-panel', '#tabs').height($('#editor-image').outerHeight() -  $('ul.ui-tabs-nav', '#tabs').outerHeight() - 50);

			// set laoder
			this._setLoader();
			
			// build and store the image object			
			$('<img />').attr('src', this._loadImage($.Plugin.getURI() + this.src)).one('load', function() {
				// store original width and height
				$(this).data('width', this.width).data('height', this.height);	
				
				$(this).appendTo('#editor-image').hide();					
	
				// create canvas object
				$(this).canvas();
	
				self.position();

				//self._createImageSlider();

				self._createToolBox();
				
				// remove loader
				$('div.loading', '#editor-image').remove();

			});

			$('#transform_tab').accordion({
				collapsible : true,
				active : false,
				autoHeight : false,
				changestart : function(e, ui) {
					var action = $(ui.newHeader).data('action');

					self.reset(true);

					if(action) {
						self._initTransform(action);
					}
				}

			});

			$('#tabs').tabs('option', 'select', function() {
				self.reset(true);

				$('#transform_tab').accordion('activate', false);
				
				// reset fx units
				self._resetFX();
			});

			$('button.save', '#editor').button({
				disabled : true,
				icons: {
					primary: "ui-icon-circle-check"
				}
			}).click(function(e) {
				self.save();
				e.preventDefault();
			});

			$('button.revert', '#editor').button({
				disabled : true,
				icons: {
					primary: "ui-icon-circle-arrow-w"
				}
			}).click(function(e) {
				self.revert(e);
				e.preventDefault();
			});

			$('button.undo', '#editor').button({
				disabled : true,
				icons: {
					primary: "ui-icon-arrowreturnthick-1-w"
				}
			}).click(function(e) {
				self.undo(e);
				e.preventDefault();
			});

			$('button.apply', '#editor').button({
				icons: {
					primary: "ui-icon-check"
				}
			}).click(function(e) {
				self.apply($(this).data('function'));
				e.preventDefault();
			});
			
			$('button.reset', '#editor').button({
				disabled : true,
				icons: {
					primary: "ui-icon-closethick"
				}
			}).click(function(e) {
				self._resetTransform($(this).data('function'));
				e.preventDefault();
			});
		},
		
		_createImageSlider : function() {
			var self = this, $img = $('img', '#editor-image'), canvas = $img.canvas('getCanvas');
			
			var cw = $(canvas).width();
			var ch = $(canvas).height();
			
			var iw = $img.data('width');
			var ih = $img.data('height');
			
			if (iw == cw && ih == ch) {
				$('div.ui-tabs-panel', '#tabs').height($('div.ui-tabs-panel', '#tabs').height() + 30);
				return;
			}			
			
			var wd = (iw 	- cw) / 100;
			var hd = (ih 	- ch) / 100;

			var pw = $('#editor-image').width(), ph = $('#editor-image').height();
			
			$('#editor_image_slider').show().slider({
				min : 1,
				max : 100,
				step: 1,
				start : function() {
					self.reset();
					
					$(canvas).css('position', 'static');
				},
				slide: function(event, ui) {
					var nw = cw + ui.value * wd;
					var nh = ch + ui.value * hd;
					
					$(canvas).css({
						'width' : nw,
						'height': nh,
						'top' 	: (ph - nh) / 2,
						'left'  : (pw - nw) / 2
					});
					
					if (ui.value === 0) {
						$(canvas).css('position', 'relative');
					}
				}
			});
		},

		_createToolBox : function() {
			var self = this, $img = $('img', '#editor-image'), canvas = $img.canvas('getCanvas');

			var iw = canvas.width;
			var ih = canvas.height;
			// scaled width & height
			var sw = $(canvas).width();
			var sh = $(canvas).height();
			
			var sl = tinyMCEPopup.getLang('imgmanager_ext_dlg.original', 'Original');
			
			if (iw > sw && ih > sh) {
				sl = tinyMCEPopup.getLang('imgmanager_ext_dlg.scaled', 'Scaled');
			}

			// setup presets
			$('#crop_presets option, #resize_presets option').each(function() {
				var v = $(this).val();

				if(v && /[0-9]+x[0-9]+/.test(v)) {
					v = v.split('x');

					var w = parseFloat(v[0]), h = parseFloat(v[1]);

					if(w >= sw && h >= sh) {
						$(this).remove();
					}
				}
			});

			// resize presets
			$('#resize_presets').change(function() {
				var v = $(this).val();

				if(v) {
					v = v.split('x');
					var w = parseFloat($.trim(v[0])), h = parseFloat($.trim(v[1]));

					$('#resize_width').val(w).data('tmp', w);
					$('#resize_height').val(h).data('tmp', h);
					
					var ratio = $('span.checkbox', '#resize_constrain').is('.checked') ? w / h : false;
					
					$(canvas).resize('setRatio', ratio);
					$(canvas).resize('setSize', w, h);
				}

			}).prepend('<option value="' + sw + 'x' + sh + '" selected="selected">' + sw + ' x ' + sh + ' (' + sl + ')</option>');

			// resize values
			$('#resize_width').val(sw).data('tmp', sw).change(function() {
				var w = $(this).val(), $height = $('#resize_height');

				// if constrain is on
				if($('span.checkbox', '#resize_constrain').hasClass('checked')) {
					var tw = $(this).data('tmp'), h = $height.val();

					var temp = ((h / tw) * w).toFixed(0);
					$height.val(temp).data('tmp', temp);
				}
				// store new tmp value
				$(this).data('tmp', w);

				var ratio = $('span.checkbox', '#resize_constrain').is('.checked') ? w / $height.val() : false;

				$(canvas).resize('setRatio', ratio);
				$(canvas).resize('setSize', w, $height.val());
			});

			$('#resize_height').val(sh).data('tmp', sh).change(function() {
				var h = $(this).val(), $width = $('#resize_width');

				// if constrain is on
				if($('span.checkbox', '#resize_constrain').hasClass('checked')) {
					var th = $(this).data('tmp'), w = $width.val();

					var temp = ((w / th) * h).toFixed(0);
					$width.val(temp).data('tmp', temp);
				}
				// store new tmp value
				$(this).data('tmp', h);

				var ratio = $('span.checkbox', '#resize_constrain').is('.checked') ? $width.val() / h : false;

				$(canvas).resize('setRatio', ratio);
				$(canvas).resize('setSize', $width.val(), h);
			});

			// resize constrain
			$('span.checkbox', '#resize_constrain').click(function() {
				$(this).toggleClass('checked');
				
				var ratio = $(this).hasClass('checked') ? ({width : $('#resize_width').val(), height : $('#resize_height').val()}) : false;
				
				$(canvas).resize('setConstrain', ratio);
			});

			// crop constrain
			$('#crop_constrain').click(function() {
				$(this).toggleClass('checked');

				//$(canvas).crop('setConstrain', $(this).is(':checked') ? {width : });
				if ($(this).is(':checked')) {
					$('#crop_presets').change();
				} else {
					$(canvas).crop('setConstrain', false);
				}			
			});

			$('#crop_presets').change(function() {
				var img = $img.get(0);

				var v = $(this).val();

				var s = {
					width 	: img.width,
					height 	: img.height
				};
				
				$.extend(s, $(canvas).crop('getArea'));

				if(/:/.test(v)) {
					var r = v.split(':'), r1 = parseInt($.trim(r[0])), r2 = parseInt($.trim(r[1]));
					var ratio = r1 / r2;

					if(r2 > r1) {
						ratio = r2 / r1;
					}

					// landscape
					if(s.width > s.height) {
						if(r2 > r1) {
							ratio = r2 / r1;
						}

						s.height = Math.round(s.width / ratio);
						// portrait
					} else {
						s.width = Math.round(s.height / ratio);
					}
				} else {
					v = v.split('x'); s.width = parseInt($.trim(v[0])), s.height = parseInt($.trim(v[1]));
					var ratio = s.width / s.height;
				}
				
				if ($('#crop_constrain').is(':checked')) {
					$(canvas).crop('setRatio', ratio);
				}
				$(canvas).crop('setArea', s);
			}).prepend('<option value="' + sw + 'x' + sh + '" selected="selected">' + sw + ' x ' + sh + ' (' + sl + ')</option>');

			$('#transform-crop-cancel').click(function() {
				self.reset();
			});

			var dim = $.Plugin.sizeToFit($img.get(0), {
				width 	: 85,
				height 	: 85
			});

			$.each([90, -90], function(i, v) {
				var rotate = $img.clone().attr(dim).appendTo('#rotate_flip').hide().wrap('<div/>').after('<span class="label">' + tinyMCEPopup.getLang('imgmanager_ext_dlg.' + v, v) + '</span>');

				$(rotate).canvas({
					click : function() {
						self.apply('rotate', v);
						$(rotate).canvas('rotate', v);
					}

				});

				$(rotate).canvas('resize', dim.width, dim.height);
				$(rotate).canvas('rotate', v);
			});

			$.each(['vertical', 'horizontal'], function(i, v) {
				var flip = $img.clone().attr(dim).appendTo('#rotate_flip').hide().wrap('<div/>').after('<span class="label">' + tinyMCEPopup.getLang('imgmanager_ext_dlg.' + v, v) + '</span>');

				$(flip).canvas({
					click : function() {
						self.apply('flip', v);

						$(flip).canvas('flip', v);
					}

				});

				$(flip).canvas('resize', dim.width, dim.height);
				$(flip).canvas('flip', v);
			});

			this._createFX();
		},

		_createFX : function() {
			var self = this, $img = $('img', '#editor-image');

			$('#editor_effects').empty();

			// Effects
			if($.support.canvas) {
				var dim = $.Plugin.sizeToFit($img.get(0), {
					width : 70,
					height : 70
				});

				$.each({
					'lighten' 		: [0.1, 0.5],
					'darken' 		: [0.1, 0.5],
					'desaturate' 	: [10, 50],
					'saturate' 		: [10, 50]
				}, function(k, v) {
					var fx = $img.clone().attr(dim).appendTo('#editor_effects').wrap('<div class="editor_effect"/>').after('<span class="label">' + tinyMCEPopup.getLang('imgmanager_ext_dlg.' + k, k) + '</span>');

					var controls = $('<div class="editor_effect_unit"><span class="minus">&#45;</span><span class="plus">&#43;</span><span class="unit"/></div>').hide().insertAfter(fx);
					
					$(fx).canvas({
						click : function() {
							self.apply(k, v[0]);
							// reset units and hide controls
							$('div.editor_effect_unit', '#editor_effects').not(controls).data('unit', 0).hide();
							$('div.editor_effect', '#editor_effects').data('state', 0);

							var x = $(controls).data('unit') + 1;
							$(controls).data('unit', x).show().children('span.unit').html(x);
						}
					});
					
					$(fx).next('canvas').add(controls).hover(function() {
						$('span.minus, span.plus', $(controls)).show();
					}, function() {
						$('span.minus, span.plus', $(controls)).hide();
					});
					
					$(controls).data('unit', 0).children('span.minus').click(function(e) {
						var x = $(controls).data('unit');
						
						if (x > 0) {
							// only undo on control button click
							if (e.pageX && e.pageY) {
								self.undo();
							}							
							x = x - 1;
							$(this).siblings('span.unit').html(x);	
						}
						
						if (x == 0) {
							$(controls).hide();
						}
						
						$(controls).data('unit', x);
					});
					
					$(controls).children('span.plus').click(function() {
						self.apply(k, v[0]);
						var x = $(controls).data('unit') + 1;
						
						$(this).siblings('span.unit').html(x);
						
						$(controls).data('unit', x);
					});
					
					$(fx).canvas('resize', dim.width, dim.height);
					$(fx).canvas(k, v[1]);
				});

				$('<hr/>').appendTo('#editor_effects');

				$.each(['greyscale', 'sepia', 'invert', 'threshold'], function(i, v) {
					var fx = $img.clone().attr(dim).appendTo('#editor_effects').wrap('<div class="editor_effect"/>').after('<span class="label">' + tinyMCEPopup.getLang('imgmanager_ext_dlg.' + v, v) + '</span>');

					var $p = $(fx).parent('div.editor_effect');
					
					$p.data('state', 0);
					
					$(fx).canvas({
						click : function() {
							// reset units and hide controls
							$('div.editor_effect_unit', '#editor_effects').data('unit', 0).hide();
							$('div.editor_effect', '#editor_effects').not($p).data('state', 0);
							
							var s = $p.data('state');
							
							if (s == 0) {
								self.apply(v);
								s = 1;
							} else {
								self.undo();
								s = 0;
							}
							
							$p.data('state', s);
						}
					});
					$(fx).canvas('resize', dim.width, dim.height);
					$(fx).canvas(v);
				});

			}
		},
		
		_resetFX : function() {
			$('div.editor_effect_unit', '#editor_effects').data('unit', 0).hide();
			$('div.editor_effect', '#editor_effects').data('state', 0);
		},

		_resizeWin : function() {

		},

		_initTransform : function(fn) {
			var img = $('img', '#editor-image').get(0);
			var canvas = $(img).canvas('getCanvas');
			
			this.position();

			switch (fn) {
				case 'resize' :
				
					$(canvas).resize({
						width 	: canvas.width,
						height 	: canvas.height,
						ratio 	: $('span.checkbox', '#resize_constrain').is('.checked') ? getRatio(canvas) : false,
						resize 	: function(e, size) {
							$('#resize_width').val(size.width);
							$('#resize_height').val(size.height);
						},
						stop 	: function() {
							$('#resize_reset').button('enable');
						}
					});

					break;
				case 'crop'	:

					$(canvas).crop({
						width 	: canvas.width,
						height 	: canvas.height,
						ratio 	: $('#crop_constrain').is(':checked') ? getRatio(canvas) : false,
						clone 	: $(img).canvas('clone'),
						stop 	: function() {
							$('#crop_reset').button('enable');
						}
					});

					break;
				case 'rotate' :

					break;
			}
		},
		
		_resetTransform : function(fn) {
			var img = $('img', '#editor-image').get(0), canvas = $(img).canvas('getCanvas');

			switch (fn) {
				case 'resize' :
				
					this.position();
				
					$(canvas).resize('reset');
					$('#resize_reset').button('disable');
					
					var w = $(canvas).width() 	|| canvas.width;
					var h = $(canvas).height() 	|| canvas.height;
					
					$('#resize_width').val(w).data('tmp', w);
					$('#resize_height').val(h).data('tmp', h);
					
					$('#resize_presets').val($('#resize_presets option:first').val());

					break;
				case 'crop'	:

					$(canvas).crop('reset');
					$('#crop_reset').button('disable');
					
					$('#crop_presets').val($('#crop_presets option:first').val());

					break;
				case 'rotate' :

					break;
			}
		},

		undo : function(e) {
			this.stack.pop();
			
			$('img', '#editor-image').canvas('undo');

			if(!this.stack.length) {
				$('button.undo').button('disable');
				$('button.revert').button('disable');
				$('button.save').button('disable');
			}
			
			this.position();
			
			if (e) {
				//this._resetFX();
				$('div.editor_effect_unit:visible span.minus', '#editor_effects').click();
			}
		},

		revert : function(e) {
			var $img = $('img', '#editor-image');

			$img.canvas('clear').canvas('draw');

			this.stack = [];

			$('button.undo').button('disable');
			$('button.revert').button('disable');
			$('button.save').button('disable');
			
			this.position();
			
			if (e) {
				this._resetFX();
			}
		},

		reset : function(rw) {
			var self = this, $img = $('img', '#editor-image'), canvas = $img.canvas('getCanvas');
			
			$.each(['resize', 'crop', 'rotate'], function(i, fn) {
				self._resetTransform(fn);
			});	
			
			if (rw) {
				$(canvas).resize("remove").crop("remove").rotate("remove");
			}
					
			this.position();
		},

		position : function() {
			var self = this, $img = $('img', '#editor-image'), canvas = $img.canvas('getCanvas');
			var pw = $('#editor-image').width(), ph = $('#editor-image').height();

			if (canvas.width > pw || canvas.height > ph) {				
				$(canvas).css($.Plugin.sizeToFit(canvas, {
					width 	: pw - 20,
					height 	: ph - 20
				}));
			} else {
				$(canvas).css({
					width : '',
					height: ''
				});
			}
			
			var ch = $(canvas).height() || canvas.height;
			
			$(canvas).css({
				'top' : (ph - ch) / 2
			});
		},

		apply : function() {
			var self = this, $img = $('img', '#editor-image'), canvas = $img.canvas('getCanvas');

			var args = $.makeArray(arguments);

			var fn = args.shift();
			var args = args;

			switch (fn) {
				case 'resize' :
					var w = $('#resize_width').val();
					var h = $('#resize_height').val();

					$img.canvas('resize', w, h, true);
					args = [w, h];
					
					self.position();

					break;
				case 'crop' :
					var s = $(canvas).crop('getArea');

					$img.canvas('crop', s.width, s.height, s.x, s.y, true);
					args = [s.width, s.height, s.x, s.y];

					$('#transform_tab').accordion('activate', false);
					
					self.position();

					break;
				case 'rotate' :
					$img.canvas('rotate', args[0], true);

					self.position();

					break;
				case 'flip' :
					$img.canvas('flip', args[0], true);
					
					self.position();

					break;
				case 'desaturate' :
				case 'saturate' :
				case 'blur':
				case 'lighten':
				case 'darken':
					$img.canvas(fn, args[0], true);
					break;
				case 'threshold':
					$img.canvas(fn, 128, true);
					break;
				case 'greyscale' :
				case 'sepia' :
				case 'invert' :
				case 'halftone':
					$img.canvas(fn, true);
					break;
			}

			this.stack.push({
				task : fn,
				args : args
			});

			$('button.undo').button('enable');
			$('button.revert').button('enable');
			$('button.save').button('enable');

			this.reset(true);
		},

		getMime : function(s) {
			var mime = 'image/jpeg';
			var ext = $.String.getExt(name);

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

		/**
		 * Create save stack
		 */
		save : function(name) {
			var self = this, $img = $('img', '#editor-image'), canvas = $img.canvas('getCanvas');

			if(!this.stack.length) {
				return;
			}

			var extras = ''; 
			
			if (!$.browser.mozilla) {
				extras += '<div class="row">' + '	<label for="image_quality">' + tinyMCEPopup.getLang('imgmanager_ext_dlg.quality', 'Quality') + '</label>' + '	<div id="image_quality_slider" class="slider"></div>' + '	<input type="text" id="image_quality" value="100" class="quality" /> %' + '</div>';

			}

			var name = $.String.basename(this.src);
			name = $.String.stripExt(name);
			var ext = $.String.getExt(this.src);

			$.Dialog.prompt(tinyMCEPopup.getLang('imgmanager_ext_dlg.save_image', 'Save Image'), {
				text : tinyMCEPopup.getLang('dlg.name', 'Name'),
				elements : extras,
				height : 180,
				value : name,
				onOpen : function() {
					$('#image_quality_slider').slider({
						min : 10,
						step : 10,
						slide : function(event, ui) {
							$('#image_quality').val(ui.value);
						},

						value : 100
					});
				},

				confirm : function(name) {
					var quality = $('#image_quality').val() || 100;

					// set loading message
					$('<div/>').appendTo('#editor-image').addClass('loading').css($(canvas).position()).css({
						width : $(canvas).width(),
						height: $(canvas).height()
					});
					name = (name + '.' + ext) || $.String.basename(self.src);

					var data = $img.canvas('output', self.getMime(name), quality);

					$.JSON.request('saveEdit', {
						'json' : [self.src, name, self.stack],
						'data' : data
					}, function(o) {
						$('div.loading', '#editor-image').remove();

						if(o.error && o.error.length) {
							$.Dialog.alert(o.error);
						}

						if(o.files) {
							self.src = o.files[0];
						}

						// refresh image and reset
						$(self.image).attr('src', self._loadImage($.Plugin.getURI() + self.src)).load(function() {
							self._createFX();

							$(this).canvas('remove').canvas();
						});
						
						var s = self.settings;
						
						// fire save callback
						s.save.apply(s.scope || self, [self.src]);

						// clear stack
						self.stack = [];
						// disable undo / revert
						$('button.undo').button('disable');
						$('button.revert').button('disable');
						$('button.save').button('disable');
					});

					$(this).dialog('close');
				}
			});
		},

		_loadImage : function(src) {
			return src + '?' + new Date().getTime();
		}

	};

	window.EditorDialog = EditorDialog;
})(jQuery);
