<?php

namespace Svd\CoreBundle\Entity\Repository;

use Knp\Component\Pager\Paginator;
use Svd\CoreBundle\Entity\EntityInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Entity repository
 */
interface BaseRepositoryInterface
{
    /**
     * Set translator
     *
     * @param TranslatorInterface $translator translator
     */
    public function setTranslator(TranslatorInterface $translator);

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
     * @param EntityInterface $entity entity
     * @param bool            $flush  flag, if flush should be done?
     * @param bool            $clear  flag, if clear should be done?
     *
     * @return self
     */
    public function insert(EntityInterface $entity, $flush = false, $clear = false);

    /**
     * Update
     *
     * @param EntityInterface $entity entity
     * @param bool            $flush  flag, if flush should be done?
     * @param bool            $clear  flag, if clear should be done?
     *
     * @return self
     */
    public function update(EntityInterface $entity, $flush = false, $clear = false);

    /**
     * Delete
     *
     * @param EntityInterface $entity entity
     * @param bool            $flush  flag, if flush should be done?
     * @param bool            $clear  flag, if clear should be done?
     *
     * @return self
     */
    public function delete(EntityInterface $entity, $flush = false, $clear = false);

    /**
     * Iterate by
     *
     * @param array      $criteria criteria
     * @param array|null $orderBy  order by
     *
     * @return object[]
     */
    public function iterateBy(array $criteria = [], array $orderBy = ['id' => 'asc']);
}
