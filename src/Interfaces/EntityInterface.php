<?php
/**
 * 2019-04-19.
 */

declare(strict_types=1);

namespace Creative\DbI18nBundle\Interfaces;

/**
 * Interface EntityInterface.
 *
 * @package Creative\DbI18nBundle\Interfaces
 *
 * Database entity for store translation MUST implements this interface
 */
interface EntityInterface
{
    /**
     * Load translated string.
     *
     * @return string|null
     */
    public function getTranslation(): ?string;

    /**
     * Load data to entity.
     * For example: imagine that entity has `domain`, `locale`, `key` and `translation` params
     * This method may be called as
     * ```
     * $entity->load([
     *    'domain' => $domain,
     *    'locale' => $locale,
     *    'key' => $key,
     *    'translation' => $translation,
     * ]);
     * ```
     * and return valid entity for store in database.
     *
     * @param array $params
     *
     * @return EntityInterface
     */
    public function load(array $params): self;
}
