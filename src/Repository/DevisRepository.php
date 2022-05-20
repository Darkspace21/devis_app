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

    public function liste_devis($user_id){
        $conn = $this->getEntityManager()->getConnection();
        $sql = "
        Select devis.id, type_prestation.nom_prestation, garage.nom_garage, devis.prix 
        from db_devis.devis
        inner join garage on garage.id= devis.garage_id
        inner join type_prestation on type_prestation.id =devis.type_prestation_id 
        where devis.user_id = :user_id
        ";
            $stmt = $conn->prepare($sql);
            $stmt->execute(array(":user_id"=>$user_id));
            return $stmt->fetchAll();
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

    // defini la liste des pieces necessaire en fonction de la presta choisi
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

    // defini le temps pour changer une pieces données
    public function temps_prestation($id_piece){
        $conn = $this->getEntityManager()->getConnection();
        $sql = 
        "SELECT pieces_necessaire.*
        FROM `pieces_necessaire`
        where pieces_necessaire.genartnr  =:id_piece
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute(array(":id_piece"=>$id_piece));
        return $stmt->fetch();
    }

    // prix et information de la liste des pièces choisis
    public function prix_piece($piece_id,$vehicule_id){
        $conn = $this->getEntityManager()->getConnection();
        $sql = 
        "SELECT db_devis.t001.marke,
        db_devis.t400.artnr,
        db_devis.t030.bez,
        db_devis.t400.vknzielnr,
        db_devis.tariffs_sup.c_all 
        from db_devis.t400
		inner join db_devis.t001 on db_devis.t400.dlnr = db_devis.t001.dlnr
        left join db_devis.tariffs_sup on db_devis.tariffs_sup.reference = db_devis.t400.artnr
        inner join db_devis.t320 on db_devis.t400.genartnr = db_devis.t320.genartnr
        inner join db_devis.t030 on db_devis.t320.beznr = db_devis.t030.beznr
        where
        db_devis.t400.genartnr = :piece_id
        and db_devis.t400.vknzielnr = :vehicule_id
        and db_devis.t030.sprachnr = '006'
        and db_devis.tariffs_sup.c_all <> '0'
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute(array(":piece_id"=>$piece_id,":vehicule_id"=>$vehicule_id));
        return $stmt->fetch();
    }

    public function creer_devis($user_id,$type_prestation_id,$prix_total,$garage_id,$prix_main_oeuvre ){
        $conn = $this->getEntityManager()->getConnection();
        $sql = 
        "
        INSERT INTO devis (user_id, type_prestation_id, prix, garage_id, main_oeuvre)
        VALUES (:user_id , :type_prestation_id , :prix_total, :garage_id, :prix_main_oeuvre)
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute(array("user_id"=>$user_id,":garage_id"=>$garage_id,":type_prestation_id"=>$type_prestation_id,"prix_total"=>$prix_total,":prix_main_oeuvre"=>$prix_main_oeuvre));
    }

    // defini le temps pour changer une pieces données
    public function max_id_devis($user_id){
        $conn = $this->getEntityManager()->getConnection();
        $sql = 
        "SELECT MAX(id) as id
        FROM `devis`
        where devis.user_id  = :user_id
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute(array(":user_id"=>$user_id));
        return $stmt->fetch();
    }
    
    public function devis_pieces_choisi($genartnr, $temps, $nom, $marque, $prix, $devis_id){
        $conn = $this->getEntityManager()->getConnection();
        $sql = 
        "
        INSERT INTO pieces_choisi (genartnr, temps, nom, marque, prix, devis_id )
        VALUES (:genartnr , :temps , :nom, :marque, :prix,:devis_id )
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute(array("genartnr"=>$genartnr,":temps"=>$temps,":nom"=>$nom,"marque"=>$marque,":prix"=>$prix,":devis_id"=>$devis_id));
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
