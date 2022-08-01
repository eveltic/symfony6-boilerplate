<?php
namespace Eveltic\Crud\Controller;

use Eveltic\Crud\Event\CrudEvents;
use Eveltic\Crud\Event\IndexPostLoadEvent;
use Eveltic\Crud\Event\SecurityEvent;
use Eveltic\Crud\Exception\AccessDeniedException;
use Eveltic\Crud\Factory\CrudFactory;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;



abstract class AbstractCrudController extends AbstractController
{
    use CrudTrait;

    #[Route('/data', name: 'data', methods: ['GET'])]
    public function data(Request $request, CrudFactory $crudFactory): JsonResponse
    {

        $aRows = [];
        for($i = 1; $i <= 800; $i++){
            $aRows[] = ['id' => $i, 'name' => sprintf('Item %s', $i), 'price' => sprintf('$%s', $i)];
        }

        $aResult = array_slice($aRows, $request->get('offset'), $request->get('limit'));

        $aReturn = [
            'total' => count($aRows)
            , 'totalNotFiltered' => count($aResult)
            , 'rows' => $aResult
        ];


        return new JsonResponse($aReturn);
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(Request $request, LoggerInterface $logger, TranslatorInterface $translator, CrudFactory $crudFactory): Response
    {
        try {
            /* Check crud configuration */
            $this->checkCrudConfiguration();

            /* Get crud configuration */
            $configuration = $this->getConfiguration();
    
            /* Get injection methods from controller */
            $data = $this->getInjectMethods(__FUNCTION__);
    
            /* Dispatch security events */
            $crudConfiguration = $this->dispatchEvent($configuration, SecurityEvent::class, CrudEvents::SECURITY, $configuration);
            
            /* Check access */
            $this->checkMethodAccess(__FUNCTION__, $configuration);

            /* Get crud object */
            $crud = $crudFactory->getCrud($configuration, $request);
    
            /* Dispatch post load events */
            $crud = $this->dispatchEvent($configuration, IndexPostLoadEvent::class, CrudEvents::INDEX_POST_LOAD, $crud);
    
            /* Return response */
            return $this->render(sprintf('%s%s.html.twig', $configuration->getConfiguration('template_folder'), $configuration->getConfiguration('method')), compact('crud', 'configuration', 'data'));
        } catch (AccessDeniedException $exception) {
            return $this->render(sprintf('%s%s.html.twig', $configuration->getConfiguration('error_folder'), 'base'), ['errorMessage' => $exception->getMessage(), 'errorCode' => 403]);
        } catch (Exception $exception) {
            if ($this->getParameter('kernel.environment') == 'dev') {
                throw $exception;
            } else {
                $logger->critical($exception->getMessage(), [debug_backtrace(),]);
                return $this->render(sprintf('%s%s.html.twig', $configuration->getConfiguration('error_folder'), 'base'), ['errorMessage' => $translator->trans('An error has been detected and the application has stopped. Please contact the administrator with a description of the error, date and time.'), 'errorCode' => 500]);
            }
        }

    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(): Response
    {
        return new Response(sprintf('<!DOCTYPE html><html><head></head><body>Eveltic Crud Controller <strong>%s</strong></body></html>', __FUNCTION__));
    }

    #[Route('/{id}/read', name: 'read', methods: ['GET'])]
    public function read(): Response
    {
        return new Response(sprintf('<!DOCTYPE html><html><head></head><body>Eveltic Crud Controller <strong>%s</strong></body></html>', __FUNCTION__));
    }

    #[Route('/{id}/update', name: 'update', methods: ['GET', 'POST'])]
    public function update(): Response
    {
        return new Response(sprintf('<!DOCTYPE html><html><head></head><body>Eveltic Crud Controller <strong>%s</strong></body></html>', __FUNCTION__));
    }

    #[Route('/{id}/clone', name: 'clone', methods: ['GET', 'POST'])]
    public function clone(): Response
    {
        return new Response(sprintf('<!DOCTYPE html><html><head></head><body>Eveltic Crud Controller <strong>%s</strong></body></html>', __FUNCTION__));
    }

    #[Route('/delete', name: 'delete', methods: ['GET', 'POST'])]
    public function delete(): Response
    {
        return new Response(sprintf('<!DOCTYPE html><html><head></head><body>Eveltic Crud Controller <strong>%s</strong></body></html>', __FUNCTION__));
    }
}
