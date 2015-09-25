<?php

namespace Svd\CoreBundle\Model;

use DateTime;

/**
 * Model
 */
trait DeletedAtTrait
{
    /**
     * @var DateTime
     */
    protected $deletedAt;

    /**
     * Set deleted at
     *
     * @param DateTime $deletedAt deleted at
     *
     * @return self
     */
    public function setDeletedAt(DateTime $deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Get deleted at
     *
     * @return DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }
}
