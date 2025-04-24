/* global SiteimproveAccessibilityCmsComponents */

wp.apiFetch({ path: '/siteimprove-accessibility/daily-stats' }).then(
	(stats) => {
		SiteimproveAccessibilityCmsComponents.renderComplianceDashboard(
			stats,
			'siteimprove-daily-stats'
		);
	}
);
