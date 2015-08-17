/* Copyright (c) 2009 Brandon Aaron (http://brandonaaron.net)
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 * Thanks to: http://adomas.org/javascript-mouse-wheel/ for some pointers.
 * Thanks to: Mathias Bank(http://www.mathias-bank.de) for a scope bug fix.
 *
 * Version: 3.0.2
 * 
 * Requires: 1.2.2+
 */
(function(c){var a=["DOMMouseScroll","mousewheel"];c.event.special.mousewheel={setup:function(){if(this.addEventListener){for(var d=a.length;d;){this.addEventListener(a[--d],b,false)}}else{this.onmousewheel=b}},teardown:function(){if(this.removeEventListener){for(var d=a.length;d;){this.removeEventListener(a[--d],b,false)}}else{this.onmousewheel=null}}};c.fn.extend({mousewheel:function(d){return d?this.bind("mousewheel",d):this.trigger("mousewheel")},unmousewheel:function(d){return this.unbind("mousewheel",d)}});function b(f){var d=[].slice.call(arguments,1),g=0,e=true;f=c.event.fix(f||window.event);f.type="mousewheel";if(f.wheelDelta){g=f.wheelDelta/120}if(f.detail){g=-f.detail/3}d.unshift(f,g);return c.event.handle.apply(this,d)}})(jQuery);
/* Copyright (c) 2011 JulianSong
 * Version: 1.0.0
 * 
 * Requires: 1.2.2+
 */
(function($){
	$.fn.slip=function(options,ff,lf){
		  var defaults ={      
			   counter :1,
			   distance:0,
			   duration:300,
			   length:0,
			   horizontal:true,
			   stop_slip:false 
           }; 
		   var ffun=null;
		   if(typeof(ff)!="undefined"){
				ffun=ff;
			}
		   var lfun=null;
		   if(typeof(lf)!="undefined"){
				lfun=lf;
		   }
		   var opts = $.extend({},defaults, options);    
		   this.css("position","absolute");
			if(opts.horizontal){
				this.css("width",opts.distance *(opts.length+1));
			}else{
				this.css("height",opts.distance *(opts.length+1));
			}
		  $.fn.slipLeft=function(duration,d){
			if(!setCounter(false)){
				return this;
			}
			if(typeof(d)==="undefined"){
				d=opts.distance;
			}
			this.animate({left : "+=" + d}, opts.duration);		
			return this;
		}
		$.fn.slipRight=function(duration,d){
			if(!setCounter(true)){
				return this;
			}
			if(typeof(d)==="undefined"){
				d=opts.distance;
			}
			this.animate({left : "-=" + d},duration);
			return this;
		}
		$.fn.slipTop=function(duration,d){
			if(!setCounter(false)){
				return this;
			}
			if(typeof(d)==="undefined"){
				d=opts.distance;
			}
			this.animate({top : "+=" + opts.distance}, duration);
			return this;
		}
		$.fn.slipBottom=function(duration,d){
			if(!setCounter(true)){
				return this;
			}
			if(typeof(d)==="undefined"){
				d=opts.distance;
			}
			this.animate({top : "-=" + opts.distance}, duration);
			return this;
		}
		$.fn.slipUseMouse=function(duration,fun){
			 this.mousewheel(
				function(event, delta){
					if (delta > 0) {
						if(opts.horizontal){
							$(this).slipLeft(duration);
						}else{
							$(this).slipTop(duration);
						}
					} else {
						if(opts.horizontal){
							$(this).slipRight(duration);
						}else{
							$(this).slipBottom(duration);
						}
					}
					fun(opts.counter);
					return $(this);
				} 
			);
		}
		$.fn.slipUseKeyboard=function(duration,fun){
			var sliper=$(this)
			$(window).keydown(
				function(event){
					if(opts.horizontal){
						if(event.keyCode==38||event.keyCode==40)
						return sliper;
					}else{
						if(event.keyCode==37||event.keyCode==39)
						return sliper;
					}
					switch(event.keyCode) {
						case 37:
							sliper.slipLeft(duration);
							break;
						case 38:
							sliper.slipTop(duration);
							break;
						case 39:
							sliper.slipRight(duration);
							break;
						case 40:
							sliper.slipBottom(duration);
							break;
					}
					fun(opts.counter);
					return sliper;
				}
			);
		}
		$.fn.printCount=function(c,p){
			 if(typeof(p)!="undefined"){
				opts.counter=p;
			 }
			 $(c).html(opts.counter);
			 return this;
		}
		$.fn.slipEnable=function(){
			opts.stop_slip=false;
		}
		$.fn.slipUnable=function(){
			opts.stop_slip=true;
		}
		$.fn.slipTo=function(c,duration){
			if(c<1||c>opts.length){
				return this;
			}
			if(c<opts.counter){
			   var interval = opts.counter - c;
			   if(opts.horizontal){
					this.animate({left : "+=" + (opts.distance * interval)}, duration);
			   }else{
					this.animate({top : "+=" + (opts.distance * interval)}, duration);   
			   }
			}else{
			   var interval = c - opts.counter;
			   if(opts.horizontal){
					this.animate({left : "-=" + (opts.distance * interval)}, duration);
			   }else{
					this.animate({top : "-=" + (opts.distance * interval)}, duration);   
			   }
			}
			opts.counter=c;
			return this;
		}
		setCounter=function(add){
			if(opts.stop_slip){
				return false;
			}
			if(add){
				if(opts.counter+1>opts.length){
				   if(lfun!=null){
					   lfun(opts.counter);
				   }
				   return false;	
				}else{
				  opts.counter++;
				}
			}else{
				if(opts.counter-1<1){
				   if(ffun!=null){
					   ffun(opts.counter);
				   }
				   return false;	
				}else{
				  opts.counter--;
				}
			}
			return true;
		}
		$.fn.counter=function(c){
			 if(typeof(c)=="number"){
				opts.counter=c;
				return this;
			 }else{
				return opts.counter;
			 }
		}
		$.fn.clength=function(l){
			 if(typeof(l)=="number"){
				opts.length=l;
				if(opts.horizontal){
					this.css("width",opts.distance *(opts.length+1));
				}else{
					this.css("height",opts.distance *(opts.length+1));
				}
				return this;
			 }else{
				return opts.length;
			 }
		}
	}
})(jQuery);