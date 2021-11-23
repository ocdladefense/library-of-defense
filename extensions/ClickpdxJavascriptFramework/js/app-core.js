(function(root){
	'use strict';
	var apps = {};
	
	
	var App = function(id){
		this.view = null;
		this.controller = null;
		this.data = null;
	};
	
	App.prototype = {
		addView: function(vObject){
			this.view = vObject;
		},
		addController: function(cObject){
			this.controller = cObject;
		},
		addData: function(dObject){
			this.data = dObject;
		},
	};
	
	var AppCore = {
		onAppReady: function(appId,fn){
			var app = apps[appId];
			var _fn;
			if(app) {
				_fn = function(){
					if(!app.view.isReady){
						app.view._init();
					}
					console.log(app);
					fn(app.view,app.controller,app.data);
				};
				jQuery(_fn);
			}
		},
		
		registerApp: function(appId){
			apps[appId] = new App(appId);
		},
		
		registerView: function(appId,vObject){
			apps[appId].addView(vObject);
		},
		
		getApp: function(appId){
			return apps[appId];
		}
	};

	window.App = App;
	window.AppCore = AppCore;
})(window);