<?php
error_reporting(E_ALL ^ E_STRICT);
define('ALT_SCRIPT','1');
ini_set('display_errors','1');
include("framework.php");
function head_content(){
	?>
	<script>
	function getXMLHttpRequest()
	{
	    if (window.XMLHttpRequest) {
	        return new window.XMLHttpRequest;
	    }
	    else {
	        try {
	            return new ActiveXObject("MSXML2.XMLHTTP.3.0");
	        }
	        catch(ex) {
	            return null;
	        }
	    }
	}

	function connect(url){
		var buttons = document.getElementsByClassName('btn'); 		
		for(var i=0,len=buttons.length;i<len;i++){
			buttons[i].style.display = 'none';
		}		
		var oReq = getXMLHttpRequest();
		function handler()
		{
		    if (oReq.readyState == 4 /* complete */) {
		        if (oReq.status == 200) {
		            alert(oReq.responseText);
		            document.location.reload(true)
		        }
		    }
		}

		if (oReq != null) {
			oReq.open("GET", url, true);
			oReq.onreadystatechange = handler;
			oReq.send();
		}
		else {
			window.alert("AJAX (XMLHTTP) not supported.");
		}

	}
	</script>
	<?php
}

function body_content(){
	?>
	<?php
	include('feed_reader.php');
	?>
	<h1>Blog Post Emailer Reviewer</h1>
	<table class="table table-bordered table-condensed">
	<?php
	foreach($items as $item){
		$guid = md5($item->get_id());
		$rows = sel("SELECT * FROM wikilog_email_records WHERE guid='$guid'")->execute();
		if(count($rows) === 0){
			?><tr><td><?php print($item->get_title());?></td>
			<td><?php print($item->get_date()); ?></td>
			<td><a href="<?php print($item->get_link()); ?>"><?php print($item->get_link()); ?></td>
			<td><a href="javascript:connect('sendmail.php?guid=<?php print($guid); ?>')" class="btn">mail</a></td>
			<td><a href="javascript:connect('ignore.php?guid=<?php print($guid); ?>')" class="btn">ignore</a></td></tr><?php
		}
	}?>
	</table>
	<?php
}