<?php

namespace App\EventSubscriber;

use InvalidArgumentException;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class KernelSubscriber implements EventSubscriberInterface
{
    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $parameterBag;
    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $urlGenerator;

    /**
     * KernelSubscriber constructor.
     * @param ParameterBagInterface $parameterBag
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(ParameterBagInterface $parameterBag, UrlGeneratorInterface $urlGenerator)
    {
        $this->parameterBag = $parameterBag;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['localeEvent', 20]
            ],
        ];
    }

    /**
     * localeEvent
     *
     * @param  mixed $event
     * @return void
     */
    public function localeEvent(RequestEvent $event): void
    {
        // Do not use sub requests
        if ($event->getRequestType() !== HttpKernel::MAIN_REQUEST) {
            return;
        }

        /* Check default locale and default locales array from services.yaml, if not present load english by default */
        if(!$this->parameterBag->has('app.locale')) throw new ParameterNotFoundException('app.locale', sprintf('%s', __METHOD__));
        if(!$this->parameterBag->has('app.locales')) throw new ParameterNotFoundException('app.locales', sprintf('%s', __METHOD__));
        $sDefaultLocale = $this->parameterBag->get('app.locale');
        $aDefaultLocales = $this->parameterBag->get('app.locales');
        if(!is_string(($sDefaultLocale))) throw new InvalidArgumentException('The "app.locale" parameter must be a string, update it in services.yaml file');
        if(!is_array(($aDefaultLocales))) throw new InvalidArgumentException('The "app.locales" parameter must be an array, update it in services.yaml file');

        /* Get the request and apply locale transformation */
        $request = $event->getRequest();
        if (!empty($request->get('_locale'))) {
            $sLocale = ((in_array($request->get('_locale'), $aDefaultLocales)) ? $request->get('_locale') : $sDefaultLocale);
            $request->getSession()->set('_locale', $sLocale);
            $aParsedUrl = parse_url($request->getRequestUri());
            $aParameters = [];
            if(isset($aParsedUrl['query'])){
                parse_str($aParsedUrl['query'], $aParameters);
            }
            unset($aParameters['_locale']);
            $event->setResponse(new RedirectResponse(strtok($request->getRequestUri(), '?') . (count($aParameters) > 0 ? '?' . http_build_query($aParameters) : '')));
        }

        $sLocale = $request->getSession()->get('_locale') ?? $sDefaultLocale;
        $request->setLocale($sLocale);
    }
}
