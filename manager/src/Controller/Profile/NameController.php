<?php

namespace App\Controller\Profile;

use App\Model\User\UseCase\Name\Command;
use App\Model\User\UseCase\Name\Form;
use App\Model\User\UseCase\Name\Handler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile", name="profile")
 */
class NameController extends AbstractController
{
    /**
     * @Route("/name", name=".name", methods={"GET","POST"})
     */
    public function index(Request $request, Handler $handler)
    {
        $command = new Command();
        $form = $this->createForm(Form::class, $command);
        $command->id = $this->getUser()->getId();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Name was successfully changed');
                return $this->redirectToRoute('profile');
            } catch (\DomainException $e) {
                $this->addFlash('error', $e->getMessage());
                return $this->redirectToRoute('profile.name');
            }
        }

        return $this->render('app/profile/name/change.html.twig', [
            'form' => $form->createView()
        ]);
    }
}