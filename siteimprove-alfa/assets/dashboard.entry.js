import { renderComplianceDashboard } from "@siteimprove/accessibility-cms-components";

const stats = await wp.apiFetch({path: '/siteimprove-alfa/daily-stats'});

renderComplianceDashboard(stats, 'siteimprove-alfa-dashboard-container');
