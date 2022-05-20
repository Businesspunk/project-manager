<?php

namespace App\Controller\Work\Projects;

use App\Annotation\GuidAnnotation;
use App\Model\Work\Entity\Projects\Role\Permission;
use App\Model\Work\Entity\Projects\Role\Role;
use App\Model\Work\UseCase\Projects\Role\Create;
use App\Model\Work\UseCase\Projects\Role\Edit;
use App\Model\Work\UseCase\Projects\Role\Copy;
use App\Model\Work\UseCase\Projects\Role\Delete;
use App\ReadModel\Work\Project\RoleFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/work/projects/roles", name="work.projects.roles", requirements={"id"=GuidAnnotation::PATTERN})
 * @IsGranted("ROLE_WORK_ROLES_MANAGE")
 */
class RolesController extends AbstractController
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
    public function index(RoleFetcher $fetcher): Response
    {
        return $this->render('app/work/projects/role/index.html.twig', [
            'roles' => $fetcher->all(),
            'permissions' => Permission::getNames()
        ]);
    }

    /**
     * @Route("/create", name=".create")
     */
    public function create(Request $request, Create\Handler $handler): Response
    {
        $command = new Create\Command();
        $form = $this->createForm(Create\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Role was successfully created');
                return $this->redirectToRoute('work.projects.roles');
            } catch (\DomainException $e) {
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
                $this->logger->warning($e->getMessage());
            }
        }

        return $this->render('app/work/projects/role/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}", name=".show")
     */
    public function show(Role $role): Response
    {
        return $this->render('app/work/projects/role/show.html.twig', compact('role'));
    }

    /**
     * @Route("{id}/edit", name=".edit")
     */
    public function edit(Role $role, Request $request, Edit\Handler $handler): Response
    {
        $command = Edit\Command::createFromRole($role);
        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Role was successfully updated');
                return $this->redirectToRoute('work.projects.roles.show', ['id' => $role->getId()]);
            } catch (\DomainException $e) {
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
                $this->logger->warning($e->getMessage());
            }
        }

        return $this->render('app/work/projects/role/edit.html.twig', [
            'role' => $role,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("{id}/clone", name=".clone")
     */
    public function clone(Role $role, Request $request, Copy\Handler $handler): Response
    {
        $command = new Copy\Command($role->getId());
        $form = $this->createForm(Copy\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Role was successfully cloned');
                return $this->redirectToRoute('work.projects.roles');
            } catch (\DomainException $e) {
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
                $this->logger->warning($e->getMessage());
            }
        }

        return $this->render('app/work/projects/role/copy.html.twig', [
            'form' => $form->createView(),
            'role' => $role
        ]);
    }

    /**
     * @Route("{id}/delete", name=".delete")
     */
    public function delete(string $id, Request $request, Delete\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.roles.show', ['id' => $id]);
        }

        $command = new Delete\Command($id);

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Role was successfully deleted');
        } catch (\DomainException $e) {
            $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            $this->logger->warning($e->getMessage());
        }
        return $this->redirectToRoute('work.projects.roles');
    }
}
