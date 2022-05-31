<?php

namespace App\Controller\Work\Projects;

use App\Controller\ControllerFlashTrait;
use App\Controller\ErrorHandler;
use App\Model\Work\Entity\Projects\Project\Project;
use App\ReadModel\Work\Project\Filter;
use App\ReadModel\Work\Project\ProjectFetcher;
use App\Model\Work\UseCase\Projects\Project\Create;
use App\Security\Voter\Work\ProjectAccess;
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
    use ControllerFlashTrait;

    public const PER_PAGE = 10;

    private $errorHandler;
    private $translator;

    public function __construct(ErrorHandler $errorHandler, TranslatorInterface $translator)
    {
        $this->errorHandler = $errorHandler;
        $this->translator = $translator;
    }

    /**
     * @Route("", name="")
     */
    public function index(Request $request, ProjectFetcher $fetcher): Response
    {
        if ($this->isGranted('ROLE_WORK_PROJECTS_MANAGE')) {
            $filter = Filter\Filter::all();
        } else {
            $filter = Filter\Filter::forMember($this->getUser()->getId());
        }

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
            'form' => $form->createView(),
            'actionsGranted' => [
                'attribute' => ProjectAccess::CREATE,
                'subject' => Project::class
            ]
        ]);
    }

    /**
     * @Route("/create", name=".create")
     */
    public function create(Request $request, ProjectFetcher $fetcher, Create\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::CREATE, Project::class);
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
                $this->addExceptionFlash($e);
                $this->errorHandler->handle($e);
            }
        }

        return $this->render('app/work/projects/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
