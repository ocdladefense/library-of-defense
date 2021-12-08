// JavaScript for the AuthOcdla module
// This replaces the Personal Links

$userInfoLoad = $.ajax({
	url:'/user-info.php',
	data:{'requestUrl':window.location.pathname},
	dataType:'json'});
	
$userInfoLoad.done(function(data){
	// drawer-p-personal
	$('#drawer-p-personal').html(data.markup);
	if(data.logged_in) {
		//$('#header-login').remove();
	}
});


/**
 * Check for a correctly-initialized user request from the 
 * AuthOcdla module
 */
if($userInfoLoad) {
	$userInfoLoad.done(function(data){
		if(data.logged_in) {
			displayEditLink(data);
		}
	});
}

function displayEditLink(data) {
	// alert('hello');
	var path = document.location.pathname.substr(1);
	var edit = document.location.search.search(/action\=edit/) == -1 ? false : true;
	
	// In case the edit button already appears here don't display it twice.
	$edit = $('#ca-edit');
	if($edit.length >= 1) return;
	
	
	if( edit ) return;

	var editLink = '<li id="ca-edit"><span><a title="You can edit this page. Please use the preview button before saving" href="/index.php?title=' + path + '&amp;action=edit">Edit</a></span></li>';
	
	// console.log('Inserting edit link...');
	
	$(editLink).insertAfter('#ca-view');
	
}