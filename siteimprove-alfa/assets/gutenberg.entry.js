import { SinglePageReporting } from '@siteimprove/accessibility-cms-components';

(function (wp) {
	const {
		plugins: { registerPlugin },
		editor: { PluginDocumentSettingPanel, PluginPrePublishPanel },
		element,
		data: { useSelect },
		apiFetch,
		components: { Button, Modal },
		url: { addQueryArgs },
	} = wp;

	const { useState, useEffect, RawHTML } = element;
	const { __ } = wp.i18n;

	const SiteimproveAlfaScanResult = () => {
		const [data, setData] = useState(null);
		const post = useSelect(
			(select) => select('core/editor').getCurrentPost(),
			[]
		);

		useEffect(() => {
			if (post && post.id) {
				apiFetch({ path: `/siteimprove-alfa/scan-result/${post.id}` })
					.then((response) => {
						setData({ failedItems: response });
					})
					.catch(() => {
						setData(null);
					});
			}
		}, [post]);

		return (
			<>
				<PluginDocumentSettingPanel
					name="siteimprove-alfa-scan-result-panel"
					title={__('Siteimprove Alfa', 'siteimprove-alfa')}
					className="siteimprove-alfa-scan-result-panel"
				>
					<SinglePageReporting data={data} />

					<AccessibilityCheckModal post={post} />
				</PluginDocumentSettingPanel>

				<PluginPrePublishPanel>
					<p>
						<RawHTML>
							{__(
								'<strong>Siteimprove:</strong> Accessibility Check',
								'siteimprove-alfa'
							)}
						</RawHTML>
					</p>

					<AccessibilityCheckModal post={post} />
				</PluginPrePublishPanel>
			</>
		);
	};

	const AccessibilityCheckModal = ({ post }) => {
		const [isModalOpen, setModalOpen] = useState(false);

		const handleActionConfirm = () => {
			if (post && post.link) {
				window.location = addQueryArgs(post.link, {
					preview: 'true',
					'siteimprove-auto-check': 'true',
				});
			}
			setModalOpen(false);
		};

		const handleActionCancel = () => {
			setModalOpen(false);
		};

		return (
			<>
				<Button isPrimary onClick={() => setModalOpen(true)}>
					{__('Run Accessibility Check', 'siteimprove-alfa')}
				</Button>
				{isModalOpen && (
					<Modal
						title={__(
							'Siteimprove Accessibility Check',
							'siteimprove-alfa'
						)}
						onRequestClose={handleActionCancel}
					>
						<p>
							<RawHTML>
								{__(
									'<strong>Please wait while we work on your request!</strong><br/>' +
										'You will be redirected to the preview page where the accessibility check will run automatically.<br/>' +
										"Once it's done you will be redirected back to this page.",
									'siteimprove-alfa'
								)}
							</RawHTML>
						</p>
						<div style={{ textAlign: 'center' }}>
							<Button
								isSecondary
								onClick={handleActionCancel}
								style={{ margin: '5px' }}
							>
								{__('Cancel', 'siteimprove-alfa')}
							</Button>
							<Button
								isPrimary
								onClick={handleActionConfirm}
								style={{ margin: '5px' }}
							>
								{__('Confirm', 'siteimprove-alfa')}
							</Button>
						</div>
					</Modal>
				)}
			</>
		);
	};

	registerPlugin('siteimprove-alfa-scan-result', {
		render: SiteimproveAlfaScanResult,
		icon: 'admin-generic',
	});
})(window.wp);
