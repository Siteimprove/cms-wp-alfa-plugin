import {JQuery as AlfaJQuery} from "@siteimprove/alfa-jquery";
import {Audit as AlfaAudit} from "@siteimprove/alfa-act/dist/audit";
import rules from "@siteimprove/alfa-rules";
import * as alfaJson from "@siteimprove/alfa-json";

(function( $ ) {
    'use strict';

    const { __ } = wp.i18n;

    $(document).on('ready', function() {
        bind_click_event();
    });

    /**
     * @returns void
     */
    function bind_click_event() {
        $('#wp-admin-bar-stim-alfa-check-accessibility a').one('click', function(e) {
            e.preventDefault();

            var $this = $(this);
            $this.find('.label').html(__('Checking Accessibility...', 'siteimprove-alfa'));

            accessibility_check()
                .then(() => {
                    $this.find('.label').html(__('Accessibility results saved', 'siteimprove-alfa'));
                    $this.prop('href', ( siteimprove_alfa_save_scan_data.view_link || '/wp-admin/admin.php?page=stim_alfa' ));
                })
                .catch((error) => {
                    console.error('Error during Alfa audit evaluation:', error);
                    $this.find('.label').html(__('Accessibility check failed', 'siteimprove-alfa'));
                    bind_click_event();
                });
        });
    }

    /**
     * @returns {Promise<(function(Object): {type: string, request: Object})|*>}
     */
    async function accessibility_check() {
        const html_dom = $('html').clone().find('#wpadminbar').remove().end(); // get DOM and remove the admin bar
        const alfa_page = await AlfaJQuery.toPage(html_dom);

        return evaluate_page(alfa_page).then((audit_scan) => {
            return wp.apiFetch({
                path: '/siteimprove-alfa/save-scan',
                method: 'POST',
                data: audit_scan,
            });
        });
    }

    /**
     * @param alfa_page
     * @returns {Promise<(function(Object): {type: string, request: Object})|*>}
     */
    async function evaluate_page(alfa_page) {
        const outcomes = await AlfaAudit.of(alfa_page, rules).evaluate();

        return process_audit_scan(outcomes, siteimprove_alfa_save_scan_data.post_id);
    }

    /**
     * @param outcomes
     * @param post_id
     * @returns {{post_id, scan_results: *[], scan_stats: {}}}
     */
    function process_audit_scan(outcomes, post_id) {
        let audit_scan = {
            post_id: post_id,
            scan_results: [],
            scan_stats: {}
        };

        outcomes.forEach((outcome) => {
            if (outcome._outcome === 'failed') {
                // process outcome stat
                const rule = outcome.rule.uri.split('/').pop();
                audit_scan.scan_stats[rule] = audit_scan.scan_stats[rule] || {};
                outcome.rule.requirements.forEach((requirement) => {
                    if (requirement.level) {
                        requirement.level._values.forEach((level) => {
                            audit_scan.scan_stats[rule][level.value] = (audit_scan.scan_stats[rule][level.value] || 0) + 1;
                        })
                    }
                });

                // process outcome result
                audit_scan.scan_results.push(outcome.toJSON({
                    verbosity: alfaJson.Serializable.Verbosity.Low
                }));
            }
        });

        return audit_scan;
    }
})( jQuery );
