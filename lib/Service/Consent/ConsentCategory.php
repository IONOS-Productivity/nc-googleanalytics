<?php

// SPDX-FileCopyrightText: 2024 2024 STRATO AG
//
// SPDX-License-Identifier: AGPL-3.0-or-later

declare(strict_types=1);

namespace OCA\NCGoogleAnalytics\Service\Consent;

enum ConsentCategory: string {
	case TECHNICAL = 'technical';
	case STATISTICS = 'statistics';
	case MARKETING = 'marketing';
	case PARTNERSHIPS = 'partnerships';
}
