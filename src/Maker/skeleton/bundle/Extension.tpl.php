<?= "<?php\n" ?>
namespace <?= $namespace; ?>;

use InvalidArgumentException;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Parser as YamlParser;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class <?= $class_name ?> extends Extension implements PrependExtensionInterface
{
    /**
     * Loads a specific configuration.
     *
     * @throws InvalidArgumentException When provided tag is not defined in this extension
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $baseConfiguration = new Configuration();
        try {
            $config = $this->processConfiguration($baseConfiguration, $configs);
        } catch (InvalidConfigurationException $e) {
            // Fallback: ignore invalid config from the container user config file and abort use configuration in load
            echo '[<?= $sBundleName ?> Bundle] invalid user config, bundle config was skipped: ' . $e->getMessage();
            $config = [];
        }
        // Use the config only if it is fully validated from the processed configuration
        if (!empty($config)) {
            $container->setParameter('<?= strtolower($sBundleName) ?>.options', (array)($config['options'] ?? []));
        }
        // Load the services (with parameters loaded)
        try {
            $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
            $loader->load('services.yaml');
        } catch (\Exception $e) {
            echo '[<?= $sBundleName ?> Bundle] invalid services config found: ' . $e->getMessage();
        }
    }

    /**
     * Allow an extension to prepend the extension configurations.
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $baseConfiguration = new Configuration();
        // Load the configuration from files
        // The configuration of our ThemeExtension
        $configs = $container->getExtensionConfig($this->getAlias());
        try {
            // use the Configuration class to generate a config array with the config extension
            $config = $this->processConfiguration($baseConfiguration, $configs);
        } catch (InvalidConfigurationException $e) {
            // Fallback: ignore invalid config from the container user config file and abort use configuration in prepend
            echo '<?= $sBundleName ?> Bundle: invalid config (prepend): ' . $e->getMessage() . PHP_EOL . '    The config options for the bundle were skipped' . PHP_EOL;
            $config = [];
        }

        // Create the parameter for the service (dependency with <?= strtolower($sBundleName) ?>.extension.class) even if empty config
        $container->setParameter('<?= strtolower($sBundleName) ?>.options', (array)($config['options'] ?? []));

        // Create the parameter for the service (dependency with <?= strtolower($sBundleName) ?>.extension.class) even if empty config
        $container->setParameter('<?= strtolower($sBundleName) ?>.routes', (array)($config['routes'] ?? []));
    }
}