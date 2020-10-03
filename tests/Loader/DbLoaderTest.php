<?php
/**
 * 23.03.2020
 */

declare(strict_types=1);

namespace Creative\DbI18nBundle\Tests\Loader;

use Creative\DbI18nBundle\Entity\Translation;
use Creative\DbI18nBundle\Loader\DbLoader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Translation\MessageCatalogue;

class DbLoaderTest extends KernelTestCase
{
    public function testIsServiceConfigured(): void
    {
        self::bootKernel();
        self::assertInstanceOf(DbLoader::class, self::$container->get('translation.loader.db'));
    }

    protected function setUp(): void
    {
        self::bootKernel();
        $doctrine = self::$container->get('doctrine');
        /** @var EntityManager $em */
        $em = $doctrine->getManager();

        $schemaTool = new SchemaTool($em);

        $schemaTool->dropSchema([$em->getClassMetadata(Translation::class)]);
        $schemaTool->updateSchema([$em->getClassMetadata(Translation::class)]);

        $item = (new Translation())
            ->setDomain('db_messages')
            ->setKey('translatable.key')
            ->setLocale('en')
            ->setTranslation('This is a translation of key');
        $doctrine = self::$container->get('doctrine');
        $em = $doctrine->getManager();
        $em->persist($item);
        $em->flush();

        parent::setUp();
    }

    public function testLoadCatalogue(): void
    {
        $service = self::$container->get('translation.loader.db');
        $cat = $service->load(null, 'en', 'db_messages');
        self::assertInstanceOf(MessageCatalogue::class, $cat);
        self::assertSame('This is a translation of key', $cat->get('translatable.key', 'db_messages'));
    }
}
