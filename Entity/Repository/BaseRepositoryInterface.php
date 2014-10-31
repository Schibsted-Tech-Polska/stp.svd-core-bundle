<?php

namespace Svd\CoreBundle\Entity\Repository;

use Knp\Component\Pager\Paginator;
use Svd\CoreBundle\Entity\EntityInterface as Entity;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Entity repository
 */
interface BaseRepositoryInterface
{
    /**
     * Set translator
     *
     * @param Translator $translator translator
     */
    public function setTranslator(Translator $translator);

    /**
     * Set paginator
     *
     * @param Paginator $paginator paginator
     */
    public function setPaginator(Paginator $paginator);

    /**
     * Get one with full data
     *
     * @param array      $criteria criteria
     * @param array|null $orderBy  sort criteria
     *
     * @return self|null
     */
    public function getOneBy(array $criteria, array $orderBy = null);

    /**
     * Get one with full data or throw error 404
     *
     * @param array      $criteria criteria
     * @param array|null $orderBy  sort criteria
     *
     * @return self
     *
     * @throws NotFoundHttpException
     */
    public function getOneByOr404(array $criteria, array $orderBy = null);

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
    public function getBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * Insert
     *
     * @param Entity $entity entity
     * @param bool   $flush  flag, if flush should be done?
     *
     * @return self
     */
    public function insert(Entity $entity, $flush = false);

    /**
     * Update
     *
     * @param Entity $entity entity
     * @param bool   $flush  flag, if flush should be done?
     *
     * @return self
     */
    public function update(Entity $entity, $flush = false);

    /**
     * Delete
     *
     * @param Entity $entity entity
     * @param bool   $flush  flag, if flush should be done?
     *
     * @return self
     */
    public function delete(Entity $entity, $flush = false);
}
