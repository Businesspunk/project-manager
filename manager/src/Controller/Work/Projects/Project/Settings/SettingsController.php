<?php

namespace App\Controller\Work\Projects\Project\Settings;

use App\Model\Work\Entity\Projects\Project\Project;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Model\Work\UseCase\Projects\Project\Archive;
use App\Model\Work\UseCase\Projects\Project\Edit;
use App\Model\Work\UseCase\Projects\Project\Reinstate;
use App\Model\Work\UseCase\Projects\Project\Delete;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/work/projects/{id}/settings", name="work.projects.project.settings")
 * @IsGranted("ROLE_WORK_PROJECTS_MANAGE")
 */
class SettingsController extends AbstractController
{
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
    public function settings(Project $project): Response
    {
        return $this->render('app/work/projects/project/settings/show.html.twig', compact('project'));
    }

    /**
     * @Route("/archive", name=".archive")
     */
    public function archive(string $id, Request $request, Archive\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('archive', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.project', ['id' => $id]);
        }

        $command = new Archive\Command($id);

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Project was successfully archived');
        } catch (\DomainException $e) {
            $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            $this->logger->warning($e->getMessage());
        }
        return $this->redirectToRoute('work.projects.project.settings', ['id' => $id]);
    }

    /**
     * @Route("/reinstate", name=".reinstate")
     */
    public function reinstate(string $id, Request $request, Reinstate\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('reinstate', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.project', ['id' => $id]);
        }

        $command = new Reinstate\Command($id);

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Project was successfully reinstated');
        } catch (\DomainException $e) {
            $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            $this->logger->warning($e->getMessage());
        }
        return $this->redirectToRoute('work.projects.project.settings', ['id' => $id]);
    }

    /**
     * @Route("/delete", name=".delete")
     */
    public function delete(string $id, Request $request, Delete\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.project', ['id' => $id]);
        }

        $command = new Delete\Command($id);

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Project was successfully deleted');
        } catch (\DomainException $e) {
            $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            $this->logger->warning($e->getMessage());
        }
        return $this->redirectToRoute('work.projects');
    }

    /**
     * @Route("/edit", name=".edit")
     */
    public function create(Project $project, Request $request, Edit\Handler $handler): Response
    {
        $command = Edit\Command::createFromProject($project);
        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Project was successfully updated');
                return $this->redirectToRoute('work.projects.project.settings', ['id' => $project->getId()]);
            } catch (\DomainException $e) {
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
                $this->logger->warning($e->getMessage());
            }
        }

        return $this->render('app/work/projects/project/settings/edit.html.twig', [
            'form' => $form->createView(),
            'project' => $project
        ]);
    }
}
