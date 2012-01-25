/*
  dw_scroller.js
  Pausing Vertical Scroller v 2.0
  version date April 2003
  requires dw_core.js
    
  This code is from Dynamic Web Coding 
  at http://www.dyn-web.com/
  Copyright 2001-3 by Sharon Paine 
  See Terms of Use at http://www.dyn-web.com/bus/terms.html
  Permission granted to use this code 
  as long as this entire notice is included.
*/

scrollerObj.ar = new Array();

// left, top, width, height, alignment of content, id of container (if rel-pos)
function scrollerObj(x,y,w,h,al,hld) {
  this.x=x||0; this.y=y||0; this.w=w; this.h=h; 
  this.al = al || "left"; this.hld = hld;
  this.ctr=1; this.items=new Array(); this.timerId=0;
  
  // defaults
	this.pause 	= 4000;	// how long to pause on messages
	this.spd 		= 55;		// frequency of calls to scroll
	this.inc 		= 3;		// how much to scroll per call
	this.fontFam = "verdana, helvetica, arial, sans-serif";
	this.fontSz 	= "12px";
	this.fontClr  = "#000000";
}

function addScrollerItem(txt,url) {
	this.items[this.items.length] = new Array(txt,url);
}

function setScrollerFont(fam,sz,clr) {
	this.fontFam=fam; this.fontSz=sz; this.fontClr=clr;
}

function setScrollerTiming(pause,spd,inc) {
	this.pause=pause; this.spd=spd; this.inc=inc;
}

function createScroller() {
  if (this.created) return;
  // rewrite 1st item to last
	this.items[this.items.length] = this.items[0];
	scrollerObj.ar[scrollerObj.ar.length] = this;	// add it to global list of scrollers
  this.obj = "scrollerObj"+scrollerObj.ar.length; eval(this.obj + "=this"); 
	// separate functions for assembling content
	if (document.layers) this.buildN4();
	else this.build();
  this.created = true;
}

function buildScroller() {
  var elem, str;
  // scrWndo
  if (document.getElementById && document.createElement) {
    elem = document.createElement("div");
    // if rel-pos
    if (typeof this.hld != "undefined") {
      this.holder = document.getElementById(this.hld);
      this.scrWndo = this.holder.appendChild(elem);
    } else {
		  this.scrWndo = document.body.appendChild(elem);
    }
    this.scrWndo.id = "scrWndo"+scrollerObj.ar.length;
		this.scrWndo.style.position = "absolute";
    this.scrWndo.style.visibility = "hidden";
  } else if (document.all) {
    str = '<div id="scrWndo'+scrollerObj.ar.length+'" style="position:absolute; visibility:hidden; "></div>';
		// if rel-pos
    if (typeof this.hld != "undefined") {
      this.holder = document.all[this.hld];
      if (typeof this.holder.innerHTML != "undefined")
        this.holder.innerHTML = str;
    } else {
      document.body.insertAdjacentHTML("beforeEnd",str);
    }
  }
  this.scrWndo = new dynObj("scrWndo"+scrollerObj.ar.length,this.x,this.y,this.w,this.h);
  with (this.scrWndo.css) {
  	clip = "rect(0px, "+this.w+"px, "+this.h+"px, 0px)";
		overflow = "hidden"; zIndex=1000;	
	} 
  // set up scrolling content layer (scrCont)
  if (document.getElementById && document.createElement) {
    elem = document.createElement("div");
    this.scrCont = this.scrWndo.el.appendChild(elem);
    this.scrCont.id = "scrCont" + scrollerObj.ar.length;
    this.scrCont.style.position = "absolute";
  } else if (document.all) {
    var str = '<div id="scrCont'+scrollerObj.ar.length+'" style="position:absolute"></div>';
    this.scrWndo.writeLyr(str);
  }
  this.scrCont = new dynObj("scrCont"+scrollerObj.ar.length);
	this.scrCont.css.visibility = "inherit"; 
  this.scrCont.css.zIndex=1;
  this.scrCont.shiftTo(0,0);
  this.wrapItems();
  this.scrWndo.show();
  this.timerId = setTimeout(this.obj + ".controlScroll()",this.pause);
}

// assemble and write scroller content html 
function wrapItems() {
   var itemStart, itemEnd, str = "";
  // to format items centered vertically and horizontally
  if (this.al == "center") {
    itemStart = '<table width="' + this.w + '" cellpadding="0" cellspacing="0" border="0"><tr><td style="text-align:center; height:' + this.h + 'px" valign="middle">';
    itemEnd = '</td></tr></table>';
  // to format left aligned content
  } else if (this.al == "left") {
    itemStart = '<div style="height:' + this.h + 'px">';
    itemEnd = '</div>';
  }
  for (var i=0; i<this.items.length; i++) {
    if (typeof this.items[i][1] != "undefined") { // if item has url
      str += itemStart + '<a style="text-decoration:none; font-family:'+this.fontFam+'; font-size:'+this.fontSz+'; color:'+this.fontClr+'" href="'+ this.items[i][1] + '">' + this.items[i][0] + '</a>' + itemEnd;
    } else {  // no url, wrap in span with styles attached
      str += itemStart + '<div style="font-family:'+this.fontFam+'; font-size:'+this.fontSz+'; color:'+this.fontClr+'">' + this.items[i][0] + '</div>' + itemEnd;
    }
  }
  this.scrCont.writeLyr(str);
}

function buildScrollerNS4() {
  // create wndo layer  
  if (typeof this.hld != "undefined") {
    this.holder = getLyrRef(this.hld,document);
    this.scrWndo = new Layer(this.w,this.holder);
  }
	else this.scrWndo = new Layer(this.w);
	this.scrWndo.resizeTo(this.w,this.h);
	this.scrWndo.moveTo(this.x,this.y);

	// create the scrolling content layer 
	var str = '<div id="scrCont'+scrollerObj.ar.length+'" style="position:absolute"></div>';
	this.scrWndo.document.write(str);
	this.scrWndo.document.close();
	this.scrCont = new dynObj("scrCont"+scrollerObj.ar.length);
	this.scrCont.css.visibility = "inherit"; 
  this.scrCont.css.zIndex=1;
  this.scrCont.shiftTo(0,0);
  this.wrapNS4Items();
	this.scrWndo.visibility = "show";
  this.timerId = setTimeout(this.obj + ".controlScroll()",this.pause);
}

function wrapNS4Items() {
	var itemStart, itemEnd, str = "";
	if (this.al=="center") this.v = "middle";
	else this.v = "top";
  itemStart = '<table width="' + this.w + '" cellpadding="0" cellspacing="0" border="0"><tr><td align="'+this.al+'" height="' + this.h + '" valign="'+this.v+'">';
  itemEnd = '</td></tr></table>';
  for (var i=0; i<this.items.length; i++) {
    if (typeof this.items[i][1] != "undefined") { // if item has url
      str += itemStart + '<a style="text-decoration:none; font-family:'+this.fontFam+'; font-size:'+this.fontSz+'; color:'+this.fontClr+'" href="'+ this.items[i][1] + '">' + this.items[i][0] + '</a>' + itemEnd;
    } else {  // no url, wrap in span with styles attached
      str += itemStart + '<div style="font-family:'+this.fontFam+'; font-size:'+this.fontSz+'; color:'+this.fontClr+'">' + this.items[i][0] + '</div>' + itemEnd;
    }
  }
  this.scrCont.writeLyr(str);
}

function controlScroll() {
	if (this.ctr>this.items.length-1) this.startOver();
	else {
		var y = parseInt(this.scrCont.css.top);
		if (y> -this.h * this.ctr) { 
			this.scrCont.shiftBy(0,-this.inc);	
			this.timerId = setTimeout(this.obj+".controlScroll()",this.spd);	
		} else {
				this.ctr++;
				this.timerId = setTimeout(this.obj+".controlScroll()",this.pause);	
		}
	}
}

// restore scroller top to 0 and counter variable to 1
function startOver() {
	this.ctr = 1;
	this.scrCont.shiftTo(0,0);
	this.controlScroll();	
}

scrollerObj.prototype.addItem = addScrollerItem;
scrollerObj.prototype.create = createScroller;
scrollerObj.prototype.setFont = setScrollerFont; 
scrollerObj.prototype.setTiming = setScrollerTiming;
scrollerObj.prototype.buildN4 = buildScrollerNS4; 
scrollerObj.prototype.build = buildScroller;
scrollerObj.prototype.wrapItems = wrapItems;
scrollerObj.prototype.wrapNS4Items = wrapNS4Items;
scrollerObj.prototype.controlScroll = controlScroll;
scrollerObj.prototype.startOver = startOver;

// remove layers from table for ns6+/mozilla (overflow/clip bug?)
function positionGecko() {
	if (navigator.userAgent.indexOf("Gecko")>-1) {
    for (var i=0; i<scrollerObj.ar.length; i++) {
			if (scrollerObj.ar[i].holder) {
				scrollerObj.ar[i].holder.removeChild(scrollerObj.ar[i].scrWndo.el);
				document.body.appendChild(scrollerObj.ar[i].scrWndo.el);
				scrollerObj.ar[i].scrWndo.css.zIndex = 1000;
        var x = scrollerObj.ar[i].holder.offsetLeft + scrollerObj.ar[i].x;
				var y = scrollerObj.ar[i].holder.offsetTop + scrollerObj.ar[i].y;
				scrollerObj.ar[i].scrWndo.shiftTo(x,y);
			}
  	}
  }
}

// ns6+/mozilla need to reposition layers onresize
function rePosGecko() {
  for (var i=0; i<scrollerObj.ar.length; i++) {
		var x = scrollerObj.ar[i].holder.offsetLeft + scrollerObj.ar[i].x;
		var y = scrollerObj.ar[i].holder.offsetTop + scrollerObj.ar[i].y;
		scrollerObj.ar[i].scrWndo.shiftTo(x,y);
  }
}

function setMouseEvents() {
  for (var i=0; i<scrollerObj.ar.length; i++) {
    scrollerObj.ar[i].scrCont.el.onmouseover = new Function("haltScroll("+i+")")
    scrollerObj.ar[i].scrCont.el.onmouseout = restartScroll;
  }
}

function haltScroll(num) {
  clearTimeout(scrollerObj.ar[num].timerId);
}

function restartScroll(e) {
  // get numeric portion of id (after scrCont)
  var num = parseInt(this.id.slice(7)) - 1;
  e = e? e: window.event? window.event: "";
  if (e) {
    var current = this;
    var related =  e.relatedTarget? e.relatedTarget: e.toElement? e.toElement: "";
  		if (related) {
        if (current != related && !contains(current, related)) 
          scrollerObj.ar[num].controlScroll();
      } else scrollerObj.ar[num].controlScroll(); // ns4
  }
}

// adapted from http://www.brainjar.com/dhtml/events/default6.asp
function contains(a, b) {
	// extra checks in case alt-tab away while over menu
	if (b && b.parentNode) {	
	  // Return true if node a contains node b.
	  while (b.parentNode)
	    if ((b = b.parentNode) == a)
	      return true;
	  return false;
	} else if (b && b.parentElement) {
		while (b.parentElement)
		    if ((b = b.parentElement) == a)
		      return true;
		  return false;
	}
}