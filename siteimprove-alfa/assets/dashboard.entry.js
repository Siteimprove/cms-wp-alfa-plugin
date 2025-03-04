import {renderComplianceDashboard, renderScanIssueReporting} from '@siteimprove/accessibility-cms-components';

wp.apiFetch({ path: '/siteimprove-alfa/daily-stats' }).then((stats) => {
	renderComplianceDashboard(stats, 'siteimprove-daily-stats');
});

/*const pagesWithIssuesCallback = async function (params) {
	return await wp.apiFetch({
		path: '/siteimprove-alfa/pages-with-issues',
		data: params,
	});
}*/

Promise.all([
	wp.apiFetch({ path: '/siteimprove-alfa/issues' }),
	wp.apiFetch({ path: '/siteimprove-alfa/pages-with-issues' })
]).then(([issues, pages]) => {
	renderScanIssueReporting(issues, pages/*WithIssuesCallback*/, 'siteimprove-scan-report')
})
