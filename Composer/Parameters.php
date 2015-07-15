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
