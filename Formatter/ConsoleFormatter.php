<?php

namespace Svd\CoreBundle\Formatter;

use RuntimeException;
use Symfony\Bridge\Monolog\Formatter\ConsoleFormatter as Formatter;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Formatter
 */
class ConsoleFormatter extends Formatter
{
    /** @var string */
    protected $hash;

    /** @var Session */
    protected $session;

    /**
     * @param Session $session               Session object
     * @param string  $format                The format of the message
     * @param string  $dateFormat            The format of the timestamp: one supported by DateTime::format
     * @param bool    $allowInlineLineBreaks Whether to allow inline line breaks in log entries
     */
    public function __construct(Session $session, $format = null, $dateFormat = null, $allowInlineLineBreaks = false)
    {
        $this->session = $session;
        parent::__construct($format, $dateFormat, $allowInlineLineBreaks);
    }

    /**
     * {@inheritdoc}
     */
    public function format(array $record)
    {
        if (!$this->session->isStarted()) {
            $this->session->start();
        }

        if (!$this->hash) {
            try {
                $this->hash = substr($this->session->getId(), 0, 8);
            } catch (RuntimeException $e) {
                $this->hash = '????????';
            }
            $this->hash .= '-' . substr(uniqid(), -8);
        }

        $record['hash'] = $this->hash;
        $record['level_name'] = str_repeat(' ', 9 - strlen($record['level_name'])) . $record['level_name'];

        $ret = parent::format($record);

        return $ret;
    }
}
