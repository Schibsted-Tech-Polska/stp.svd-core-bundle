<?php

namespace Svd\CoreBundle\Document;

use DateTime;

/**
 * Base document trait
 */
trait BaseDocumentTrait
{
    /**
     * Set created at
     *
     * @param DateTime $createdAt created at
     *
     * @return self
     */
    public function setCreatedAt(DateTime $createdAt)
    {
        /** @noinspection PhpUndefinedFieldInspection */
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
        /** @noinspection PhpUndefinedFieldInspection */
        return $this->createdAt;
    }

    /**
     * Set updated at
     *
     * @param DateTime $updatedAt updated at
     *
     * @return self
     */
    public function setUpdatedAt(DateTime $updatedAt)
    {
        /** @noinspection PhpUndefinedFieldInspection */
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
        /** @noinspection PhpUndefinedFieldInspection */
        return $this->updatedAt;
    }
}
