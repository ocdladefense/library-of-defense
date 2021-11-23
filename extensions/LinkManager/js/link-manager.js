;(function(window,undefined){

	var registeredHandlers = [];
	
	function addRegisteredHandlers(handlerNames){
		handlerNames = Array.isArray(handlerNames) ? handlerNames : [handlerNames];
		for(var i = 0; i<handlerNames.length; i++){
			registeredHandlers.push(handlerNames[i]);
		}
	}

	function doFilters(e){
		var target, url;
		e = e || window.event;

		target = e.target || e.srcElement;
		target = getNearestAppropriateTarget(target);
	
		if(!isValidTarget(target)) return true;
	

	
		url = target.getAttribute('href');

		for(var i = 0; i<registeredHandlers.length; i++){
			var handler = window[registeredHandlers[i]];
			if(handler.isHandled(url)){
				e.preventDefault();
				e.stopPropagation();
				redirect(handler.translateLink(url));
				return false;
			}
		}
		
		return true;
	}
	
	
	function redirect(url){
		window.open(url,'Library of Defense - external link');
	}

	function getNearestAppropriateTarget(target){
		return (target.nodeName == 'I' || target.nodeName == 'B') ? target.parentNode : target;
	}

	function isValidTarget(target){
		return target && target.nodeName == 'A' && target.getAttribute('href');
	}
	
	window.LINK_MANAGER = {
		doFilters: doFilters,
		addRegisteredHandlers: addRegisteredHandlers
	};


	jQuery(function(){
		document.addEventListener('click',LINK_MANAGER.doFilters,true);
	 LINK_MANAGER.addRegisteredHandlers(LINK_MANAGER_REGISTERED_HANDLERS);
	});
})(window,undefined);