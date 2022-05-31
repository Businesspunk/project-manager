<?php

namespace App\Controller\Work\Projects\Project\Settings;

use App\Controller\ControllerFlashTrait;
use App\Controller\ErrorHandler;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Security\Voter\Work\ProjectAccess;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Model\Work\UseCase\Projects\Project\Archive;
use App\Model\Work\UseCase\Projects\Project\Edit;
use App\Model\Work\UseCase\Projects\Project\Reinstate;
use App\Model\Work\UseCase\Projects\Project\Delete;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/work/projects/{id}/settings", name="work.projects.project.settings")
 */
class SettingsController extends AbstractController
{
    use ControllerFlashTrait;

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
    public function settings(Project $project): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::VIEW, $project);
        return $this->render('app/work/projects/project/settings/show.html.twig', [
            'project' => $project,
            'actionsGranted' => ProjectAccess::EDIT
        ]);
    }

    /**
     * @Route("/archive", name=".archive")
     */
    public function archive(Project $project, Request $request, Archive\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::EDIT, $project);
        $id = $project->getId();
        if (!$this->isCsrfTokenValid('archive', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.project', ['id' => $id]);
        }

        $command = new Archive\Command($id);

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Project was successfully archived');
        } catch (\DomainException $e) {
            $this->addExceptionFlash($e);
            $this->errorHandler->handle($e);
        }
        return $this->redirectToRoute('work.projects.project.settings', ['id' => $id]);
    }

    /**
     * @Route("/reinstate", name=".reinstate")
     */
    public function reinstate(Project $project, Request $request, Reinstate\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::EDIT, $project);
        $id = $project->getId();
        if (!$this->isCsrfTokenValid('reinstate', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.project', ['id' => $id]);
        }

        $command = new Reinstate\Command($id);

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Project was successfully reinstated');
        } catch (\DomainException $e) {
            $this->addExceptionFlash($e);
            $this->errorHandler->handle($e);
        }
        return $this->redirectToRoute('work.projects.project.settings', ['id' => $id]);
    }

    /**
     * @Route("/delete", name=".delete")
     */
    public function delete(Project $project, Request $request, Delete\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::EDIT, $project);
        $id = $project->getId();
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.project', ['id' => $id]);
        }

        $command = new Delete\Command($id);

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Project was successfully deleted');
        } catch (\DomainException $e) {
            $this->addExceptionFlash($e);
            $this->errorHandler->handle($e);
        }
        return $this->redirectToRoute('work.projects');
    }

    /**
     * @Route("/edit", name=".edit")
     */
    public function edit(Project $project, Request $request, Edit\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::EDIT, $project);
        $command = Edit\Command::createFromProject($project);
        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Project was successfully updated');
                return $this->redirectToRoute('work.projects.project.settings', ['id' => $project->getId()]);
            } catch (\DomainException $e) {
                $this->addExceptionFlash($e);
                $this->errorHandler->handle($e);
            }
        }

        return $this->render('app/work/projects/project/settings/edit.html.twig', [
            'form' => $form->createView(),
            'project' => $project
        ]);
    }
}
