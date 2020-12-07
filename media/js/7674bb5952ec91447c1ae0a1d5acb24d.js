// Universal transparent-PNG enabler for MSIE/Win 5.5+
// http://dsandler.org
// From original code: http://www.youngpup.net/?request=/snippets/sleight.xml
// and background-image code: http://www.allinthehead.com/retro/69
// also:
//  * use sizingMethod=crop to avoid scaling PNGs (who would do such a thing?)
//  * only do this once, to make it compatible with CSS rollovers

if (navigator.platform == "Win32" && navigator.appName == "Microsoft Internet Explorer" && window.attachEvent) {
	window.attachEvent("onload", enableAlphaImages);
}

function enableAlphaImages(){
	var rslt = navigator.appVersion.match(/MSIE (\d+\.\d+)/, '');
	var itsAllGood = (rslt != null && Number(rslt[1]) >= 5.5);
	if (itsAllGood) {
		for (var i=0; i<document.all.length; i++){
			var obj = document.all[i];
			var bg = obj.currentStyle.backgroundImage;
			var img = document.images[i];
			if (bg && bg.match(/\.png/i) != null) {
				var img = bg.substring(5,bg.length-2);
				var offset = obj.style["background-position"];
				obj.style.filter =
				"progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"+img+"', sizingMethod='crop')";
				obj.style.backgroundImage = "url('"+BLANK_IMG+"')";
				obj.style["background-position"] = offset; // reapply
			} else if (img && img.src.match(/\.png$/i) != null) {
				var src = img.src;
				img.style.width = img.width + "px";
				img.style.height = img.height + "px";
				img.style.filter =
				"progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"+src+"', sizingMethod='crop')"
				img.src = BLANK_IMG;
			}
		}
	}
}
Effect.ResizeWindow = Class.create();
Object.extend(Object.extend(Effect.ResizeWindow.prototype, Effect.Base.prototype), {
  initialize: function(win, top, left, width, height) {
    this.window = win;
    this.window.resizing = true;
    
    var size = win.getSize();
    this.initWidth    = parseFloat(size.width);
    this.initHeight   = parseFloat(size.height);

    var location = win.getLocation();
    this.initTop    = parseFloat(location.top);
    this.initLeft   = parseFloat(location.left);

    this.width    = width != null  ? parseFloat(width)  : this.initWidth;
    this.height   = height != null ? parseFloat(height) : this.initHeight;
    this.top      = top != null    ? parseFloat(top)    : this.initTop;
    this.left     = left != null   ? parseFloat(left)   : this.initLeft;

    this.dx     = this.left   - this.initLeft;
    this.dy     = this.top    - this.initTop;
    this.dw     = this.width  - this.initWidth;
    this.dh     = this.height - this.initHeight;
    
    this.r2      = $(this.window.getId() + "_row2");
    this.content = $(this.window.getId() + "_content");
        
    this.contentOverflow = this.content.getStyle("overflow") || "auto";
    this.content.setStyle({overflow: "hidden"});
    
    // Wired mode
    if (this.window.options.wiredDrag) {
      this.window.currentDrag = win._createWiredElement();
      this.window.currentDrag.show();
      this.window.element.hide();
    }

    this.start(arguments[5]);
  },
  
  update: function(position) {
    var width  = Math.floor(this.initWidth  + this.dw * position);
    var height = Math.floor(this.initHeight + this.dh * position);
    var top    = Math.floor(this.initTop    + this.dy * position);
    var left   = Math.floor(this.initLeft   + this.dx * position);

    if (window.ie) {
      if (Math.floor(height) == 0)  
        this.r2.hide();
      else if (Math.floor(height) >1)  
        this.r2.show();
    }      
    this.r2.setStyle({height: height});
    this.window.setSize(width, height);
    this.window.setLocation(top, left);
  },
  
  finish: function(position) {
    // Wired mode
    if (this.window.options.wiredDrag) {
      this.window._hideWiredElement();
      this.window.element.show();
    }

    this.window.setSize(this.width, this.height);
    this.window.setLocation(this.top, this.left);
    this.r2.setStyle({height: null});
    
    this.content.setStyle({overflow: this.contentOverflow});
      
    this.window.resizing = false;
  }
});

Effect.ModalSlideDown = function(element) {
  var windowScroll = WindowUtilities.getWindowScroll();    
  var height = element.getStyle("height");  
  element.setStyle({top: - (parseFloat(height) - windowScroll.top) + "px"});
  
  element.show();
  return new Effect.Move(element, Object.extend({ x: 0, y: parseFloat(height) }, arguments[1] || {}));
};


Effect.ModalSlideUp = function(element) {
  var height = element.getStyle("height");
  return new Effect.Move(element, Object.extend({ x: 0, y: -parseFloat(height) }, arguments[1] || {}));
};

PopupEffect = Class.create();
PopupEffect.prototype = {    
  initialize: function(htmlElement) {
    this.html = $(htmlElement);      
    this.options = Object.extend({className: "popup_effect", duration: 0.4}, arguments[1] || {});
    
  },
  show: function(element, options) { 
    var position = Position.cumulativeOffset(this.html);      
    var size = this.html.getDimensions();
    var bounds = element.win.getBounds();
    this.window =  element.win;      
    // Create a div
    if (!this.div) {
      this.div = document.createElement("div");
      this.div.className = this.options.className;
      this.div.style.height = size.height + "px";
      this.div.style.width  = size.width  + "px";
      this.div.style.top    = position[1] + "px";
      this.div.style.left   = position[0] + "px";   
      this.div.style.position = "absolute"
      document.body.appendChild(this.div);
    }                                                   
    if (this.options.fromOpacity)
      this.div.setStyle({opacity: this.options.fromOpacity})
    this.div.show();          
    var style = "top:" + bounds.top + ";left:" +bounds.left + ";width:" + bounds.width +";height:" + bounds.height;
    if (this.options.toOpacity)
      style += ";opacity:" + this.options.toOpacity;
    
    new Effect.Morph(this.div ,{style: style, duration: this.options.duration, afterFinish: this._showWindow.bind(this)});    
  },

  hide: function(element, options) {     
    var position = Position.cumulativeOffset(this.html);      
    var size = this.html.getDimensions();    
    this.window.visible = true; 
    var bounds = this.window.getBounds();
    this.window.visible = false; 

    this.window.element.hide();

    this.div.style.height = bounds.height;
    this.div.style.width  = bounds.width;
    this.div.style.top    = bounds.top;
    this.div.style.left   = bounds.left;
    
    if (this.options.toOpacity)
      this.div.setStyle({opacity: this.options.toOpacity})

    this.div.show();                                 
    var style = "top:" + position[1] + "px;left:" + position[0] + "px;width:" + size.width +"px;height:" + size.height + "px";

    if (this.options.fromOpacity)
      style += ";opacity:" + this.options.fromOpacity;
    new Effect.Morph(this.div ,{style: style, duration: this.options.duration, afterFinish: this._hideDiv.bind(this)});    
  },
  
  _showWindow: function() {
    this.div.hide();
    this.window.element.show(); 
  },
  
  _hideDiv: function() {
    this.div.hide();
  }
}


/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    design
 * @package     base_default
 * @copyright   Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

ieHover = function() {
    var items, iframe;
    items = $$('#nav ul', '#nav div', '.truncated_full_value .item-options', '.tool-tip');
    $$('#checkout-step-payment', '.tool-tip').each(function(el) {
        el.show();
        el.setStyle({'visibility':'hidden'})
    })
    for (var j=0; j<items.length; j++) {
        iframe = document.createElement('IFRAME');
        iframe.src = BLANK_URL;
        iframe.scrolling = 'no';
        iframe.frameBorder = 0;
        iframe.className = 'hover-fix';
        iframe.style.width = items[j].offsetWidth+"px";
        iframe.style.height = items[j].offsetHeight+"px";
        items[j].insertBefore(iframe, items[j].firstChild);
    }
    $$('.tool-tip', '#checkout-step-payment').each(function(el) {
        el.hide();
        el.setStyle({'visibility':'visible'})
    })
}
Event.observe(window, 'load', ieHover);

