//<pre>
// Script to embed interactive maps into pages that have coordinate templates
// also check my commons page [[:commons:User:Dschwen]] for more tools

//
// Constructor for WikiMiniAtlas
//
//  creates an interactive Map inside the DIV given by obj
//  obj must have the following attributes:
//   wma_lat, wma_lon, wma_site
//  optional attributes:
//   wma_language, wma_zoom
// 
function WikiMiniAtlas( obj )
{
 // defaults
 this.coordinate_region = '';
 this.width = 500;
 this.height = 300;

 // globals
 this.widget = null;
 this.map = null;
 this.nx = 0;
 this.ny = 0;
 this.tile = 0;
 this.old_onmouseup = null;
 this.old_onmousemove;

 this.date = new Date();
 this.lastwheel = 0;
 this.wheeldelay = 200;

 this.dragging = null;
 this.gx = 0;
 this.gy = 0;
 this.zoom = 1;
 this.marker = { lat: 0.0, lon: 0.0, obj: null, locked: true };
 this.taget_button = null;
 this.settings = null;
 this.xmlhttp = false;
 this.xmlhttp_callback = false;
 this.lang = '';
 this.site = '';

 this.circ_eq = 40075.0; // equatorial circumfence in km
 this.scalelabel = null;
 this.scalebar = null;

 this.tileset = 0;

 //
 // Insert the map Widget into the page.
 //
 // do this when constructing the object 
 // the obj parameter should be a div 
 // which will be filled with a WMA
 // function wikiminiatlasInstall() {
 // jetzt obj
 //var coordinates = document.getElementById('wikiminiatlas_widget');

 if( obj != null && this.widget == null) {
  // vorher noch obj anpassen (padding weg)
  obj.style.padding = '0';

  this.marker.lat = parseFloat( obj.getAttribute( 'wma_lat' ) || '0' );
  this.marker.lon = parseFloat( obj.getAttribute( 'wma_lon' ) || '0' );
  this.zoom   = parseInt( obj.getAttribute( 'wma_zoom' ) || '1' );
  this.width  = obj.clientWidth;
  this.height = obj.clientHeight;
  this.site   = obj.getAttribute( 'wma_site' ) || 'en';
  this.lang   = obj.getAttribute( 'wma_lang' ) || this.site;

  this.widget = obj;

  var WikiMiniAtlasHTML;
  if( this.lang == 'co' || this.lang == 'commons' ) this.lang = 'en';

  // Fill missing i18n items
  for( var item in this.strings )
   if( !this.strings[item][this.lang] ) this.strings[item][this.lang] = this.strings[item]['en'];

  var button_plus = this.newElement( 'img', 
   { 
    src: this.imgbase + 'button_plus.png', 
    title: this.strings.zoomIn[this.lang], 
    style: { left: '10px', top: '10px' } 
   }, { mousedown: this.zoomIn } ); 

  var button_minus = this.newElement( 'img',
   {
    src: this.imgbase + 'button_minus.png',
    title: this.strings.zoomOut[this.lang],
    style: { left: '10px', top: '32px' }
   }, { mousedown: this.zoomOut } );

  this.taget_button = this.newElement( 'img',
   {
    src: this.imgbase + 'button_target_locked.png',
    title: this.strings.center[this.lang],
    style: { left: '10px', top: '54px' }
   }, { mousedown: this.moveToTarget } );

  var button_menu = this.newElement( 'img',
   {
    src: this.imgbase + 'button_menu.png',
    title: this.strings.settings[this.lang],
    style: { right: '40px', top: '8px', width: '18px', zIndex: '50'  }
   }, { click: this.toggleSettings } );



  var homepage_link = this.newElement( 'img',
   {
    href: 'http://meta.wikimedia.org/wiki/WikiMiniAtlas/' + this.lang,
    target: '_top',
    style: { zIndex: '11', position: 'absolute', right: '10px', bottom: '3px', color: 'black', fontSize: '5pt' }
   }, {} );
  homepage_link.appendChild( document.createTextNode( "WikiMiniAtlas" ) );

  var scalebox = document.createElement( 'div' );
  this.scalebar = document.createElement( 'div' );
  this.scalelabel = document.createElement( 'div' );
  this.scalelabel.appendChild( document.createTextNode( "null" ) );
  scalebox.appendChild( this.scalebar );
  scalebox.appendChild( this.scalelabel );

  WikiMiniAtlasHTML = '';

  //WikiMiniAtlasHTML += '<div id="wikiminiatlas_map" style="position:absolute; width:'+this.width+'px; height:'+this.height+'px; border: 1px solid gray; cursor: move; background-color: #aaaaaa; clip:rect(0px, '+this.width+'px, '+this.height+'px, 0px);"></div>';
  
  // Settings page
  WikiMiniAtlasHTML += 
   '<div id="wikiminiatlas_settings">' +
   '<h4>' + this.strings.settings[this.lang] + '</h4>' +
   '<p class="option">' + this.strings.mode[this.lang] + ' <select onchange="wmaSelectTileset(this.value)">';
 
  for( var i = 0; i < this.tilesets.length; i++ )
  {
   WikiMiniAtlasHTML +=
    '<option value="'+i+'">' + this.tilesets[i].name + '</option>';
  }

  WikiMiniAtlasHTML +=
   '</select></p>' +
   '<p class="option">' + this.strings.labelSet[this.lang] + ' <select onchange="wmaLabelSet(this.value)">'

  for( var i in this.sites )
  {
   WikiMiniAtlasHTML +=
    '<option value="'+i+'">' + this.sites[i] + '</option>';
  }

  WikiMiniAtlasHTML +=
   '</select></p>' +
   '<p class="option">' + this.strings.linkColor[this.lang] + ' <select onchange="wmaLinkColor(this.value)">' +
   '<option value="#2255aa">blue</option>' +
   '<option value="red">red</option>' +
   '<option value="white">white</option>' + 
   '<option value="black">black</option></select></p>' +
   //'<p class="option" style="font-size: 50%; color:gray">Debug info:<br>marker: ' + this.marker.lat + ', ' + this.marker.lon + '<br>site:' + this.site+', uilang' + this.lang + '</p>' +
   '<a href="http://tools.wikimedia.de/"><img src="/images/wikimedia-toolserver-button.png" border="0"></a>' +
   '</div>' +
   '</div>';

  this.widget.style.clip = 'rect(0px,' + this.width + 'px,' + this.height + 'px,0px)';
  this.widget.oncontextmenu = function() { return false; };
  this.widget.innerHTML = WikiMiniAtlasHTML ;

  this.widget.appendChild( button_minus );
  this.widget.appendChild( button_plus );
  this.widget.appendChild( button_menu );
  this.widget.appendChild( this.taget_button );
  this.widget.appendChild( homepage_link );

  old_onmouseup = document.onmouseup;
  old_onmousemove = document.onmousemove;

  this.initializeMap();
  this.moveToTarget();
 }
}

//
// Globals
//
WikiMiniAtlas.prototype.imgbase = 'http://tools.wikimedia.de/~dschwen/wikiminiatlas/tiles/';
WikiMiniAtlas.prototype.database = 'http://tools.wikimedia.de/~dschwen/wikiminiatlas/label/';


//
// Hook up instalation function
//
//addOnloadHook(wikiminiatlasInstall);

WikiMiniAtlas.prototype.toggle = function()
{
 if(widget.style.visibility != "visible")
   widget.style.visibility="visible";
 else
   widget.style.visibility="hidden";

 return false;
}

WikiMiniAtlas.prototype.toggleSettings = function()
{
 if(settings.style.visibility != "visible")
   settings.style.visibility="visible";
 else
   settings.style.visibility="hidden";

 return false;
}

// initializeWikiMiniAtlasMap()
WikiMiniAtlas.prototype.initializeMap = function()
{
 this.widget.onmousedown = this.mouseDownHandler;
 document.onmousemove = this.mouseMoveHandler;
 document.onmouseup = this.mouseUpHandler;

 if (window.addEventListener)
  /** DOMMouseScroll is for mozilla. */
  this.widget.addEventListener('DOMMouseScroll', this.wheelHandler, false);
 else
  /** IE/Opera. */
  this.widget.onmousewheel = this.wheelHandler;

 //a'dblclick', map
 this.widget.ondblclick = this.dblclickHandler;

 this.nx = Math.floor( this.width / 128 ) + 2;
 this.ny = Math.floor( this.height / 128 ) + 2;
console.log( 'nx, ny = ' + this.nx , ',' + this.ny );
 this.tile = new Array( this.nx * this.ny );

 var n = 0;
 var thistile;

 for(var j = 0; j < this.ny; j++)
  for(var i = 0; i < this.nx; i++)
  {
   this.widget.innerHTML += '<div id="wmatile'+n+'" style="position:absolute; width:128px; height:128px;"></div>';
   thistile = document.getElementById('wmatile'+n);
   thistile.onmousedown = this.mouseDownHandler;
   n++;
  }

 this.initializeXMLHTTP();
 this.initializeXMLHTTPCallBacks();
  
 //this.widget.innerHTML += '<div id="wmamarker" style="z-index:21; position:absolute; width:11px; height:11px; background-image:url(\''+wikiminiatlas_imgbase+'red_dot.png\'); background-repeat: no-repeat"></div>';

 this.marker.obj = this.newElement( 'div', 
  { style: { width: '11px', height: '11px', zIndex: '21', position: 'absolute', 
             backgroundImage: 'url(\'' + this.imgbase + 'red_dot.png\')', 
             backgroundRepeat: 'none' } 
  }, {} );

 this.widget.appendChild( this.marker.obj );
}

WikiMiniAtlas.prototype.setStyleZPWH = function( obj, z, p, w ,h )
{
 obj.style.zIndex = z;
 obj.style.position = p;
 obj.style.width = w;
 obj.style.height = h;
}

WikiMiniAtlas.prototype.setStyleButton = function( obj, image )
{
 obj.style.zIndex = 30;
 obj.style.position = 'absolute';
 obj.style.width = '18px';
 obj.style.cursor = 'pointer'; 
 obj.src = imgbase + image;
}

WikiMiniAtlas.prototype.newElement = function( tag, props, events )
{
 var el = document.createElement( tag );

 for( prop in props )
  if( typeof( props[prop] ) == 'object' )
   for( subprop in props[prop] ) 
   {
    //console.log('setting subprop ' + prop + '.' + subprop + '=' + props[prop][subprop] );
    el[prop][subprop] = props[prop][subprop];
    //console.log('done setting subprop');
    //el[prop].setProperty( subprop, props[prop][subprop] );
   }
  else 
  {
   //console.log( 'setting prop'+ prop + '=' + props[prop] );
   el[prop] = props[prop];
   //console.log('done setting prop');
  }
   //el.setProperty( prop, props[prop] );

 for( ev in events )
 {
  //console.log( 'adding event '+ ev + '=' + events[ev] );
  this.addEvent( el, ev, events[ev] );
  //console.log('done adding event');
 }

 return el;
}

//
// Set new map Position (to wikiminiatlas_gx, wikiminiatlas_gy)
//
//function moveWikiMiniAtlasMapTo()
WikiMiniAtlas.prototype.moveMapTo = function()
{
 if( this.gy < 0 ) this.gy = 0;
 if( this.gx < 0 ) this.gx += Math.floor( this.getZoomsize() * 256 );

 var lx = Math.floor( this.gx / 128 ) % this.nx;
 var ly = Math.floor( this.gy / 128 ) % this.ny;
 var fx = this.gx % 128;
 var fy = this.gy % 128;
 var n;
 var thistile;
 var tileurl;
 var dataurl;

 this.updateScalebar();
 //document.getElementById('debugbox').innerHTML='';

 for(var j = 0; j < this.ny; j++)
  for(var i = 0; i < this.nx; i++)
  {
   n = ( (i+lx) % this.nx ) + ( (j+ly) % this.ny ) * this.nx;

   thistile = document.getElementById('wmatile'+n);
   thistile.style.left = (i*128-fx) + 'px';
   thistile.style.top  = (j*128-fy) + 'px';

   //thistile.innerHTML = (Math.floor(wikiminiatlas_gx/128)+i)+','+(Math.floor(wikiminiatlas_gy/128)+j);
   tileurl = 'url("' + 
    this.tilesets[ this.tileset ].getTileURL( (Math.floor( this.gy/128)+j), (Math.floor(this.gx/128)+i), this.zoom ) + '")';
   dataurl = this.getDataURL( ( Math.floor( this.gy/128 ) + j ), ( Math.floor( this.gx/128 ) + i ), this.zoom );

   if( this.tile[n] != tileurl )
   {
    this.tile[n] = tileurl;
    thistile.style.backgroundImage = tileurl;

    if( this.xmlhttp[n] &&
     ( this.xmlhttp[n].readyState == 1 ||
       this.xmlhttp[n].readyState == 2 ||
       this.xmlhttp[n].readyState == 3 ) )
    {
     this.xmlhttp[n].abort();
    }

    this.xmlhttp[n].open( "GET", dataurl, true );
    thistile.innerHTML = 'loading';
    this.xmlhttp[n].onreadystatechange = this.xmlhttp_callback[n];
    this.xmlhttp[n].send( null );
   }

   var newcoords = this.latLonToXY( this.marker.lat, this.marker.lon );
   var newx = ( newcoords.x - this.gx );
   if( newx < -100 ) newx += ( this.getZoomsize() * 256 );
   this.marker.obj.style.left = ( newx - 6 ) + 'px';
   this.marker.obj.style.top  = ( newcoords.y - this.gy -6 ) + 'px';
  }

}

//
// Double-Click handler
//
//function dblclickWikiMiniAtlasMap(ev)
WikiMiniAtlas.prototype.dblclickMap = function( ev )
{
 ev = ev || window.event;
 var test = mouseCoords(ev);
 alert( test.x + ',' + test.y );
}

//
// Mouse down handler (start map-drag)
//
//function mouseDownWikiMiniAtlasMap(ev)
WikiMiniAtlas.prototype.mouseDownHandler = function( ev )
{
 ev = ev || window.event;
 this.dragging = wmaMouseCoords(ev);
}

//
// Mouse up handler (finish map-drag)
//
//function mouseUpWikiMiniAtlasMap()
WikiMiniAtlas.prototype.mouseUpHandler = function()
{
 this.dragging = null;
 if( old_onmouseup != null ) old_onmouseup();
}

// Mouse move handler
//function mouseMoveWikiMiniAtlasMap(ev)
WikiMiniAtlas.prototype.mouseMoveHandler = function( ev )
{
 //window.scrollTo(0,0);
 if( this.dragging != null )
 {
  var newev = ev || window.event;
  var newcoords = mouseCoords(newev);

  this.gx -= ( newcoords.x - this.dragging.x );
  this.gy -= ( newcoords.y - this.dragging.y );
  this.dragging = newcoords;

  moveMapTo();

  if( this.marker.locked )
  {
   this.marker.locked = false;
   this.updateTargetButton();
  }
 }

 if( old_onmousemove != null ) old_onmousemove( ev ); 
}

//function wheelWikiMiniAtlasMap(ev)
WikiMiniAtlas.prototype.wheelHandler = function( ev )
{
 var now = date.getTime();
 //if( now - lastwheel > 100 ) 

 var delta = 0;
 if (!ev) /* For IE. */
  var ev = window.event;
 if (ev.wheelDelta) { /* IE/Opera. */
  delta = ev.wheelDelta/120;
  if (window.opera)
   delta = -delta;
 } 
 else if (ev.detail) { /** Mozilla case. */
  delta = -ev.detail/3;
 }

 if( delta>0 ) zoomIn();
 if( delta<0 ) zoomOut();

 if (ev.preventDefault)
  ev.preventDefault();
 ev.returnValue = false;

 console.log('Wheel.');
}

//function wmaMouseCoords(ev)
WikiMiniAtlas.prototype.mouseCoords = function( ev )
{
 if(ev.pageX || ev.pageY)
 {
  return {x:ev.pageX, y:ev.pageY};
 }
 return {
  x:ev.clientX + document.body.scrollLeft - document.body.clientLeft,
  y:ev.clientY + document.body.scrollTop  - document.body.clientTop
 };
}

//function wmaGetDataURL(y,x,z)
WikiMiniAtlas.prototype.getDataURL = function( y, x, z )
{
 return this.database + this.site + '_' + ( this.getZoomsize() - y - 1 ) + '_' + ( x % ( this.getZoomsize() * 2 ) ) + '_' + z;
}

WikiMiniAtlas.prototype.tilesetUpgrade = function()
{
 for( var i = tileset+1; i < tilesets.length; i++ )
 {
  if( tilesets[i].maxzoom > (zoom+1) )
  {
   tileset = i;
   zoom++;
   return;
  }
 }
}

WikiMiniAtlas.prototype.tilesetDowngrade = function()
{
 for( var i = this.tileset-1; i >= 0; i-- )
 {
  if( this.tilesets[i].minzoom < ( this.zoom - 1 ) )
  {
   this.tileset = i;
   this.zoom--;
   return;
  }
 }
}

//function wmaZoomIn( ev )
WikiMiniAtlas.prototype.zoomIn = function( ev )
{
 var mapcenter = this.XYToLatLon( this.gx + this.width/2, this.gy + this.height/2 );
 var rightclick = false;

 if(!ev) var ev = window.event;
 if(ev) {
  if (ev.which) rightclick = (ev.which == 3);
  else if (ev.button) rightclick = (ev.button == 2);
 } 

 if( rightclick )
 {
  this.zoom = this.tilesets[this.tileset].maxzoom;
 }
 else
 {
  if( thia.zoom >= thia.tilesets[thia.tileset].maxzoom )
   thia.tilesetUpgrade();
  else 
   thia.zoom++;
 }

 var newcoords;

 if( this.marker.locked )
  newcoords = this.latLonToXY( this.marker.lat, this.marker.lon );
 else
  newcoords = this.latLonToXY( mapcenter.lat, mapcenter.lon );

 this.gx = newcoords.x - this.width/2;
 this.gy = newcoords.y - this.height/2;
 this.moveMapTo();

 return false;
}

//function wmaZoomOut( e )
WikiMiniAtlas.prototype.zoomOut = function( e )
{
 var mapcenter = this.XYToLatLon( this.gx + this.width/2, this.gy + this.height/2 );
 var rightclick = false;

 if(!ev) var ev = window.event;
 if(ev) {
  if (ev.which) rightclick = (ev.which == 3);
  else if (ev.button) rightclick = (ev.button == 2);
 }

 if( rightclick )
 {
  this.zoom = this.tilesets[tileset].minzoom;
 }
 else
 {
  if( thsi.zoom <= thsi.tilesets[thsi.tileset].minzoom ) 
   this.tilesetDowngrade();
  else 
   this.zoom--;
 }

 var newcoords = thia.latLonToXY( mapcenter.lat, mapcenter.lon );
 this.gx = newcoords.x - this.width/2;
 this.gy = newcoords.y - this.height/2;
 this.moveMapTo();

 return false;
}

//function wmaSelectTileset( n )
WikiMiniAtlas.prototype.selectTileset = function( n )
{
 var newz = this.zoom;

 if( newz > this.tilesets[n].maxzoom ) newz = this.tilesets[n].maxzoom;
 if( newz < this.tilesets[n].minzoom ) newz = this.tilesets[n].minzoom;
 
 this.tileset = n;

 if( this.zoom != newz ) {
  var mapcenter = this.XYToLatLon( this.gx + this.width/2, this.gy + this.height/2 );
  this.zoom = newz;
  var newcoords = this.latLonToXY( mapcenter.lat, mapcenter.lon );
  this.gx = newcoords.x - this.width/2;
  this.gy = newcoords.y - this.height/2;
 }
  
 this.moveWikiMiniAtlasMapTo();
 this.toggleSettings();
}

//function wmaLinkColor(c)
WikiMiniAtlas.prototype.linkColor = function( c )
{
 //alert(list.value);
 //TODO: document.styleSheets[0].cssRules[0].style.color = c;
 this.toggleSettings();
 return false;
}

//function wmaLabelSet(s)
WikiMiniAtlas.prototype.labelSet = function( s )
{
 //alert(list.value);
 this.site = s;
 for( var n = 0; n < nx * ny; n++) this.tile[n]='';
 this.moveMapTo();
 this.toggleSettings();
 return false;
}

//function wmaUpdateScalebar()
WikiMiniAtlas.prototype.updateScalebar = function()
{
 var sblocation = this.XYToLatLon( this.gx + this.width/2, this.gy + this.height/2 );
 var slen1 = 50, slen2;
 var skm1,skm2;
 //scalelabel.innerHTML = 
 skm1 = Math.cos( sblocation.lat * 0.0174532778) * this.circ_eq * slen1 / ( 256 * this.getZoomsize() );
 skm2 = Math.pow( 10, Math.floor( Math.log(skm1) / Math.log(10) ) );
 slen2 = slen1*skm2/skm1;
 if( 5*slen2 < slen1 ) { slen2=slen2*5; skm2=skm2*5; }
 if( 2*slen2 < slen1 ) { slen2=slen2*2; skm2=skm2*2; }
 this.scalelabel.innerHTML = skm2 + ' km';
 this.scalebar.style.width = slen2;
}

//function wmaUpdateTargetButton()
WikiMiniAtlas.prototype.updateTargetButton = function()
{
 if( this.marker.locked )
  this.taget_button.src = this.imgbase + 'button_target_locked.png';
 else
  this.taget_button.src = this.imgbase + 'button_target.png';
}

//function wmaMoveToTarget()
WikiMiniAtlas.prototype.moveToTarget = function()
{
 var newcoords = this.latLonToXY( this.marker.lat, this.marker.lon);
 this.gx = newcoords.x - this.width / 2;
 this.gy = newcoords.y - this.height / 2;
 this.moveMapTo();

 this.marker.locked = true;
 this.updateTargetButton();
}

//function wmaLatLonToXY(lat,lon)
WikiMiniAtlas.prototype.latLonToXY = function( lat, lon )
{
 var newx = Math.floor( ( lon / 360.0 ) * this.getZoomsize() * 256 );
 if( newx < 0 ) newx += this.getZoomsize() * 256;
 return { y: Math.floor( ( 0.5 - lat / 180.0 ) * this.getZoomsize() * 128 ), 
          x: newx };
}

//function wmaXYToLatLon(x,y)
WikiMiniAtlas.prototype.XYToLatLon = function( x , y )
{
 return { lat: 180.0 * ( 0.5 - y / ( this.getZoomsize() * 128 ) ), 
          lon: 360.0 * ( x / ( this.getZoomsize() * 256 ) ) };
}

// get the current zoomsize in number of tiles
WikiMiniAtlas.prototype.getZoomsize = function()
{
 return this.tilesets[this.tileset].zoomsize[ this.zoom ];
}

//
// Try to create an XMLHTTP request object for each tile
// with maximum browser compat.
// code adapted from http://jibbering.com/2002/4/httprequest.html
//
//function wmaInitializeXMLHTTP()
WikiMiniAtlas.prototype.initializeXMLHTTP = function()
{
 var i;
 var n_total = this.nx * this.ny;


 /*@cc_on @*/
 /*@if (@_jscript_version >= 5)
 // Internet Explorer (uses Conditional compilation)
 // traps security blocked creation of the objects.
  //wmaDebug('Microsoft section');
  try {
   this.xmlhttp = new Array(n_total);
   for(i=0; i< n_total; i++) this.xmlhttp[i] = new ActiveXObject("Msxml2.XMLHTTP");
   wmaDebug('* Msxml2.XMLHTTP success');
  } catch (e) {
   try {
    for(i=0; i< n_total; i++) this.xmlhttp[i] = new ActiveXObject("Microsoft.XMLHTTP");
    wmaDebug('* Microsoft.XMLHTTP success');
   } catch (E) {
    this.xmlhttp = false;
   }
  }
 @end @*/

 // Firefox, Konqueror, Safari, Mozilla
 wmaDebug('Firefox/Konqueror section');
 if (!this.xmlhttp && typeof XMLHttpRequest!='undefined') {
  try {
   this.xmlhttp = new Array(n_total);
   for(i=0; i< n_total; i++) this.xmlhttp[i] = new XMLHttpRequest();
   wmaDebug('* XMLHttpRequest success');
  } catch (e) {
   this.xmlhttp=false;
  }
 }

 // ICE browser
 wmaDebug('ICE section');
 if (!this.xmlhttp && window.createRequest) {
  try {
   this.xmlhttp = new Array(n_total);
   for(i=0; i< n_total; i++) this.xmlhttp[i] = new window.createRequest();
   wmaDebug('* window.createRequest success');
  } catch (e) {
   this.xmlhttp=false;
  }
 }
}

//
// Every tile needs a callback function for its xmlhttprequest
// Build them all
//
//function wmaInitializeXMLHTTPCallBacks()
WikiMiniAtlas.prototype.initializeXMLHTTPCallBacks = function()
{
 var i;
 var n_total = this.nx * this.ny;
 this.xmlhttp_callback = new Array(n_total);
 for(i=0; i< n_total; i++)
  this.xmlhttp_callback[i] = new Function("if (this.xmlhttp["+i+"].readyState==4) { document.getElementById('wmatile"+i+"').innerHTML = wikiminiatlas_xmlhttp["+i+"].responseText; }");
}

 // cross-browser event attachment (John Resig)
 // http://www.quirksmode.org/blog/archives/2005/10/_and_the_winner_1.html
WikiMiniAtlas.prototype.addEvent = function ( obj, type, fn )
{
 if (obj.addEventListener)
  obj.addEventListener( type, fn, false );
 else if (obj.attachEvent)
 {
  obj["e"+type+fn] = fn;
  obj[type+fn] = function() { obj["e"+type+fn]( window.event ); }
  obj.attachEvent( "on"+type, obj[type+fn] );
 }
}

function wmaDebug(text)
{
 //document.getElementById('debugbox').innerHTML+=text+'<br />';
}


//
// Long data prototypes
//

WikiMiniAtlas.prototype.strings =
{
 mapCoastline : {
  bg:'Брегова линия',
  ca:'Línia de costa',
  da:'Kystlinje',
  de:'Landmassen',
  en:'Coastline',
  eu:'Kostaldea',
  es:'Costa',
  fr:'Littoral',
  gl:'Litoral',
  he:'קו החוף',
  hu:'partvonal',
  id:'Garis pantai',
  is:'Strandlína',
  it:'Costa',
  ja:'地図',
  lt:'Kranto linija',
  nl:'Kustlijn',
  no:'Kystlinje',
  pl:'Zarys wybrzeży',
  pt:'Litoral',
  ru:'Векторная',
  sk:'Pobrežie',
  sl:'Obris obale',
  fi:'Rantaviiva',
  sv:'Kustlinje',
   zh:'地图',
   'zh-cn':'地图',
   'zh-sg':'地图',
   'zh-tw':'地圖',
   'zh-hk':'地圖',
   ar:'شريط ساحلي'
  },
  mapLandsat : {
   bg:'Спътникова снимка',
   ca:'Imatge per satèl·lit',
   da:'Satellitfoto',
   de:'Satellitenbild',
   en:'Landsat',
   eu:'Landsat',
   es:'Landsat',
   fr:'Landsat',
   gl:'Imaxe por satélite',
   he:'תמונת לווין',
   hu:'Landsat',
   id:'Landsat',
   is:'Gervihnattarmynd',
   it:'Immagine satellitare',
   ja:'衛星写真',
   lt:'Landsat',
   nl:'Landsat',
   no:'Satellittbilde',
   pl:'Landsat',
   pt:'Fotografia por satélite',
   ru:'Спутниковая фотография',
   sk:'Satelitná snímka',
   sl:'Satelitska slika',
   fi:'Satelliittikuva',
   sv:'Satellitfoto',
   zh:'卫星照片',
   'zh-cn':'卫星照片',
   'zh-sg':'卫星照片',
   'zh-tw':'衛星照片',
   'zh-hk':'衛星照片',
   ar:'صورة بالساتل'
  },
  center : {
   bg:'Центриране на обекта',
   ca:'Centra en la localització',
   da:'Centrér',
   de:'Auf Ziel zentrieren',
   en:'Center on target',
   eu:'Helburuan zentratu',
   es:'Centrarse en destino',
   fr:'Centrer sur la cible',
   gl:'Centrarse na localización',
   he:'מרכז על המטרה',
   hu:'A kijelölés középre',
   id:'Ketengahkan sasaran',
   is:'Miðja við staðsetninguna',
   it:'Centro sul bersaglio',
   ja:'最初の地点へ移動',
   lt:'centruoti objektą',
   nl:'Centreren op doel',
   no:'Sentrer på målet',
   pl:'Centruj na lokalizacji',
   pt:'Centrar-se no destino',
   ru:'Центрировать объект',
   sk:'Cieľ do stredu',
   sl:'Osredotoči cilj',
   fi:'Keskitä kohde',
   sv:'Centrera på målet',
   zh:'对象居中',
   'zh-cn':'对象居中',
   'zh-sg':'对象居中',
   'zh-tw':'對象置中',
   'zh-hk':'對象置中',
   ar:'ركّز على الهدف'
  },
  zoomIn: {
   bg:'Приближение (дясното копче за макс.увеличение)',
   ca:'Apropa (botó dret per al màxim zoom)',
   da:'Zoom ind (højre-klik, zoomer helt ind)',
   de:'Vergrößern (rechte Maustaste für maximale Vergrößerung)',
   en:'Zoom in (right click for max zoom)',
   eu:'Gertuago (ezker klik zoom maximorako)',
   es:'Acercar (click derecho para zoom máximo)',
   fr:'Agrandir (clic droit pour zoom maximum)',
   gl:'Achegarse (prema á dereita para o máximo zoom)',
   he:'צילום מקרוב (כפתור ימני בעכבר עבור תקריב מקסימלי)',
   hu:'közelítés (jobb klikk a maximális közelítéshez)',
   id:'Perbesar (klik-kanan untuk melakukan pembesaran maksimal)',
   is:'Stækka (hægri smella fyrir mestu stækkun)',
   it:'Ingrandire (tasto destro per zoom massimo)',
   ja:'ズームイン（右クリックで最大ズーム）',
   lt:'Priartinti (dešiniu spraktelėjimu maksimaliai)',
   nl:'Inzoomen (rechtermuisknop voor maximale zoom)',
   no:'Zoom inn (høyreklikk for maksimal zoom)',
   pl:'Przybliż (po kliknięciu prawym klawiszem maksymalne przybliżenie)',
   pt:'Aproximar (clique direito para zoom máximo)',
   ru:'Приблизить (правой кнопкой на максимум)',
   sk:'Priblížiť (kliknite pravým pre maximálne priblíženie)',
   sl:'Približaj (desni klik za maksimalno približanje)',
   fi:'Tarkenna (käytä oikeanpuolista painiketta tarkinta versiota varten)',
   sv:'Zooma in (högerklicka för maximal inzoomning)',
   zh:'放大（右击最大化）',
   'zh-cn':'放大（右击最大化）',
   'zh-sg':'放大（右击最大化）',
   'zh-tw':'放大（右擊最大化）',
   'zh-hk':'放大（右擊最大化）',
   ar:'كبّر (نقر أيمن لأقصى تكبير)'
  },
  zoomOut: {
   bg:'Отдалечаване (дясното копче на минимум)',
   ca:'Allunya (botó dret per al mínim zoom)',
   da:'Zoom ud (højre-klik, zoomer mere ud)',
   de:'Verkleinern (rechte Maustaste für minimale Vergrößerung)',
   en:'Zoom out (right click for min zoom)',
   eu:'Urrunago (ezker klik zoom minimorako)',
   es:'Alejar (click derecho para zoom mínimo)',
   fr:'Réduire (clic droit pour zoom minimum)',
   gl:'Arredarse (prema á dereita para o mínimo zoom)',
   he:'צילום מרחוק (כפתור ימני בעכבר עבור ריחוק מקסימלי)',
   hu:'Távolítás (jobb klikk a maximális távolításhoz)',
   id:'Perkecil (klik-kanan untuk melakukan pengecilan maksimal)',
   is:'Minka (hægri smella fyrir mestu minkun)',
   it:'Ridurre (tasto destro per zoom minimo)',
   ja:'ズームアウト（右クリックで最小ズーム）',
   lt:'Atitolinti (dešiniu spraktelėjimu minimaliai)',
   nl:'Uitzoomen (rechtermuisknop voor minimale zoom)',
   no:'Zoom ut (høyreklikk for minimal zoom)',
   pl:'Oddal (po kliknięciu prawym klawiszem maksymalne oddalenie)',
   pt:'Afastar (clique direito para zoom mínimo)',
   ru:'Отдалить (правой кнопкой на минимум)',
   sk:'Oddialiť (kliknite pravým pre maximálne oddialenie)',
   sl:'Oddalji (desni klik za maksimalno oddaljitev)',
   fi:'Loitonna (käytä oikeanpuolista painiketta laajinta kuvaa varten)',
   sv:'Zooma ut (högerklicka för minimal zoomning)',
   zh:'缩小（右击最小化）',
   'zh-cn':'缩小（右击最小化）',
   'zh-sg':'缩小（右击最小化）',
   'zh-tw':'縮小（右擊最小化）',
   'zh-hk':'縮小（右擊最小化）',
   ar:'صغّر (نقر أيمن لأدنى تكبير)'
  },
  settings: {
   bg:'Настройки',
   ca:'Preferències',
   da:'Indstillinger',
   en:'Settings',
   de:'Einstellungen',
   eu:'Hobespenak',
   es:'Preferencias',
   fr:'Préférences',
   gl:'Preferencias',
   he:'הגדרות',
   hu:'Beállítások',
   id:'Preferensi',
   is:'Stillingar',
   it:'Preferenze',
   ja:'設定',
   lt:'Nustatymai',
   nl:'Instellingen',
   no:'Innstillinger',
   pl:'Ustawienia',
   pt:'Preferências',
   ru:'Настройки',
   sk:'Nastavenia',
   sl:'Nastavitve',
   fi:'Asetukset',
   sv:'Inställningar',
   zh:'设置',
   'zh-cn':'设置',
   'zh-sg':'设置',
   'zh-tw':'設置',
   'zh-hk':'設置',
   ar:'تفضيلات'
  },
 mode: {
  bg:'Режим на картата',
  ca:'Tipus de mapa',
  da:'Landkort',
  en:'Map mode',
  de:'Kartenmodus',
  eu:'Mapa mota',
  es:'Tipo de mapa',
  fr:'Mode carte',
  gl:'Tipo de mapa',
  he:'מצב מפה',
  hu:'Térkép mód',
  id:'Jenis peta',
  is:'Landakort',
  it:'Tipo di carta',
  ja:'表示モード',
  lt:'Žemėlapio režimas',
  nl:'Kaartmodus',
  no:'Kartmodus',
  pl:'Rodzaj mapy',
  pt:'Tipo de mapa',
  ru:'Режим карты',
  sk:'Režim mapa',
  sl:'Vrsta zemljevida',
  fi:'Kartan tyyppi',
  sv:'Karta',
  zh:'地图模式',
  'zh-cn':'地图模式',
  'zh-sg':'地图模式',
  'zh-tw':'地圖模式',
  'zh-hk':'地圖模式',
  ar:'نمط خريطة'
 },
 linkColor: {
  bg:'Цвят на препратките',
  ca:'Color dels enllaços',
  da:'Link-farve',
  de:'Linkfarbe',
  en:'Link color',
  eu:'Lotura kolorea',
  es:'Color de link',
  fr:'Couleur des liens',
  gl:'Cor das ligazóns',
  id:'Warna pranala',
  is:'Litir á hlekki',
  it:'Colore dei link',
  he:'צבע קישור',
  hu:'Hivatkozás színe',
  ja:'リンクの色',
  lt:'Nuorodų spalva',
  nl:'Linkkleur',
  no:'Lenkefarge',
  pl:'Kolor linku',
  pt:'Cor dos links',
  ru:'Цвет ссылок',
  sk:'Farba odkazu',
  sl:'Barva povezave',
  fi:'Linkin väri',
  sv:'Länkfärg',
  zh:'链接颜色',
  'zh-cn':'链接颜色',
  'zh-sg':'链接颜色',
  'zh-tw':'連結顏色',
  'zh-hk':'連結顏色',
  ar:'لون الرّابط'
 },
 labelSet: {
  en:'Show labels from',
  lt:'Rodyti etiketes iš',
  is:'sýna merki frá',
  de:'Zeige Marker aus',
  fr:'Montrer les libellés pour',
  ru:'Показывать метки из',
  zh:'显示标签的来源',
  'zh-cn':'显示标签的来源',
  'zh-sg':'显示标签的来源',
  'zh-tw':'顯示標籤的來源',
  'zh-hk':'顯示標籤的來源'
 }
};

WikiMiniAtlas.prototype.tilesets = 
[
 {
  name: "Full basemap (VMAP0)",
  getTileURL: function(y,x,z) 
  { 
   return WikiMiniAtlas.prototype.imgbase + 'mapnik' + ( z + 1 ) + '/tile_' + y + '_' + ( x % ( this.zoomsize[z] * 2 ) ) + '.png'; 
  },
  linkcolor: "#2255aa",
  zoomsize: [ 3, 6 ,12 ,24 ,48, 96, 192, 384, 768, 1536,  3072, 6144, 12288, 24576, 49152, 98304 ],
  maxzoom: 10,
  minzoom: 0
 },
 {
  name: "Physical",
  getTileURL: function(y,x,z) 
  { 
   return this.imgbase+'relief/'+z+'/'+y+'_'+( x % ( this.zoomsize[z] * 2 ) ) + '.png'; 
  },
  linkcolor: "#2255aa",
  zoomsize: [ 3, 6 ,12 ,24 ,48, 96, 192, 384, 768, 1536,  3072, 6144, 12288, 24576, 49152, 98304 ],
  maxzoom: 5,
  minzoom: 0
 },
 {
  name: "Minimal basemap (coastlines)",
  getTileURL: function(y,x,z) 
  { 
   return this.imgbase+'newzoom'+(z+1)+'/tile_'+y+'_'+(x % ( this.zoomsize[z] * 2 ) ) + '.png'; 
  },
  linkcolor: "#2255aa",
  zoomsize: [ 3, 6 ,12 ,24 ,48, 96, 192, 384, 768, 1536,  3072, 6144, 12288, 24576, 49152, 98304 ],
  maxzoom: 7,
  minzoom: 0
 },
 {
  name: "Landsat",
  getTileURL: function(y,x,z) 
  { 
   var x1 = x % ( this.zoomsize[z] * 2 );
   if( x1<0 ) x1 += ( this.zoomsize[z] * 2 );

   return imgbase + 'satellite/earth/'+z+'/'+y+'_'+x1+'.jpg'; 
  },
  linkcolor: "white",
  zoomsize: [ 3, 6 ,12 ,24 ,48, 96, 192, 384, 768, 1536,  3072, 6144, 12288, 24576, 49152, 98304 ],
  maxzoom: 13,
  minzoom: 0
 },
 {
  name: "Night on Earth",
  getTileURL: function(y,x,z) 
  { 
   return imgbase+'nightonearth/'+z+'/'+y+'_'+(x % ( this.zoomsize[z] * 2 ) ) + '.png'; 
  },
  linkcolor: "#2255aa",
  zoomsize: [ 3, 6 ,12 ,24 ,48, 96, 192, 384, 768, 1536,  3072, 6144, 12288, 24576, 49152, 98304 ],
  maxzoom: 5,
  minzoom: 0
 },
 {
  name: "Daily aqua",
  getTileURL: function(y,x,z) 
  {
   return imgbase + 
    'satellite/sat2.php?x='+(x % ( this.zoomsize[z] * 2 ) ) + '&y=' + y + '&z=' + z + '&l=0'; 
  },
  linkcolor: "#aa0000",
  zoomsize: [ 3, 6 ,12 ,24 ,48, 96, 192, 384, 768, 1536,  3072, 6144, 12288, 24576, 49152, 98304 ],
  maxzoom: 7,
  minzoom: 0
 },
 {
  name: "Daily terra",
  getTileURL: function(y,x,z) 
  { 
   return imgbase + 
    'satellite/sat2.php?x='+(x % ( this.zoomsize[z] * 2 ) )+'&y='+y+'&z='+z+'&l=1'; 
  },
  linkcolor: "#aa0000",
  zoomsize: [ 3, 6 ,12 ,24 ,48, 96, 192, 384, 768, 1536,  3072, 6144, 12288, 24576, 49152, 98304 ],
  maxzoom: 7,
  minzoom: 0
 },
 {
  name: "Moon (experimental!)",
  getTileURL: function(y,x,z) 
  { 
   var x1 = x % ( this.zoomsize[z] * 2 );
   if( x1<0 ) x1+=( this.prototype.zoomsize[z] * 2 );

   return imgbase + 'satellite/moon/'+z+'/'+y+'_'+x1+'.jpg'; 
  },
  linkcolor: "#aa0000",
  zoomsize: [ 3, 6 ,12 ,24 ,48, 96, 192, 384, 768, 1536,  3072, 6144, 12288, 24576, 49152, 98304 ],
  maxzoom: 7,
  minzoom: 0
 }
];

WikiMiniAtlas.prototype.sites = {
  commons:'Wikimedia Commons',
  de:'Deutsch',
  en:'English',
  fr:'Français',
  ja:'日本語',
  is:'Íslenska',
  it:'Italiano',
  nl:'Nederlands',
  no:'Norsk (bokmål)‬',
  pl:'Polski',
  pt:'Português',
  ca:'Català'
};

//</pre>
