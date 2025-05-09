<?php

namespace App\Repository;

use App\Entity\Livres;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Livres>
 */
class LivresRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livres::class);
    }
    public function searchByCriteria(array $criteria)
    {
        $qb = $this->createQueryBuilder('l')
            ->leftJoin('l.categorie', 'c');

        if (!empty($criteria['titre'])) {
            $qb->andWhere('l.titre LIKE :titre')
               ->setParameter('titre', '%'.$criteria['titre'].'%');
        }

        if (!empty($criteria['editeur'])) {
            $qb->andWhere('l.editeur LIKE :editeur')
               ->setParameter('editeur', '%'.$criteria['editeur'].'%');
        }

        if (!empty($criteria['categorie'])) {
            $qb->andWhere('c.libelle = :categorie')
               ->setParameter('categorie', $criteria['categorie']);
        }

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return Livres[] Returns an array of Livres objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Livres
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
