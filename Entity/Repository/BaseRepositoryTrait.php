<?php

namespace Svd\CoreBundle\Entity\Repository;

use Doctrine\DBAL\Driver\Statement;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Knp\Component\Pager\Paginator;
use Svd\CoreBundle\Entity\EntityInterface;
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
     * @param EntityInterface $entity entity
     * @param bool            $flush  flag, if flush should be done?
     *
     * @return self
     */
    public function insert(EntityInterface $entity, $flush = false)
    {
        return $this->save($entity, $flush);
    }

    /**
     * Update
     *
     * @param EntityInterface $entity entity
     * @param bool            $flush  flag, if flush should be done?
     *
     * @return self
     */
    public function update(EntityInterface $entity, $flush = false)
    {
        return $this->save($entity, $flush);
    }

    /**
     * Delete
     *
     * @param EntityInterface $entity entity
     * @param bool            $flush  flag, if flush should be done?
     *
     * @return self
     */
    public function delete(EntityInterface $entity, $flush = false)
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
     * @param EntityInterface $entity entity
     * @param bool            $flush  flag, if flush should be done?
     *
     * @return self
     */
    protected function save(EntityInterface $entity, $flush = false)
    {
        $this->getEntityManager()
            ->persist($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }

        return $this;
    }

    /**
     * Get IN clause SQL (to support SQL's "IN()" clause inside prepaired statements in simpliest way)
     *
     * @param string $variableName variable name
     * @param array  $values       values
     * @param string $clause       clause
     *
     * @return string
     */
    protected function getInClauseSql($variableName, array $values, $clause)
    {
        $ret = '';
        $count = count($values);

        if ($count > 0) {
            $arrayVariables = [];
            for ($i = 1; $i <= $count; $i++) {
                $arrayVariables[] = $variableName . $i;
            }
            $ret = implode(',', $arrayVariables);

            if (!empty($clause)) {
                $ret = sprintf($clause, $ret);
            }
        }

        return $ret;
    }

    /**
     * Bind array values (to support SQL's "IN()" clause inside prepaired statements in simpliest way)
     *
     * @param Statement    $statement    statement
     * @param string       $variableName variable name
     * @param array        $values       values
     * @param integer|null $type         type
     *
     * @return self
     */
    protected function bindArrayValues(Statement $statement, $variableName, array $values, $type = null)
    {
        $i = 1;
        foreach ($values as $value) {
            $statement->bindValue($variableName . $i, $value, $type);
            $i++;
        }

        return $this;
    }
}
