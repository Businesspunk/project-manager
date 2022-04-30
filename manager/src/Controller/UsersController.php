<?php

namespace App\Controller;

use App\Model\User\Entity\User\User;
use App\Model\User\UseCase\Create;
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
     * @Route ("/create", name="users.show")
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
                return $this->redirectToRoute('users.show');
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
     * @Route ("/{id}", name="users.show")
     */
    public function show(User $user): Response
    {
        return $this->render('app/users/show.html.twig', compact('user'));
    }
}