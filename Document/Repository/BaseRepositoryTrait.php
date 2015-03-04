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
     * @param array $criteria criteria
     *
     * @return object|null
     */
    abstract public function findOneBy(array $criteria);

    /**
     * Get one with full data
     *
     * @param array      $criteria criteria
     * @param array|null $sort     sort criteria
     *
     * @return object|null
     */
    public function getOneBy(array $criteria, array $sort = null)
    {
        $results = $this->findBy($criteria, $sort, 1);
        $result = array_shift($results);

        return $result;
    }

    /**
     * Get one with full data or throw error 404
     *
     * @param array      $criteria criteria
     * @param array|null $sort     sort criteria
     *
     * @return object
     *
     * @throws NotFoundHttpException
     */
    public function getOneByOr404(array $criteria, array $sort = null)
    {
        $result = $this->getOneBy($criteria, $sort);
        if (!$result) {
            throw new NotFoundHttpException();
        }

        return $result;
    }

    /**
     * Finds documents by a set of criteria
     *
     * @param array        $criteria criteria
     * @param array|null   $sort     sort criteria
     * @param integer|null $limit    limit
     * @param integer|null $skip     skip
     *
     * @return array
     */
    abstract public function findBy(array $criteria, array $sort = null, $limit = null, $skip = null);

    /**
     * Get all with full data
     *
     * @param array        $criteria criteria
     * @param array|null   $sort     sort criteria
     * @param integer|null $limit    limit
     * @param integer|null $skip     skip
     *
     * @return array
     */
    public function getBy(array $criteria, array $sort = null, $limit = null, $skip = null)
    {
        $result = $this->findBy($criteria, $sort, $limit, $skip);

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
