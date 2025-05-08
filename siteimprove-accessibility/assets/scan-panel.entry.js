import { Audit as AlfaAudit } from '@siteimprove/alfa-act/dist/audit';
import { JQuery as AlfaJQuery } from '@siteimprove/alfa-jquery';
import * as AlfaJson from '@siteimprove/alfa-json';
import AlfaRules from '@siteimprove/alfa-rules';
import { Rules as AlfaRuleFilter } from '@siteimprove/alfa-test-utils';

/* global siteimproveAccessibilityScan, jQuery, SiteimproveAccessibilityCmsComponents, requestAnimationFrame */

(function ($) {
	'use strict';

	const { __ } = wp.i18n;
	let isPageScanned = false;

	$(window).on('load', function () {
		$('.siteimprove-scan-button').on('click', onScanClick);

		$('#siteimprove-scan-panel-button.visible').show();
		$('#siteimprove-scan-panel-button, #siteimprove-scan-hide').on(
			'click',
			function () {
				$('#siteimprove-scan-panel').toggle();
				if (!isPageScanned) {
					isPageScanned = true;
					$('.siteimprove-scan-button').trigger('click');
				}
			}
		);

		if (siteimproveAccessibilityScan.auto_check) {
			$('#siteimprove-scan-panel-button').trigger('click');
		}
	});

	const onScanClick = function () {
		requestAnimationFrame(() => {
			const $this = $(this);
			const $label = $this.find('span');
			$label.html(__('Checking page…', 'siteimprove-accessibility'));
			$this.attr('disabled', 'disabled');

			requestAnimationFrame(() =>
				accessibilityCheck().then((auditScan) => {
					wp.apiFetch({
						path: '/siteimprove-accessibility/save-scan',
						method: 'POST',
						data: auditScan,
					})
						.then(() => {
							SiteimproveAccessibilityCmsComponents.renderSinglePageReporting(
								{ failedItems: auditScan.scan_results },
								'siteimprove-scan-results'
							);
						})
						.finally(() => {
							$label.html(
								__('Check page', 'siteimprove-accessibility')
							);
							$this.removeAttr('disabled');
						});
				})
			);
		});
	};

	/**
	 * @return {Promise<(function(Object): {type: string, request: Object})|*>}  Processed audit scan object.
	 */
	async function accessibilityCheck() {
		// clone the DOM and remove the admin bar and the scan panel
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
		const customRules = getCustomRules();
		const alfaResult = await AlfaAudit.of(alfaPage, customRules).evaluate();

		return processAuditScan(
			alfaResult,
			siteimproveAccessibilityScan.post_id
		);
	}

	/**
	 * This method is responsible to define which rules to use for the evaluation.
	 *
	 * @return {Object} List of rules to run the evaluation with.
	 */
	function getCustomRules() {
		// Currently we only use AA and A conformance level rules.
		return AlfaRules.filter((rule) => AlfaRuleFilter.aaFilter(rule));
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
				const conformance =
					SiteimproveAccessibilityCmsComponents.getRuleMeta(
						rule
					).conformance;
				auditScan.scan_stats[rule] = auditScan.scan_stats[rule] || {
					conformance,
					occurrence: 0,
				};
				auditScan.scan_stats[rule].occurrence += 1;

				// process outcome result
				auditScan.scan_results.push(
					outcome.toJSON({
						verbosity: AlfaJson.Serializable.Verbosity.Low,
					})
				);
			}
		}

		return auditScan;
	}
})(jQuery);
