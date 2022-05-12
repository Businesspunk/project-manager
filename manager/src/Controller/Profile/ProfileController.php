<?php

namespace App\Controller\Profile;

use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile", name="profile")
 */
class ProfileController extends AbstractController
{
    private $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }
    /**
     * @Route("", name="", methods={"GET"})
     */
    public function index(): Response
    {
        $id = $this->getUser()->getId();
        $detail = $this->users->get(new Id($id));

        return $this->render('app/profile.html.twig', compact('detail'));
    }
}