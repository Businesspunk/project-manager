<?php

namespace App\Controller\Work\Projects\Project;

use App\Controller\ControllerFlashTrait;
use App\Controller\ErrorHandler;
use App\Annotation\GuidAnnotation;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Model\Work\Entity\Projects\Task\Id;
use App\Model\Work\Entity\Projects\Task\Task;
use App\Model\Work\Entity\Projects\Task\TaskRepository;
use App\ReadModel\Work\Task\Filter\Filter;
use App\ReadModel\Work\Task\Filter\Form;
use App\ReadModel\Work\Task\TaskFetcher;
use App\Security\Voter\Work\ProjectAccess;
use App\Security\Voter\Work\TaskAccess;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Model\Work\UseCase\Projects\Task\Create;

/**
 * @Route("/work/projects/{id}/tasks", name="work.projects.project.tasks", requirements={"id"=GuidAnnotation::PATTERN})
 */
class TasksController extends AbstractController
{
    use ControllerFlashTrait;

    private const PER_PAGE = 50;

    private $translator;
    private $errorHandler;

    public function __construct(ErrorHandler $errorHandler, TranslatorInterface $translator)
    {
        $this->errorHandler = $errorHandler;
        $this->translator = $translator;
    }

    /**
     * @Route("", name="")
     */
    public function index(Project $project, Request $request, TaskFetcher $tasks): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::VIEW, $project);

        $filter = Filter::all();
        $projectId = $project->getId()->getValue();

        $form = $this->createForm(Form::class, $filter, ['project_id' => $projectId]);
        $filter->project = $projectId;
        $form->handleRequest($request);

        $pagination = $tasks->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort', 'id'),
            $request->query->get('direction', 'desc')
        );

        return $this->render('app/work/projects/tasks/index.html.twig', [
            'tasks' => $pagination,
            'form' => $form->createView(),
            'project' => $project
        ]);
    }

    /**
     * @Route("/create", name=".create")
     */
    public function create(Project $project, Request $request, TaskRepository $tasks, Create\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(TaskAccess::MANAGE, Task::class);
        $command = new Create\Command($project->getId()->getValue(), $this->getUser()->getId());
        $parentId = $request->query->get('parent');
        $command->parent = $parentId;
        $parent = $parentId ? $tasks->get(new Id($parentId)) : null;
        $form = $this->createForm(Create\Form::class, $command, ['parentId' => $parentId]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Task was successfully created');
                return $this->redirectToRoute('work.projects.project.tasks', [
                    'id' => $project->getId()->getValue()
                ]);
            } catch (\DomainException $e) {
                $this->addExceptionFlash($e);
                $this->errorHandler->handle($e);
            }
        }

        return $this->render('app/work/projects/tasks/task/create.html.twig', [
            'parent' => $parent,
            'project' => $project,
            'form' => $form->createView()
        ]);
    }
}
