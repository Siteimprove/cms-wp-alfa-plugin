import { renderComplianceDashboard } from '@siteimprove/accessibility-cms-components';

wp.apiFetch({ path: '/siteimprove-accessibility/daily-stats' }).then(
	(stats) => {
		renderComplianceDashboard(stats, 'siteimprove-daily-stats');
	}
);
