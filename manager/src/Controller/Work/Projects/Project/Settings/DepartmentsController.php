<?php

namespace App\Controller\Work\Projects\Project\Settings;

use App\Controller\ControllerFlashTrait;
use App\Controller\ErrorHandler;
use App\Model\Work\Entity\Projects\Department\Id;
use App\Model\Work\Entity\Projects\Project\Project;
use App\ReadModel\Work\Project\DepartmentFetcher;
use App\Security\Voter\Work\ProjectAccess;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Model\Work\UseCase\Projects\Project\Department\Delete;
use App\Model\Work\UseCase\Projects\Project\Department\Create;
use App\Model\Work\UseCase\Projects\Project\Department\Edit;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/work/projects/{id}/settings/departments", name="work.projects.project.settings.departments")
 */
class DepartmentsController extends AbstractController
{
    use ControllerFlashTrait;

    private $translator;
    private $errorHandler;

    public function __construct(ErrorHandler $errorHandler, TranslatorInterface $translator)
    {
        $this->errorHandler = $errorHandler;
        $this->translator = $translator;
    }

    private function getDefaultRedirectUrl(Project $project): Response
    {
        return $this->redirectToRoute('work.projects.project.settings.departments', ['id' => $project->getId()]);
    }

    /**
     * @Route("", name="", methods={"GET"})
     */
    public function index(Project $project, DepartmentFetcher $fetcher): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::VIEW, $project);
        $departments = $fetcher->allOfProject($project->getId());
        return $this->render(
            'app/work/projects/project/settings/department/index.html.twig',
            [
                'departments' => $departments,
                'project' => $project,
                'actionsGranted' => ProjectAccess::EDIT
            ]
        );
    }

    /**
     * @Route("/create", name=".create", methods={"GET", "POST"})
     */
    public function create(Project $project, Request $request, Create\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::EDIT, $project);
        $command = new Create\Command($project->getId());
        $form = $this->createForm(Create\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Department was successfully created');
                return $this->getDefaultRedirectUrl($project);
            } catch (\DomainException $e) {
                $this->addExceptionFlash($e);
                $this->errorHandler->handle($e);
            }
        }

        return $this->render('app/work/projects/project/settings/department/create.html.twig', [
            'project' => $project,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{departmentId}/edit", name=".edit", methods={"GET", "POST"})
     */
    public function edit(Project $project, string $departmentId, Request $request, Edit\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::EDIT, $project);
        $department = $project->getDepartment(new Id($departmentId));
        $command = Edit\Command::createFromDepartment($project, $department);
        $form = $this->createForm(Create\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Department was successfully updated');
                return $this->getDefaultRedirectUrl($project);
            } catch (\DomainException $e) {
                $this->addExceptionFlash($e);
                $this->errorHandler->handle($e);
                return $this->getDefaultRedirectUrl($project);
            }
        }

        return $this->render('app/work/projects/project/settings/department/edit.html.twig', [
            'project' => $project,
            'department' => $department,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{departmentId}/delete", name=".delete", methods={"POST"})
     */
    public function delete(Project $project, string $departmentId, Request $request, Delete\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::EDIT, $project);
        $redirect = $this->getDefaultRedirectUrl($project);
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $redirect;
        }
        $command = new Delete\Command($project->getId(), $departmentId);
        try {
            $handler->handle($command);
            $this->addFlash('success', 'Department was successfully deleted');
            return $redirect;
        } catch (\DomainException $e) {
            $this->addExceptionFlash($e);
            $this->errorHandler->handle($e);
        }

        return $redirect;
    }
}
