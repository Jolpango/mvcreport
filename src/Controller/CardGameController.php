<?php

namespace App\Controller;

use App\Cards\CardGame;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CardGameController extends AbstractController
{
    private function getGame(SessionInterface $session): CardGame
    {
        $game = null;
        if ($session->get("game")) {
            $game = unserialize($session->get("game"));
        } else {
            $game = new CardGame();
        }
        return $game;
    }
    /**
     * @Route("/game", name="game")
     */
    public function cardGameIndexRoute(SessionInterface $session): Response
    {
        return $this->render("gamelinks.html.twig", ["game" => unserialize($session->get("game"))]);
    }
    /**
     * @Route("/game/card", name="game-doc")
     */
    public function cardGameDocRoute(): Response
    {
        return $this->render("game/gamedoc.html.twig");
    }
    /**
     * @Route("/game/reset", name="game-reset")
     */
    public function cardGameResetRoute(SessionInterface $session): Response
    {
        $session->remove("game");
        return $this->redirectToRoute("game");
    }
    /**
     * @Route("/game/play", name="game-play")
     */
    public function cardGamePlayRoute(SessionInterface $session): Response
    {
        $game = $this->getGame($session);
        $session->set("game", serialize($game));
        return $this->render($game->renderPath(), $game->renderData());
    }

    /**
     * @Route("/game/play/process", name="game-play-process", methods={"POST"})
     */
    public function cardGamePostRoute(Request $request, SessionInterface $session): Response
    {
        $game = $this->getGame($session);
        $messages = $game->processRequest($request);
        foreach ($messages as $message) {
            $this->addFlash("messages", $message);
        }
        $session->set("game", serialize($game));
        return $this->redirectToRoute("game-play");
    }
}
