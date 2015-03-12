<?php

namespace Svd\CoreBundle\Composer;

use Composer\Script\Event;

/**
 * Composer
 */
class Parameters
{
    /**
     * Parse DATABASE_URL
     *
     * @param Event $event event
     */
    public static function parseDatabaseUrl(Event $event)
    {
        self::parseDatabaseParameters($event, 'DATABASE_URL', 'DOCTRINE__', [
            'host',
            'pass',
            'path',
            'port',
            'user'
        ]);
    }

    /**
     * Parse MONGOLAB_URI
     *
     * @param Event $event event
     */
    public static function parseMongolabUri(Event $event)
    {
        self::parseDatabaseParameters($event, 'MONGOLAB_URI', 'DOCTRINE_MONGODB__', [
            'host',
            'pass',
            'path',
            'port',
            'user'
        ]);
    }

    /**
     * Parse database parameters
     *
     * @param Event  $event           event
     * @param string $srcParam        src parameter, e.g.: DATABASE_URL
     * @param string $destParamPrefix dest parameter prefix, e.g.: DOCTRINE
     * @param array  $usedFields      used fields, e.g.: ['user', 'pass']
     */
    protected static function parseDatabaseParameters(Event $event, $srcParam, $destParamPrefix, array $usedFields)
    {
        $databaseParameters = getenv($srcParam);
        if (!empty($databaseParameters)) {
            $items = parse_url($databaseParameters);
            if (!empty($items)) {
                if (in_array('host', $usedFields)) {
                    putenv($destParamPrefix . 'DATABASE_HOST=' . $items['host']);
                }
                if (in_array('path', $usedFields)) {
                    putenv($destParamPrefix . 'DATABASE_DBNAME=' . substr($items['path'], 1));
                }
                if (in_array('pass', $usedFields)) {
                    putenv($destParamPrefix . 'DATABASE_PASSWORD=' . $items['pass']);
                }
                if (in_array('port', $usedFields)) {
                    putenv($destParamPrefix . 'DATABASE_PORT=' . $items['port']);
                }
                if (in_array('user', $usedFields)) {
                    putenv($destParamPrefix . 'DATABASE_USER=' . $items['user']);
                }

                $io = $event->getIO();
                $io->write("Parameters from '" . $srcParam . "' variable have been updated.");
            }
        }
    }
}
