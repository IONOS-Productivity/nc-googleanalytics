<?php

// SPDX-FileCopyrightText: 2024 STRATO AG
//
// SPDX-License-Identifier: AGPL-3.0-or-later

declare(strict_types=1);

namespace OCA\NCGoogleAnalytics\Service\Consent;

interface IConsentService {
	public function getConsent(array $cookies): IConsent;
}
