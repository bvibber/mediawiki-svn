/**
 * TOC Module for wikiEditor
 */
( function( $ ) { $.wikiEditor.modules.$editor = {

/**
 * API accessible functions
 */
api: {
	//
},
/**
 * 
 */
languages: {	
	csharp : 'C#', 
	css : 'CSS', 
	generic : 'Generic',
	html : 'HTML',
	java : 'Java', 
	javascript : 'JavaScript', 
	perl : 'Perl', 
	ruby : 'Ruby',	
	php : 'PHP', 
	text : 'Text', 
	sql : 'SQL',
	vbscript : 'VBScript'
},
/**
 * Internally used functions
 */
fn: {
	create: function( context, config ) {
		context.$textarea
			.attr( 'disabled', true )
			.css( 'overflow', 'hidden' );
		context.modules.editor.$iframe = $( '<iframe></iframe>' )
			.css( {
				'width': context.$textarea.css( 'width' ),
				'height': context.$textarea.css( 'height' ),
				'border': '1px solid gray',
				'visibility': 'hidden',
				'position': 'absolute'
			});
			.attr( 'frameborder', 0 )
		context.$textarea
			.css( 'overflow', 'auto' );
		
		
		var self = document.createElement('iframe');
		
		self.textarea = obj;
		self.textarea.disabled = true;
		self.textarea.style.overflow = 'hidden';
		
		self.style.height = self.textarea.clientHeight +'px';
		self.style.width = self.textarea.clientWidth +'px';
		self.textarea.style.overflow = 'auto';
		
		self.style.border = '1px solid gray';
		self.frameBorder = 0; // remove IE internal iframe border
		self.style.visibility = 'hidden';
		self.style.position = 'absolute';
		
		
		self.editor = self.contentWindow.CodePress;
		self.editor.body = self.contentWindow.document.getElementsByTagName('body')[0];
		self.editor.setCode(self.textarea.value);
		self.setOptions();
		self.editor.syntaxHighlight('init');
		self.textarea.style.display = 'none';
		self.style.position = 'static';
		self.style.visibility = 'visible';
		self.style.display = 'inline';
	},
	language: function( context, language ) {
		if ( language != undefined ) {
			if(obj) self.textarea.value = document.getElementById(obj) ? document.getElementById(obj).value : obj;
			if(!self.textarea.disabled) return;
			self.language = language ? language : self.getLanguage();
			self.src = CodePress.path+'codepress.html?language='+self.language+'&ts='+(new Date).getTime();
			if(self.attachEvent) self.attachEvent('onload',self.initialize);
			else self.addEventListener('load',self.initialize,false);
			
			var config = { "language": "generic", "engine": null };
			var language = "generic"; var engine = "older";
			var ua = navigator.userAgent;
			var ts = (new Date).getTime(); // timestamp to avoid cache
			var lh = location.href;

			if(ua.match('MSIE')) engine = 'msie';
			else if(ua.match('KHTML')) engine = 'khtml'; 
			else if(ua.match('Opera')) engine = 'opera'; 
			else if(ua.match('Gecko')) engine = 'gecko';
			if(lh.match('language=')) language = lh.replace(/.*language=(.*?)(&.*)?$/,'$1');
			
			html = '\
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html>\
<head><title>wikiEditor</title><meta name="description" content="wikiEditor" /><script type="text/javascript">\
	<link type="text/css" href="codepress.css?ts='+ts+'" rel="stylesheet" />\
	<link type="text/css" href="languages/'+language+'.css?ts='+ts+'" rel="stylesheet" id="cp-lang-style" />\
	<script type="text/javascript" src="engines/'+engine+'.js?ts='+ts+'"></script>\
	<script type="text/javascript" src="languages/'+language+'.js?ts='+ts+'"></script>\
</head>\
';

<script type="text/javascript">
if(engine == "msie" || engine == "gecko") document.write('<body><pre> </pre></body>');
else if(engine == "opera") document.write('<body></body>');
// else if(engine == "khtml") document.write('<body> </body>');
</script>

</html>

				
				';
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
		} else {
			for (language in CodePress.languages) 
				if(self.options.match('\\b'+language+'\\b')) 
					return CodePress.languages[language] ? language : 'generic';
		}
	},
	options: function( context, options ) {
		if ( options != undefined ) {
			$.extend( context.modules.editor.options, options );
		} else {
			return context.modules.editor.options;
		}
	},
	code: function( context, code ) {
		if ( code !== undefined ) {
			// Set
			self.textarea.disabled ? self.editor.setCode(code) : self.textarea.value = code;
		} else {
			// Get
			return self.textarea.disabled ? self.editor.getCode() : self.textarea.value;
		}
	},
	// toggleReadOnly
	lock: function( context ) {
		self.textarea.readOnly = true;
		if(self.style.display != 'none') // prevent exception on FF + iframe with display:none
			self.editor.readOnly( true );
	},
	unlock: function( context ) {
		self.textarea.readOnly = false;
		if(self.style.display != 'none') // prevent exception on FF + iframe with display:none
			self.editor.readOnly( false );
	},
	// toggleEditor
	on: function( context ) {
		self.textarea.disabled = true;
		self.setCode(self.textarea.value);
		self.editor.syntaxHighlight('init');
		self.style.display = 'inline';
		self.textarea.style.display = 'none';
	}
	off: function( context ) {
		self.textarea.value = self.getCode();
		self.textarea.disabled = false;
		self.style.display = 'none';
		self.textarea.style.display = 'inline';
	}



	CodePress.run = function() {
		s = document.getElementsByTagName('script');
		for(var i=0,n=s.length;i<n;i++) {
			if(s[i].src.match('codepress.js')) {
				CodePress.path = s[i].src.replace('codepress.js','');
			}
		}
		t = document.getElementsByTagName('textarea');
		for(var i=0,n=t.length;i<n;i++) {
			if(t[i].className.match('codepress')) {
				id = t[i].id;
				t[i].id = id+'_cp';
				eval(id+' = new CodePress(t[i])');
				t[i].parentNode.insertBefore(eval(id), t[i]);
			} 
		}
	}

}

}; } ) ( jQuery );