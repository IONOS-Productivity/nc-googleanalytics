<?php

namespace OCA\NCGoogleAnalytics\Controller;

use OCA\NCGoogleAnalytics\Config;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataDownloadResponse;
use OCP\AppFramework\Http\TextPlainResponse;
use OCP\IConfig;
use OCP\IRequest;

class JavaScriptController extends Controller
{
    /**
     * constructor of the controller
     *
     * @param string $appName
     * @param IRequest $request
     * @param IConfig $config
     */
    public function __construct(
        $appName,
        IRequest $request,
        protected Config $config
    ) {
        parent::__construct($appName, $request);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     *
     * @return TextPlainResponse|DataDownloadResponse
     */
    public function tracking(): TextPlainResponse|DataDownloadResponse
    {
        $gtmId = $this->config->getTrackingKey();

        if (!isset($gtmId)) {
            $response = new TextPlainResponse('// tracking disabled');
            $response->addHeader('Content-Type', 'text/javascript');
            return $response;
        }

        $script = file_get_contents(__DIR__ . '/../../js/track.js');
        $script = str_replace('%GTM_ID%', $gtmId, $script);

        return new DataDownloadResponse($script, 'gtm.js', 'text/javascript');
    }
}
