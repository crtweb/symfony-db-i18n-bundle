<?php

namespace Creative\DbI18nBundle\Repository;

use Creative\DbI18nBundle\Entity\Translation;
use Creative\DbI18nBundle\Interfaces\TranslationRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Translation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Translation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Translation[]    findAll()
 * @method Translation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TranslationRepository extends ServiceEntityRepository implements TranslationRepositoryInterface
{
    /**
     * TranslationRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Translation::class);
    }

    /**
     * @param string $domain
     * @param string $locale
     *
     * @return array|\Creative\DbI18nBundle\Interfaces\EntityInterface[]|\Doctrine\Common\Collections\Collection|mixed
     */
    public function findByDomainAndLocale(string $domain, string $locale)
    {
        return $this->createQueryBuilder('t', 't.key')
            ->where('t.domain = :domain')
            ->andWhere('t.locale = :locale')
            ->setParameter('domain', $domain)
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $locale
     *
     * @return ArrayCollection
     */
    public function findForUpdate(string $locale): ArrayCollection
    {
        $result = $this->createQueryBuilder('t')
            ->where('t.locale = :locale')
            ->setParameter('locale', $locale)
            ->orderBy('t.key', 'ASC')
            ->getQuery()->getResult();

        return new ArrayCollection($result);
    }
}
