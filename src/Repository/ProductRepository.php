<?php

namespace App\Repository;

use App\Classe\Search;
use App\Entity\Product;
use App\Entity\Category;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function save(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Request to get an array of product by filter
     * @param Search $search
     * @return Product[] 
     */
    public function findWithSearch(Search $search){
        $query = $this
        ->createQueryBuilder('p')
        ->select('c', 'p')
        ->join('p.category', 'c');

        if(!empty($search->string)){
            $query = $query
                ->andWhere('p.name LIKE :string')
                ->setParameter('string', "%{$search->string}%")
            ;
        }

        return $query->getQuery();

    }

    /**
     * Request to get an array of product by filter
     * @param Search $search
     * @return Product[] 
     */
    public function findWithSearchAndCategory(Search $search, $category){
        $query = $this
        ->createQueryBuilder('p')
        ->select('c', 'p')
        ->join('p.category', 'c');

        if(!empty($category)) {
            $query = $query
                ->andWhere('c.id = (:category)')
                ->setParameter('category', $category)
            ;
        }

        if(!empty($search->string)){
            $query = $query
                ->andWhere('p.name LIKE :string')
                ->setParameter('string', "%{$search->string}%")
            ;
        }

        return $query->getQuery();
    }

    public function findByCategory(Search $search, Category $category){
        $query = $this
        ->createQueryBuilder('p')
        ->select('c', 'p')
        ->join('p.category', 'c');

        if(!empty($category)) {
            $query = $query
                ->andWhere('c == (:category)')
                ->setParameter('category', $category)
            ;
        }

        if(!empty($search->string)){
            $query = $query
                ->andWhere('p.name LIKE :string')
                ->setParameter('string', "%{$search->string}%")
            ;
        }

        return $query->getQuery();
    }

    /**
     * @return Product[] Returns 3 last product by date
     */
    public function findLastProducts(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
