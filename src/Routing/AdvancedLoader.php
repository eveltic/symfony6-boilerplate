<?php
namespace App\Routing;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class AdvancedLoader
 * @package App\Routing
 */
class AdvancedLoader extends Loader
{
    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $parameterBag;

    /**
     * AdvancedLoader constructor.
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(ParameterBagInterface $parameterBag)
    {
        parent::__construct();
        $this->parameterBag = $parameterBag;
    }

    /**
     * @param mixed $resource
     * @param string|null $type
     * @return RouteCollection
     */
    public function load($resource, string $type = null): RouteCollection
    {
        $collection = new RouteCollection();
        if(is_dir($this->parameterBag->get('kernel.project_dir') . '/src/Bundles')){
            $oFilesystem = new Filesystem();
            $aKernelBundles = $this->parameterBag->get('kernel.bundles');
            $oFiles = (new Finder())->in($this->parameterBag->get('kernel.project_dir') . '/src/Bundles')->directories()->depth(0);
            foreach ($oFiles as $oFolder){
                $sStandardRoutesFile = $oFolder->getPathName() . '/Resources/config/routes.yaml';
                $sFolderName = rtrim($oFolder->getFileName(), 'Bundle');
                if($oFilesystem->exists($sStandardRoutesFile) AND array_key_exists(rtrim(sprintf('%sBundle', $sFolderName), '/'), $aKernelBundles)){
                    $sResource = sprintf('@%sBundle/Resources/config/routes.yaml', $sFolderName);
                    $importedRoutes = $this->import($sResource, 'yaml');
                    $collection->addCollection($importedRoutes);
                }
            }
        }    
        return $collection;
    }

    /**
     * @param mixed $resource
     * @param string|null $type
     * @return bool
     */
    public function supports($resource, string $type = null): bool
    {
        return 'advanced_extra' === $type;
    }
}