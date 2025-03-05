<fieldset>
	<label>
		<input type="checkbox" name="rule" value="aaFilter" checked>
		<?php echo __('Filter matching the AA conformance rules, i.e. rules for level A and AA success criteria.', 'siteimprove_accessibility'); ?>
	</label><br/>
	<label>
		<input type="checkbox" name="rule" value="wcag20Filter">
		<?php echo __('Filter matching the WCAG 2.0 rules.', 'siteimprove_accessibility'); ?>
	</label><br/>
	<label>
		<input type="checkbox" name="rule" value="wcag20aaFilter">
		<?php echo __('Filter matching the WCAG 2.0 AA conformance rules.', 'siteimprove_accessibility'); ?>
	</label><br/>
	<label>
		<input type="checkbox" name="rule" value="wcag21aaFilter">
		<?php echo __('Filter matching the WCAG 2.1 AA conformance rules.', 'siteimprove_accessibility'); ?>
	</label><br/>
	<label>
		<input type="checkbox" name="rule" value="ARIAFilter">
		<?php echo __('Filter matching the rules that check for ARIA conformance', 'siteimprove_accessibility'); ?>
	</label><br/>
	<label>
		<input type="checkbox" name="rule" value="bestPracticesFilter">
		<?php echo __('Filter matching Best Practice rules.', 'siteimprove_accessibility'); ?>
	</label>
</fieldset>