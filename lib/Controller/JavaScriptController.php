<?php

// SPDX-FileCopyrightText: 2024 2024 STRATO AG
//
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\NCGoogleAnalytics\Controller;

use OCA\NCGoogleAnalytics\Config;
use OCA\NCGoogleAnalytics\Service\Consent\IConsentService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\Attribute\NoCSRFRequired;
use OCP\AppFramework\Http\Attribute\PublicPage;
use OCP\AppFramework\Http\DataDownloadResponse;
use OCP\AppFramework\Http\TextPlainResponse;
use OCP\IRequest;

class JavaScriptController extends Controller {
	/**
	 * constructor of the controller
	 *
	 * @param string $appName
	 * @param IRequest $request
	 * @param Config $config
	 * @param IConsentService $consentService
	 */
	public function __construct(
		$appName,
		IRequest $request,
		protected Config $config,
		protected IConsentService $consentService,
	) {
		parent::__construct($appName, $request);
	}

	/**
	 * @return TextPlainResponse|DataDownloadResponse
	 */
	#[NoAdminRequired]
	#[NoCSRFRequired]
	#[PublicPage]
	public function tracking(): TextPlainResponse|DataDownloadResponse {
		$gtmId = $this->config->getTrackingKey();

		if (!isset($gtmId)) {
			$response = new TextPlainResponse('// tracking disabled');
			$response->addHeader('Content-Type', 'text/javascript');
			return $response;
		}

		try {
			$consent = $this->consentService->getConsent($_COOKIE);
			if (!$consent->hasStatisticsConsent()) {
				throw new \Exception('no statistics consent');
			}
		} catch (\Exception $e) {
			$response = new TextPlainResponse('// tracking disabled: no statistics consent');
			$response->addHeader('Content-Type', 'text/javascript');
			return $response;
		}

		$script = file_get_contents(__DIR__ . '/../../js/track.js');
		$script = str_replace('%GTM_ID%', $gtmId, $script);

		return new DataDownloadResponse($script, 'gtm.js', 'text/javascript');
	}
}
