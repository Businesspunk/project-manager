<?php

namespace App\Controller\Work\Projects;

use App\Controller\ControllerFlashTrait;
use App\Controller\ErrorHandler;
use App\Model\Work\Entity\Projects\Task\Task;
use App\Model\Work\UseCase\Projects\Task\ChildOf;
use App\Model\Work\UseCase\Projects\Task\Move;
use App\Model\Work\UseCase\Projects\Task\Remove;
use App\Model\Work\UseCase\Projects\Task\Take;
use App\Model\Work\UseCase\Projects\Task\Start;
use App\ReadModel\Work\Task\Filter\Filter;
use App\ReadModel\Work\Task\Filter\Form;
use App\ReadModel\Work\Task\TaskFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Model\Work\UseCase\Projects\Task\Type;
use App\Model\Work\UseCase\Projects\Task\Status;
use App\Model\Work\UseCase\Projects\Task\Priority;
use App\Model\Work\UseCase\Projects\Task\Progress;
use App\Model\Work\UseCase\Projects\Task\Plan;
use App\Model\Work\UseCase\Projects\Task\Executor;
use App\Model\Work\UseCase\Projects\Task\Edit;

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
                'form' => $form->createView(),
                'project' => null
            ]);
    }

    /**
     * @Route("/{id}", name=".show")
     */
    public function show(
        Task $task,
        Request $request,
        Type\Handler $typeHandler,
        Status\Handler $statusHandler,
        Priority\Handler $priorityHandler,
        Progress\Handler $progressHandler
    ): Response {
        $command = Type\Command::fromTask($task);
        $command->type = $task->getType()->getValue();
        $typeForm = $this->createForm(Type\Form::class, $command);
        $typeForm->handleRequest($request);

        if ($typeForm->isSubmitted() && $typeForm->isValid()) {
            try {
                $typeHandler->handle($command);
                $this->addFlash('success', 'Type was successfully changed');
                return $this->redirectDefault($task);
            } catch (\DomainException $e) {
                $this->addExceptionFlash($e);
                $this->errorHandler->handle($e);
            }
        }

        $command = Status\Command::fromTask($task);
        $command->status = $task->getStatus()->getValue();
        $statusForm = $this->createForm(Status\Form::class, $command);
        $statusForm->handleRequest($request);

        if ($statusForm->isSubmitted() && $statusForm->isValid()) {
            try {
                $statusHandler->handle($command);
                $this->addFlash('success', 'Status was successfully changed');
                return $this->redirectDefault($task);
            } catch (\DomainException $e) {
                $this->addExceptionFlash($e);
                $this->errorHandler->handle($e);
            }
        }

        $command = Priority\Command::fromTask($task);
        $command->priority = $task->getPriority();
        $priorityForm = $this->createForm(Priority\Form::class, $command);
        $priorityForm->handleRequest($request);

        if ($priorityForm->isSubmitted() && $priorityForm->isValid()) {
            try {
                $priorityHandler->handle($command);
                $this->addFlash('success', 'Priority was successfully changed');
                return $this->redirectDefault($task);
            } catch (\DomainException $e) {
                $this->addExceptionFlash($e);
                $this->errorHandler->handle($e);
            }
        }

        $command = Progress\Command::fromTask($task);
        $command->progress = $task->getProgress();
        $progressForm = $this->createForm(Progress\Form::class, $command);
        $progressForm->handleRequest($request);

        if ($progressForm->isSubmitted() && $progressForm->isValid()) {
            try {
                $progressHandler->handle($command);
                $this->addFlash('success', 'Progress was successfully changed');
                return $this->redirectDefault($task);
            } catch (\DomainException $e) {
                $this->addExceptionFlash($e);
                $this->errorHandler->handle($e);
            }
        }

        return $this->render('app/work/projects/tasks/task/show.html.twig', [
            'task' => $task,
            'project' => $task->getProject(),
            'forms' => [
                'typeForm' => $typeForm->createView(),
                'statusForm' => $statusForm->createView(),
                'priorityForm' => $priorityForm->createView(),
                'progressForm' => $progressForm->createView()
            ]
        ]);
    }

    /**
     * @Route("/{id}/edit", name=".edit")
     */
    public function edit(Task $task, Request $request, Edit\Handler $handler): Response
    {
        $command = Edit\Command::fromTask($task);
        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Task was successfully updated');
                return $this->redirectDefault($task);
            } catch (\DomainException $e) {
                $this->addExceptionFlash($e);
                $this->errorHandler->handle($e);
            }
        }

        return $this->render('app/work/projects/tasks/task/edit.html.twig', [
            'task' => $task,
            'project' => $task->getProject(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/move", name=".move")
     */
    public function move(Task $task, Request $request, Move\Handler $handler): Response
    {
        $command = Move\Command::fromTask($task);
        $form = $this->createForm(Move\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Task was successfully moved');
                return $this->redirectDefault($task);
            } catch (\DomainException $e) {
                $this->addExceptionFlash($e);
                $this->errorHandler->handle($e);
            }
        }

        return $this->render('app/work/projects/tasks/task/move.html.twig', [
            'task' => $task,
            'project' => $task->getProject(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/take", name=".take", methods={"POST"})
     */
    public function take(Task $task, Request $request, Take\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('take', $request->request->get('token'))) {
            return $this->redirectDefault($task);
        }

        $command = Take\Command::fromTask($task);
        $command->member = $this->getUser()->getId();

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Task was successfully taken');
        } catch (\DomainException $e) {
            $this->addExceptionFlash($e);
        }
        return $this->redirectDefault($task);
    }

    /**
     * @Route("/{id}/start", name=".start", methods={"POST"})
     */
    public function start(Task $task, Request $request, Start\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('start', $request->request->get('token'))) {
            return $this->redirectDefault($task);
        }

        $command = Start\Command::fromTask($task);

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Task was successfully started');
        } catch (\DomainException $e) {
            $this->addExceptionFlash($e);
        }
        return $this->redirectDefault($task);
    }

    /**
     * @Route("/{id}/delete", name=".delete", methods={"POST"})
     */
    public function delete(Task $task, Request $request, Remove\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectDefault($task);
        }

        $command = Remove\Command::fromTask($task);

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Task was successfully deleted');
        } catch (\DomainException $e) {
            $this->addExceptionFlash($e);
        }
        return $this->redirectToRoute('work.projects.tasks');
    }

    /**
     * @Route("/{id}/parent/set", name=".parent.set")
     */
    public function setParent(Task $task, Request $request, ChildOf\Handler $handler): Response
    {
        $command = ChildOf\Command::fromTask($task);
        $form = $this->createForm(ChildOf\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Parent for task was successfully added');
                return $this->redirectDefault($task);
            } catch (\DomainException $e) {
                $this->addExceptionFlash($e);
                $this->errorHandler->handle($e);
            }
        }

        return $this->render('app/work/projects/tasks/task/parent.html.twig', [
            'task' => $task,
            'project' => $task->getProject(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/assign", name=".assign")
     */
    public function assign(Task $task, Request $request, Executor\Assign\Handler $handler): Response
    {
        $command = Executor\Assign\Command::fromTask($task);
        $form = $this->createForm(
            Executor\Assign\Form::class,
            $command,
            ['project_id' => $task->getProject()->getId()->getValue()]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Executors was successfully added');
                return $this->redirectDefault($task);
            } catch (\DomainException $e) {
                $this->addExceptionFlash($e);
                $this->errorHandler->handle($e);
            }
        }

        return $this->render('app/work/projects/tasks/task/executor.html.twig', [
            'task' => $task,
            'project' => $task->getProject(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/revoke/{executorId}", name=".revoke", methods={"POST"})
     */
    public function revokeExecutor(
        Task $task,
        string $executorId,
        Request $request,
        Executor\Revoke\Handler $handler
    ): Response {
        if (!$this->isCsrfTokenValid('revoke', $request->request->get('token'))) {
            return $this->redirectDefault($task);
        }

        $command = Executor\Revoke\Command::fromTask($task);
        $command->member = $executorId;

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Executor was successfully deleted');
        } catch (\DomainException $e) {
            $this->addExceptionFlash($e);
        }
        return $this->redirectDefault($task);
    }

    /**
     * @Route("/{id}/plan/edit", name=".plan.edit")
     */
    public function planDate(Task $task, Request $request, Plan\Set\Handler $handler): Response
    {
        $command = Plan\Set\Command::fromTask($task);
        $form = $this->createForm(Plan\Set\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Plan was successfully changed');
                return $this->redirectDefault($task);
            } catch (\DomainException $e) {
                $this->addExceptionFlash($e);
                $this->errorHandler->handle($e);
            }
        }

        return $this->render('app/work/projects/tasks/task/plan.html.twig', [
            'task' => $task,
            'project' => $task->getProject(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/plan/remove", name=".plan.remove", methods={"POST"})
     */
    public function removePlanDate(Task $task, Request $request, Plan\Remove\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectDefault($task);
        }

        $command = Plan\Remove\Command::fromTask($task);

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Plan was successfully deleted');
        } catch (\DomainException $e) {
            $this->addExceptionFlash($e);
        }
        return $this->redirectDefault($task);
    }

    private function redirectDefault(Task $task): RedirectResponse
    {
        return $this->redirectToRoute('work.projects.tasks.show', ['id' => $task->getId()->getValue()]);
    }
}
