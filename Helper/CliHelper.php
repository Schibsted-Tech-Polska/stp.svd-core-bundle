<?php

namespace Svd\CoreBundle\Helper;

use Svd\CoreBundle\Manager\LoggerManager;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Process\Process;

/**
 * Helper
 */
class CliHelper
{
    /** @var string */
    protected $lockDir;

    /** @var string */
    protected $filename;

    /** @var int */
    protected $pid;

    /** @var LoggerManager */
    protected $loggerManager;

    /** @var Session */
    protected $session;

    /**
     * Construct
     *
     * @param string        $lockDir       lock directory
     * @param LoggerManager $loggerManager logger object
     */
    public function __construct($lockDir, LoggerManager $loggerManager)
    {
        $this->lockDir = $lockDir;
        $this->loggerManager = $loggerManager;
    }

    /**
     * Set filename
     *
     * @param string $filename file name
     *
     * @return self
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
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Lock cron
     *
     * @return int
     *
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function lock()
    {
        $lockFile = $this->lockDir . $this->getFilename();

        if (file_exists($lockFile)) {
            $this->pid = file_get_contents($lockFile);

            if ($this->isRunning()) {
                $this->getLogger()
                    ->notice("Cron already in progress...", array('pid' => $this->pid));
                exit();
            } else {
                $this->getLogger()
                    ->alert("Previous job died...", array('pid' => $this->pid));
            }
        }

        $this->pid = getmypid();
        if (file_put_contents($lockFile, $this->pid) !== false) {
            chmod($lockFile, 0666);
        }

        return $this->pid;
    }

    /**
     * Unlock cron
     *
     * @return bool
     */
    public function unlock()
    {
        $lockFile = $this->lockDir . $this->getFilename();

        if (file_exists($lockFile)) {
            unlink($lockFile);
        }

        return true;
    }

    /**
     * Get logger manager
     *
     * @return LoggerManager
     */
    public function getLoggerManager()
    {
        return $this->loggerManager;
    }

    /**
     * Write output
     *
     * @param string $message message
     * @param int    $level   level
     * @param array  $context context
     */
    public function write($message, $level = null, $context = array())
    {
        if ($level === null) {
            $level = Logger::DEBUG;
        }

        $this->getLogger()
            ->log($level, $message, $context);
    }

    /**
     * Get monolog logger
     *
     * @return Logger
     */
    public function getLogger()
    {
        return $this->loggerManager->getLogger();
    }

    /**
     * Check if command is running
     *
     * @return bool
     */
    protected function isRunning()
    {
        $process = new Process("ps -e | awk '{print $1}'");
        $process->run();
        $pids = explode("\n", $process->getOutput());

        if (in_array($this->pid, $pids)) {
            return true;
        }

        return false;
    }
}
