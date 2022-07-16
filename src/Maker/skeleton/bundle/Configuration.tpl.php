<?= "<?php\n" ?>
namespace <?= $namespace; ?>;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class <?= $class_name ?> implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('<?= strtolower($class_name) ?>');
        $rootNode = $treeBuilder->getRootNode();
        $rootNodeChildren = $rootNode->children();

        $rootNodeChildren = $this->createUserConfiguration($rootNodeChildren);
        $rootNodeChildren->end();

        return $treeBuilder;
    }

    public function createUserConfiguration(NodeBuilder $rootNodeChildren): NodeBuilder
    {
        return $rootNodeChildren;
    }
}