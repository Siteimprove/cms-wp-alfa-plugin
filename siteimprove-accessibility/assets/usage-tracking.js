/* global jQuery, siteimproveUsageTrackingInitParams */
(function (apiKey,$) {
	(function (p,e,n,d,o){p[o]=p[o] || function (){(p[o].q=p[o].q || []).push(arguments);};const t=e.createElement(n),s=e.getElementsByTagName(n)[0];t.async=1;t.src=d;s.parentNode.insertBefore(t,s);})(window,document,'script','https://cdn.pendo.io/agent/static/'+apiKey+'/pendo.js','pendo');
	/* global pendo */
	window.addEventListener('load', function () {
		if (typeof pendo === 'undefined') return;
		const defaultParams = {apiKey, visitor: {id: 'WORDPRESS-ANONYMOUS-ID'}, account: {id: '017065616813'}, disableGuides: true};
		pendo.initialize($.extend(true, defaultParams, siteimproveUsageTrackingInitParams));
	});
})('730de0a8-013b-42b8-58b9-5c52d8158b20', jQuery);
