<?php

namespace App\Repository;

use App\Entity\PopularityResult;
use App\Exception\NoTermInDbException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PopularityResult|null find($id, $lockMode = null, $lockVersion = null)
 * @method PopularityResult|null findOneBy(array $criteria, array $orderBy = null)
 * @method PopularityResult[]    findAll()
 * @method PopularityResult[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PopularityResultRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PopularityResult::class);
    }

    // /**
    //  * @return PopularityResult[] Returns an array of PopularityResult objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function findOneByTerm($term): ?PopularityResult
    {
        $term = $this->createQueryBuilder('p')
            ->andWhere('p.term = :val')
            ->setParameter('val', $term)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        if ($term === null) {
            throw new NoTermInDbException();
        }

        return $term;
    }
}
