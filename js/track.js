window.dataLayer = window.dataLayer || [];
/**
 * Restrict tag deployment with a blocklist
 * https://developers.google.com/tag-platform/tag-manager/restrict
 */
dataLayer.push({
	'gtm.blocklist': ['f', 'u'],
});

/* eslint-disable */
(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
		new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer', '%GTM_ID%');
