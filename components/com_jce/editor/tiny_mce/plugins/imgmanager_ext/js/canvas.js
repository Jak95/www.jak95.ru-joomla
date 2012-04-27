(function($) {

	$.support.canvas = !!document.createElement('canvas').getContext;

	$.widget("ui.canvas", {

		stack : [],
		
		options : {
			click : $.noop
		},

		_init : function() {
			var $el = $(this.element), $parent = $el.parent();

			if ($.support.canvas) {
				// create canvas
				this.canvas = document.createElement('canvas');
				$(this.canvas).insertAfter(this.element);
				// store context
				this.context = this.canvas.getContext('2d');
				
				this.draw();
			} else {
				return false;
			}
			
			// add click handler
			$(this.canvas).click(this.options.click);
		},

		getContext : function() {
			return this.context;
		},

		getCanvas : function() {
			return this.canvas;
		},
		
		draw : function() {
			var w = this.options.width || $(this.element).width(), h = this.options.height || $(this.element).height();
			
			$(this.canvas).attr({
				width : w,
				height: h
			});
			
			this.context.drawImage((this.element).get(0), 0, 0, w, h);
		},

		copy : function() {
			return this.canvas.raphael ? this.canvas.clone() : $(this.canvas).clone().get(0);
		},

		clone : function() {
			var copy = this.copy();
			
			copy.getContext('2d').drawImage(this.canvas, 0, 0);
			
			return copy;
		},

		clear : function() {
			var ctx = this.context;

			var w = $(this.element).width(), h = $(this.element).height();

			if (ctx) {
				ctx.clearRect(0, 0, w, h);
			} 
		},

		resize : function(w, h, save) {
			var ctx = this.context;
			
			w = parseInt(w), h = parseInt(h);

			if (ctx) {
				
				if (save) {
					this.save();
				}

				var copy = this.copy();
				copy.getContext('2d').drawImage(this.canvas, 0, 0, w, h);
				
				$(this.canvas).attr({
					width : w,
					height: h
				});

				ctx.drawImage(copy, 0, 0);
			}
		},

		crop : function(w, h, x, y, save) {
			var ctx = this.context;

			w = parseInt(w), h = parseInt(h), x = parseInt(x), y = parseInt(y);

			if (ctx) {
				if (save) {
					this.save();
				}

				if (x < 0)
					x = 0;
				if (x > this.canvas.width - 1) {
					x = this.canvas.width - 1;
				}

				if (y < 0)
					y = 0;
				if (y > this.canvas.height -1 ) {
					y = this.canvas.height - 1;
				}

				if (w < 1)
					w = 1;
				if (x + w > this.canvas.width) {
					w = this.canvas.width - x;
				}

				if (h < 1)
					h = 1;
				if (y + h > this.canvas.height) {
					h = this.canvas.height - y;
				}

				var copy = this.copy();

				copy.getContext('2d').drawImage(this.canvas, 0, 0);

				$(this.canvas).attr({
					width : w,
					height: h
				});

				ctx.drawImage(copy, x, y, w, h, 0, 0, w, h);
			}
		},
		
		/*
		 * Based on Pixastic Lib - Rotate - v0.1.1
		 * Copyright (c) 2008 Jacob Seidelin, jseidelin@nihilogic.dk, http://blog.nihilogic.dk/
		 * License: [http://www.pixastic.com/lib/license.txt]
		 */
		rotate : function(angle, save) {
			var ctx = this.context, w = this.canvas.width, h = this.canvas.height, ph = $(this.element).parent().outerHeight();

			var radian = -parseFloat(angle) * Math.PI / 180;

			var dimAngle = radian;

			if (dimAngle > Math.PI*0.5) {
				dimAngle = Math.PI - dimAngle;
			}
			if (dimAngle < -Math.PI*0.5) {
				dimAngle = -Math.PI - dimAngle;
			}

			var diag = Math.sqrt(w * w + h * h);

			var diagAngle1 = Math.abs(dimAngle) - Math.abs(Math.atan2(h, w));
			var diagAngle2 = Math.abs(dimAngle) + Math.abs(Math.atan2(h, w));

			var nw 	= Math.abs(Math.cos(diagAngle1) * diag);
			var nh 	= Math.abs(Math.sin(diagAngle2) * diag);

			if (ctx) {
				if (save) {
					this.save();
				}

				var copy = this.clone();
				
				$(this.canvas).attr({
					width : nw,
					height: nh
				});

				ctx.translate(nw / 2, nh / 2);
				ctx.rotate(radian);
				ctx.drawImage(copy, -w / 2, -h / 2);

			}			
		},

		flip : function(axis, save) {
			var ctx = this.context, w = this.canvas.width, h = this.canvas.height;

			if (ctx) {
				if (save) {
					this.save();
				}

				var copy = this.copy();
				copy.getContext('2d').drawImage(this.canvas, 0, 0, w, h, 0, 0, w, h);
				
				ctx.clearRect(0, 0, w, h);
				
				$(this.canvas).attr({
					width : w,
					height: h
				});
				
				if (axis == "horizontal") {
					ctx.scale(-1,1);
					ctx.drawImage(copy, -w, 0, w, h);
				} else {
					ctx.scale(1,-1);
					ctx.drawImage(copy, 0, -h, w, h);
				}

			}
		},
	
		/*
		 * Based on Pixastic Lib - Greyscale filter - v0.1.1
		 * Copyright (c) 2008 Jacob Seidelin, jseidelin@nihilogic.dk, http://blog.nihilogic.dk/
		 * License: [http://www.pixastic.com/lib/license.txt]
		 */
		greyscale : function(save) {
			var ctx = this.context, w = this.canvas.width, h = this.canvas.height;

			if (save) {
				this.save();
			}
			
			var useAverage = false;

			var imgData = ctx.getImageData(0, 0, w, h);
			var data = imgData.data;

			var p 	= w*h;
			var pix = p*4, pix1, pix2;

			if (useAverage) {
				while (p--)
					data[pix-=4] = data[pix1=pix+1] = data[pix2=pix+2] = (data[pix]+data[pix1]+data[pix2])/3
			} else {
				while (p--)
					data[pix-=4] = data[pix1=pix+1] = data[pix2=pix+2] = (data[pix]*0.3 + data[pix1]*0.59 + data[pix2]*0.11);
			}

			ctx.putImageData(imgData, 0, 0);
		},
	
		/*
		 * Based on Pixastic Lib - Sepai filter - v0.1.1
		 * Copyright (c) 2008 Jacob Seidelin, jseidelin@nihilogic.dk, http://blog.nihilogic.dk/
		 * License: [http://www.pixastic.com/lib/license.txt]
		 */
		sepia : function(save) {
			var ctx = this.context, w = this.canvas.width, h = this.canvas.height;

			if (save) {
				this.save();
			}

			mode = 1;

			var imgData = ctx.getImageData(0, 0, w, h);
			var data = imgData.data;

			var w4 = w*4;
			var y = h;
			do {
				var offsetY = (y-1)*w4;
				var x = w;
				do {
					var offset = offsetY + (x-1)*4;

					if (mode) {
						// a bit faster, but not as good
						var d = data[offset] * 0.299 + data[offset+1] * 0.587 + data[offset+2] * 0.114;
						var r = (d + 39);
						var g = (d + 14);
						var b = (d - 36);
					} else {
						// Microsoft
						var or = data[offset];
						var og = data[offset+1];
						var ob = data[offset+2];
	
						var r = (or * 0.393 + og * 0.769 + ob * 0.189);
						var g = (or * 0.349 + og * 0.686 + ob * 0.168);
						var b = (or * 0.272 + og * 0.534 + ob * 0.131);
					}

					if (r < 0) r = 0; if (r > 255) r = 255;
					if (g < 0) g = 0; if (g > 255) g = 255;
					if (b < 0) b = 0; if (b > 255) b = 255;

					data[offset] = r;
					data[offset+1] = g;
					data[offset+2] = b;

				} while (--x);
			} while (--y);

			ctx.putImageData(imgData, 0, 0);
		},
		
		/*
		 * Based on Pixastic Lib - Invert filter - v0.1.1
		 * Copyright (c) 2008 Jacob Seidelin, jseidelin@nihilogic.dk, http://blog.nihilogic.dk/
		 * License: [http://www.pixastic.com/lib/license.txt]
		 */
		invert : function(save) {
			var ctx = this.context, w = this.canvas.width, h = this.canvas.height;

			if (save) {
				this.save();
			}
			
			var invertAlpha = false;

			var imgData = ctx.getImageData(0, 0, w, h);
			var data = imgData.data;

			var p = w * h;

			var pix = p*4, pix1 = pix + 1, pix2 = pix + 2, pix3 = pix + 3;

			while (p--) {
				data[pix-=4] = 255 - data[pix];
				data[pix1-=4] = 255 - data[pix1];
				data[pix2-=4] = 255 - data[pix2];
				if (invertAlpha)
					data[pix3-=4] = 255 - data[pix3];
			}

			ctx.putImageData(imgData, 0, 0);
		},
		
		threshold : function(threshold, save) {
			var ctx = this.context, w = this.canvas.width, h = this.canvas.height;

			if (save) {
				this.save();
			}

			var imgData = ctx.getImageData(0, 0, w, h);
			var data = imgData.data;
			var p = w * h;
			
			threshold = parseInt(threshold) || 128;
			
			threshold = threshold * 3;
			
			// Loop through data.
	        for (var i = 0; i < p * 4; i += 4) {
	
	          // First bytes are red bytes.        
	          // Get red value.
	          var r =data[i];
	
	          // Second bytes are green bytes.
	          // Get green value.
	          var g = data[i + 1];
	
	          // Third bytes are blue bytes.
	          // Get blue value.
	          var b = data[i + 2];
	          
	          if ((r + b + g) >= threshold) {
	          	r = 255;
	          	g = 255;
	          	b = 255;
	          } else {
	          	r = 0;
	          	g = 0;
	          	b = 0;
	          }
	
	          // Assign average to red, green, and blue.
	          data[i] 		= r;
	          data[i + 1] 	= g;
	          data[i + 2] 	= b;
	        }
	        
	        ctx.putImageData(imgData, 0, 0);
		},
		
		/*
		 * Based on Pixastic Lib - Pointillize filter - v0.1.1
		 * Copyright (c) 2008 Jacob Seidelin, jseidelin@nihilogic.dk, http://blog.nihilogic.dk/
		 * License: [http://www.pixastic.com/lib/license.txt]
		 */
		halftone : function(save) {
			var ctx = this.context, w = this.canvas.width, h = this.canvas.height;

			if (save) {
				this.save();
			}
			
			var radius 	= 3;
			var density = 0.6;

			var w4 	= w * 4;
			var y 	= h;

			var canvasWidth 	= w;
			var canvasHeight 	= h;

			var pixel = document.createElement("canvas");
			pixel.width = pixel.height = 1;
			var pixelCtx = pixel.getContext("2d");

			var copy = this.clone();

			var diameter = radius * 2;

			// create white background
			this.clear();

			var dist = 1 / density;

			for (var y=0;y<h+radius;y+=diameter*dist) {
				for (var x=0;x<w+radius;x+=diameter*dist) {
					rndX = x;
					rndY = y;

					var pixX = rndX - radius;
					var pixY = rndY - radius;
					
					if (pixX < 0) pixX = 0;
					if (pixY < 0) pixY = 0;

					var cx = rndX + 0;
					var cy = rndY + 0;
					
					if (cx < 0) cx = 0;
					if (cx > w) cx = w;
					if (cy < 0) cy = 0;
					if (cy > h) cy = h;

					var diameterX = diameter;
					var diameterY = diameter;

					if (diameterX + pixX > w)
						diameterX = w - pixX;
					if (diameterY + pixY > h)
						diameterY = h - pixY;
					if (diameterX < 1) diameterX = 1;
					if (diameterY < 1) diameterY = 1;

					pixelCtx.drawImage(copy, pixX, pixY, diameterX, diameterY, 0, 0, 1, 1);
					var data = pixelCtx.getImageData(0,0,1,1).data;

					ctx.fillStyle = "rgb(" + data[0] + "," + data[1] + "," + data[2] + ")";
					ctx.beginPath();
					ctx.arc(cx, cy, radius, 0, Math.PI*2, true);
					ctx.closePath();
					ctx.fill();	
				}
			}
		},

		/*
		 * Based on Pixastic Lib - Lighten filter - v0.1.1
		 * Copyright (c) 2008 Jacob Seidelin, jseidelin@nihilogic.dk, http://blog.nihilogic.dk/
		 * License: [http://www.pixastic.com/lib/license.txt]
		 */
		lighten : function(amount) {
			var ctx = this.context, w = this.canvas.width, h = this.canvas.height;
			amount = parseFloat(amount);
			amount = Math.max(-1, Math.min(1, amount));

			this.save();

			var imgData 	= ctx.getImageData(0, 0, w, h);
			var data 		= imgData.data;
			
			var p = w * h;

			var pix = p*4, pix1 = pix + 1, pix2 = pix + 2;
			var mul = amount + 1;

			while (p--) {
				if ((data[pix-=4] = data[pix] * mul) > 255)
					data[pix] = 255;

				if ((data[pix1-=4] = data[pix1] * mul) > 255)
					data[pix1] = 255;

				if ((data[pix2-=4] = data[pix2] * mul) > 255)
					data[pix2] = 255;

			}
			
			ctx.putImageData(imgData, 0, 0);
		},
		
		darken : function(amount) {
			this.lighten(-amount);
		},
		
		/*
		 * Based on Pixastic Lib - HSL Filter - v0.1.1
		 * Copyright (c) 2008 Jacob Seidelin, jseidelin@nihilogic.dk, http://blog.nihilogic.dk/
		 * License: [http://www.pixastic.com/lib/license.txt]
		 */
		saturate : function(v, save) {
			var ctx = this.context, w = this.canvas.width, h = this.canvas.height;
			v = parseFloat(v);
			v = v / 100;
			v = Math.max(-1, Math.min(1, v));

			if (save) {
				this.save();
			}

			var imgData 	= ctx.getImageData(0, 0, w, h);
			var data 		= imgData.data;
			
			// this seems to give the same result as Photoshop
			if (v < 0) {
				var satMul = 1+v;
			} else {
				var satMul = 1+v*2;
			}
			
			var p = w * h;

			var pix = p*4, pix1 = pix + 1, pix2 = pix + 2, pix3 = pix + 3;

			while (p--) {

				var r = data[pix-=4];
				var g = data[pix1=pix+1];
				var b = data[pix2=pix+2];

				if (v != 0) {
					// ok, here comes rgb to hsl + adjust + hsl to rgb, all in one jumbled mess. 
					// It's not so pretty, but it's been optimized to get somewhat decent performance.
					// The transforms were originally adapted from the ones found in Graphics Gems, but have been heavily modified.
					var vs = r;
					if (g > vs) vs = g;
					if (b > vs) vs = b;
					var ms = r;
					if (g < ms) ms = g;
					if (b < ms) ms = b;
					var vm = (vs-ms);
					var l = (ms+vs)/510;
					if (l > 0) {
						if (vm > 0) {
							if (l <= 0.5) {
								var s = vm / (vs+ms) * satMul;
								if (s > 1) s = 1;
								var v = (l * (1+s));
							} else {
								var s = vm / (510-vs-ms) * satMul;
								if (s > 1) s = 1;
								var v = (l+s - l*s);
							}
							if (r == vs) {
								if (g == ms)
									var h = 5 + ((vs-b)/vm) + 0;
								else
									var h = 1 - ((vs-g)/vm) + 0;
							} else if (g == vs) {
								if (b == ms)
									var h = 1 + ((vs-r)/vm) + 0;
								else
									var h = 3 - ((vs-b)/vm) + 0;
							} else {
								if (r == ms)
									var h = 3 + ((vs-g)/vm) + 0;
								else
									var h = 5 - ((vs-r)/vm) + 0;
							}
							if (h < 0) h+=6;
							if (h >= 6) h-=6;
							var m = (l+l-v);
							var sextant = h>>0;
							if (sextant == 0) {
								r = v*255; g = (m+((v-m)*(h-sextant)))*255; b = m*255;
							} else if (sextant == 1) {
								r = (v-((v-m)*(h-sextant)))*255; g = v*255; b = m*255;
							} else if (sextant == 2) {
								r = m*255; g = v*255; b = (m+((v-m)*(h-sextant)))*255;
							} else if (sextant == 3) {
								r = m*255; g = (v-((v-m)*(h-sextant)))*255; b = v*255;
							} else if (sextant == 4) {
								r = (m+((v-m)*(h-sextant)))*255; g = m*255; b = v*255;
							} else if (sextant == 5) {
								r = v*255; g = m*255; b = (v-((v-m)*(h-sextant)))*255;
							}
						}
					}
				}

				if (r < 0) 
					data[pix] = 0
				else if (r > 255)
					data[pix] = 255
				else
					data[pix] = r;

				if (g < 0) 
					data[pix1] = 0
				else if (g > 255)
					data[pix1] = 255
				else
					data[pix1] = g;

				if (b < 0) 
					data[pix2] = 0
				else if (b > 255)
					data[pix2] = 255
				else
					data[pix2] = b;

			}	
			
			ctx.putImageData(imgData, 0, 0);
		},
		
		desaturate : function(v, save) {
			this.saturate(-v, save);
		},
		
		save : function() {
			var ctx = this.context;

			this.stack.push({
				width 	: this.canvas.width,
				height	: this.canvas.height,
				data	: $.support.canvas ? ctx.getImageData(0, 0, this.canvas.width, this.canvas.height) : ''
			});
		},

		undo : function() {
			var ctx = this.context, img = $(this.element).get(0);

			var props = this.stack.pop();
			
			$(this.canvas).attr({
				width : $(img).width(),
				height: $(img).height()
			});

			if (props.data) {
				ctx.putImageData(props.data, 0, 0);
			} else {
				ctx.restore();
				ctx.drawImage(img, 0, 0);
			}
		},

		load : function() {
			var ctx = this.context;
			
			var w = this.canvas.width, h = this.canvas.height;
			
			var data = ctx.getImageData(0, 0, w, h);
			ctx.clearRect(0, 0, w, h);
			ctx.putImageData(data, 0, 0);
		},
		
		output : function(mime, quality) {			
			quality = parseInt(quality) || 100;
			
			quality = Math.max(Math.min(quality, 100), 10);
			// divide by 100 to give value between 0.1 and 1
			quality = quality / 100;
			
			// reload the data
			this.load();
			
			try {
				return this.canvas.toDataURL(mime, quality);	
			} catch(e) {
				return this.canvas.toDataURL(mime);
			}		
		},

		remove : function() {
			$(this.canvas).remove();
			this.destroy();
		}
	});

})(jQuery);