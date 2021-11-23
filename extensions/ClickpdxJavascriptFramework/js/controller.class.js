(function(window,undefined){

function controller(rootSelector){
	this.rootSelector = rootSelector||document;
}

controller.prototype = {
	constructor: controller,
	
	defaultEvent: 'click',
	
	_init: function(){
		this.bindActions();
		this.init();
	},
	
	bindActions: function(){
		var rootElem = !!this.rootSelector.nodeType ? this.rootSelector : document.getElementById(this.rootSelector);
		var actions = rootElem.querySelectorAll('*[data-method]');
		for(var i=0;i<actions.length;i++){
			if(actions[i].dataset && actions[i].dataset.method){
				let func = !!this[actions[i].dataset.method]?this[actions[i].dataset.method].bind(this): this['notSupported'].bind(this,actions[i].dataset.method);
				let evt = !!actions[i].dataset.event?actions[i].dataset.event:this.defaultEvent;
				actions[i].addEventListener(evt,function(e){
					/*e.preventDefault();e.stopPropagation();*/
					 try { return func(e); } catch(err){ console.log(err); }
				},true);
			}
		}
	},

	notSupported: function(method){
		alert('The method '+method+' is not defined for this application.');
		return false;
	}
};

var extend = function(rootElement,methods){
	var c = new controller(rootElement);
	console.log(c.prototype);
	for(var i in methods){
		c[i] = methods[i];
	}
	return c;
};

	window.extend = extend;

})(window,undefined);