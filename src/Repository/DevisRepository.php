<?php

namespace App\Repository;

use App\Entity\Devis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Devis|null find($id, $lockMode = null, $lockVersion = null)
 * @method Devis|null findOneBy(array $criteria, array $orderBy = null)
 * @method Devis[]    findAll()
 * @method Devis[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DevisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Devis::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Devis $entity, bool $flush = true): void
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
    public function remove(Devis $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function listeMarque(){
        $conn = $this->getEntityManager()->getConnection();
        $sql = "
            SELECT DISTINCT marque FROM `liste_voiture`;
        ";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
    }

    public function listeModele(){
        $conn = $this->getEntityManager()->getConnection();
        $sql = "
            SELECT DISTINCT marque, modele FROM `liste_voiture`;
        ";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
    }

    public function listeVersion(){
        $conn = $this->getEntityManager()->getConnection();
        $sql = "
            SELECT * FROM `liste_voiture`
        ";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
    }

    public function ktypnr($marque,$modele,$version){
        $conn = $this->getEntityManager()->getConnection();
        $sql = "
        select DISTINCT db_devis.t120.ktypnr,
        marque.bez as marque,
        modele.bez as modele,
        vehicule.bez as vehicule
        from db_devis.t012 as marque, db_devis.t100,
        db_devis.t012 as modele, db_devis.t110,
        db_devis.t012 as vehicule, db_devis.t120
        where marque.sprachnr = '006'
        and modele.sprachnr = '006'
        and vehicule.sprachnr = '006'
        and db_devis.t120.kmodnr = t110.kmodnr
        and db_devis.t110.hernr = t100.hernr
        and db_devis.t100.lbeznr = marque.lbeznr
        and db_devis.t110.lbeznr = modele.lbeznr
        and db_devis.t120.lbeznr = vehicule.lbeznr
        and marque.bez=:marque
        and modele.bez=:modele
        and vehicule.bez=:version
        ;";
            $stmt = $conn->prepare($sql);
            $stmt->execute(array(":marque"=>$marque,":modele"=>$modele,":version"=>$version));
            return $stmt->fetch();
    }

    public function pieces_necessaire($presta){
        $conn = $this->getEntityManager()->getConnection();
        $sql = 
        "SELECT pieces_necessaire.*, type_prestation_pieces_necessaire.obligatoire FROM `pieces_necessaire`
        inner join type_prestation_pieces_necessaire on type_prestation_pieces_necessaire.pieces_necessaire_id=pieces_necessaire.id
        inner join type_prestation on type_prestation_pieces_necessaire.type_prestation_id=type_prestation.id
        where type_prestation.nom_prestation =:presta
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute(array(":presta"=>$presta));
        return $stmt->fetchAll();
    }

    

    // /**
    //  * @return Devis[] Returns an array of Devis objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Devis
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
