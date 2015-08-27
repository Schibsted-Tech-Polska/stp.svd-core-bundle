<?php

namespace Svd\CoreBundle\Entity\Repository;

use Doctrine\DBAL\Driver\Statement;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use InvalidArgumentException;
use Knp\Component\Pager\Paginator as KnpPaginator;
use Svd\CoreBundle\Entity\EntityInterface;
use Svd\CoreBundle\Model\ModelInterface;
use Svd\CoreBundle\Model\Virtual\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Base repository trait
 */
trait BaseRepositoryTrait
{
    /** @var TranslatorInterface */
    protected $translator;

    /** @var KnpPaginator */
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
     * Set KNP paginator
     *
     * @param KnpPaginator $paginator KNP paginator
     */
    public function setPaginator(KnpPaginator $paginator)
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
     * Get paginator
     *
     * @param ModelInterface $model            model
     * @param array          $criteria         criteria
     * @param array|null     $orderBy          order by
     * @param integer        $neighboursNumber neighbours number
     * @param boolean        $getFirstLast     get first last
     *
     * @return Paginator
     *
     * @throws InvalidArgumentException
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getPaginator(ModelInterface $model, array $criteria, array $orderBy = null, $neighboursNumber = 1,
        $getFirstLast = false)
    {
        if (!method_exists($model, 'getId')) {
            throw new InvalidArgumentException('ModelInterface argument need to have getId method.');
        }

        $idKey = 'id';
        $qb = $this->getPaginatorQueryBuilder($idKey, $model, $criteria, $orderBy);

        $paginator = new Paginator();
        $i = 1;
        $first = null;
        $previous = [];
        $next = [];
        $last = null;

        /** @var IterableResult */
        $iterableResult = $qb->getQuery()
            ->iterate(null, AbstractQuery::HYDRATE_ARRAY);
        foreach ($iterableResult as $results) {
            $result = array_shift($results);
            $id = $result[$idKey];
            $isRequestedId = $id == $model->getId();

            if ($i == 1 && $getFirstLast) {
                $first = $isRequestedId ? false : $id;
            }
            if ($isRequestedId) {
                $paginator->setCurrentNo($i);
                if ($getFirstLast) {
                    $last = false;
                }
            } else {
                if ($paginator->getCurrentNo() === null) {
                    array_unshift($previous, $id);
                    if (count($previous) > $neighboursNumber) {
                        array_pop($previous);
                    }
                } else {
                    if (count($next) < $neighboursNumber) {
                        array_push($next, $id);
                    } elseif (!$getFirstLast) {
                        break;
                    }
                }
            }
            if ($getFirstLast) {
                $last = $id;
                $paginator->setTotalNumber($i);
            }

            $i++;
        }

        if ($first) {
            /** @var ModelInterface $first */
            $first = $this->getOneBy([
                $idKey => $first,
            ]);
            $paginator->setFirst($first);
        }
        $paginator->setPrevious($this->getNeighbours($previous, $idKey, $neighboursNumber));
        $paginator->setNext($this->getNeighbours($next, $idKey, $neighboursNumber));
        if ($last) {
            /** @var ModelInterface $last */
            $last = $this->getOneBy([
                $idKey => $last,
            ]);
            $paginator->setLast($last);
        }

        return $paginator;
    }

    /**
     * Get paginator query builder
     *
     * @param string         $idKey    ID key
     * @param ModelInterface $model    model
     * @param array          $criteria criteria
     * @param array|null     $orderBy  order by
     *
     * @return QueryBuilder
     */
    protected function getPaginatorQueryBuilder($idKey, ModelInterface $model, array $criteria, array $orderBy = null)
    {
        // just to use $model object (this object could be needed when someone would like to to overwrite this method)
        $model->getId();

        $alias = 't';
        $qb = $this->createQueryBuilder($alias)
            ->select($alias . '.' . $idKey);
        foreach ($criteria as $column => $value) {
            $qb->andWhere($alias . '.' . $column . ' = :' . $column)
                ->setParameter($column, $value);
        }
        if (isset($orderBy)) {
            foreach ($orderBy as $column => $direction) {
                $qb->addOrderBy($alias . '.' . $column, $direction);
            }
        }

        return $qb;
    }

    /**
     * Get neighbours
     *
     * @param array   $ids              IDs
     * @param string  $idKey            ID key
     * @param integer $neighboursNumber neighbours number
     *
     * @return ModelInterface[]
     */
    protected function getNeighbours($ids, $idKey, $neighboursNumber)
    {
        $neighbours = [];

        foreach ($ids as $i => $id) {
            $neighbours[] = $this->getOneBy([
                $idKey => $id,
            ]);
        }
        for ($i = count($neighbours); $i < $neighboursNumber; $i++) {
            $neighbours[] = false;
        }

        return $neighbours;
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
