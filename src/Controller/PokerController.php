<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use App\Poker\PokerGame;
use App\Repository\UserRepository;

class PokerController extends AbstractController
{
    private function getCurrentUser(UserRepository $userRepository, SessionInterface $session): User|null {
        $username = $session->get("user");
        if (!$username) {
            return null;
        }
        $user = $userRepository->find($username);
        if (!$user) {
            return null;
        }
        return $user;
    }
    /**
     * Returns current pokergame from session. New game is started if none exists
     * @param SessionInterface $session
     * 
     * @return PokerGame
     */
    private function getCurrentGame(SessionInterface $session): PokerGame {
        return $session->get("pokergame") ?? new PokerGame();
    }
    /**
     * @Route("/proj", name="proj")
     */
    public function index(SessionInterface $session, UserRepository $userRepository): Response
    {
        $user = $this->getCurrentUser($userRepository, $session);
        if (!$user) {
            return $this->redirectToRoute("proj-login");
        }
        return $this->render("poker/index.html.twig", ["user" => $user]);
    }
    /**
     * @Route("/proj/play", name="proj-play", methods={"GET"})
     */
    public function play(SessionInterface $session, UserRepository $userRepository): Response
    {
        $user = $this->getCurrentUser($userRepository, $session);
        if (!$user) {
            return $this->redirectToRoute("proj-login");
        }
        $game = $this->getCurrentGame($session);
        return $this->render($game->renderPath(), ["game" => $game, "user" => $user]);
    }
    /**
     * @Route("/proj/play", name="proj-play-process", methods={"POST"})
     */
    public function process(SessionInterface $session, UserRepository $userRepository, Request $request, ManagerRegistry $doctrine): Response
    {
        $user = $this->getCurrentUser($userRepository, $session);
        if (!$user) {
            return $this->redirectToRoute("proj-login");
        }
        $game = $this->getCurrentGame($session);
        $messages = $game->processRequest($request, $user);
        foreach ($messages as $message) {
            $this->addFlash("messages", $message);
        }
        $session->set("pokergame", $game);
        $doctrine->getManager()->flush();
        return $this->redirectToRoute("proj-play");
    }
    /**
     * @Route("/proj/play/reset", name="proj-reset-game", methods={"POST"})
     */
    public function reset(SessionInterface $session): Response
    {
        $session->set("pokergame", null);
        return $this->redirectToRoute("proj-play");
    }
}
