import { renderComplianceDashboard } from '@siteimprove/accessibility-cms-components';

wp.apiFetch({ path: '/siteimprove-alfa/daily-stats' }).then((stats) => {
	renderComplianceDashboard(stats, 'siteimprove-daily-stats');
});

wp.apiFetch({ path: '/siteimprove-alfa/issues' }).then((results) => {
	// eslint-disable-next-line no-console
	console.log('Issues', results);
	// TODO: render issues
});

wp.apiFetch({ path: '/siteimprove-alfa/pages-with-issues' }).then((results) => {
	// eslint-disable-next-line no-console
	console.log('Pages With Issues', results);
	// TODO: render pages with issues
});
