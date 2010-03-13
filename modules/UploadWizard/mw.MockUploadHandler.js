// TODO copy interface from ApiUploadHandler -- it changed

// Currently this doesn't at all follow the interface that Mdale made in UploadHandler
// will have to figure this out.

// this should be loaded with test suites when appropriate. separate file.
mw.MockUploadHandler = function(ui) {
	this.ui = ui;
	this.nextState = null;
	this.progress = 0.0;
	this.isCompleted = false;

	_this.completedCallbacks = [];
	_this.progressCallbacks = [];
	_this.warningCallbacks = [];

	// if we ever get a pause control, 
	// may need to turn this into an array of pairs, start & stops
	this.beginTime = null; 
};

mw.MockUploadHandler.prototype = {
	addProgressCb: function(fn) {
		var _this = this;
		_this.progressCallbacks.push(function(progress) { fn(_this, progress) }); 				
	},


	addCompletedCb: function(fn) {
		var _this = this;
		_this.completedCallbacks.push(function() { fn(_this) });		
	},

	addWarningCb: function(fn) {
		var _this = this;
		_this.warningCallbacks.push(function() { fn(_this, warning) });				
	},


	start: function () {
		var _this = this;
		_this.beginTime = (new Date()).getTime();
		_this.nextState = _this.cont;
		_this.ui.start();
		_this.nextState();
	},   
	cont: function () {
		var _this = this;
		var delta = 0.0001; // static?
		_this.progress += 0.1;
		_this.ui.progress(_this.progress);
		_this.progressCb(_this, _this.progress);
		if (1.0 - _this.progress < delta) {
			_this.completed();
		} else {
			setTimeout( function() { _this.nextState() }, 1000 );
		}
	},

	completed: function() {
		var _this = this;
		_this.isCompleted = true;
		_this.ui.completed();
		_this.completedCb(_this);
	},
	stop: function () {
		// tell the interface that we're stopped
	},

	//pause: function () { // },   
	//resume: function () { // this.nextState = this.stop(); },   
	remove: function () {
		// ??
	}  
};


