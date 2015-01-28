<?php

namespace Svd\CoreBundle\Command;

use Doctrine\ORM\EntityManager;
use Exception;
use Svd\CoreBundle\Helper\CliHelper;
use Svd\CoreBundle\Util\Tools;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;

// @codingStandardsIgnoreStart
declare(ticks = 1);
// @codingStandardsIgnoreEnd

/**
 * Command
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
abstract class BaseCommand extends ContainerAwareCommand
{
    /** @var InputInterface */
    protected $input;

    /** @var OutputInterface */
    protected $output;

    /** @var EntityManager */
    protected $entityManager;

    /** @var Logger */
    protected $logger;

    /** @var CliHelper */
    protected $cliHelper;

    /** @var bool */
    protected $transaction = false;

    /**
     * On error
     *
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function onError()
    {
        $errors = array(
            E_ERROR,
            E_CORE_ERROR,
            E_COMPILE_ERROR,
            E_USER_ERROR,
        );
        $error = error_get_last();

        if (in_array($error['type'], $errors)) {
            $this->getContainer()
                ->get('svd_core.cli.helper')
                ->unlock();

            $this->write($error['message'], Logger::CRITICAL);
            exit();
        }
    }

    /**
     * On user break
     *
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function onUserBreak()
    {
        $this->getContainer()
            ->get('svd_core.cli.helper')
            ->unlock();

        $this->write('User killed process', Logger::INFO);
        exit();
    }

    /**
     * Configure
     */
    protected function configure()
    {
        $this->addOption('dry-run', null, InputOption::VALUE_OPTIONAL, 'Execute command in dry run mode', false);
        $this->addOption('transaction', 't', InputOption::VALUE_OPTIONAL, 'Execute command using transaction',
            $this->transaction);
    }

    /**
     * Execute
     *
     * @param InputInterface  $input  input
     * @param OutputInterface $output output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $this->cliHelper = $this->getContainer()
            ->get('svd_core.cli.helper');

        $this->logger = $this->createLogger();
        $this->entityManager = $this->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->registerShutdownFunctions();
        $this->cliHelper->setFilename(str_replace(':', '_', $this->getName()) . '.lock');
        $this->cliHelper->lock();

        $this->write('Start cron ' . str_repeat('-', 50), Logger::INFO);
        $this->write('Cron parameters', Logger::INFO, $this->input->getOptions());
        $stopwatch = new Stopwatch();
        $stopwatch->start('command');

        $this->validate();

        $this->entityManager->getConnection()
            ->getConfiguration()
            ->setSQLLogger(null);

        $this->beginTransaction();
        try {
            $this->process();

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();

            $this->write((string) $e, Logger::ERROR);
        }


        $event = $stopwatch->stop('command');
        $this->write('Cron summary', Logger::INFO, array(
            'memory diff' => Tools::humanizeMemory($event->getMemory()),
            'time diff' => Tools::humanizeTime($event->getDuration()),
        ));
        $this->write('End cron ' . str_repeat('=', 52), Logger::INFO);

        $this->cliHelper->unlock();
    }

    /**
     * Create monolog logger
     *
     * @return Logger
     */
    protected function createLogger()
    {
        $logService = $this->getContainer()
            ->getParameter('svd_core.log_dir.command');

        $this->cliHelper
            ->getLoggerManager()
            ->createLogger($logService, str_replace(':', '_', $this->getName()) . '.log');
        $logger = $this->cliHelper
            ->getLoggerManager()
            ->getLogger();

        return $logger;
    }

    /**
     * Register shutdown functions
     */
    protected function registerShutdownFunctions()
    {
        register_shutdown_function(array(
            $this,
            'onError'
        ));
        pcntl_signal(SIGINT, array(
            $this,
            'onUserBreak'
        ));
    }

    /**
     * Begin transaction
     */
    protected function beginTransaction()
    {
        if ($this->input->getOption('transaction')) {
            $this->entityManager->getConnection()
                ->beginTransaction();
        }
    }

    /**
     * Commit
     */
    protected function commit()
    {
        if ($this->input->getOption('transaction')) {
            if (!$this->input->getOption('dry-run')) {
                $this->entityManager->getConnection()
                    ->commit();
            } else {
                $this->entityManager->getConnection()
                    ->rollback();
            }
        }
    }

    /**
     * Rollback
     */
    protected function rollback()
    {
        if ($this->input->getOption('transaction')) {
            $this->entityManager->getConnection()
                ->rollback();
        }
    }

    /**
     * Write output
     *
     * @param string $message message
     * @param int    $level   level
     * @param array  $context context
     */
    protected function write($message, $level = null, $context = array())
    {
        if ($level === null) {
            $level = Logger::DEBUG;
        }

        $this->logger->log($level, $message, $context);
    }

    /**
     * Validate configuration
     */
    protected function validate()
    {
        $this->input->setOption('dry-run', (bool) $this->input->getOption('dry-run'));
        $this->input->setOption('transaction', (bool) $this->input->getOption('transaction'));
    }

    /**
     * Process
     */
    abstract protected function process();
}
