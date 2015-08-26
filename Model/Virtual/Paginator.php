<?php

namespace Svd\CoreBundle\Model\Virtual;

use Svd\CoreBundle\Model\ModelInterface;

/**
 * Model virtual
 */
class Paginator
{
    /** @var ModelInterface[] */
    protected $previous;

    /** @var ModelInterface[] */
    protected $next;

    /** @var ModelInterface */
    protected $first;

    /** @var ModelInterface */
    protected $last;

    /** @var integer */
    protected $currentNo;

    /** @var integer */
    protected $totalNumber;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->previous = [];
        $this->next = [];
    }

    /**
     * Get previous
     *
     * @return ModelInterface
     */
    public function getPrevious($no = 1)
    {
        $key = $no - 1;
        if (array_key_exists($key, $this->previous)) {
            $value = $this->previous[$key];
        } else {
            $value = null;
        }

        return $value;
    }

    /**
     * Set previous
     *
     * @param ModelInterface[] $values values
     *
     * @return self
     */
    public function setPrevious(array $values)
    {
        $this->previous = $values;

        return $this;
    }

    /**
     * Get next
     *
     * @return ModelInterface
     */
    public function getNext($no = 1)
    {
        $key = $no - 1;
        if (array_key_exists($key, $this->next)) {
            $value = $this->next[$key];
        } else {
            $value = null;
        }

        return $value;
    }

    /**
     * Set next
     *
     * @param ModelInterface[] $values values
     *
     * @return self
     */
    public function setNext(array $values)
    {
        $this->next = $values;

        return $this;
    }

    /**
     * Get first
     *
     * @return ModelInterface
     */
    public function getFirst()
    {
        return $this->first;
    }

    /**
     * Set first
     *
     * @param ModelInterface $first first
     *
     * @return self
     */
    public function setFirst(ModelInterface $first)
    {
        $this->first = $first;

        return $this;
    }

    /**
     * Get last
     *
     * @return ModelInterface
     */
    public function getLast()
    {
        return $this->last;
    }

    /**
     * Set last
     *
     * @param ModelInterface $last last
     *
     * @return self
     */
    public function setLast(ModelInterface $last)
    {
        $this->last = $last;

        return $this;
    }

    /**
     * Get current no
     *
     * @return integer
     */
    public function getCurrentNo()
    {
        return $this->currentNo;
    }

    /**
     * Set current no
     *
     * @param integer $currentNo current no
     *
     * @return self
     */
    public function setCurrentNo($currentNo)
    {
        $this->currentNo = $currentNo;

        return $this;
    }

    /**
     * Get total number
     *
     * @return integer
     */
    public function getTotalNumber()
    {
        return $this->totalNumber;
    }

    /**
     * Set total number
     *
     * @param integer $totalNumber total number
     *
     * @return self
     */
    public function setTotalNumber($totalNumber)
    {
        $this->totalNumber = $totalNumber;

        return $this;
    }
}
