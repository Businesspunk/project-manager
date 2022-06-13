<?php

namespace App\Controller\Work\Projects;

use App\Controller\ControllerFlashTrait;
use App\Controller\ErrorHandler;
use App\ReadModel\Work\Task\Filter\Filter;
use App\ReadModel\Work\Task\Filter\Form;
use App\ReadModel\Work\Task\TaskFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/work/projects/tasks", name="work.projects.tasks")
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
    public function index(Request $request, TaskFetcher $tasks): Response
    {
        if ($this->isGranted('ROLE_WORK_PROJECTS_MANAGE')) {
            $filter = Filter::all();
        } else {
            $filter = Filter::forMember($this->getUser()->getId());
        }

        $form = $this->createForm(Form::class, $filter, ['project_id' => null]);
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
                'form' => $form->createView()
            ]);
    }
}
