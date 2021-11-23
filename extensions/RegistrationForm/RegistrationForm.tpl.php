<?php
$replacements['issue'] = 'report';
?>
<div class='site-contact-form'>
	<!--<div>
		<img class="contact-form-button" width="46" alt="Make a suggestion." title="Make a suggestion." src="/extensions/ContactForm/images/suggest-icon.png" />
		<img class="contact-form-button" width="65" alt="Submit an issue." title="Tell us something isn't working." src="/extensions/ContactForm/images/issue-icon.png" />
	</div>
	-->
	<h2 class='title'><?php print $title; ?></h2>
	<form method="post" id="ocdla-contact-form">
	<?php if($formType=='suggest'): ?>
		<div class='form-text-item' id="ocdla-contact-form-explanation">
			Help us improve the Library of Defense.  Have a suggestion for articles or features that we don't yet have?<br />We'd like to hear from you.
		</div>
	<?php else: ?>
		<div class='form-text-item' id="ocdla-contact-form-explanation">
			Help us improve the Library of Defense.  Let us know if you experienced an issue with the website.
		</div>	
	<?php endif; ?>
	<div>
		<input id="formType" name="formType" type='hidden' value='<?php print $formType; ?>' />
		<input id="formAction" name="formAction" type='hidden' value='sendEmail' />
	</div>
	<?php if($formType=='suggest'): ?>	
			<fieldset>
				<legend>Suggestion</legend>
				<div class='form-item'>
					<label for="issueCat">
						Suggestion category:
					</label>
					<select id="issueCat" name="issueCat">
						<option value="brokenLink">Usability</option>
						<option value="missingPage">Search</option>
						<option value="adminIssue">I'd like to edit</option>	
						<option value="adminIssue">Mobile</option>	
					</select>
				</div>
				<div class='form-item'>
					<label for="theDesc">
						More information:
					</label>
					<textarea id="issueDesc" name="issueDesc" rows="4" cols="15" placeholder="Provide us some more information so we can improve the Library of Defense."></textarea>
				</div>
		</fieldset>
	
	<?php else: ?>
			<fieldset>
				<legend>Issue Info</legend>
				<div class='form-item'>
					<label for="issueCat">
						Type of Issue:
					</label>
					<select id="issueCat" name="issueCat">
						<option value="generalIssue">General Website Issue</option>
						<option value="brokenLink">Broken Link</option>
						<option value="missingPage">Missing Page</option>
						<option value="badSearch">Missing Search Results</option>
						<!--<option value="editCat" disabled="disabled">Editing</option>
							<option value="editEdit">-Couldn't edit a page</option>
							<option value="editSave">-Couldn't save a page</option>			
							-->
					</select>
				</div>
				<div class='form-item'>
					<label for="theDesc">
						Description of this issue:
					</label>
					<textarea id="issueDesc" name="issueDesc" rows="4" cols="15" placeholder="Describe the issue."></textarea>
				</div>
		</fieldset>
	<?php endif; ?>
		<fieldset>
			<legend>Signature</legend>
			<?php if($formType=='suggest'): ?>
				<div class='form-text-item' id="ocdla-contact-form-disclaimer">
					By default, your suggestion will be submitted anonymously, without any personal-identifying information.
				</div>
			<?php else: ?>
				<div class='form-text-item' id="ocdla-contact-form-disclaimer">
					By default, your report will be submitted anonymously, without any personal-identifying information.
				</div>
			<?php endif; ?>
		<div class='form-item'>
			<input type="checkbox" name="signMe" id="signMe" value="1" />Add my signature to this suggestion.
		</div>
		</fieldset>

<?php if($user->isAdmin): ?>	
		<fieldset>
			<legend class='trigger' onclick="return ocdlaContact.collapse('contactFormPreview');">Preview</legend>
			<div id="mw-customcollapsible-contactFormPreview" class="mw-collapsible mw-collapsed">
				<span style="font-style:italic;">
					You're seeing this section because you're a site administrator.  Click the <em>preview<em> button, below, to see how this email would look if it were actually submitted.
				</span>
				<button id="preview-button" onclick="return ocdlaContact.getPreview();">Preview email</button>
				<div class='form-item' id="ocdla-contact-form-preview">
				
				</div>
			</div>	
		</fieldset>
<?php endif; ?>
		<div class='form-item'>
			<input type="submit" value="Send" />
			<!--<input type="submit" value="Cancel" />-->
		</div>
		
		
</div>
</form>
	
	
	
</div>