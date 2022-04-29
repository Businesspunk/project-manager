<?php

namespace App\Controller\Profile;

use App\ReadModel\User\UserFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile")
 */
class ProfileController extends AbstractController
{
    private $userFetcher;

    public function __construct(UserFetcher $userFetcher)
    {
        $this->userFetcher = $userFetcher;
    }

    /**
     * @Route("/", name="profile")
     * @return Response
     */
    public function index(): Response
    {
        $user = $this->getUser();
        $id = $user->getId();
        $detail = $this->userFetcher->getDetail($id);

        return $this->render('app/profile.html.twig', compact('detail'));
    }
}