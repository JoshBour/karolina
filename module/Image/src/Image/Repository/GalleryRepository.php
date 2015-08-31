<?php
namespace Image\Repository;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\EntityRepository;
use Zend\Paginator\Paginator;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;

class GalleryRepository extends EntityRepository{

    public function findAssoc(){
        $qb = $this->createQueryBuilder('g')
            ->select('g.galleryId as id, g.name as value')
            ->orderBy("g.galleryId","DESC");

        $resultArray = [];
        foreach($qb->getQuery()->getResult() as $result){
            $resultArray[$result["id"]] = $result["value"];
        }

        return $resultArray;
    }

    public function search($value, $page = 1, $count = null, $sort = null)
    {
        if (!empty($value)) {
            $qb = $this->createQueryBuilder('g');
            $qb->where($qb->expr()->like('g.name', '?1'))
                ->setParameter(1,'%' . $value . '%');

            if($sort) $qb->orderBy("g.".$sort["column"],$sort["type"]);


            $paginator = new Paginator(new DoctrineAdapter(new ORMPaginator($qb)));
            if($count) $paginator->setDefaultItemCountPerPage($count);
            $paginator->setCurrentPageNumber($page);

            return $paginator;
        } else {
            throw new \InvalidArgumentException("The gallery's name can't be empty.");
        }
    }
}