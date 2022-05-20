<?php

namespace App\Controller\Work\Projects\Project\Settings;

use App\Annotation\GuidAnnotation;
use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Security\Voter\Work\ProjectAccess;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Model\Work\UseCase\Projects\Project\Member\Assign;
use App\Model\Work\UseCase\Projects\Project\Member\Edit;
use App\Model\Work\UseCase\Projects\Project\Member\Delete;

/**
 * @Route(
 *     "/work/projects/{id}/settings/members",
 *     name="work.projects.project.settings.members",
 *     requirements={"id"=GuidAnnotation::PATTERN}
 * )
 */
class MembersController extends AbstractController
{
    private $logger;
    private $translator;

    public function __construct(LoggerInterface $logger, TranslatorInterface $translator)
    {
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * @Route("", name="", methods={"GET"})
     */
    public function index(Project $project): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::VIEW, $project);
        return $this->render('app/work/projects/project/settings/member/index.html.twig', [
            'project' => $project,
            'actionsGranted' => ProjectAccess::MANAGE_MEMBERS
        ]);
    }

    /**
     * @Route("/assign", name=".assign", methods={"GET", "POST"})
     */
    public function assign(Project $project, Request $request, Assign\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::MANAGE_MEMBERS, $project);
        $redirect = $this->redirectToRoute(
            'work.projects.project.settings.members',
            ['id' => $project->getId()]
        );

        if (!$project->getDepartments()) {
            $this->addFlash('error', 'Add departments before');
            return $redirect;
        }
        $command = new Assign\Command($project->getId());
        $form = $this->createForm(Assign\Form::class, $command, compact('project'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Member was successfully assigned');
                return $redirect;
            } catch (\DomainException $e) {
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
                $this->logger->warning($e->getMessage());
            }
        }

        return $this->render('app/work/projects/project/settings/member/create.html.twig', [
            'project' => $project,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{member}/edit", name=".edit", methods={"GET", "POST"})
     */
    public function edit(Project $project, Member $member, Request $request, Edit\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::MANAGE_MEMBERS, $project);
        $redirect = $this->redirectToRoute(
            'work.projects.project.settings.members',
            ['id' => $project->getId()]
        );

        $membership = $project->getMembershipByMember($member);

        $departments = [];
        foreach ($membership->getDepartments() as $department) {
            $departments[$department->getName()] = $department->getId()->getValue();
        }

        $roles = [];
        foreach ($membership->getRoles() as $role) {
            $roles[$role->getName()]  = $role->getId()->getValue();
        }

        $command = new Edit\Command($project->getId(), $member->getId(), $departments, $roles);
        $form = $this->createForm(Edit\Form::class, $command, compact('project'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Member was successfully updated');
                return $redirect;
            } catch (\DomainException $e) {
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
                $this->logger->warning($e->getMessage());
            }
        }

        return $this->render('app/work/projects/project/settings/member/edit.html.twig', [
            'project' => $project,
            'member' => $member,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{memberId}/delete", name=".delete", methods={"POST"})
     */
    public function delete(Project $project, string $memberId, Request $request, Delete\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::MANAGE_MEMBERS, $project);
        $redirect = $this->getDefaultRedirectUrl($project);

        if ($this->getUser()->getId() === $memberId) {
            $this->addFlash(
                'error',
                $this->translator->trans('Can not delete yourself from the project', [], 'exceptions')
            );
            return $redirect;
        }

        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $redirect;
        }
        $command = new Delete\Command($project->getId(), $memberId);
        try {
            $handler->handle($command);
            $this->addFlash('success', 'Member was successfully deleted');
            return $redirect;
        } catch (\DomainException $e) {
            $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            $this->logger->warning($e->getMessage());
        }

        return $redirect;
    }

    private function getDefaultRedirectUrl(Project $project): Response
    {
        return $this->redirectToRoute('work.projects.project.settings.members', ['id' => $project->getId()]);
    }
}
