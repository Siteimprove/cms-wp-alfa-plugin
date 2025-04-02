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

	const SiteimproveAccessibilityScanResult = () => {
		const [data, setData] = useState(null);
		const post = useSelect(
			(select) => select('core/editor').getCurrentPost(),
			[]
		);

		useEffect(() => {
			if (post && post.id) {
				apiFetch({
					path: `/siteimprove-accessibility/scan-result/${post.id}`,
				})
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
					name="siteimprove-accessibility-scan-result-panel"
					title={__(
						'Siteimprove Accessibility',
						'siteimprove-accessibility'
					)}
					className="siteimprove-accessibility-scan-result-panel"
				>
					<AccessibilityCheckModal post={post} data={data} />

					<SinglePageReporting data={data} />
				</PluginDocumentSettingPanel>

				<PluginPrePublishPanel>
					<p>
						<RawHTML>
							{__(
								'<strong>Siteimprove:</strong> Accessibility Check',
								'siteimprove-accessibility'
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
						data-observe-key="a11y-WordPress-EditorModalOpenButton"
					>
						<span>
							{__('Check page', 'siteimprove-accessibility')}
						</span>
					</Button>
					{data?.date &&
						sprintf(
							// translators: %s: date of last scan.
							__('Last checked: %s', 'siteimprove-accessibility'),
							data?.date
						)}
				</div>
				{isModalOpen && (
					<Modal
						title={__(
							'Siteimprove Accessibility Check',
							'siteimprove-accessibility'
						)}
						onRequestClose={handleActionCancel}
					>
						<p>
							<RawHTML>
								{__(
									'You will be redirected to the preview page where the accessibility check will run automatically.',
									'siteimprove-accessibility'
								)}
							</RawHTML>
						</p>
						<div style={{ textAlign: 'center' }}>
							<Button
								isSecondary
								onClick={handleActionCancel}
								style={{ margin: '5px' }}
								data-observe-key="a11y-WordPress-EditorModalCancelButton"
							>
								{__('Cancel', 'siteimprove-accessibility')}
							</Button>
							<Button
								isPrimary
								onClick={handleActionConfirm}
								style={{ margin: '5px' }}
								data-observe-key="a11y-WordPress-EditorModalConfirmButton"
							>
								{__('Confirm', 'siteimprove-accessibility')}
							</Button>
						</div>
					</Modal>
				)}
			</>
		);
	};

	registerPlugin('siteimprove-accessibility-scan-result', {
		render: SiteimproveAccessibilityScanResult,
		icon: 'admin-generic',
	});
})(window.wp);
