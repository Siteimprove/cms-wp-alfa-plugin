import {renderScanIssueReporting} from '@siteimprove/accessibility-cms-components';

const issues = await wp.apiFetch({path: '/siteimprove-alfa/issues'});

const pagesWithIssuesCallback = async function (params) {
	const queryString = jQuery.param(params);
	return await wp.apiFetch({
		path: '/siteimprove-alfa/pages-with-issues?' + queryString,
		method: 'GET'
	});
}

renderScanIssueReporting(issues, pagesWithIssuesCallback, 'siteimprove-scan-report');
