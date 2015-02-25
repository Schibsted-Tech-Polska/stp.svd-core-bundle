<?php

namespace Svd\CoreBundle\Document\Repository;

use Knp\Component\Pager\Paginator;
use Svd\CoreBundle\Document\DocumentInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Document repository
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
     * @param DocumentInterface $document document
     * @param bool              $flush  flag, if flush should be done?
     *
     * @return self
     */
    public function insert(DocumentInterface $document, $flush = false);

    /**
     * Update
     *
     * @param DocumentInterface $document document
     * @param bool              $flush  flag, if flush should be done?
     *
     * @return self
     */
    public function update(DocumentInterface $document, $flush = false);

    /**
     * Delete
     *
     * @param DocumentInterface $document document
     * @param bool              $flush  flag, if flush should be done?
     *
     * @return self
     */
    public function delete(DocumentInterface $document, $flush = false);
}
