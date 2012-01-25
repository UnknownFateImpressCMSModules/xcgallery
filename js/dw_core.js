/*
		dw_core.js
		version date: April 2003
				
		This code is from Dynamic Web Coding 
    at http://www.dyn-web.com/
    Copyright 2001-3 by Sharon Paine 
    See Terms of Use at http://www.dyn-web.com/bus/terms.html
    Permission granted to use this code 
    as long as this entire notice is included.
*/

/////////////////////////////////////////////////////////////////////
//  dynObj constructor
//		arguments: id (required): id of positioned div.
//		left,top,width,height optional arguments.
/////////////////////////////////////////////////////////////////////
function dynObj(id,x,y,w,h) {
	this.el = (document.getElementById)? document.getElementById(id): (document.all)? document.all[id]: (document.layers)? getLyrRef(id,document): null;
	if (!this.el) return null;
	this.doc = (document.layers)? this.el.document: this.el;
	this.css = (this.el.style)? this.el.style: this.el;
	var px = (document.layers||window.opera)? "": "px";
	this.x = x || 0;	if (x) this.css.left = this.x+px;
	this.y = y || 0;	if (y) this.css.top = this.y+px;
	this.width = w? w: (this.el.offsetWidth)? this.el.offsetWidth: (this.css.clip.width)? this.css.clip.width: 0;
	this.height = h? h: (this.el.offsetHeight)? this.el.offsetHeight: (this.css.clip.height)? this.css.clip.height: 0;
	// if w/h passed, set style width/height
	if (w){ (document.layers)? this.css.clip.width=w+px: this.css.width=w+px;}
	if (h){ (document.layers)? this.css.clip.height=h+px: this.css.height=h+px;}
	this.obj = id + "_dynObj"; 	eval(this.obj + "=this");
}

dynObj.prototype.show = function () { this.css.visibility = "visible"; }
dynObj.prototype.hide = function () { this.css.visibility = "hidden"; }

dynObj.prototype.shiftTo = function (x,y) {
	if (x!=null) this.x=x; if (y!=null) this.y=y;	
	// rounded below (this.x/y can hold decimals)
	if (this.css.moveTo) { 
		this.css.moveTo(Math.round(this.x),Math.round(this.y)); 
	} else { 
		this.css.left=Math.round(this.x)+"px"; 
		this.css.top=Math.round(this.y)+"px"; 
	}
}

dynObj.prototype.shiftBy = function (x,y) {
	this.shiftTo(this.x+x,this.y+y);
}

dynObj.prototype.writeLyr = function (cntnt) {
	if (typeof this.doc.innerHTML!="undefined") {
      this.doc.innerHTML = cntnt;
  } else if (document.layers) {
			this.doc.write(cntnt);
			this.doc.close();
  }
}

dynObj.prototype.setBgClr = function (bg) {
	if (document.layers) this.doc.bgColor=bg;
	else this.css.backgroundColor=bg;
}

// get reference to nested layer for ns4
// from dhtmllib.js by Mike Hall of www.brainjar.com
function getLyrRef(lyr,doc) {
	if (document.layers) {
		var theLyr;
		for (var i=0; i<doc.layers.length; i++) {
	  	theLyr = doc.layers[i];
			if (theLyr.name == lyr) return theLyr;
			else if (theLyr.document.layers.length > 0) 
	    	if ((theLyr = getLyrRef(lyr,theLyr.document)) != null)
					return theLyr;
	  }
		return null;
  }
}

