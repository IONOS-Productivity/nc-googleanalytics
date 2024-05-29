// SPDX-FileCopyrightText: Mikhailo Matiyenko-Kupriyanov <kupriyanov+nextcloud@strato.de>
// SPDX-License-Identifier: AGPL-3.0-or-later
module.exports = {
	extends: [
		'@nextcloud',
	],
	overrides: [
		{
			files: ['js/track.js'],
			rules: {
				semi: 'off',
			},
			globals: {
				dataLayer: 'readonly',
			},
		},
	],
}
