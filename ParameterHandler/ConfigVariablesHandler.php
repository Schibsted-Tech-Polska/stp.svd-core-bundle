<?php

namespace Svd\CoreBundle\ParameterHandler;

use Composer\Script\Event;

/**
 * ParameterHandler
 */
class ConfigVariablesHandler
{
    /**
     * Populate environment
     *
     * @param Event $event event
     */
    public static function populateEnvironment(Event $event)
    {
        $variable = 'DATABASE_URL';

        $databaseUrl = getenv($variable);
        if (!empty($databaseUrl)) {
            $items = parse_url($databaseUrl);
            if (!empty($items)) {
                putenv('DOCTRINE__DATABASE_HOST=' . $items['host']);
                putenv('DOCTRINE__DATABASE_DBNAME=' . substr($items['path'], 1));
                putenv('DOCTRINE__DATABASE_PASSWORD=' . $items['pass']);
                putenv('DOCTRINE__DATABASE_PORT=' . $items['port']);
                putenv('DOCTRINE__DATABASE_USER=' . $items['user']);

                $io = $event->getIO();
                $io->write("Parameters from variable '" . $variable . "' has been updated.");
            }
        }
    }
}
