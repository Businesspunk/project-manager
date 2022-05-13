<?php

namespace App\Controller\Work\Projects;

use App\ReadModel\Work\Project\Filter;
use App\ReadModel\Work\Project\ProjectFetcher;
use App\Model\Work\UseCase\Projects\Project\Create;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/work/projects", name="work.projects")
 */
class ProjectsController extends AbstractController
{
    public const PER_PAGE = 10;

    private $logger;
    private $translator;

    public function __construct(LoggerInterface $logger, TranslatorInterface $translator)
    {
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * @Route("", name="")
     */
    public function index(Request $request, ProjectFetcher $fetcher): Response
    {
        $filter = new Filter\Filter();
        $form = $this->createForm(Filter\Form::class, $filter);
        $form->handleRequest($request);

        $projects = $fetcher->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort', 'sort'),
            $request->query->get('direction', 'asc')
        );

        return $this->render('app/work/projects/index.html.twig', [
            'projects' => $projects,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/create", name=".create")
     */
    public function create(Request $request, ProjectFetcher $fetcher, Create\Handler $handler): Response
    {
        $command = new Create\Command();
        $command->sort = $fetcher->getMaxSort() + 1;
        $form = $this->createForm(Create\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Project was successfully created');
                return $this->redirectToRoute('work.projects');
            } catch (\DomainException $e) {
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
                $this->logger->warning($e->getMessage());
            }
        }

        return $this->render('app/work/projects/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
