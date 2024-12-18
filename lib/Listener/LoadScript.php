<?php

/**
 * SPDX-FileLicenseText: 2024 STRATO AG
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\NCGoogleAnalytics\Listener;

use OC\Security\CSP\ContentSecurityPolicyNonceManager;
use OCA\NCGoogleAnalytics\Service\ConsentDetection;
use OCP\AppFramework\Http\Events\BeforeTemplateRenderedEvent;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\IURLGenerator;
use OCP\Util;

/**
 * Inject tracking script
 */
class LoadScript implements IEventListener {
	public function __construct(
		private IURLGenerator $urlGenerator,
		private ContentSecurityPolicyNonceManager $nonceManager,
		private ConsentDetection $consentDetection,
	) {
	}

	public function handle(Event $event): void {
		if (!($event instanceof BeforeTemplateRenderedEvent)) {
			return;
		}

		if (!$this->consentDetection->isConsentGiven()) {
			return;
		}

		Util::addHeader(
			'script',
			[
				'src' => $this->urlGenerator->linkToRoute('googleanalytics.JavaScript.tracking'),
				'nonce' => $this->nonceManager->getNonce(),
			],
			''
		);
	}
}
