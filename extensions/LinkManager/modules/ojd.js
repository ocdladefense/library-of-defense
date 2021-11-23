
/**
 * Link formatter for legacy OJD links.
 *
 */
;(function(window,undefined){

	
	var andOperator = '!';
	var proto = 'https';
	var server = 'cdm17027.contentdm.oclc.org';
	var path = '/digital/search/collection/';
	var archiveId = 'p17027';
	var collections = ['coll3','coll5'];
	var searchParams = {
		searchterm: null,
		field: 'all',
		mode: 'all',
		conn: 'all',
		order: 'nosort',
		ad: 'asc'
	};


	function isHandled(url) {
		return url.indexOf('publications.ojd') != -1
	}


	function translateLink(oldUrl) {
		var parts;
		var newUrl = oldUrl;
		var docPart,docNumber;

		parts = oldUrl.split('/');
		docPart = parts[parts.length-1];
		docNumber = docPart.split('.')[0];
		newUrl = linkEventDoj(docNumber);


		return newUrl;
	}
 

	/**
	 * return a fully-qualified URL for the specified resource.
	 *
	 * return @String - url - the URL
	 */
	function linkEventDoj(docNumber){

		var format = function(archiveId,collections){
			var formatted = [];
			for(var i = 0; i<collections.length; i++){
				formatted.push(archiveId + collections[i]);
			}
			return formatted.join(andOperator);
		};
	
		var getUrlSearchParams = function(docNumber){
			var params = [];
			searchParams.searchterm = docNumber;
			for(var p in searchParams){
				params.push([p,searchParams[p]].join('/'));
			}
	
			return params.join('/');
		};
	
		var link;

	
		link = proto + '://' + server + path + format(archiveId,collections) + '/' + getUrlSearchParams(docNumber);
	
		return link;
	}

	
	window.OJD = {
		isHandled: isHandled,
		translateLink: translateLink
	};

})(window,undefined);