<?php
namespace App\Maker;


use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

final class MakeBundle extends AbstractMaker
{
    private ParameterBagInterface $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public static function getCommandName(): string
    {
        return 'make:bundle';
    }

    /**
     * {@inheritdoc}
     */
    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->setDescription('Creates new Bundle inside application src/Bundles folder')
            ->addArgument('name', InputArgument::REQUIRED, sprintf('The bundle name (e.g. <fg=yellow>%s</>)', Str::asClassName(Str::getRandomTerm())))
            ->addOption('description', null, InputOption::VALUE_OPTIONAL, sprintf('The bundle description (e.g. <fg=yellow>%s</>)', Str::asClassName(Str::getRandomTerm())), 'A common library for any symfony related application')
            ->addOption('homepage', null, InputOption::VALUE_OPTIONAL, sprintf('The bundle homepage (e.g. <fg=yellow>%s</>)', 'https://yourhomepage.com'), 'https://eveltic.com')
            ->addOption('keywords', null, InputOption::VALUE_OPTIONAL, sprintf('The bundle keywords (e.g. <fg=yellow>%s</>)', 'php,symfony,bundle'), 'php,symfony,bundle')
            ->addOption('license', null, InputOption::VALUE_OPTIONAL, sprintf('The bundle license (e.g. <fg=yellow>%s</>)', 'MIT'), 'MIT')
            ->addOption('authors', null, InputOption::VALUE_OPTIONAL, sprintf('The bundle author (e.g. <fg=yellow>%s</>)', 'John smith'), 'Eveltic')
            ->addOption('php', null, InputOption::VALUE_OPTIONAL, sprintf('Minimal supported PHP version (must be at least the same than main application version)(e.g. <fg=yellow>%s</>)', '8.0.0'), '8.0.0')
            ->setHelp(file_get_contents(__DIR__ . '/help/MakeBundle.txt'));
    }

    public static function getCommandDescription(): string
    {
        return 'Creates new Bundle inside application src/Bundles folder';
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        try {
            // Get bundle data
            $bundleName = ucfirst( str_ends_with($input->getArgument('name'), 'Bundle') ? substr($input->getArgument('name'), 0, -6) :  $input->getArgument('name')); /* Remove Bundle keyword if present at string ending */
            $bundleDescription = ucfirst($input->getOption('description'));
            $bundleHomepage = $input->getOption('homepage');
            $bundleKeywords = ucfirst(str_replace(',', '","', $input->getOption('keywords')));
            $bundleLicense = ucfirst($input->getOption('license'));
            $bundleAuthors = ucfirst($input->getOption('authors'));
            $bundlePhp = $input->getOption('php');

            $bundlePath = $this->parameterBag->get('kernel.project_dir') . '/src/Bundles/';
            // Generate the folder structure
            $this->generateFolderStructure($io, $bundleName, $bundlePath);
            // Bundle Main File
            $extensionClassNameDetails = $generator->createClassNameDetails($input->getArgument('name'), sprintf('Bundles\\%s\\', $bundleName), 'Bundle');
            $generator->generateClass($extensionClassNameDetails->getFullName(), __DIR__ . '/skeleton/bundle/Bundle.tpl.php', ['sBundleName' => $bundleName,]);
            // Dependency Injection
            $extensionClassNameDetails = $generator->createClassNameDetails($input->getArgument('name'), sprintf('Bundles\\%s\\DependencyInjection\\', $bundleName), 'Extension');
            $generator->generateClass($extensionClassNameDetails->getFullName(), __DIR__ . '/skeleton/bundle/Extension.tpl.php', ['sBundleName' => $bundleName,]);
            // Configuration
            $generator->generateClass(sprintf('App\\Bundles\\%s\\DependencyInjection\\Configuration', $bundleName), __DIR__ . '/skeleton/bundle/Configuration.tpl.php', ['sBundleName' => $bundleName,]);
            $generator->writeChanges();
            // Config & other Files
            $oFilesystem = new Filesystem();
            $routes = str_replace('[:bundleName:]', strtolower($bundleName), "app_frontend_[:bundleName:]:\n  resource: '../../Controller/Frontend'\n  type: annotation\n  prefix: [:bundleName:]\n  options:\n    expose: true\napp_api_[:bundleName:]:\n  resource: '../../Controller/Api/'\n  type: annotation\n  prefix: api/[:bundleName:]\n  options:\n    expose: true\napp_ajax_[:bundleName:]:\n  resource: '../../Controller/Ajax/'\n  type: annotation\n  prefix: ajax/[:bundleName:]\n  options:\n    expose: true\napp_backend_[:bundleName:]:\n  resource: '../../Controller/Backend/'\n  type: annotation\n  prefix: backend/[:bundleName:]\n  options:\n    expose: true");
            $composer = str_replace(
                ['[:bundleName:]', '[:bundleNameCamelCase:]', '[:description:]', '[:homepage:]', '[:keywords:]', '[:license:]', '[:authors:]', '[:php:]', "'"], 
                [strtolower($bundleName), $bundleName, $bundleDescription, $bundleHomepage, $bundleKeywords, $bundleLicense, $bundleAuthors, $bundlePhp, '"'], 
                "{\n  'name':         '[:bundleName:]', \n  'description':  '[:description:]', \n  'homepage':     '[:homepage:]', \n  'type':         'symfony-bundle', \n  'keywords':     ['[:keywords:]'], \n  'license':      '[:license:]', \n  'authors': [\n    {'name': '[:authors:]'}\n  ], \n  'extra': {\n    'branch-alias': {\n      'dev-master': '1.0'\n    }\n  }, \n  'require':      {\n      'php': '>= [:php:]'\n  }, \n  'require-dev' :   {\n        }, \n  'autoload': {\n    'psr-4': {\n      'App\\\\Bundles\\\\[:bundleNameCamelCase:]\\\\': ''\n    }\n  }\n}");
            $oFilesystem->dumpFile(sprintf('%s%s/Resources/config/routes.yaml', $bundlePath, $bundleName), $routes);
            $io->writeln(' <fg=blue>created</>: ' . sprintf('src/Bundles/%s/Resources/config/routes.yaml', $bundleName));
            $oFilesystem->dumpFile(sprintf('%s%s/Resources/config/services.yaml', $bundlePath, $bundleName), '');
            $io->writeln(' <fg=blue>created</>: ' . sprintf('src/Bundles/%s/Resources/config/services.yaml', $bundleName));
            $oFilesystem->dumpFile(sprintf('%s%s/composer.json', $bundlePath, $bundleName), $composer);
            $io->writeln(' <fg=blue>created</>: ' . sprintf('src/Bundles/%s/composer.json', $bundleName));
            $oFilesystem->dumpFile(sprintf('%s%s/.gitignore', $bundlePath, $bundleName), '');
            $io->writeln(' <fg=blue>created</>: ' . sprintf('src/Bundles/%s/.gitignore', $bundleName));
            // Add to config/bundles.php
            $sBundlesFilePath = $this->parameterBag->get('kernel.project_dir') . '/config/bundles.php';
            $aContents = file($sBundlesFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $aContents[count($aContents) - 2] .= "\n" . sprintf("    App\Bundles\%s\%sBundle::class => ['all' => true], # Bundle Created with extended symfony maker tool", $bundleName, preg_replace('/'. preg_quote('Bundle', '/') . '$/', '', $bundleName));
            file_put_contents($sBundlesFilePath, implode("\n", $aContents));
            $io->writeln(' <fg=blue>created</>: ' . sprintf('%s added to bundles.php array', $bundleName));
            $this->writeSuccessMessage($io);
        } catch (\Exception $exception) {
            /* Set error message */
            $io->error(sprintf('The command failed, these are the details.'));
            $io->writeln(sprintf('Code: %s', $exception->getCode()));
            $io->writeln(sprintf('Message: %s', $exception->getMessage()));
            $io->writeln(sprintf('Trace:'));
            $io->writeln(sprintf('%s', $exception->getTraceAsString()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureDependencies(DependencyBuilder $dependencies)
    {

    }

    private function generateFolderStructure(ConsoleStyle $io, string $sBundleName, string $sBundlePath)
    {
        try {
            // Get vars
            $oFilesystem = new Filesystem();
            // Check folder
            if ($oFilesystem->exists($sBundlePath . $sBundleName)) {
                $io->error(sprintf('The bundle %s already exists, please select another one.', $sBundleName));
                $io->writeln('To exit the menu please press Ctrl+C');
                do {
                    $sBundleName = $io->ask('Name of the bundle', '', function ($sBundleName) use ($io, $sBundlePath, $oFilesystem) {
                        if ($oFilesystem->exists($sBundlePath . $sBundleName)) {
                            $io->error(sprintf('The bundle %s already exists', $sBundleName));
                        }
                        return $sBundleName;
                    });
                } while ($oFilesystem->exists($sBundlePath . $sBundleName));
            }
            // Generate folder structure
            $io->section(sprintf('We are generating the folder structure for the bundle %s.', $sBundleName));
            $aFolderStructure = [
                'Command',
                'Controller',
                'Controller/Ajax',
                'Controller/Api',
                'Controller/Backend',
                'Controller/Frontend',
                'DataFixtures',
                'DependencyInjection',
                'Entity',
                'EntityListener',
                'Event',
                'EventListener',
                'EventSubscriber',
                'Form',
                'Form/Type',
                'Manager',
                'Menu',
                'Mercure',
                'Model',
                'Repository',
                'Resources',
                'Resources/config',
                'Resources/public',
                'Resources/translations',
                'Resources/views',
                'Security',
                'Twig',
                'Twig/Extension',
            ];
            // Prepend base path
            array_walk($aFolderStructure, function (&$value, $key) use ($sBundlePath, $sBundleName) {
                $value = sprintf('%s%s/%s', $sBundlePath, $sBundleName, $value);
            });
            $oFilesystem->mkdir($aFolderStructure);

            // Generate a .keep file to track the folder into git
            $oFinder = new Finder();
            $oFiles = $oFinder->directories()->in(sprintf('%s%s', $sBundlePath, $sBundleName));
            foreach ($oFiles as $oFolder) {
                $oFilesystem->dumpFile(sprintf('%s/.gitkeep', $oFolder->getLinkTarget()), '');
            }
            return true;
        } catch (IOException $exception) {
            /* Set error message */
            $io->error(sprintf('The command failed, these are the details.'));
            $io->writeln(sprintf('Code: %s', $exception->getCode()));
            $io->writeln(sprintf('Message: %s', $exception->getMessage()));
            $io->writeln(sprintf('Trace:'));
            $io->writeln(sprintf('%s', $exception->getTraceAsString()));
            return false;
        }
    }
}