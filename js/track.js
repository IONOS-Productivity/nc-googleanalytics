const ENC_TRACKING = {
	privacyConsentCookie: "PRIVACY_CONSENT",
	pathsToSanitize: [
		/^((\/index\.php|)\/apps\/files\/personal)(.*)$/,
		/^((\/index\.php|)\/apps\/files\/personal\/)(.*)$/,
		/^((\/index\.php|)\/apps\/files\/resent)$/,
		/^((\/index\.php|)\/apps\/files\/resent\/)(.*)$/,
		/^((\/index\.php|)\/apps\/files\/favorites)$/,
		/^((\/index\.php|)\/apps\/files\/favorites\/)(.*)$/,
		/^((\/index\.php|)\/apps\/files\/trashbin)$/,
		/^((\/index\.php|)\/apps\/files\/trashbin\/)(.*)$/,
		/^((\/index\.php|)\/apps\/files)$/,
		/^((\/index\.php|)\/apps\/files\/)(.*)$/,
	],
	lastEventId: 0,
	eventCounter: 0,
	/**
	 * Array of paths as regex to sanitize
	 * @param {string} url
	 * @returns {string}
	 */
	sanitizeUrl: (url) => {
		console.group('sanitizeUrl');
		const urlObj = new URL(url);

		console.log('URL:before', url);

		for (const path of ENC_TRACKING.pathsToSanitize) {
			if (path.test(urlObj.pathname)) {
				const matched = urlObj.pathname.match(path);

				let pathParametersHash = '';

				if (matched[1].endsWith('/')) {
					pathParametersHash = '~sanitized~';
				}

				const pathnameSanitized = `${matched[1]}${pathParametersHash}`;
				urlObj.pathname = `${pathnameSanitized}`;
				if (urlObj.search) {
					urlObj.search = '';
				}
				console.log('URL:after', urlObj.toString());
				console.groupEnd();
				return urlObj.toString();
			}
		}
		console.groupEnd();
		return url;
	},

	/**
	 * Sanitize GTM events
	 * @param {IArguments} args arguments passed to GTM event
	 * @returns {IArguments} sanitized arguments
	 */
	sanitizeGTMEvent: (args) => {
		if (args.length === 0) {
			return args;
		}

		const event = args[0];
		if (event.event === undefined) {
			return args;
		}

		let sanitizedArgs = args;
		ENC_TRACKING.eventCounter++;
		if (event.event === 'gtm.historyChange-v2') {
			console.trace('historyChange-v2 event', event);
			sanitizedArgs[0]['gtm.oldUrl'] = `${ENC_TRACKING.sanitizeUrl(sanitizedArgs[0]['gtm.oldUrl'])}?_=${ENC_TRACKING.lastEventId}`; //sanitizeUrl(sanitizedArgs[0]['gtm.oldUrl']);
			sanitizedArgs[0]['gtm.newUrl'] = `${ENC_TRACKING.sanitizeUrl(document.location.href)}?_=${ENC_TRACKING.eventCounter}`; //sanitizeUrl(document.location.href);

			ENC_TRACKING.lastEventId = ENC_TRACKING.eventCounter;
		} else if (event.event.startsWith('gtm.historyChange')) {
			console.warn('Unsupported historyChange event', event);
		}

		return sanitizedArgs;
	}
};

console.log('ENC_TRACKING', ENC_TRACKING);

window.dataLayer = window.dataLayer || [];
dataLayer.push({
	'gtm.blocklist': ['f', 'u']
});

const dataLayerPush = window.dataLayer.push;
window.dataLayer.push = function () {
	console.group('dataLayer.push');
	console.log('dataLayer.push:before', arguments[0]);
	const sanitized = ENC_TRACKING.sanitizeGTMEvent(arguments);
	console.log('dataLayer.push:sanitized', sanitized);
	dataLayerPush.apply(window.dataLayer, sanitized);
	console.groupEnd();
}

function gtag() {
	console.log('gtag', arguments);
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
		const cookie = getCookie(ENC_TRACKING.privacyConsentCookie);
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

<!-- Google Tag Manager -->
(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
		new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	updateConsent();
})(window,document,'script','dataLayer', '%GTM_ID%');
<!-- End Google Tag Manager -->
