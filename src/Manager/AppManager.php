<?php


namespace App\Manager;


use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;

/**
 * Class AppManager
 * @package App\Manager
 */
class AppManager
{
    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $parameterBag;

    /**
     * AppManager constructor.
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    /**
     * @return bool
     */
    public function isAppInProduction(): bool
    {
        $sKernelEnvironment = $this->parameterBag->get('kernel.environment');
        $sServerIp = $_SERVER['SERVER_ADDR']??'127.0.0.1';
        $sClientIp = $_SERVER['REMOTE_ADDR']??'127.0.0.1';

        $bLocalServer = !filter_var($sServerIp, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
        $bDebugEnvironment = $sKernelEnvironment !== 'prod';
        $bIsServerAndClientTheSame = $sServerIp === $sClientIp;

        return ( (($bLocalServer === false AND $bIsServerAndClientTheSame === false) OR (PHP_SAPI === 'cli')) AND $bDebugEnvironment === false);
    }

    /**
     * @return array
     */
    public function getAppBundles(): array
    {
        static $aBundles;
        if(!isset($aBundles)){
            $oFinder = new Finder();
            $sBundlesFolder = $this->parameterBag->get('kernel.root_dir') . '/Bundles/';
            $oFiles = $oFinder->directories()->in($sBundlesFolder)->depth(0);
            $aBundles = [];
            foreach ($oFiles as $oFolder) {
                $aBundles[] = $oFolder->getFileName();
            }
        }
        return $aBundles;
    }
}