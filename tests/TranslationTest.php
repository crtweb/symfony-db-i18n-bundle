<?php
/**
 * 2019-04-22
 */

declare(strict_types=1);

namespace Creative\DbI18nBundle\Tests;

use Creative\DbI18nBundle\Entity\Translation;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

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
        $this->assertNull($this->entity->getId());
    }

    public function testSetDomain()
    {
        $this->assertInstanceOf(Translation::class, $this->entity->setDomain('domain'));
    }

    public function testSetLocale()
    {
        $this->assertInstanceOf(Translation::class, $this->entity->setLocale('ru'));
    }

    public function testGetKey()
    {
        $this->assertNull($this->entity->getKey());
    }

    public function testGetDomain()
    {
        $this->assertNull($this->entity->getDomain());
        $this->entity->setDomain('domain');
        $this->assertEquals('domain', $this->entity->getDomain());
    }

    public function testGetTranslation()
    {
        $this->assertNull($this->entity->getTranslation());
        $this->entity->setTranslation('translation');
        $this->assertEquals('translation', $this->entity->getTranslation());
    }

    public function testGetLocale()
    {
        $this->assertNull($this->entity->getLocale());
        $this->entity->setLocale('en');
        $this->assertEquals('en', $this->entity->getLocale());
    }

    public function testSetTranslation()
    {
        $this->assertInstanceOf(Translation::class, $this->entity->setTranslation('translation'));
    }

    public function testSetKey()
    {
        $this->assertInstanceOf(Translation::class, $this->entity->setKey('key'));
    }

    public function testLoad()
    {
        $params = [
            'locale' => 'en',
            'key' => 'key',
            'translation' => 'translation',
            'domain' => 'domain',
        ];

        $this->assertInstanceOf(Translation::class, $this->entity->load($params));
        $this->assertEquals('en', $this->entity->getLocale());
        $this->assertEquals('key', $this->entity->getKey());
        $this->assertEquals('translation', $this->entity->getTranslation());
        $this->assertEquals('domain', $this->entity->getDomain());
    }
}
