<?php
/**
 * 2019-04-19.
 */

declare(strict_types=1);

namespace Creative\DbI18nBundle\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ObjectRepository;

interface TranslationRepositoryInterface extends ObjectRepository
{
    /**
     * @param string $domain
     * @param string $locale
     *
     * @return array|Collection|EntityInterface[]
     */
    public function findByDomainAndLocale(string $domain, string $locale);
}
