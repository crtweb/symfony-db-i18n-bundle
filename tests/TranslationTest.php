<?php
/**
 * 2019-04-22
 */

declare(strict_types=1);

namespace Creative\DbI18nBundle\Tests;

use Creative\DbI18nBundle\Entity\Translation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase as TestCase;

class TranslationTest extends TestCase
{
    /**
     * @var Translation
     */
    private $entity;

    public function setUp(): void
    {
        parent::setUp();
        $this->entity = new Translation();
    }

    public function testGetId()
    {
        self::assertNull($this->entity->getId());
    }

    public function testSetDomain()
    {
        self::assertInstanceOf(Translation::class, $this->entity->setDomain('domain'));
    }

    public function testSetLocale()
    {
        self::assertInstanceOf(Translation::class, $this->entity->setLocale('ru'));
    }

    public function testGetKey()
    {
        self::assertNull($this->entity->getKey());
    }

    public function testGetDomain()
    {
        self::assertNull($this->entity->getDomain());
        $this->entity->setDomain('domain');
        self::assertEquals('domain', $this->entity->getDomain());
    }

    public function testGetTranslation()
    {
        self::assertNull($this->entity->getTranslation());
        $this->entity->setTranslation('translation');
        self::assertEquals('translation', $this->entity->getTranslation());
    }

    public function testGetLocale()
    {
        self::assertNull($this->entity->getLocale());
        $this->entity->setLocale('en');
        self::assertEquals('en', $this->entity->getLocale());
    }

    public function testSetTranslation()
    {
        self::assertInstanceOf(Translation::class, $this->entity->setTranslation('translation'));
    }

    public function testSetKey()
    {
        self::assertInstanceOf(Translation::class, $this->entity->setKey('key'));
    }

    public function testLoad()
    {
        $params = [
            'locale' => 'en',
            'key' => 'key',
            'translation' => 'translation',
            'domain' => 'domain',
        ];

        self::assertInstanceOf(Translation::class, $this->entity->load($params));
        self::assertEquals('en', $this->entity->getLocale());
        self::assertEquals('key', $this->entity->getKey());
        self::assertEquals('translation', $this->entity->getTranslation());
        self::assertEquals('domain', $this->entity->getDomain());
    }
}
