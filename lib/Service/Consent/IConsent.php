<?php

// SPDX-FileCopyrightText: 2024 2024 STRATO AG
//
// SPDX-License-Identifier: AGPL-3.0-or-later

declare(strict_types=1);

namespace OCA\NCGoogleAnalytics\Service\Consent;

interface IConsent {
	public function hasTechnicalConsent(): bool;

	public function hasStatisticsConsent(): bool;

	public function hasMarketingConsent(): bool;

	public function hasPartnershipsConsent(): bool;
}
