<?php
/**
 * SPDX-FileLicenseText: 2024 STRATO AG
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\NCGoogleAnalytics\Listener;

use OCA\NCGoogleAnalytics\Service\ConsentDetection;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\Security\CSP\AddContentSecurityPolicyEvent;

/**
 * Configure Google sites for content security policy (CSP).
 */
class AddCsp implements IEventListener {
	public function __construct(
		private ConsentDetection $consentDetection
	) {
	}

	public function handle(Event $event): void {
		if (!($event instanceof AddContentSecurityPolicyEvent)) {
			return;
		}

		if (!$this->consentDetection->isConsentGiven()) {
			return;
		}

		$policy = new ContentSecurityPolicy();

		$policy->addAllowedScriptDomain("*.googletagmanager.com");
		$policy->addAllowedImageDomain("*.googletagmanager.com");
		$policy->addAllowedConnectDomain("*.googletagmanager.com");

		$policy->addAllowedScriptDomain("tagmanager.google.com");
		$policy->addAllowedImageDomain("tagmanager.google.com");
		$policy->addAllowedConnectDomain("tagmanager.google.com");

		$policy->addAllowedScriptDomain("*.google-analytics.com");
		$policy->addAllowedImageDomain("*.google-analytics.com");
		$policy->addAllowedConnectDomain("*.google-analytics.com");

		// additional SCP for GTM preview mode
		$policy->addAllowedStyleDomain("https://www.googletagmanager.com");
		$policy->addAllowedStyleDomain("https://fonts.googleapis.com");

		$policy->addAllowedFontDomain("https://fonts.gstatic.com");

		$policy->addAllowedImageDomain("https://fonts.gstatic.com");
		$policy->addAllowedImageDomain("https://fonts.googleapis.com");

		$event->addPolicy($policy);
	}
}
