import { renderComplianceDashboard } from '@siteimprove/accessibility-cms-components';

wp.apiFetch({ path: '/siteimprove-alfa/daily-stats' }).then(stats => {
    renderComplianceDashboard(stats, 'siteimprove-alfa-dashboard-container');
});

wp.apiFetch({ path: '/siteimprove-alfa/issues' }).then(results => {
    console.log('Issues', results);
    // TODO: render issues
})

wp.apiFetch({ path: '/siteimprove-alfa/pages-with-issues' }).then(results => {
    console.log('Pages With Issues', results);
    // TODO: render pages with issues
})