window.dataLayer = window.dataLayer || [];
dataLayer.push({
	'gtm.blocklist': ['f', 'u']
});

function gtag() {
	dataLayer.push(arguments);
}

gtag('consent', 'default', {
	'ad_storage': 'denied',
	'ad_user_data': 'denied',
	'ad_personalization': 'denied',
	'analytics_storage': 'denied'
});

/**
 * Update the consent for Google Analytics depending on IONOS consent cookie
 */
function updateConsent() {
	function getCookie(cname) {
		let name = cname + "=";
		let ca = document.cookie.split(';');
		for (let i = 0; i < ca.length; i++) {
			let c = ca[i];
			while (c.charAt(0) === ' ') {
				c = c.substring(1);
			}
			if (c.indexOf(name) === 0) {
				return c.substring(name.length, c.length);
			}
		}
		return "";
	}

	/**
	 * @typedef {Object} PrivacyConsent
	 * @property {boolean} technical
	 * @property {boolean} statistics
	 * @property {boolean} marketing
	 * @property {boolean} partnerships
	 */
	let privacyConsentDecoded = {};

	try {
		const cookie = getCookie('PRIVACY_CONSENT');
		if (cookie === '' || !cookie) {
			throw new Error('No privacy consent cookie found')
		}

		const cookieDecoded = atob(cookie);
		privacyConsentDecoded = JSON.parse(cookieDecoded);
	} catch (e) {
		console.warn('Failed to parse privacy consent cookie', e);
	}

	if (privacyConsentDecoded['marketing'] === true) {
		gtag('consent', 'update', {
			'ad_user_data': 'granted',
			'ad_personalization': 'granted',
		});
	}
}

(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
		new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	updateConsent();
})(window,document,'script','dataLayer', '%GTM_ID%');
