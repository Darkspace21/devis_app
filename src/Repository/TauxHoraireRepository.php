<?php

namespace App\Repository;

use App\Entity\TauxHoraire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TauxHoraire|null find($id, $lockMode = null, $lockVersion = null)
 * @method TauxHoraire|null findOneBy(array $criteria, array $orderBy = null)
 * @method TauxHoraire[]    findAll()
 * @method TauxHoraire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TauxHoraireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TauxHoraire::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(TauxHoraire $entity, bool $flush = true): void
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
    public function remove(TauxHoraire $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function ajout_taux_horaire($T1,$T2,$T3){
        $conn = $this->getEntityManager()->getConnection();
        $sql = 
        "INSERT INTO taux_horaire (t1, t2, t3)
        VALUES (:T1, :T2, :T3)
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute(array(":T1"=>$T1,":T2"=>$T2,"T3"=>$T3));
    }

    public function maxId($T1,$T2,$T3){
        $conn = $this->getEntityManager()->getConnection();
        $sql = 
        "SELECT MAX(id) as id FROM `taux_horaire` 
        where t1= :T1 and t2= :T2 and t3= :T3
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute(array(":T1"=>$T1,":T2"=>$T2,"T3"=>$T3));
        return $stmt->fetch();
    }

    // /**
    //  * @return TauxHoraire[] Returns an array of TauxHoraire objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TauxHoraire
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
