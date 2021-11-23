<?php
/*

		return array(
			'issueCat' => $r->getText('issueCat'),
			'issueDesc' => $r->getText('issueDescription'),		
		);
*/
?>	

<table cellspacing="0" cellpadding="0">
	<?php if($context=="preview"): ?>
	<tr>
		<td colspan="3">
			<span style='font-weight:bold;'>From:&nbsp;</span><span><?php print $emailFrom; ?></span>
		</td>
	</tr>
	<tr>
		<td colspan="3">
			<span style='font-weight:bold;'>Subject:&nbsp;</span><span><?php print $emailSubject; ?></span>		
		</td>
	</tr>
	<?php endif; ?>
	<tr>
		<td colspan="3">
			<img src="https://libraryofdefense.ocdla.org/skins/lod/images/masthead-email.gif" title="Library of Defense header" />
		</td>
	</tr>

	<tr>
		<td colspan="3">
			<h2>Website Issue/Suggestion</h2>
		</td>
	</tr>
	<?php
	foreach($vars as $name=>$value): 
		$value = empty($value) ? '<span style="font-style:italic;">No value provided</span>' : $value;
	?>
	<tr>
			<td width="25%"><?php print $name; ?></td>
			<td wdith="70%" style="padding-left:15px;"><?php print $value; ?></td>		
			<td>&nbsp;</td>
	</tr>
	<?php endforeach; ?>
	<tr>
		<td colspan="3" style="height:50px;">
		&nbsp;
		</td>
	</tr>
	<tr>
		<td>
			<a title="Ocdla:Privacy policy" href="https://libraryofdefense.ocdla.org/Ocdla:Privacy_policy">Privacy policy</a>
		</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>


</table>