<?php
/**
 * 2019-04-19.
 */

declare(strict_types=1);

namespace Creative\DbI18nBundle\Interfaces;

/**
 * Interface DbLoaderInterface.
 *
 * @package Creative\DbI18nBundle\Interfaces
 */
interface DbLoaderInterface
{
    /**
     * @return TranslationRepositoryInterface
     */
    public function getRepository(): TranslationRepositoryInterface;
}
