<?php

namespace App\Twig;


use Datetime;
use Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

/**
 * Class MiscExtension
 * @package App\Twig
 */
class MiscExtension extends AbstractExtension
{
    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $parameterBag;
    /**
     * @var Security
     */
    private Security $security;
    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $urlGenerator;
    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * MiscExtension constructor.
     * @param ParameterBagInterface $parameterBag
     * @param Security $security
     * @param UrlGeneratorInterface $urlGenerator
     * @param TranslatorInterface $translator
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ParameterBagInterface $parameterBag, Security $security, UrlGeneratorInterface $urlGenerator, TranslatorInterface $translator, EntityManagerInterface $entityManager)
    {
        $this->parameterBag = $parameterBag;
        $this->security = $security;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
        $this->entityManager = $entityManager;
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('isFile', [$this, 'isFile']),
            new TwigFilter('modifyDatetime', [$this, 'modifyDatetime']),
            new TwigFilter('routeExists', [$this, 'routeExists']),
            new TwigFilter('userHasRole', [$this, 'userHasRole']),
            new TwigFilter('userHasAllRoles', [$this, 'userHasAllRoles']),
            new TwigFilter('arrayFlip', [$this, 'arrayFlip']),
            new TwigFilter('values', [$this, 'values']),
            new TwigFilter('boolval', [$this, 'boolval']),
            new TwigFilter('getEnv', [$this, 'getEnv']),
            new TwigFilter('getParameter', [$this, 'getParameter']),
            new TwigFilter('stringToHexColour', [$this, 'stringToHexColour']),
            new TwigFilter('strPad', [$this, 'strPad']),
            new TwigFilter('arraySum', [$this, 'arraySum']),
        ];
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('isFile', [$this, 'isFile']),
            new TwigFunction('modifyDatetime', [$this, 'modifyDatetime']),
            new TwigFunction('routeExists', [$this, 'routeExists']),
            new TwigFunction('userHasRole', [$this, 'userHasRole']),
            new TwigFunction('arrayFlip', [$this, 'arrayFlip']),
            new TwigFunction('values', [$this, 'values']),
            new TwigFunction('currentDatetime', [$this, 'currentDatetime']),
            new TwigFunction('compareDatetime', [$this, 'compareDatetime']),
            new TwigFunction('boolval', [$this, 'boolval']),
            new TwigFunction('getEnv', [$this, 'getEnv']),
            new TwigFunction('getParameter', [$this, 'getParameter']),
            new TwigFunction('stringToHexColour', [$this, 'stringToHexColour']),
            new TwigFunction('strPad', [$this, 'strPad']),
            new TwigFunction('arraySum', [$this, 'arraySum']),
        ];
    }

    /**
     * @return TwigTest[]
     */
    public function getTests()
    {
        return [
            new TwigTest('instanceof', [$this, 'isInstanceof']),
        ];
    }

    /**
     * @param $path
     * @return bool
     */
    public function isFile($path): bool
    {
        return is_file(realpath($path));
    }

    /**
     * @param $text
     * @param int $min_brightness
     * @param int $spec
     * @return string
     * @throws Exception
     */
    public function stringToHexColour($text, $min_brightness = 60, $spec = 3): string
    {
        // Check inputs
        if(!is_int($min_brightness)) throw new \Exception("$min_brightness is not an integer");
        if(!is_int($spec)) throw new \Exception("$spec is not an integer");
        if($spec < 2 or $spec > 10) throw new \Exception("$spec is out of range");
        if($min_brightness < 0 or $min_brightness > 255) throw new \Exception("$min_brightness is out of range");
        $hash = md5($text);  //Gen hash of text
        $colors = array();
        for($i=0;$i<3;$i++){
            // Convert hash into 3 decimal values between 0 and 255
            $colors[$i] = max(array(round(((hexdec(substr($hash,$spec * $i,$spec)))/hexdec(str_pad('',$spec,'F')))*255),$min_brightness));
        }
        // Only check brightness requirements if min_brightness is about 100
        if($min_brightness > 0) {
            // Loop until brightness is above or equal to min_brightness
            while( array_sum($colors)/3 < $min_brightness ){
                for($i = 0 ; $i < 3 ; $i++){
                    // Increase each color by 10
                    $colors[$i] += 10;
                }
            }
        }
        $output = '';
        for($i = 0 ; $i < 3 ; $i++){
            // Convert each color to hex and append to output
            $output .= str_pad(dechex($colors[$i]),2,0,STR_PAD_LEFT);
        }
        return '#' . $output;
    }

    /**
     * @param $key
     * @return array|false|mixed|string
     */
    public function getParameter($key)
    {
        return $this->parameterBag->get($key);
    }

    /**
     * @param $key
     * @return array|false|mixed|string
     */
    public function getEnv($key)
    {
        $sResult = getenv($key);
        return !empty($sResult) ? $sResult : (isset($_ENV[$key]) ? $_ENV[$key] : '');
    }

    /**
     * @param Datetime $oDatetime
     * @param string $mode
     * @param int $minutes
     * @return DateTime
     * @throws Exception
     */
    public function modifyDatetime(Datetime $oDatetime, string $mode = 'add', int $minutes = 0): DateTime
    {
        $oNewDatetime = new DateTime($oDatetime->format('C'));
        $oInterval = new \DateInterval(sprintf('PT%sM', $minutes));
        if($mode === 'sub'){
            $oInterval->invert = 1;
        }
        $oNewDatetime->add($oInterval);
        return $oNewDatetime;
    }

    /**
     * @return bool
     */
    public function boolval($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @param $roles
     * @return bool
     */
    public function userHasRole($roles): bool
    {
        $bHasAccess = false;
        foreach($roles as $role){
            if($this->security->isGranted($role)){
                $bHasAccess = true;
                break;
            }
        }
        return $bHasAccess;
    }

    /**
     * @param $roles
     * @return bool
     */
    public function userHasAllRoles($roles): bool
    {
        return count(array_diff($roles, $this->security->getUser()->getRoles())) === 0;
    }

    /**
     * @param $array
     * @return array
     */
    public function arrayFlip($array): array
    {
        return array_flip($array);
    }

    /**
     * @param $array
     * @return array
     */
    public function values(array $array): array
    {
        return array_values($array);
    }

    /**
     * @param $route
     * @param array|null $params
     * @return bool
     */
    public function routeExists($route, ?array $params = []): bool
    {
        try {
            $this->urlGenerator->generate($route, $params);
            return true;
        } catch (RouteNotFoundException $e) {
            return false;
        }
    }

    /**
     * @param null $modifier
     * @return Datetime
     * @throws Exception
     */
    public function currentDatetime($modifier = null)
    {
        $oDatetime = new Datetime();
        if(!empty($modifier) AND is_string($modifier)){
            $oDatetime->modify($modifier);
        }
        return $oDatetime;
    }

    /**
     * @param $var
     * @param $instance
     * @return bool
     */
    public function isInstanceof($var, $instance)
    {
        return $var instanceof $instance;
    }

    /**
     * @param Datetime $oDatetimeA
     * @param Datetime $oDatetimeB
     * @param string $sMode
     * @param int $iMargin
     * @return bool
     * @throws Exception
     */
    public function compareDatetime(Datetime $oDatetimeA, Datetime $oDatetimeB, $sMode = '>', $iMargin = 0)
    {
        $oDatetimeBLow = new Datetime();
        $oDatetimeBLow->setTimestamp($oDatetimeB->getTimestamp() - intval($iMargin));
        $oDatetimeBHigh = new Datetime();
        $oDatetimeBHigh->setTimestamp($oDatetimeB->getTimestamp() + intval($iMargin));
        switch ($sMode) {
            case '<':
                return $oDatetimeA > $oDatetimeBHigh AND $oDatetimeA < $oDatetimeBLow;
                break;
            case '>':
            default:
                return $oDatetimeA > $oDatetimeBLow AND $oDatetimeA < $oDatetimeBHigh;
                break;
            case '==':
                return $oDatetimeA == $oDatetimeB;
                break;
        }
    }

    /**
     * @param $number
     * @param $pad_length
     * @param $pad_string
     * @return string
     */
    public function strPad($number, $pad_length, $pad_string): string
    {
        return str_pad($number, $pad_length, $pad_string, STR_PAD_LEFT);
    }

    public function arraySum(array $array): int
    {
        return array_sum($array);
    }
}
