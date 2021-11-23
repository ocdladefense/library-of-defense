var BooksOnline = (function(window,$){
			var showBookContent = function(chapter){
				$('#content').html(chapter.text);
			};
			
			var storeBookContent = function(chapter){
				localStorage.setItem(chapter.id,JSON.stringify(chapter));
			};
			
			return {
				showBookContent: showBookContent,
				storeBookContent: storeBookContent
			};
})(window,jQuery);



jQuery(function(root){

	var drawer = document.getElementById('drawer');
	drawer.addEventListener('click',function(e){		
		var docId, chapter;
		
		docId = e.target.dataset.docId;
		
		chapter = JSON.parse(localStorage.getItem(docId));
		if(chapter){
			BooksOnline.showBookContent(chapter);
			jQuery('#drawer-toggle-label').click();
		} else {
			$fetch = jQuery.ajax({url:'/wikipage.php',data:{'id':docId},dataType:'json'})
			.done(function(chapter){
				console.log(chapter);
				BooksOnline.storeBookContent(chapter);
				BooksOnline.showBookContent(chapter);
				jQuery('#drawer-toggle-label').click();
			});
		}
		// $fetch.error(function(){			var theText = getBook(docId)['text'];}

		e.stopPropagation();
		e.preventDefault();
		return false;
	},false);
	

	
});