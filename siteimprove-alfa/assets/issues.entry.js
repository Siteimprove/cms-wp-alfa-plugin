import {renderScanIssueReporting} from '@siteimprove/accessibility-cms-components';

/*const pagesWithIssuesCallback = async function (params) {
	return await wp.apiFetch({
		path: '/siteimprove-alfa/pages-with-issues',
		data: params,
	});
}*/

Promise.all([
	wp.apiFetch({path: '/siteimprove-alfa/issues'}),
	wp.apiFetch({path: '/siteimprove-alfa/pages-with-issues'})
]).then(([issues, pages]) => {
	renderScanIssueReporting({issues: issues, pages: pages}, 'siteimprove-scan-report')
});
