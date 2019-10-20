<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use Sentry;

/**
 * Class SentryPlugin
 * @package Grav\Plugin
 */
class SentryPlugin extends Plugin
{
    /**
     * @return array
     *
     * The getSubscribedEvents() gives the core a list of events
     *     that the plugin wants to listen to. The key of each
     *     array section is the event that the plugin listens to
     *     and the value (in the form of an array) contains the
     *     callable (or function) as well as the priority. The
     *     higher the number the higher the priority.
     */
    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0]
        ];
    }

    /**
     * Return an array with extracted variables from $sourceKeyValues
     * @param array $sourceKeyValues Source config associative array
     * @param array $keysToExtract Array of variables to extract
     * @return array Array with extracted variables from $sourceKeyValues
     */
    private static function extractKeyValues($sourceKeyValues, $keysToExtract)
    {
        $targetConfig = [];
        foreach ($keysToExtract as $key) {
            if (isset($sourceKeyValues[$key])) {
                $targetConfig[$key] = $sourceKeyValues[$key];
            }
        }

        return $targetConfig;
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized()
    {
        require_once __DIR__ . '/vendor/autoload.php';

        $sentryPluginConfig = $this->grav['config']->get('plugins.sentry');

        $sentryBackendConfig = $sentryPluginConfig['backend'];
        // Fallback to the main DSN if no backend dsn provided
        if (!isset($sentryBackendConfig['dsn']) || empty($sentryBackendConfig['dsn'])) {
            $sentryBackendConfig['dsn'] = $sentryPluginConfig['dsn'];
        }

        $sentryFrontendConfig = $sentryPluginConfig['frontend'];
        // Fallback to the main DSN if no frontend dsn provided
        if (!isset($sentryFrontendConfig['dsn']) || empty($sentryFrontendConfig['dsn'])) {
            $sentryFrontendConfig['dsn'] = $sentryPluginConfig['dsn'];
        }

        // Initiate Sentry into the backend
        if (isset($sentryBackendConfig['enabled']) && $sentryBackendConfig['enabled'] === true) {
           $this->initBackend($sentryBackendConfig);
        }

        // Add Sentry to the frontend
        if (isset($sentryFrontendConfig['enabled']) && $sentryFrontendConfig['enabled'] === true) {
            $this->initFrontend($sentryFrontendConfig);
        }
    }

    /**
     * Initialize Sentry for the Grav backend (server side)
     * @param array $sentryBackendConfig Sentry config for backend
     * @throws \Exception
     */
    private function initBackend($sentryBackendConfig)
    {
        if (!isset($sentryBackendConfig['dsn']) || empty($sentryBackendConfig['dsn'])) {
            throw new \Exception('[Sentry Backend Configuration Error] No DSN found');
        }

        $initVariables = [
            'dsn',
            'max_breadcrumbs',
            'attach_stacktrace',
            'release',
            'environment',
            'server_name',
        ];

        $initConfig = self::extractKeyValues($sentryBackendConfig, $initVariables);

        // @todo: find a clean way to put that in the config
        $initConfig['error_types'] = E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED & ~E_STRICT;

        Sentry\init($initConfig);
    }

    /**
     * Initialize Sentry for the frontend (browser side)
     * @param $sentryFrontendConfig Sentry config for frontend
     * @throws \Exception
     */
    private function initFrontend($sentryFrontendConfig)
    {
        if (!isset($sentryFrontendConfig['dsn']) || empty($sentryFrontendConfig['dsn'])) {
            throw new \Exception('[Sentry Frontend Configuration Error] No DSN found');
        }

        $assets = $this->grav['assets'];

        // Add Sentry Browser JS from CDN
        $assets->addJs('https://browser.sentry-cdn.com/5.7.1/bundle.min.js', '100');

        $initVariables = [
            'dsn',
            'maxBreadcrumbs',
            'debug',
            'attachStacktrace',
            'release',
            'environment',
            'serverName',
        ];

        $initSetting = self::extractKeyValues($sentryFrontendConfig, $initVariables);
        $jsonInitSetting = json_encode($initSetting);
        $inlineJS = '
            Sentry.init('.$jsonInitSetting.');
        ';

        if (isset($sentryFrontendConfig['tags']) && count($sentryFrontendConfig['tags']) > 0) {
            $inlineJS .= 'Sentry.configureScope(function(scope) {';

            foreach ($sentryFrontendConfig['tags'] as $tagKey => $tagValue) {
                $inlineJS .= 'scope.setTag("' . $tagKey .'", "'.$tagValue.'");';
            }

            $inlineJS .= '});';
        }

        $assets->addInlineJs($inlineJS, '110');
    }

}
