<?php

/**
 * General Configuration
 *
 * All of your system's general configuration settings go in here.
 * You can see a list of the default settings in craft/app/etc/config/defaults/general.php
 */

return array(

   
    // All environments
    '*' => array(
        'omitScriptNameInUrls' => true,
        'usePathInfo' => true,
        'cacheDuration' => false,
        'useEmailAsUsername' => true,
        'generateTransformsBeforePageLoad' => true,
        'siteUrl' => getenv('CRAFTENV_SITE_URL'),
        'craftEnv' => CRAFT_ENVIRONMENT,
        'defaultWeekStartDay' => 0,
        'enableCsrfProtection' => true,
        'cpTrigger' => 'admin',

        // Set the environmental variables
        'environmentVariables' => array(
            'baseUrl'  => getenv('CRAFTENV_BASE_URL'),
            'basePath' => getenv('CRAFTENV_BASE_PATH'),
        ),
    ),

    // Live (production) environment
    'live'  => array(
        'devMode' => false,
        'enableTemplateCaching' => true,
        'allowAutoUpdates' => false,
        'omitScriptNameInUrls' => true,
    ),

    // Staging (pre-production) environment
    'staging'  => array(
        'devMode' => false,
        'enableTemplateCaching' => true,
        'allowAutoUpdates' => false,
        'omitScriptNameInUrls' => true,
    ),

    // Local (development) environment
    'local'  => array(
        'devMode' => true,
        'enableTemplateCaching' => false,
        'allowAutoUpdates' => true,
    ),
);
