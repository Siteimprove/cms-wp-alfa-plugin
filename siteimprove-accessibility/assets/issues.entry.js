/* global jQuery, SiteimproveAccessibilityCmsComponents */

(async function ($) {
	const pagesWithIssuesCallback = async function (params) {
		const queryString = $.param(params);
		return await wp.apiFetch({
			path: '/siteimprove-accessibility/pages-with-issues?' + queryString,
			method: 'GET',
		});
	};

	const issues = await wp.apiFetch({
		path: '/siteimprove-accessibility/issues',
	});

	if (issues.length) {
		SiteimproveAccessibilityCmsComponents.renderScanIssueReporting(
			issues,
			pagesWithIssuesCallback,
			'siteimprove-scan-report'
		);
	} else {
		$('.siteimprove-component-placeholder').hide();
		$('.siteimprove-empty-issues-container').show();
	}
})(jQuery);
