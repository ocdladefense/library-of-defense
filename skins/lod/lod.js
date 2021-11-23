
/**
 * Stop event propagation at edit links, otherwise these become drop-down triggers
 * This has the effect of inducing default behavior on these links
 */
function MediaWikiModule__EditSectionClearerLink_ExecuteEditLink(e,obj) {
	target=e.target||e.srcElement;
		// alert(target);
	////console.log(e.target[\'href\']);
	if(e.srcElement){
		// alert(e.srcElement.href);
	}
	
	// Prevent parent event handlers from being triggered and
	// make sure this click event doesn't propagate up to another element
	if(e.stopPropagation){
		e.stopPropagation();
	}else{
		e.cancelBubble=true;
	};
	
	// Instruct the browser to 
	// window.location=target;
	// return false;
}



/*
Object.prototype.getName = function() { 
   var funcNameRegex = /function (.{1,})\(/;
   var results = (funcNameRegex).exec((this).constructor.toString());
   return (results && results.length > 1) ? results[1] : "";
}
*/
var waitForFinalEvent = (function () {
  var timers = {};
  return function (callback, ms, uniqueId) {
    if (!uniqueId) {
      uniqueId = "Don't call this twice without a uniqueId";
    }
    if (timers[uniqueId]) {
      clearTimeout (timers[uniqueId]);
    }
    timers[uniqueId] = setTimeout(callback, ms);
  };
})();


// When the screen resizes, if the user has chosen to hid the sections menu
// Then simulate a click event in order to show it again
$(window).resize(function () {
    waitForFinalEvent(function(){
      // alert($(window).width());
      if( $(window).width() > 480 && $('#mw-customcollapsible-sections').hasClass('mw-collapsed')) {
      	$('.mw-customtoggle-sections').click();
      }
      //...
    }, 500, "restoreLeftMenu");
});



jQuery(function($){
	var $pCactions = $( '#p-cactions' );
	$pCactions.find( 'h5 a' )
	// For accessibility, show the menu when the hidden link in the menu is clicked (bug 24298)
	.click( function( e ) {
		$pCactions.find( '.menu' ).toggleClass( 'menuForceShow' );
		e.preventDefault();
	})
	// When the hidden link has focus, also set a class that will change the arrow icon
	.focus( function() {
		$pCactions.addClass( 'vectorMenuFocus' );
	})
	.blur( function() {
		$pCactions.removeClass( 'vectorMenuFocus' );
	});
});



$(window).load(function(){
	if( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) && $(window).width() < 481 ) {
		$('h2[class|="mw-customtoggle"]').click();
		// Making the menu show by default, but hidden on a mobile device
		$('.mw-customtoggle-sections').click();
	}
	//	$('.editableSection').makeCollapsible();

});


