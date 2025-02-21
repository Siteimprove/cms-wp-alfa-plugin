import { JQuery as AlfaJQuery } from "@siteimprove/alfa-jquery";
import {Audit as AlfaAudit} from "@siteimprove/alfa-act/dist/audit";
import rules from "@siteimprove/alfa-rules";
import * as alfaJson from "@siteimprove/alfa-json";

(function( $ ) {
    'use strict';

    const { __ } = wp.i18n;

    $(document).ready(function() {
        bindClickEvent();
    });

    function bindClickEvent() {
        $('#wp-admin-bar-stim-alfa-check-accessibility a').one('click', function(e) {
            e.preventDefault();

            var $this = $(this);
            $this.find('.label').html(__('Checking Accessibility...', 'siteimprove-alfa'));

            evaluatePageWithAlfa()
                .then((result) => {
                    $this.find('.label').html(__('Accessibility results saved', 'siteimprove-alfa'));
                    $this.prop('href', ( siteimproveAlfaSaveScanResultAjax.view_link || '/wp-admin/admin.php?page=stim_alfa' ));
                })
                .catch((error) => {
                    console.error('Error during Alfa audit evaluation:', error);
                    $this.find('.label').html(__('Accessibility check failed', 'siteimprove-alfa'));
                    bindClickEvent();
                });
        });
    }

    async function evaluatePageWithAlfa() {
        const value = $('html').clone().find('#wpadminbar').remove().end(); // get DOM and remove the admin bar
        const alfaPage = await AlfaJQuery.toPage(value);

        return evaluateAndSave(alfaPage);
    }

    // TODO: change ajax to REST API
    async function evaluateAndSave(alfaPage) {
        const outcomes = await AlfaAudit.of(alfaPage, rules).evaluate();
        const auditScan = prepareAuditScan(outcomes);

        var dataToSend = {
            action: 'save_scan_result',
            security: siteimproveAlfaSaveScanResultAjax.security,
            post_id: siteimproveAlfaSaveScanResultAjax.post_id,
            data: JSON.stringify(auditScan),
        };

        return $.post(siteimproveAlfaSaveScanResultAjax.ajax_url, dataToSend, function(response) {
            return response;
        });
    }

    function prepareAuditScan(outcomes) {
        let auditScan = {
            scan_results: [],
            scan_stats: {}
        };

        outcomes.forEach((outcome) => {
            if (outcome._outcome === 'failed') {

                // TODO: refactor
                const rule = outcome.rule.uri.split('/').pop();
                auditScan.scan_stats[rule] = auditScan.scan_stats[rule] || {};
                outcome.rule.requirements.forEach((requirement) => {
                    if (requirement.level) {
                        requirement.level._values.forEach((level) => {
                            auditScan.scan_stats[rule][level.value] = (auditScan.scan_stats[rule][level.value] || 0) + 1;
                        })
                    }
                });

                auditScan.scan_results.push(outcome.toJSON({
                    verbosity: alfaJson.Serializable.Verbosity.Low
                }));
            }
        });

        return auditScan;
    }
})( jQuery );
