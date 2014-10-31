<?php

namespace Svd\CoreBundle\Manager;

use Monolog\Handler\AbstractHandler;
use Monolog\Handler\StreamHandler;
use Svd\CoreBundle\Exception\UnexpectedValueException;
use Svd\CoreBundle\Formatter\ConsoleFormatter;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Manager
 */
class LoggerManager
{
    /** @var Logger */
    protected $logger;

    /** @var string */
    protected $logPathPattern;

    /** @var string */
    protected $format;

    /** @var string */
    protected $level;

    /** @var string */
    protected $path;

    /** @var string */
    protected $service;

    /** @var string */
    protected $filename;

    /** @var AbstractHandler */
    protected $loggerHandler;

    /**
     * Construct
     *
     * @param Logger  $logger
     * @param Session $session
     * @param string  $logPathPattern
     * @param string  $format
     * @param string  $level
     */
    public function __construct(Logger $logger, Session $session, $logPathPattern, $format, $level)
    {
        $this->logger = $logger;
        $this->session = $session;
        $this->logPathPattern = $logPathPattern;
        $this->format = $format;
        $this->level = $level;
    }

    /**
     * Set log path
     *
     * @param string $path
     *
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get log path
     *
     * @return string
     */
    public function getPath()
    {
        if (!$this->path) {
            $path = str_replace([
                '%service%',
                '%filename%'
            ], [
                $this->getService(),
                $this->getFilename()
            ], $this->logPathPattern);
            $this->path = $path;
        }

        return $this->path;
    }

    /**
     * Set format
     *
     * @param string $format
     *
     * @return $this
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Get format
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set log path pattern
     *
     * @param string $logPathPattern
     *
     * @return $this
     */
    public function setLogPathPattern($logPathPattern)
    {
        $this->logPathPattern = $logPathPattern;

        return $this;
    }

    /**
     * Get log path pattern
     *
     * @return string
     */
    public function getLogPathPattern()
    {
        return $this->logPathPattern;
    }

    /**
     * Set logger
     *
     * @param Logger $logger
     *
     * @return $this
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Get logger
     *
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Set filename
     *
     * @param string $filename
     *
     * @return $this
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     *
     * @throws UnexpectedValueException
     */
    public function getFilename()
    {
        if (empty($this->filename)) {
            throw new UnexpectedValueException('Filename cannot be empty.');
        }

        return $this->filename;
    }

    /**
     * Set level
     *
     * @param string $level
     *
     * @return $this
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return string
     */
    public function getLevel()
    {
        return strtoupper($this->level);
    }

    /**
     * Set service
     *
     * @param string $service
     *
     * @return $this
     */
    public function setService($service)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Get service
     *
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Create logger
     *
     * @param null|string $service
     * @param null|string $filename
     */
    public function createLogger($service = null, $filename = null)
    {
        if (empty($this->loggerHandler)) {
            if (!empty($service)) {
                $this->setService($service);
            }
            if (!empty($filename)) {
                $this->setFilename($filename);
            }

            $streamHandle = new StreamHandler($this->getPath(), Logger::getLevels()[$this->getLevel()]);
            $streamHandle->setFormatter(new ConsoleFormatter($this->session, $this->getFormat()));
            $this->logger->pushHandler($streamHandle);
            $this->loggerHandler = $streamHandle;
        }
    }
}
