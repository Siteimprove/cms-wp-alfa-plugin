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
	const { __, sprintf } = wp.i18n;

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
						setData(response);
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
					title={__('Siteimprove Accessibility', 'siteimprove-alfa')}
					className="siteimprove-alfa-scan-result-panel"
				>
					<AccessibilityCheckModal post={post} data={data} />

					<SinglePageReporting data={data} />
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

	const AccessibilityCheckModal = ({ post, data }) => {
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
				<div className="siteimprove-scan-button-container">
					<Button
						className="siteimprove-scan-button"
						onClick={() => setModalOpen(true)}
					>
						<span>{__('Check page', 'siteimprove-alfa')}</span>
					</Button>
					{data?.date &&
						sprintf(
							// translators: %s: date of last scan.
							__('Last checked: %s', 'siteimprove-alfa'),
							data?.date
						)}
				</div>
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
									'You will be redirected to the preview page where the accessibility check will run automatically.',
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
