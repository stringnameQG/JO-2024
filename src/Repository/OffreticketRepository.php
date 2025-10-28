<?php

namespace App\Repository;

use App\Entity\Offreticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Offreticket>
 */
class OffreticketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Offreticket::class);
    }

    public function numberOffre(int $idOffre) : array {
        return $this->createQueryBuilder('o')
            ->select('o.compteurOffreVendue')
            ->Where('o.id = :idOffre')
            ->setParameter('idOffre', $idOffre)
            ->getQuery()
            ->getResult()
        ; 
    }

    public function valideAchat(int $reference, int $nombre)
    {   
        $numberOffre = $this->numberOffre($reference);

        $this->createQueryBuilder('o')
            ->update('App\\Entity\\Offreticket', 'o')
            ->set('o.compteurOffreVendue', $numberOffre[0]["compteurOffreVendue"] + $nombre)
            ->Where('o.id = :reference')
            ->setParameter('reference', $reference)
            ->getQuery()
            ->getResult()
        ;   

        return $this->numberPlace($reference);
    }

    public function numberPlace(int $idOffre) : array {
        return $this->createQueryBuilder('o')
            ->select('o.nombrePlace')
            ->Where('o.id = :idOffre')
            ->setParameter('idOffre', $idOffre)
            ->getQuery()
            ->getResult()
        ; 
    }


//    /**
//     * @return Offreticket[] Returns an array of Offreticket objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Offreticket
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
