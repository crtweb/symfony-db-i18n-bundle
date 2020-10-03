<?php
/**
 * 2019-04-19.
 */

declare(strict_types=1);

namespace Creative\DbI18nBundle\Loader;

use Creative\DbI18nBundle\Interfaces\DbLoaderInterface;
use Creative\DbI18nBundle\Interfaces\EntityInterface;
use Creative\DbI18nBundle\Interfaces\TranslationRepositoryInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
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
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * @var string
     */
    private $entityClass;

    /**
     * DbLoader constructor.
     * @param ParameterBagInterface $container
     * @param ManagerRegistry       $doctrine
     */
    public function __construct(ParameterBagInterface $container, ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->entityClass = $container->get('db_i18n.entity');
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
    public function load($resource, string $locale, string $domain = 'messages'): MessageCatalogue
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
     * {@inheritDoc}
     */
    public function getRepository(): TranslationRepositoryInterface
    {
        $repository = $this->doctrine->getRepository($this->entityClass);
        if ($repository instanceof TranslationRepositoryInterface) {
            return $repository;
        }

        throw new \RuntimeException(\sprintf('Cannot load repository %s', TranslationRepositoryInterface::class));
    }
}
