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


    // prix et information de tous les garages pour le devis
    public function liste_garage(){
        $conn = $this->getEntityManager()->getConnection();
        $sql = 
        "SELECT * 
        from garage
        inner join taux_horaire on taux_horaire.id = garage.taux_horaire_id
        order by t1 asc,t2 asc,t3 asc
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute(array());
        return $stmt->fetchAll();
    }

    // prix et information de tous les garages d'un utilisateur
    public function liste_garage_user($user_id){
        $conn = $this->getEntityManager()->getConnection();
        $sql = 
        "SELECT garage.*, taux_horaire.t1, taux_horaire.t2,taux_horaire.t3
        from garage
        inner join taux_horaire on taux_horaire.id = garage.taux_horaire_id
        where id_user=:user_id
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute(array(":user_id"=>$user_id));
        return $stmt->fetchAll();
    }

        // prix et information d'un garage d'un utilisateur
        public function info_garage_user($user_id,$garage_id){
            $conn = $this->getEntityManager()->getConnection();
            $sql = 
            "SELECT garage.*, taux_horaire.t1, taux_horaire.t2,taux_horaire.t3
            from garage
            inner join taux_horaire on taux_horaire.id = garage.taux_horaire_id
            where id_user=:user_id and garage.id=:garage_id
            ";
            $stmt = $conn->prepare($sql);
            $stmt->execute(array(":user_id"=>$user_id,":garage_id"=>$garage_id));
            return $stmt->fetch();
        }

    // ajouter un garage pour un user 
    public function ajout_garage($taux_horaire_id,$nom_garage,$emplacement,$id_user ){
        $conn = $this->getEntityManager()->getConnection();
        $sql = 
        "
        INSERT INTO garage (taux_horaire_id, nom_garage, emplacement, id_user)
        VALUES (:taux_horaire_id , :nom_garage , :emplacement, :id_user)
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute(array(":taux_horaire_id"=>$taux_horaire_id,":nom_garage"=>$nom_garage,"emplacement"=>$emplacement,"id_user"=>$id_user));
    }

        // mise à jour d'un garage pour un user 
        public function modifier_garage($taux_horaire_id,$nom_garage,$emplacement,$id_user ){
            $conn = $this->getEntityManager()->getConnection();
            $sql = 
            "
            update garage (taux_horaire_id, nom_garage, emplacement, id_user)
            VALUES (:taux_horaire_id , :nom_garage , :emplacement, :id_user)
            ";
            $stmt = $conn->prepare($sql);
            $stmt->execute(array(":taux_horaire_id"=>$taux_horaire_id,":nom_garage"=>$nom_garage,"emplacement"=>$emplacement,"id_user"=>$id_user));
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
