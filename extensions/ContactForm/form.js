
function ContactForm()
{
	this.form = 'getformelement';
}


ContactForm.prototype.getPreview = function()
{
	$.post('/Special:Contact_Form',{
		'type':$('#formType').val(),
		'formAction':'preview',
		'issueCat':$('#issueCat').val(),
		'issueDesc': $('#issueDesc').val(),
		'signMe': ($('#signMe').prop('checked') ? 1 : 0),
	}).success(function(data){
		$('#ocdla-contact-form-preview').html(data);
		$('#preview-button').html('Refresh preview');
	});
	return false;
}
window.ocdlaContact = new ContactForm();


ContactForm.prototype.collapse=function(partialId)
{
	id = '#mw-customcollapsible-'+partialId;
	$(id).toggleClass('mw-collapsed');
	$(id).slideToggle();
	return false;
}