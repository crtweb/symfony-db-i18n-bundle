<?php
/**
 * 2019-04-20.
 */

declare(strict_types=1);

namespace Creative\DbI18nBundle\Command;

use Creative\DbI18nBundle\Interfaces\EntityInterface;
use Creative\DbI18nBundle\Interfaces\TranslationRepositoryInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class MigrateToDatabaseCommand.
 *
 * @package Creative\DbI18nBundle\Command
 */
class MigrateToDatabaseCommand extends Command
{
    private const HELP = <<<EOL
You can load all messages, stored in translation (yaml / xml) files, 
and save it to database to use in future with db-i18n module

Application container must have a 'locales' parameter, and this parameter must be an array.

Filename, passed as argument, must be compatible with Symfony localization files agreement.
For example: <info>messages.ru.yaml</info>
             <info>messages.ru.xlf</info>
             <info>my_awesome_translations.en.xlf</info> 
EOL;

    private const BATCH_SIZE = 100;

    /**
     * @var string
     */
    protected static $defaultName = 'creative:db-i18n:migrate';

    /**
     * @var ParameterBagInterface
     */
    private $container;

    /**
     * @var TranslatorInterface|Translator
     */
    private $translator;

    /**
     * @var string
     */
    private $entityClass;

    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * @var TranslationRepositoryInterface
     */
    private $translationEntityRepository;

    /**
     * MigrateToDatabaseCommand constructor.
     *
     * @param ParameterBagInterface $container
     * @param TranslatorInterface   $translator
     * @param ManagerRegistry       $doctrine
     * @param string|null           $name
     */
    public function __construct(ParameterBagInterface $container, TranslatorInterface $translator, ManagerRegistry $doctrine, string $name = null)
    {
        parent::__construct($name);
        $this->container = $container;
        $this->translator = $translator;
        $this->entityClass = $this->container->get('db_i18n.entity');
        $this->doctrine = $doctrine;
    }

    /**
     * Configure options.
     */
    protected function configure(): void
    {
        $this->setDescription('Load data from translation file and pass it to database')
            ->addArgument('source-file', InputArgument::REQUIRED, 'File to import')
            ->setHelp(self::HELP)
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->translationEntityRepository = $this->doctrine->getRepository($this->entityClass);

        if (!method_exists($this->translator, 'getCatalogue')) {
            throw new RuntimeException('Translator service of application has no \'getCatalogue\' method');
        }

        if (!$this->container->has('locales') || !is_array($this->container->get('locales'))) {
            throw new RuntimeException('Application container must have a \'locales\' parameter, and this parameter must be an array');
        }

        $io = new SymfonyStyle($input, $output);
        $filePath = $this->locateFile($input->getArgument('source-file'));

        $locale = $this->getLocale(pathinfo($filePath, PATHINFO_FILENAME));
        $domain = trim(str_replace($locale, '', pathinfo($filePath, PATHINFO_FILENAME)), '.');
        $catalogue = $this->translator->getCatalogue($locale);

        $forExport = $catalogue->all($domain);
        $exported = $this->exportToDatabase($forExport, $locale, $this->container->get('db_i18n.domain'));

        $io->writeln(sprintf(
            'Loaded form %s: %u messages, exported to database: %s',
            $filePath,
            count($forExport),
            $exported
        ));

        return 0;
    }

    /**
     * @param array  $messages
     * @param string $locale
     * @param string $domain
     *
     * @return int
     */
    protected function exportToDatabase(array $messages, string $locale, string $domain): int
    {
        $count = 0;
        $i = 0;
        $em = $this->doctrine->getManager();
        foreach ($messages as $key => $value) {
            ++$count;
            ++$i;
            $em->persist($this->makeEntity($key, $value, $locale, $domain));
            if ($i > self::BATCH_SIZE) {
                $i = 0;
                $em->flush();
            }
        }
        $em->flush();

        return $count;
    }

    /**
     * @param string $key
     * @param string $translation
     * @param string $locale
     *
     * @return EntityInterface
     */
    protected function makeEntity(string $key, string $translation, string $locale, string $domain): EntityInterface
    {
        $entity = $this->checkEntityExists($locale, $key);
        $entity->load([
            'domain' => $domain,
            'locale' => $locale,
            'key' => $key,
            'translation' => $translation,
        ]);

        return $entity;
    }

    /**
     * @param string $locale
     * @param string $key
     *
     * @return EntityInterface|object
     */
    protected function checkEntityExists(string $locale, string $key): EntityInterface
    {
        $entity = $this->translationEntityRepository->findOneBy([
            'locale' => $locale,
            'key' => $key,
        ]);

        if ($entity === null) {
            $entity = new $this->entityClass();
        }

        return $entity;
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    protected function getLocale(string $filename): ?string
    {
        $locales = $this->container->get('locales');
        $locale = null;
        foreach ($locales as $localeParam) {
            if (strpos($filename, $localeParam) !== false) {
                $locale = $localeParam;
            }
        }

        if ($locale === null) {
            throw new RuntimeException(sprintf('No one %s found in \'%s\'', implode(', ', $locales), $filename));
        }

        return $locale;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function locateFile(string $path): string
    {
        $realPath = null;
        if (strpos($path, '/') === 0) {
            $realPath = $path;
        } else {
            $realPath = $this->container->get('kernel.root_dir') . '/../' . $path;
        }

        if (!is_file($realPath) || !is_readable($realPath)) {
            throw new RuntimeException(sprintf('Unable to load %s file', $realPath));
        }

        return $realPath;
    }
}
