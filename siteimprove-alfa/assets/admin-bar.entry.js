import { JQuery as AlfaJQuery } from '@siteimprove/alfa-jquery';
import { Audit as AlfaAudit } from '@siteimprove/alfa-act/dist/audit';
import rules from '@siteimprove/alfa-rules';
import * as alfaJson from '@siteimprove/alfa-json';
import { getRuleMeta } from '@siteimprove/accessibility-cms-components/src/helpers/transformAuditResults';

/* global siteimproveAlfaSaveScanData, jQuery */

(function ($) {
	'use strict';

	const { __, sprintf } = wp.i18n;

	$(document).on('ready', function () {
		bindClickEvent();
	});

	function bindClickEvent() {
		$('#wp-admin-bar-stim-alfa-check-accessibility a').one(
			'click',
			function (e) {
				e.preventDefault();

				const $this = $(this);
				const $label = $this.find('.label');
				$label.html(__('Checking Accessibility…', 'siteimprove-alfa'));

				accessibilityCheck()
					.then((response) => {
						if (response.count_issues > 0) {
							$label.html(
								sprintf(
									/* translators: %d: Number of issues found */
									__('%d issues found!', 'siteimprove-alfa'),
									response.count_issues
								)
							);
						} else {
							$label.html(
								__('No issues found!', 'siteimprove-alfa')
							);
						}
						$this.prop(
							'href',
							siteimproveAlfaSaveScanData.view_link
						);
					})
					.catch((error) => {
						// eslint-disable-next-line no-console
						console.error(error);
						$label.html(
							__('Accessibility check failed', 'siteimprove-alfa')
						);
						bindClickEvent();
					});
			}
		);
	}

	/**
	 * @return {Promise<(function(Object): {type: string, request: Object})|*>}  Processed audit scan object.
	 */
	async function accessibilityCheck() {
		const htmlDom = $('html').clone().find('#wpadminbar').remove().end(); // get DOM and remove the admin bar
		const alfaPage = await AlfaJQuery.toPage(htmlDom);

		return evaluatePage(alfaPage).then((auditScan) => {
			return wp.apiFetch({
				path: '/siteimprove-alfa/save-scan',
				method: 'POST',
				data: auditScan,
			});
		});
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
			url: !postId ? window.location.href : null,
			scan_results: [],
			scan_stats: {},
		};

		outcomes.forEach((outcome) => {
			if (outcome._outcome === 'failed') {
				// process outcome stat
				const rule = outcome.rule.uri.split('/').pop();
				const conformance = getRuleMeta(rule).conformance;
				auditScan.scan_stats[rule] = auditScan.scan_stats[rule] || {};
				auditScan.scan_stats[rule][conformance] =
					(auditScan.scan_stats[rule][conformance] || 0) + 1;

				// process outcome result
				auditScan.scan_results.push(
					outcome.toJSON({
						verbosity: alfaJson.Serializable.Verbosity.Low,
					})
				);
			}
		});

		return auditScan;
	}
})(jQuery);
