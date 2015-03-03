<?php

namespace Svd\CoreBundle\Document\Repository;

use Doctrine\ODM\MongoDB\DocumentManager;
use Knp\Component\Pager\Paginator;
use Svd\CoreBundle\Document\DocumentInterface;
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
     * Finds a single document by a set of criteria
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
     * Finds documents by a set of criteria
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
     * Get document manager
     *
     * @return DocumentManager
     */
    abstract protected function getDocumentManager();

    /**
     * Insert
     *
     * @param DocumentInterface $document document
     * @param bool              $flush    flag, if flush should be done?
     *
     * @return self
     */
    public function insert(DocumentInterface $document, $flush = false)
    {
        return $this->save($document, $flush);
    }

    /**
     * Update
     *
     * @param DocumentInterface $document document
     * @param bool              $flush    flag, if flush should be done?
     *
     * @return self
     */
    public function update(DocumentInterface $document, $flush = false)
    {
        return $this->save($document, $flush);
    }

    /**
     * Delete
     *
     * @param DocumentInterface $document document
     * @param bool              $flush    flag, if flush should be done?
     *
     * @return self
     */
    public function delete(DocumentInterface $document, $flush = false)
    {
        $this->getDocumentManager()
            ->remove($document);

        if ($flush) {
            $this->getDocumentManager()
                ->flush();
        }

        return $this;
    }

    /**
     * Save
     *
     * @param DocumentInterface $document document
     * @param bool              $flush    flag, if flush should be done?
     *
     * @return self
     */
    protected function save(DocumentInterface $document, $flush = false)
    {
        $this->getDocumentManager()
            ->persist($document);

        if ($flush) {
            $this->getDocumentManager()
                ->flush();
        }

        return $this;
    }
}
