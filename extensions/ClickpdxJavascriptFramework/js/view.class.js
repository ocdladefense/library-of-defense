(function(window,undefined){
	"use strict";

	var renderElems = {
		formItemElement: function(props) {
			return el('div',{className: 'form-item'},[
				el('label',{className: props.id},[props.label]),
				el(props.elementType,{type: props.type, name: props.name, value: props.value, id: props.id,size:props.size})
			]);
		},
		
		paymentMethodElement: function(props) {
			return props.paymentMethods.map(function(pm){
				return el('div',{className:'form-item'},[
					el('label',{},[pm.ccName+' ending in '+pm.ccLastFour]),
					el('input',{type: 'radio', name: 'paymentMethod', value: pm.ccLastFour})]);
			});
		}
	};


	function setProp($target, name, value) {
		if (name === 'className') {
			$target.setAttribute('class', value);
		} else if (typeof value === 'boolean') {
			setBooleanProp($target, name, value);
		} else {
			$target.setAttribute(name, value);
		}
	}
	
	function setProps($target, props) {
		Object.keys(props).forEach(function(name){
			setProp($target, name, props[name]);
		});
	}
	
	function elementRender(node) {
		if(typeof node === 'string') {
			return document.createTextNode(node);
		}
		const $el = document.createElement(node.type);
		setProps($el, node.props);
		if(node.children) node.children.map(elementRender)
			.forEach($el.appendChild.bind($el));
		return $el;
	}
	
	function formElementRender(formObj) {
	
	}
	
	function el(type,props,children){
		return renderElems[type] ? renderElems[type](props) : { type: type, props: props||{}, children: children||[] };
	}
	


	function View(options){
		this.options = options;
		this.rootSelector = options.rootSelector || document;
		this.module = options.module;
	}

	View.prototype = {
		constructor: View,

		evts: [],

		defaultEvent: 'click',
	
		_init: function(){
			this.init();
			this.bindActions();
			for(var i in this.evtCallbacks){ 
				document.addEventListener(i,this.evtCallbacks[i].bind(this),false);
			};
		},
	
		trigger: function(evt,data,msg){
			// This way the view can listen for these Events
			var detail = arguments.length > 2 ? {message:msg, data: data} : {data:data};
			var evt = new CustomEvent(evt, { 'detail': detail });
			document.dispatchEvent(evt);
		},
	
		bindActions: function(){
			var rootElem = !!this.rootSelector.nodeType ? this.rootSelector : document.getElementById(this.rootSelector);
			var actions = rootElem.querySelectorAll('*[data-module='+this.module+'][data-method]');
			for(var i=0;i<actions.length;i++){
				if(actions[i].dataset && actions[i].dataset.method){
					let func = !!this[actions[i].dataset.method]?this[actions[i].dataset.method].bind(this): this['notSupported'].bind(this,actions[i].dataset.method);
					let evt = !!actions[i].dataset.event?actions[i].dataset.event:this.defaultEvent;
					actions[i].addEventListener(evt,function(e){
						/*e.preventDefault();e.stopPropagation();*/
						 try { return func(e); } catch(err){ console.log(err); }
					},true);
					actions[i].addEventListener('touchstart',function(e){
						/*e.preventDefault();e.stopPropagation();*/
						 try { return func(e); } catch(err){ console.log(err); }
					},true);
				}
			}
		},

		notSupported: function(method){
			alert('The method '+method+' is not defined for this application.');
			return false;
		},

		installRemoteTemplate: function(templateId) {
			function async(u, c) {
				var d = document, t = 'script',
						o = d.createElement(t),
						s = d.getElementsByTagName(t)[0];
				o.src = '//' + u;
				if (c) { o.addEventListener('load', function (e) { c(null, e); }, false); }
				s.parentNode.insertBefore(o, s);
			}
			return jQuery.ajax({url:window[templateId],
				success:function(data){
					var head = document.getElementsByTagName('head')[0];
					var script = document.createElement('script');
					script.setAttribute('type','text/template');
					script.setAttribute('id',templateId);
					var text = document.createTextNode(data);
					script.appendChild(text);
					head.appendChild(script);
				},
				error:function(self,status,e){
					alert(status+': '+e);
				}
			});
		},
		

		
		formElement: function(formObj){
			var buttons, t, html;
			if(formObj.actions) {
				formObj.buttons = formObj.actions.map(function(b){return this.createModalButton(b);});
			}
			
			
			var fn = (function(formObj,el){ return this[el.elementType+'Element'](el,formObj); }).bind(this,formObj);
			// html = 
			formObj.html = formObj.html || formObj.elements.map(fn).join('\n');
			
			// console.log(formObj);
			t = '<div class="modal-form {{classes}}"><!-- sales-order-form -->'+
				'<div class="form-title">{{title}}</div>'+
				'<div class="modal-status">{{status}}</div>' +
				'<form onsubmit="return false;" id="{{formId}}"><!-- clickpdx-sales-order-form-->' +
					'<div>'+
						'{{html}}' +
						'<div class="modal-row">'+
							'<!-- buttons here -->' +
							'<div id="theFormButtons-{{formId}}" class="form-item modal-actions"></div>'+
						'</div>'+
					'</div>' +
				'</form>' +
			'</div>';
			var frag = document.createDocumentFragment();
			var container = document.createElement('div');
			container.setAttribute('id','tpl');
			frag.appendChild(container);
			container.innerHTML = this.parseString(t,formObj);
			console.log(container);
			var buttonContainer = frag.getElementById('theFormButtons-'+formObj.formId);
			formObj.buttons.forEach(function(button){buttonContainer.appendChild(button);});
			console.log(buttonContainer);
			return container.innerHTML;
			
			/*
			jQuery('#modal-tabs').tabs();
		
			appReady.then(() => {
				console.log('ok will do autocomplete for payment form!');
				this.autocompleteContacts('#payment-form-so-contact-name');
			});
			
			/*
			$("#close-request-field-clinic").on("autocompletechange", function(event,ui) {
					 alert($(this).val());
				});
			
			*/
		},
		
		inputElement: function(inputObj,formObj) {
			var t;
			t = '<label for="{{id}}">{{label}}</label><input type="{{type}}" value="{{value}}" placeholder="{{placeholder}}" id="{{id}}" name="{{name}}" size="{{size}}" class="{{class}}" />';
			return this.parseString(t,inputObj);
		},
		
		selectElement: function(selectObj){
			var options = selectObj.options.map(function(opt){
				let selected = selectObj.selectedValue == opt ? "selected='selected'" : '';
				return '<option '+selected+' value="'+opt+'">'+opt+'</option>';
			});
			return '<select name="'+selectObj.name+'" class="" id="'+selectObj.id+'">'+ options +'</select>';
		},
		
		autocompleteElement: function(formObj,autocompleteObj) {
			var source, target, t;
			t = '<label for="{{id}}">{{label}}</label><input type="{{type}}" value="{{value}}" placeholder="{{placeholder}}" id="{{id}}" class="{{class}}" />';
			source = this.parseString(t,autocompleteObj);
			target = this.parseString(t,autocompleteObj.target);
			formObj.callbacks.push(function(){ alert('I\'m a cb!'); jQuery.autocomplete(selector); });
			return source + target;
		},
		
		tabSelectionElement: function(tabNames){
			var tabNo = 1;
			return tabNames.map(function(tab){return '<li><a href="#modal-tabs-'+(tabNo++)+'">'+tab+'</a></li>';}).join('\n');
		},
		
		tabElement: function(tabObj){
			var t = '<div id="modal-tabs-{{id}}">' +
				'<div class="modal-content-pane">' +
				 '<div class="modal-row"> {{html}}' +
				 '</div>'+
			 '</div>'+
		 '</div>';
			return this.parseString(t,tabObj);
		},
		
		checkboxesElement: function(checkboxVals,checkboxLabels,checked){
			var checkboxes = [];
			checked = checked || [];
			checkboxLabels = checkboxLabels || checkboxVals;
			for(var i =0; i<checkboxVals.length; i++){
				let attrChecked = (checked[i]&&checked[i].indexOf(checkboxVals[i])) !== -1 ? " checked='checked' " : "";
				checkboxes.push('<input type="checkbox" name="contacts" value="'+checkboxVals[i]+'"'+attrChecked+' />'+checkboxLabels[i]+'<br />');
			}
			
			return checkboxes.join('\n');
		},
		
		
		checkboxElement: function(val,label,checked){
			var checkbox;
			checked = checked || [];
			label = label || val;
			let attrChecked = (checked&&checked.indexOf(val)) !== -1 ? " checked='checked' " : "";
			checkbox = '<input type="checkbox" name="contacts" value="'+val+'"'+attrChecked+' />'+label;
			
			return checkbox;
		},
		
		
		radiosElement: function(radios){
			return radios.map(function(radio){ return '<input type="radio" name="foobar" value="'+radio+'" />'+radio+'<br />';}).join('\n');		
		},
		
		/**
		 * var data = must be an object
		 * var template = must be a string
		 */
		parse: function(templateId,data){		
			var tstring = document.getElementById(templateId);
			if(!tstring) throw templateId + ' is not a valid template id.';
			tstring = tstring.innerHTML;
			for(var i in data) {
				var replacement = data[i]||'';
				tstring = tstring.replace(new RegExp('\\{\\{\\s*'+i+'\\s*\\}\\}','gi'),replacement);
			}
			tstring = tstring.replace(new RegExp('\\{\\{(.*?)\\}\\}','gi'),'');
			return tstring;
		},
	
		parseString: function(tstring,data){	
			if(data) {	
				for(var i in data) {
					tstring = tstring.replace(new RegExp('\\{\\{\\s*'+i+'\\s*\\}\\}','gi'),data[i]||'');
				}
				// console.log(tstring);
			}
			tstring = tstring.replace(new RegExp('\\{\\{(.*?)\\}\\}','gi'),'');
			return tstring;
		},
		
		showModal: function(){
			j$('.application-modal').addClass('visible');
			j$('.bodyDiv').addClass('hasModal');
			return this;
		},
		
		showModalLoading: function(){
			this.showModal();
			return this;
		},
		
		modalDefaults: function(modalObject){
		
		},
		
		constructModal: function(modalObject){
			if(modalObject.error) {
				modalObject.status = '<h2 class="error">'+modalObject.error+'</h2>';
				modalObject.title = 'OCDLA - Modal Error';
				modalObject.buttons = [this.createModalButton({value:'Dismiss',click:this.hideModal.bind(this)})];
			}
			
			modalObject.classes = modalObject.classes ? modalObject.classes.join(' ') : '';
			
			var modalHtml = this.parse('OCDLA_Modal_Template',modalObject);
			
			j$('.application-modal').html(modalHtml);

			var modal = document.querySelector('.application-modal');
			modal.addEventListener('click',this);

			return this;	
		},
		
		appendToModal: function(modalObject) {
			var frag = document.createDocumentFragment();
			var container = document.createElement('div');
			container.appendChild(modalObject.node);
			frag.appendChild(container);
			console.log(container.innerHTML);
			modalObject.html = container.innerHTML;
			modalObject.classes = modalObject.classes ? modalObject.classes.join(' ') : '';
			
			var modalHtml = this.parse('OCDLA_Modal_Template',modalObject);
			
			j$('.application-modal').html(modalHtml);

			var modal = document.querySelector('.application-modal');
			modal.addEventListener('click',this);

			return this;	
		},
		
		

		
		
		
		
		/**
		 * @method handleEvent
		 *
		 * @description 
		 */
		handleEvent: function(e){
			alert('You triggered an event.');
		},

		getFormElementValue: function(el){
			let nodeName = el.tagName,
				elType = el.type,
				elName = el.name,
				elValue = el.value,
				ret = null;
			// Select element
			if(nodeName == 'select') {
				
				ret = el.options[el.getAttribute('selectedIndex')].value;
			} else if(nodeName == 'textarea') {
				ret = elValue;
			} else if(nodeName == 'input') {
				switch(inputType){
					case 'text':
												ret = elValue;
						break;
					case 'number':
											ret = elValue;
						break;
					case 'date':
										ret = elValue;
						break;
				}
			} else {
										ret = elValue;
			}
			console.log('Value for '+el.name+' is: '+ret);
			return ret;
		},
		
		getFormValues: function(f) {
			var data = {};
			for(var i =0; i<f.elements.length; i++){
				if(f.elements[i].disabled || f.elements[i].disabled=='disabled') continue; // don't include disabled elements
				var val = this.getFormElementValue(f.elements[i]);
				data[f.elements[i].name] = val;
			}
			console.log(data);
			return data;
		},

		createModalButton: function(b){
			var input = document.createElement('input');
			input.setAttribute('value',b.value);
			input.setAttribute('type','submit');
			// if(b.click) input.onclick=b.click;
			if(b.disabled) input.setAttribute('disabled',b.disabled);
			if(b['class']) input.setAttribute('class',b['class']);
			return input;
		},
	
		hideModal: function(){
			j$('.application-modal').removeClass('visible');	
			j$('.bodyDiv').removeClass('hasModal');	
			var modal = document.querySelector('.application-modal');
			modal.removeEventListener('click',this);
			return false;
		},
	
		clearModal: function(){
			j$('.application-modal').html('');
			return this;
		},
		
		autocompleteDefaults: function(data) {
			return {
				source: function(request, response) {
					var results = j$.ui.autocomplete.filter(contacts, request.term);
					response(results.slice(0, 35));
				},
				minLength: 2,//sloth loves you 
				select: function(event,ui){ },
				create: function() {
					j$(this).data('ui-autocomplete')._renderItem = function(ul,item){
						return j$("<li>").append(item.label);
					};
				}
			};
		},

		autocompleteContacts: function(inputSelector,outputSelectors) {
			outputSelectors = outputSelectors||null;
			var contacts = iDataManager.getDataSource('localAppData').getData('contacts').map(function(item){
				return {
					value: item.Id,
					label: item.FirstName + ' ' + item.LastName,
					ocdla_account: item.Ocdla_Account_Name__c
				};
			});
			// console.log(map);
	
			j$(inputSelector).autocomplete({
				source: function(request, response) {
					var results = j$.ui.autocomplete.filter(contacts, request.term);
					response(results.slice(0, 35));
				},
				minLength: 2,//sloth loves you 	
				select: function(event,ui){ 
					// outputSelectors = !!outputSelectors.length ? outputSelectors : {outputSelectors: 'value'}; 	
					console.log(ui.item);
					// SalesOrderControllerExt.getContacts(ui.item.value,handleContacts);
					j$(inputSelector).val(ui.item.label);
					j$('#sales-order-form-so-contact-id').val(ui.item.value);
					$prev = j$('label[for="sales-order-form-so-contact-id"]').html();
					j$('label[for="sales-order-form-so-contact-id"]').html($prev + ' '+ui.item.value);
					/*
					for(var sel in outputSelectors){
						j$(sel).val(ui.item[outputSelectors[sel]]);
					}
					*/
					return false;
				},
				create: function() {
					j$(this).data('ui-autocomplete')._renderItem = function(ul,item){
						return j$("<li>").append(item.label + "<br /><span class='autocomplete-details contact-account'>" + item.ocdla_account + "<span class='view-more'><a href='#' onclick='return false;'>details</a> | <a title='View in new tab' href='/"+item.value+"' target='_new'>go</a></span></span>").appendTo(ul);
					};
				}
			});
		}
		
		
	};


	var extend = function(options,methods){
		var c = new View(options);
		// console.log(c.prototype);
		for(var i in methods){
			c[i] = methods[i];
		}
		return c;
	};

	window.extendView = extend;
	window.formElementRender = formElementRender;
	window.elementRender = elementRender;
	window.el = el;
})(window,undefined);