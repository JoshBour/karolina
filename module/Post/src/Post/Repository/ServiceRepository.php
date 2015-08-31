<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 19/6/2014
 * Time: 12:15 πμ
 */

namespace Post\Repository;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\EntityRepository;
use Zend\Paginator\Paginator;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;


class ServiceRepository extends EntityRepository
{
    public function search($value, $page = 1, $count = null, $sort = null)
    {
        if (!empty($value)) {
            $qb = $this->createQueryBuilder('s');
            $qb->where($qb->expr()->like('s.name', '?1'))
                ->setParameter(1, '%' . $value . '%');

            if ($sort) $qb->orderBy("s." . $sort["column"], $sort["type"]);


            $paginator = new Paginator(new DoctrineAdapter(new ORMPaginator($qb)));
            if ($count) $paginator->setDefaultItemCountPerPage($count);
            $paginator->setCurrentPageNumber($page);

            return $paginator;
        } else {
            throw new \InvalidArgumentException("The service's name can't be empty.");
        }
    }
} 