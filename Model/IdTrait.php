<?php

namespace Svd\CoreBundle\Model;

/**
 * Model
 */
trait IdTrait
{
    /** @var mixed */
    protected $id;

    /**
     * Get ID
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}
