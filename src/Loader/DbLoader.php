<?php
/**
 * 2019-04-19.
 */

declare(strict_types=1);

namespace Creative\DbI18nBundle\Loader;

use Creative\DbI18nBundle\Interfaces\DbLoaderInterface;
use Creative\DbI18nBundle\Interfaces\EntityInterface;
use Creative\DbI18nBundle\Interfaces\TranslationRepositoryInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Translation\Exception\InvalidResourceException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Class DbLoader
 * @package Creative\DbI18nBundle\Loader
 */
class DbLoader implements LoaderInterface, DbLoaderInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $doctrine;

    /**
     * @var string
     */
    private $entityClass;

    /**
     * DbLoader constructor.
     * @param ContainerInterface $container
     * @param EntityManagerInterface $doctrine
     */
    public function __construct(ContainerInterface $container, EntityManagerInterface $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->entityClass = $container->getParameter('db_i18n.entity');
    }

    /**
     * Loads a locale.
     *
     * @param mixed  $resource A resource
     * @param string $locale   A locale
     * @param string $domain   The domain
     *
     * @return MessageCatalogue A MessageCatalogue instance
     *
     * @throws NotFoundResourceException when the resource cannot be found
     * @throws InvalidResourceException  when the resource cannot be loaded
     */
    public function load($resource, $locale, $domain = 'messages')
    {
        $messages = $this->getRepository()->findByDomainAndLocale($domain, $locale);

        $values = array_map(static function (EntityInterface $entity) {
            return $entity->getTranslation();
        }, $messages);

        $catalogue = new MessageCatalogue($locale, [
            $domain => $values,
        ]);

        return $catalogue;
    }

    /**
     * @return TranslationRepositoryInterface|ObjectRepository
     */
    public function getRepository(): TranslationRepositoryInterface
    {
        return $this->doctrine->getRepository($this->entityClass);
    }
}
