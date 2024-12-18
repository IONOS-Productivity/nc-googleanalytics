<?php

/**
 * SPDX-FileLicenseText: 2024 STRATO AG
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\NCGoogleAnalytics\Service;

use OCP\IRequest;

/**
 * Detector to test whether tracking consent was given.
 * A cookie value is inspected.
 * The implementation is IONOS specific.
 */
class ConsentDetection {
    const CONSENT_COOKIE_NAME = "PRIVACY_CONSENT";

    public function __construct(
        private IRequest $request,
    ) {
    }

    public function isConsentGiven(): bool {
        $codedJsonStr = $this->request->getCookie(self::CONSENT_COOKIE_NAME);
        $jsonStr = base64_decode($codedJsonStr);
        $settings = json_decode($jsonStr);
        return $settings->statistics ?? false;
    }
}
