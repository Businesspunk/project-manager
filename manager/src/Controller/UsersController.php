<?php

namespace App\Controller;

use App\Model\User\Entity\User\User;
use App\Model\User\UseCase\Create;
use App\Model\User\UseCase\Edit;
use App\Model\User\UseCase\Role;
use App\ReadModel\User\UserFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route ("/users")
 */
class UsersController extends AbstractController
{
    private $logger;
    private $translator;

    public function __construct(LoggerInterface $logger, TranslatorInterface $translator)
    {
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * @Route ("/", name="users")
     */
    public function index(UserFetcher $users): Response
    {
        $users = $users->all();
        return $this->render('app/users/index.html.twig', compact('users'));
    }

    /**
     * @Route ("/create", name="users.create")
     */
    public function create(Request $request, Create\Handler $handler): Response
    {
        $command = new Create\Command();
        $form = $this->createForm(Create\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'User was successfully added');
                return $this->redirectToRoute('users');
            } catch (\DomainException $e) {
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
                $this->logger->error($e->getMessage());
            }
        }

        return $this->render('app/users/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route ("/{id}/edit", name="users.edit")
     */
    public function edit(User $user, Request $request, Edit\Handler $handler)
    {
        $command = Edit\Command::createFromUser($user);
        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'User was successfully edited');
                return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
            } catch (\DomainException $e) {
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
                $this->logger->error($e->getMessage());
            }
        }

        return $this->render('app/users/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route ("/{id}/change/role", name="users.change.role")
     */
    public function editRole(User $user, Request $request, Role\Handler $handler)
    {
        if ($user->getId()->getValue() === $this->getUser()->getId()) {
            $this->addFlash('error', 'You cannot change your role by yourself');
            return $this->redirectToRoute('users');
        }

        $command = Role\Command::createFromUser($user);
        $form = $this->createForm(Role\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Role was successfully changed');
                return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
            } catch (\DomainException $e) {
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
                $this->logger->error($e->getMessage());
            }
        }

        return $this->render('app/users/change_role.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route ("/{id}", name="users.show")
     */
    public function show(User $user): Response
    {
        return $this->render('app/users/show.html.twig', compact('user'));
    }
}