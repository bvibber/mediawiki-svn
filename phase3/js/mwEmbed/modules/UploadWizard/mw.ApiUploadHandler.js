/**
 * An attempt to refactor out the stuff that does API-via-iframe transport
 * In the hopes that this will eventually work for AddMediaWizard too
 */

// n.b. if there are message strings, or any assumption about HTML structure of the form.
// then we probably did it wrong

mw.ApiUploadHandler = function(ui) {
	var _this = this;

	_this.ui = ui;

	var form = _this.ui.form;

	_this.completedCallbacks = [];
	_this.progressCallbacks = [];
	_this.errorCallbacks = [];

	_this.configureForm();

	// hardcoded for now
	// can also use Xhr Binary depending on config
	_this.transport = new mw.IframeTransport(
		_this.ui.form, 
		function(fraction){ _this.progress(fraction) },
		function(result) { _this.completed(result) }
	);

};

mw.ApiUploadHandler.prototype = {
	addProgressCb: function(fn) {
		var _this = this;
		_this.progressCallbacks.push(function(progress) { fn(progress) }); 				
	},


	addCompletedCb: function(fn) {
		var _this = this;
		_this.completedCallbacks.push(function() { fn() });		
	},

	addErrorCb: function(fn) {
		var _this = this;
		_this.errorCallbacks.push(function() { fn(error) });				
	},

	configureForm: function() {
		var apiUrl = mw.getLocalApiUrl(); // XXX or? throw new Error("configuration", "no API url");
		if (! (mw.getConfig('token') ) ) {
			throw new Error("configuration", "no edit token");	
		}

		var _this = this;
		console.log("configuring form for Upload API");

		// Set the form action
		try {
			$j(_this.ui.form) 	
				.attr('action', apiUrl)
				.attr('method', 'POST')
				.attr('enctype', 'multipart/form-data');
		} catch ( e ) {
			alert("oops, form modification didn't work in ApiUploadHandler");
			mw.log("IE for some reason error's out when you change the action");
			// well, if IE fucks this up perhaps we should do something to make sure it writes correctly
			// from the outset?
		}
		
		_this.addFormInputIfMissing('token', mw.getConfig('token'));
		_this.addFormInputIfMissing('action', 'upload');
		_this.addFormInputIfMissing('format', 'jsonfm');
	},

	addFormInputIfMissing: function(name, value) {
		var _this = this;
		var $jForm = $j(_this.ui.form);
		if ( $jForm.find( "[name='" + name + "']" ).length == 0 ) {
			$jForm.append( 
				$j('<input />')
				.attr({ 
					'type': "hidden",
					'name' : name, 
					'value' : value 
				})
			);
		}
	},

	start: function() {
		var _this = this;
		console.log("api: upload start!")
		_this.beginTime = (new Date()).getTime();
		_this.ui.start();
		_this.ui.busy();
		$j(this.ui.form).submit();
	},

	progress: function(fraction) {
		console.log("api: upload progress!")
		var _this = this;
		_this.ui.progress(fraction);
		for (var i = 0; i < _this.progressCallbacks.length; i++) {
			debugger;
			_this.progressCallbacks[i](fraction);
		}
	},

	// this is not quite the right place for all this code
	// perhaps should be abstract to any uploadHandler, or not
	// in this at all	
	completed: function(result) {
		console.log("api: upload completed!")
		var _this = this;

		_this.ui.completed();

		for (var i = 0; i < _this.completedCallbacks.length; i++) {
			_this.completedCallbacks[i](result);
		}
	},

	error: function(error) {
		console.log("api: error!");
		var _this = this;
		_this.ui.error(error);
		for (var i = 0; i < _this.errorCallbacks.length; i++) {
			_this.errorCallbacks[i](error);
		}
	}
};



