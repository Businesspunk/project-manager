<?php

namespace App\Controller\Work\Members;

use App\Model\Work\Entity\Members\Group\Group;
use App\Model\Work\UseCase\Members\Group\Edit;
use App\Model\Work\UseCase\Members\Group\Delete;
use App\Model\Work\UseCase\Members\Group\Create;
use App\ReadModel\Work\Group\GroupFetcher;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/work/members/groups", name="work.members.groups")
 * @IsGranted ("ROLE_WORK_MEMBERS_MANAGE")
 */
class GroupController extends AbstractController
{
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
    public function index(GroupFetcher $fetcher): Response
    {
        $groups = $fetcher->all();
        return $this->render('app/work/members/group/index.html.twig', compact('groups'));
    }

    /**
     * @Route ("/create", name=".create")
     */
    public function create(Request $request, Create\Handler $handler): Response
    {
         $command = new Create\Command();
         $form = $this->createForm(Create\Form::class, $command);
         $form->handleRequest($request);

         if ($form->isSubmitted() && $form->isValid()) {
             try {
                 $handler->handle($command);
                 $this->addFlash('success', 'Group was successfully created');
                 return $this->redirectToRoute('work.members.groups');
             } catch (\DomainException $e){
                 $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
                 $this->logger->error($e->getMessage());
             }
         }

         return $this->render('app/work/members/group/create.html.twig', [
             'form' => $form->createView()
         ]);
    }

    /**
     * @Route ("/{id}/edit", name=".edit")
     */
    public function edit(Group $group, Request $request, Edit\Handler $handler): Response
    {
        $command = Edit\Command::createFromGroup($group);
        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Group was successfully edited');
                return $this->redirectToRoute('work.members.groups');
            } catch (\DomainException $e) {
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
                $this->logger->error($e->getMessage());
            }
        }

        return $this->render('app/work/members/group/edit.html.twig', [
            'form' => $form->createView(),
            'group' => $group
        ]);
    }

    /**
     * @Route ("/{id}/delete", name=".delete")
     */
    public function delete(string $id, Request $request, Delete\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('work.members.groups');
        }
        $command = new Delete\Command($id);
        try {
            $handler->handle($command);
            $this->addFlash('success', 'Group was successfully deleted');
            return $this->redirectToRoute('work.members.groups');
        } catch (\DomainException $e) {
            $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            $this->logger->error($e->getMessage());
        }

        return $this->redirectToRoute('work.members.groups');
    }
}