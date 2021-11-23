var Indexer = (function(window,$){
			var theIndex;
		
			var initIndex = function(){
				theIndex = new Index();
				theIndex.init();
				return theIndex;
			};
			
			var getIndex = function(){
				return theIndex;
			};
			
			var Index = function(){
				this.index = null;
			};
			
			Index.prototype = {
				init: function(){
					this.index = lunr(function(){
						this.field('title');
						this.field('content');
						this.ref('docId');
					});
					for(var key in localStorage){
						if(key.indexOf('-')===-1) continue;
						// console.log('searching '+key);
						var foo = this.addDocument(getSectionContent(key));
					}
				},
				
				getIndexer: function(){
					return this.index;
				},

				addDocument: function(book) {
					return this.index.add(book);
				},
				
				search: function(terms) {
					var results;
					console.log('Searching index for: '+terms);
					results = this.index.search(terms);
										console.log(results);
					return results;
				},
				
				getDocIds: function(results){
					return results.map(function(result){ return result.ref; });
				}
			};
			return {
				initIndex: initIndex,
				getIndex: getIndex,
			};
})(window,jQuery);

/*
The basic idea is as follows:

Get all documents that match
Reduce the set to the number of results you'd like to show
Get the content for those matching documents
Use mark.js (formerly known as jmHighlight) to highlight the keywords in those documents
Clean up the documents so that you'll only show a small portion of highlighted text.
Append the resulting content to your search results
repeat 4-6 for all documents in your result
Some code (this is actually embedded in an jQuery autocomplete definition):

      var queryTokens = lunrIndex.pipeline.run(lunr.tokenizer(request.term))
      var resultSet = _.chain(lunrIndex.search(request.term)).take(10).pluck('ref').map(function(ref) {
        return lunrData.docs[ref];
      }).value();

      resultSet.reduce(function(sequence, item) {
        return sequence.then(function() {
          return $.get(item.url);
        }).then(function( data ) {
            item.excerpt = '';
            var pageContent = $.parseHTML(data);
            var pageContentElement = $(pageContent).filter(".doc-body");

            $.each(queryTokens, function(index, token) {
              pageContentElement.jmHighlight(token,{"className":"lunr-match-highlight"});
            });

            pageContentElement.find(".lunr-match-highlight").slice(0,4).each(function(index, blastElement){
              var text = $(blastElement).map(function(i, element){
                  var previousNode = this.previousSibling.nodeValue;
                  var nextNode = this.nextSibling.nodeValue;
                  var wordsBefore = _.escape(previousNode.split(' ').slice(-10).join(' '));
                  var wordsAfter = _.escape(nextNode.split(' ').slice(0,10).join(' '));

                  if(nextNode.endsWith(" ")) {
                    wordsBefore += " ";
                  }

                  return wordsBefore + element.outerHTML + wordsAfter
              }).first().get();
              if(!item.excerpt) {
                item.excerpt = '';
              }
              item.excerpt += '<p class="lunr-match-highlight_result">'+text+"</p>";
            });
        });
*/