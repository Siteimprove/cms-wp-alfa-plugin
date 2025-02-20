import { SinglePageReporting } from "@siteimprove/accessibility-cms-components";

(function(wp) {
    const {
        plugins: { registerPlugin },
        editor: { PluginDocumentSettingPanel },
        element,
        data: { useSelect },
        apiFetch
    } = wp;

    const { useState, useEffect } = element;

    const SiteimproveAlfaScanResult = () => {
        const [data, setData] = useState(null);
        const postId = useSelect(
            (select) => select('core/editor').getCurrentPostId(),
            []
        );

        useEffect(() => {
            if (postId) {
                apiFetch({ path: `/siteimprove-alfa/scan-result/${postId}` })
                    .then((res) => {
                        setData(res);
                    })
                    .catch((error) => {
                        console.error('Error fetching data:', error);
                        setData(null);
                    });
            }
        }, [postId]);

        return (
            <PluginDocumentSettingPanel
                name="siteimprove-alfa-scan-result-panel"
                title="Siteimprove Alfa"
                className="siteimprove-alfa-scan-result-panel"
            >
                <SinglePageReporting data={data} />
            </PluginDocumentSettingPanel>
        );
    };

    registerPlugin('siteimprove-alfa-scan-result', {
        render: SiteimproveAlfaScanResult,
        icon: 'admin-generic',
    });
})(window.wp);
