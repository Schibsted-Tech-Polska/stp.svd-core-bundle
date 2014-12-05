<?php

namespace Svd\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Knp\Component\Pager\Paginator;
use Svd\CoreBundle\Entity\EntityInterface as Entity;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Base repository trait
 */
trait BaseRepositoryTrait
{
    /** @var TranslatorInterface */
    protected $translator;

    /** @var Paginator */
    protected $paginator;

    /**
     * Set translator
     *
     * @param TranslatorInterface $translator translator
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Set paginator
     *
     * @param Paginator $paginator paginator
     */
    public function setPaginator(Paginator $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * Finds a single entity by a set of criteria
     *
     * @param array      $criteria criteria
     * @param array|null $orderBy  sort criteria
     *
     * @return object|null
     */
    abstract public function findOneBy(array $criteria, array $orderBy = null);

    /**
     * Get one with full data
     *
     * @param array      $criteria criteria
     * @param array|null $orderBy  sort criteria
     *
     * @return object|null
     */
    public function getOneBy(array $criteria, array $orderBy = null)
    {
        $result = $this->findOneBy($criteria, $orderBy);

        return $result;
    }

    /**
     * Get one with full data or throw error 404
     *
     * @param array      $criteria criteria
     * @param array|null $orderBy  sort criteria
     *
     * @return object
     *
     * @throws NotFoundHttpException
     */
    public function getOneByOr404(array $criteria, array $orderBy = null)
    {
        $result = $this->getOneBy($criteria, $orderBy);
        if (!$result) {
            throw new NotFoundHttpException();
        }

        return $result;
    }

    /**
     * Finds entities by a set of criteria
     *
     * @param array      $criteria criteria
     * @param array|null $orderBy  sort criteria
     * @param int        $limit    limit
     * @param int        $offset   offset
     *
     * @return array
     */
    abstract public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * Get all with full data
     *
     * @param array      $criteria criteria
     * @param array|null $orderBy  sort criteria
     * @param int        $limit    limit
     * @param int        $offset   offset
     *
     * @return array
     */
    public function getBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $result = $this->findBy($criteria, $orderBy, $limit, $offset);

        return $result;
    }

    /**
     * Gets entity manager
     *
     * @return EntityManager
     */
    abstract protected function getEntityManager();

    /**
     * Insert
     *
     * @param Entity $entity entity
     * @param bool   $flush  flag, if flush should be done?
     *
     * @return self
     */
    public function insert(Entity $entity, $flush = false)
    {
        return $this->save($entity, $flush);
    }

    /**
     * Update
     *
     * @param Entity $entity entity
     * @param bool   $flush  flag, if flush should be done?
     *
     * @return self
     */
    public function update(Entity $entity, $flush = false)
    {
        return $this->save($entity, $flush);
    }

    /**
     * Delete
     *
     * @param Entity $entity entity
     * @param bool   $flush  flag, if flush should be done?
     *
     * @return self
     */
    public function delete(Entity $entity, $flush = false)
    {
        $this->getEntityManager()
            ->remove($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }

        return $this;
    }

    /**
     * Save
     *
     * @param Entity $entity entity
     * @param bool   $flush  flag, if flush should be done?
     *
     * @return self
     */
    protected function save(Entity $entity, $flush = false)
    {
        $this->getEntityManager()
            ->persist($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }

        return $this;
    }
}
