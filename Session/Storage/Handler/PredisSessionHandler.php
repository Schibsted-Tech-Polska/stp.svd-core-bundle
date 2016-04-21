<?php

namespace Svd\CoreBundle\Session\Storage\Handler;

use InvalidArgumentException;
use Predis\Client as RedisClient;
use SessionHandlerInterface;

/**
 * Session storage handler
 */
class PredisSessionHandler implements SessionHandlerInterface
{
    /** @var RedisClient */
    protected $redis;

    /** @var string */
    protected $prefix;

    /** @var int */
    protected $ttl;

    /**
     * Construct
     *
     * @param RedisClient $redis   redis
     * @param array       $options options
     *
     * @throws InvalidArgumentException
     */
    public function __construct(RedisClient $redis, array $options)
    {
        $this->redis = $redis;

        if ($diff = array_diff(array_keys($options), ['prefix', 'expiretime'])) {
            throw new InvalidArgumentException(sprintf(
                'The following options are not supported "%s"', implode(', ', $diff)
            ));
        }
        $this->ttl = isset($options['expiretime']) ? (int) $options['expiretime'] : 86400;
        $this->prefix = isset($options['prefix']) ? $options['prefix'] : 'sf2redis:';
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessionId)
    {
        return 0 === $this->redis->del($this->getKey($sessionId));
    }

    /**
     * {@inheritdoc}
     */
    public function gc($maxlifetime)
    {
        // @README: Redis will handle the expiration of keys with SETEX command
        // @README: See: http://redis.io/commands/setex
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function open($savePath, $sessionId)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($sessionId)
    {
        return $this->redis->get($this->getKey($sessionId));
    }

    /**
     * {@inheritdoc}
     */
    public function write($sessionId, $sessionData)
    {
        return $this->redis->setex($this->getKey($sessionId), $this->ttl, $sessionData);
    }

    /**
     * Get key
     *
     * @param string $sessionId session id
     *
     * @return string
     */
    protected function getKey($sessionId)
    {
        return $this->prefix . $sessionId;
    }
}
