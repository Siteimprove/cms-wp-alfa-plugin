import { JQuery as AlfaJQuery } from '@siteimprove/alfa-jquery';
import { Audit as AlfaAudit } from '@siteimprove/alfa-act/dist/audit';
import rules from '@siteimprove/alfa-rules';
import * as alfaJson from '@siteimprove/alfa-json';
import { getRuleMeta } from '@siteimprove/accessibility-cms-components/src/helpers/transformAuditResults';
import { renderSinglePageReporting } from '@siteimprove/accessibility-cms-components';

/* global siteimproveAlfaSaveScanData, jQuery, requestAnimationFrame */

(function ($) {
	'use strict';

	const { __ } = wp.i18n;
	const urlParams = new URLSearchParams(window.location.search);
	const isAutoCheckEnabled =
		urlParams.has('siteimprove-auto-check') &&
		urlParams.get('siteimprove-auto-check') === 'true';
	let isPageScanned = false;

	$(window).on('load', function () {
		$('.siteimprove-scan-button').on('click', onScanClick);

		$('#siteimprove-scan-panel-button, #siteimprove-scan-hide')
			.show()
			.on('click', function () {
				$('#siteimprove-scan-panel').toggle();
				if (!isPageScanned) {
					isPageScanned = true;
					$('.siteimprove-scan-button').trigger('click');
				}
			});

		if (isAutoCheckEnabled) {
			$('#siteimprove-scan-panel-button').trigger('click');
		}
	});

	const onScanClick = function () {
		requestAnimationFrame(() => {
			const $this = $(this);
			const $label = $this.find('span');
			$label.html(__('Checking page…', 'siteimprove-alfa'));
			$this.attr('disabled', 'disabled');

			requestAnimationFrame(() =>
				accessibilityCheck()
					.then((auditScan) => {
						wp.apiFetch({
							path: '/siteimprove-alfa/save-scan',
							method: 'POST',
							data: auditScan,
						}).then((response) => {
							if (response.count_issues > 0) {
								renderSinglePageReporting(
									{ failedItems: auditScan.scan_results },
									'siteimprove-scan-results'
								);
							} else {
								$('#siteimprove-scan-results').html(
									__('No issues found!', 'siteimprove-alfa')
								);
							}

							$label.html(__('Check page', 'siteimprove-alfa'));
							$this.removeAttr('disabled');
						});
					})
					.catch((error) => {
						// eslint-disable-next-line no-console
						console.error(error);
						$label.html(
							__('Page check failed!', 'siteimprove-alfa')
						);
						$this.removeAttr('disabled');
					})
			);
		});
	};

	/**
	 * @return {Promise<(function(Object): {type: string, request: Object})|*>}  Processed audit scan object.
	 */
	async function accessibilityCheck() {
		// clode the DOM and remove the admin bar and the scan panel
		const htmlDom = $('html')
			.clone()
			.find('#wpadminbar, .siteimprove-component')
			.remove()
			.end();

		const alfaPage = await AlfaJQuery.toPage(htmlDom);

		return await evaluatePage(alfaPage);
	}

	/**
	 * @param {Promise} alfaPage
	 * @return {Promise<(function(Object): {type: string, request: Object})|*>}  Processed audit scan object.
	 */
	async function evaluatePage(alfaPage) {
		const outcomes = await AlfaAudit.of(alfaPage, rules).evaluate();

		return processAuditScan(outcomes, siteimproveAlfaSaveScanData.post_id);
	}

	/**
	 * @param {Iterable} outcomes
	 * @param {number}   postId
	 * @return {{post_id, scan_results: *[], scan_stats: {}}} Processed autid scan object.
	 */
	function processAuditScan(outcomes, postId) {
		const auditScan = {
			post_id: postId,
			url: window.location.href,
			title: document.title,
			scan_results: [],
			scan_stats: {},
		};

		for (const outcome of outcomes) {
			if (outcome._outcome === 'failed') {
				// process outcome stat
				const rule = outcome.rule.uri.split('/').pop();
				const conformance = getRuleMeta(rule).conformance;
				auditScan.scan_stats[rule] = auditScan.scan_stats[rule] || {conformance: conformance, occurrence: 0};
				auditScan.scan_stats[rule].occurrence += 1;

				// process outcome result
				auditScan.scan_results.push(
					outcome.toJSON({
						verbosity: alfaJson.Serializable.Verbosity.Low,
					})
				);
			}
		}

		return auditScan;
	}
})(jQuery);
