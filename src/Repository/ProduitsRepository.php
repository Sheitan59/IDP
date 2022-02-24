<?php

namespace App\Repository;

use App\Classe\Search;
use App\Entity\Produits;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Produits|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produits|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produits[]    findAll()
 * @method Produits[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Produits[]    findWithSearch(class $search)
 * 
 */
class ProduitsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produits::class);
    }


    /**
     * 
     * @return Product[]
     */
    public function findWithSearch(Search $search)
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('c', 'p')
            ->join('p.categorie', 'c');

        if (!empty($search->categories)) {
            $query = $query
                ->andWhere('c.id IN (:categorie)')
                ->setParameter('categorie', $search->categories);
        }

        if (!empty($search->string)) {
            $recherche = strtolower($search->string);
            $query = $query
                ->andWhere('p.slug LIKE :string')
                ->setParameter('string', "%{$recherche}%");
        }

        return $query->getQuery()->getResult();
    }


    /*
    public function findOneBySomeField($value): ?Produits
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
