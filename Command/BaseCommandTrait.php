<?php
namespace Svd\CoreBundle\Command;

/**
 * Command
 */
trait BaseCommandTrait
{
    /**
     * Set argument
     *
     * @param string  $name        Name
     * @param int     $mode        Mode: InputArgument::REQUIRED or InputArgument::OPTIONAL
     * @param string  $description Description
     * @param mixed   $default     Default
     *
     * @return self
     */
    protected function setArgument($name, $mode = null, $description = '', $default = null)
    {
        $arguments = $this->getDefinition()->getArguments();

        $newArguments = [];
        foreach ($arguments as $argument) {
            if ($argument->getName() != $name) {
                $newArguments[] = $argument;
            }
        }

        $this->getDefinition()
            ->setArguments($newArguments);

        $this->addArgument($name, $mode, $description, $default);

        return $this;
    }
}
