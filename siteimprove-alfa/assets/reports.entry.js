import { renderComplianceDashboard } from '@siteimprove/accessibility-cms-components';

wp.apiFetch({ path: '/siteimprove-alfa/daily-stats' }).then((stats) => {
	renderComplianceDashboard(stats, 'siteimprove-daily-stats');
});
