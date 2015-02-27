<?php

namespace Svd\CoreBundle\Model;

use DateTime;

/**
 * Model
 */
trait CreatedAtTrait
{
    /**
     * @var DateTime
    */
    protected $createdAt;

    /**
     * Set created at
     *
     * @param DateTime $createdAt created at
     *
     * @return self
     */
    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get created at
     *
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
