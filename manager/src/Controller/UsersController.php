<?php

namespace App\Controller;

use App\Annotation\GuidAnnotation;
use App\Model\User\Entity\User\User;
use App\Model\User\UseCase\Create;
use App\Model\User\UseCase\Edit;
use App\Model\User\UseCase\Block;
use App\Model\User\UseCase\Activate;
use App\Model\User\UseCase\Role;
use App\ReadModel\User\Filter;
use App\ReadModel\User\UserFetcher;
use App\ReadModel\Work\Member\MemberFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Model\User\UseCase\SignUp\Confirm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/users", name="users")
 * @IsGranted("ROLE_MANAGE_USERS")
 */
class UsersController extends AbstractController
{
    use ControllerFlashTrait;

    private const PER_PAGE = 10;

    private $errorHandler;
    private $translator;

    public function __construct(ErrorHandler $errorHandler, TranslatorInterface $translator)
    {
        $this->errorHandler = $errorHandler;
        $this->translator = $translator;
    }

    /**
     * @Route("", name="", methods={"GET"})
     */
    public function index(Request $request, UserFetcher $users): Response
    {
        $filter = new Filter\Filter();
        $form = $this->createForm(Filter\Form::class, $filter);

        $form->handleRequest($request);
        $users = $users->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort', 'name'),
            $request->query->get('direction', 'desc')
        );

        return $this->render('app/users/index.html.twig', [
            'users' => $users,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/create", name=".create", methods={"GET","POST"})
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
                $this->addExceptionFlash($e);
                $this->errorHandler->handle($e);
            }
        }

        return $this->render('app/users/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name=".edit", methods={"GET","POST"})
     */
    public function edit(User $user, Request $request, Edit\Handler $handler): Response
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
                $this->addExceptionFlash($e);
                $this->errorHandler->handle($e);
            }
        }

        return $this->render('app/users/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/{id}/confirm", name=".confirm", methods={"POST"})
     */
    public function confirm(User $user, Request $request, Confirm\Manually\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('confirm', $request->request->get('token'))) {
            return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
        }

        $command = new Confirm\Manually\Command($user->getId());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->addExceptionFlash($e);
            $this->errorHandler->handle($e);
        }

        return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
    }

    /**
     * @Route("/{id}/block", name=".block", methods={"POST"})
     */
    public function block(User $user, Request $request, Block\Handler $handler): Response
    {
        if ($user->getId()->getValue() === $this->getUser()->getId()) {
            $this->addFlash('error', 'Unable to block yourself');
            return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
        }

        if (!$this->isCsrfTokenValid('block', $request->request->get('token'))) {
            return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
        }

        $command = new Block\Command($user->getId());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->addExceptionFlash($e);
            $this->errorHandler->handle($e);
        }

        return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
    }

    /**
     * @Route("/{id}/activate", name=".activate", methods={"POST"})
     */
    public function activate(User $user, Request $request, Activate\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('activate', $request->request->get('token'))) {
            return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
        }

        $command = new Activate\Command($user->getId());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->addExceptionFlash($e);
            $this->errorHandler->handle($e);
        }

        return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
    }

    /**
     * @Route("/{id}/change/role", name=".change.role", methods={"GET", "POST"})
     */
    public function editRole(User $user, Request $request, Role\Handler $handler): Response
    {
        if ($user->getId()->getValue() === $this->getUser()->getId()) {
            $this->addFlash('error', 'You cannot change your role by yourself');
            return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
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
                $this->addExceptionFlash($e);
                $this->errorHandler->handle($e);
            }
        }

        return $this->render('app/users/change_role.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/{id}", name=".show", requirements={"id"=GuidAnnotation::PATTERN}, methods={"GET"})
     */
    public function show(User $user, MemberFetcher $fetcher): Response
    {
        $member = $fetcher->find($user->getId());
        return $this->render('app/users/show.html.twig', compact('user', 'member'));
    }
}
