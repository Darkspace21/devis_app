<?php

namespace App\Repository;

use App\Entity\Garage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Garage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Garage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Garage[]    findAll()
 * @method Garage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GarageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Garage::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Garage $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Garage $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }


    // prix et information de la liste des pièces choisis
    public function liste_garage(){
        $conn = $this->getEntityManager()->getConnection();
        $sql = 
        "SELECT * 
        from garage
        inner join taux_horaire on taux_horaire.id = garage.taux_horaire_id
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute(array());
        return $stmt->fetchAll();
    }

    // /**
    //  * @return Garage[] Returns an array of Garage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Garage
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
