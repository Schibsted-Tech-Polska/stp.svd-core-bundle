<?php

namespace Svd\CoreBundle\Model;

use DateTime;

/**
 * Model
 */
trait UpdatedAtTrait
{
    /**
     * @var DateTime
     */
    protected $updatedAt;

    /**
     * Set updated at
     *
     * @param DateTime $updatedAt updated at
     *
     * @return self
     */
    public function setUpdatedAt(DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updated at
     *
     * @return DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
