<?php


namespace App\Manager;


use Symfony\Component\HttpFoundation\RequestStack;

class UserAgentManager
{
    /**
     * @var RequestStack
     */
    public RequestStack $requestStack;

    /**
     * UserAgent constructor.
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /** @noinspection PhpComposerExtensionStubsInspection */
    public function getFingerprint($aExtraData = []): string
    {
        return md5(json_encode(array_merge([
            //            'LOCALE' => $this->requestStack->getMainRequest()->getLocale(),
            //            'HTTP_ACCEPT' => $this->requestStack->getMainRequest()->server->get('HTTP_ACCEPT'),
            'IP' => $this->requestStack->getMainRequest()->getClientIp(), // Client IP
            'HTTP_ACCEPT' => $this->requestStack->getMainRequest()->server->get('HTTP_ACCEPT'), // Client browser accept
            'HTTP_USER_AGENT' => $this->requestStack->getMainRequest()->server->get('HTTP_USER_AGENT'), // Client browser name
            'HTTP_ACCEPT_LANGUAGE' => $this->requestStack->getMainRequest()->server->get('HTTP_ACCEPT_LANGUAGE'), // Accepted browser languages
            'HTTP_ACCEPT_ENCODING' => $this->requestStack->getMainRequest()->server->get('HTTP_ACCEPT_ENCODING'), // Accepted browser encodings
        ], $aExtraData)));
    }

    public function getBrowserMetadata(): array
    {
        return [
          'browser' => $this->requestStack->getMainRequest()->server->get('HTTP_USER_AGENT')
        ];
    }

    /**
     * @return array|false
     */
    public function getAllHeaders(): array|false
    {
        if (!function_exists('getallheaders')) {
            $headers = [];
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
            return $headers;
        } else {
            return getallheaders();
        }
    }
}