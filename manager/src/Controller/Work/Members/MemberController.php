<?php

namespace App\Controller\Work\Members;

use App\Model\User\Entity\User\User;
use App\Model\Work\Entity\Members\Member\Member;
use App\ReadModel\User\UserFetcher;
use App\ReadModel\Work\Member\Filter;
use App\ReadModel\Work\Member\MemberFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Model\Work\UseCase\Members\Member\Create;
use App\Model\Work\UseCase\Members\Member\Move;
use App\Model\Work\UseCase\Members\Member\Edit;
use App\Model\Work\UseCase\Members\Member\Archive;
use App\Model\Work\UseCase\Members\Member\Reinstate;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/work/members", name="work.members")
 * @IsGranted ("ROLE_WORK_MEMBERS_MANAGE")
 */
class MemberController extends AbstractController
{
    private const PER_PAGE = 10;

    private $logger;
    private $translator;

    public function __construct(LoggerInterface $logger, TranslatorInterface $translator)
    {
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * @Route ("", name="")
     */
    public function index(Request $request, MemberFetcher $fetcher): Response
    {
        $filter = new Filter\Filter();
        $form = $this->createForm(Filter\Form::class, $filter);
        $form->handleRequest($request);

        $members = $fetcher->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort', 'name'),
            $request->query->get('direction', 'asc')
        );

        return $this->render('app/work/members/index.html.twig', [
            'members' => $members,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route ("/{id}", name=".show")
     */
    public function show(Member $member, UserFetcher $fetcher): Response
    {
        $user = $fetcher->find($member->getId());
        return $this->render('app/work/members/show.html.twig', compact('member', 'user'));
    }

    /**
     * @Route ("/create/{id}", name=".create")
     */
    public function create(User $user, Request $request, Create\Handler $handler): Response
    {
        $command = new Create\Command($user->getId());
        $command->firstName = $user->getName()->getFirstName();
        $command->lastName = $user->getName()->getLastName();
        $command->email = $user->getEmail();

        $form = $this->createForm(Create\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Member was successfully created');
                return $this->redirectToRoute('work.members');
            } catch (\DomainException $e){
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
                $this->logger->warning($e->getMessage());
            }
        }

        return $this->render('app/work/members/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route ("/{id}/edit", name=".edit")
     */
    public function edit(Member $member, Request $request, Edit\Handler $handler): Response
    {
        $command = Edit\Command::createFromMember($member);
        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Member was successfully updated');
                return $this->redirectToRoute('work.members.show', ['id' => $member->getId()]);
            } catch (\DomainException $e){
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
                $this->logger->warning($e->getMessage());
            }
        }

        return $this->render('app/work/members/edit.html.twig', [
            'form' => $form->createView(),
            'member' => $member
        ]);
    }

    /**
     * @Route ("/{id}/move", name=".move")
     */
    public function move(Member $member, Request $request, Move\Handler $handler): Response
    {
        $command = new Move\Command($member->getId());
        $form = $this->createForm(
            Move\Form::class,
            $command,
            ['currentGroup' => $member->getGroup()->getId()->getValue()]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Member was successfully moved');
                return $this->redirectToRoute('work.members.show', ['id' => $member->getId()]);
            } catch (\DomainException $e){
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
                $this->logger->warning($e->getMessage());
            }
        }

        return $this->render('app/work/members/move.html.twig', [
            'form' => $form->createView(),
            'member' => $member
        ]);
    }

    /**
     * @Route ("/{id}/archive", name=".archive")
     */
    public function archive(string $id, Request $request, Archive\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('archive', $request->request->get('token'))) {
            return $this->redirectToRoute('work.members.show', ['id' => $id]);
        }

        $command = new Archive\Command($id);

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Member was successfully archived');
        } catch (\DomainException $e){
            $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            $this->logger->warning($e->getMessage());
        }
        return $this->redirectToRoute('work.members.show', ['id' => $id]);
    }

    /**
     * @Route ("/{id}/reinstate", name=".reinstate")
     */
    public function reinstate(string $id, Request $request, Reinstate\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('reinstate', $request->request->get('token'))) {
            return $this->redirectToRoute('work.members.show', ['id' => $id]);
        }

        $command = new Reinstate\Command($id);

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Member was successfully reinstated');
        } catch (\DomainException $e){
            $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            $this->logger->warning($e->getMessage());
        }
        return $this->redirectToRoute('work.members.show', ['id' => $id]);
    }
}