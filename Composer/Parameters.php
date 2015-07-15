<?php

namespace Svd\CoreBundle\Composer;

use Composer\Script\Event;

/**
 * Composer
 *
 * @deprecated since version 1.3.24, will be removed in 1.4.
 *             Use https://github.com/Schibsted-Tech-Polska/svd.composer-helper instead.
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
        self::parseUrlParameters($event, 'DATABASE_URL', 'DOCTRINE__DATABASE_', [
            'host' => 'HOST',
            'pass' => 'PASSWORD',
            'path' => 'DBNAME ',
            'port' => 'PORT',
            'user' => 'USER',
        ]);
    }

    /**
     * Parse MONGOLAB_URI
     *
     * @param Event $event event
     */
    public static function parseMongolabUri(Event $event)
    {
        self::parseUrlParameters($event, 'MONGOLAB_URI', 'DOCTRINE_MONGODB__DATABASE_', [
            'host' => 'HOST',
            'path' => 'DBNAME ',
            'user' => 'USER',
            'pass' => 'PASSWORD',
            'port' => 'PORT',
        ]);
    }

    /**
     * Parse CLEARDB_DATABASE_URL
     *
     * @param Event  $event  event
     * @param string $envVar env variable
     */
    public static function parseCleardbUrl(Event $event)
    {
        self::parseUrlParameters($event, 'CLEARDB_DATABASE_URL', 'DOCTRINE__DATABASE_', [
            'host' => 'HOST',
            'pass' => 'PASSWORD',
            'path' => 'DBNAME ',
            'user' => 'USER',
        ]);
    }

    /**
     * Parse CLOUDAMQP_URL
     *
     * @param Event  $event  event
     * @param string $envVar env variable
     */
    public static function parseCloudAmqUrl(Event $event)
    {
        self::parseUrlParameters($event, 'CLOUDAMQP_URL', 'OLD_SOUND_RABBIT_MQ__', [
            'host' => 'HOST',
            'pass' => 'PASSWORD',
            'path' => 'VHOST',
            'user' => 'USER'
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
    protected static function parseUrlParameters(Event $event, $srcParam, $destParamPrefix, array $usedFields)
    {
        $databaseParameters = getenv($srcParam);
        if (!empty($databaseParameters)) {
            $items = parse_url($databaseParameters);
            if (!empty($items)) {
                foreach ($usedFields as $key => $envName) {
                    if ($key == 'path') {
                        $value = substr($items[$key], 1);
                    } else {
                        $value = $items[$key];
                    }
                    putenv($destParamPrefix . $envName . '=' . $value);
                }

                $io = $event->getIO();
                $io->write("Parameters from '" . $srcParam . "' variable have been updated.");
            }
        }
    }
}
