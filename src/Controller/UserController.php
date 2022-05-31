<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use App\Repository\UserRepository;

class UserController extends AbstractController
{
    /**
     * @Route("/proj/user", name="proj-user", methods={"GET"})
     */
    public function user(SessionInterface $session, UserRepository $userRepository): Response
    {
        if (!$session->get("user")) {
            return $this->redirectToRoute("proj-login");
        }
        
        $user = $userRepository->find($session->get("user"));
        if ($user->getAdmin()) {
            $all = $userRepository->findAll();
            $data = [
                "currentUser" => $user,
                "allUsers" => $all
            ];
            return $this->render("poker/admin.html.twig", $data);
        }
        return $this->render("poker/user.html.twig", ["currentUser" => $user]);
    }
    /**
     * @Route("/proj/add", name="proj-add-balance", methods={"POST"})
     */
    public function addBalance(SessionInterface $session, Request $request , ManagerRegistry $doctrine): Response
    {
        $userName = $session->get("user");
        if (!$userName) {
            return $this->redirectToRoute("proj");
        }
        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository(User::class)->find($userName);
        $user->setBalance($user->getBalance() + $request->get("balance"));
        $entityManager->flush();
        $this->addFlash("messages", "Successfully inserted " . $request->get("balance") . " coins");
        return $this->redirectToRoute("proj-user");
    }
    /**
     * @Route("/proj/change-image", name="proj-change-image", methods={"POST"})
     */
    public function changeImage(SessionInterface $session, Request $request , ManagerRegistry $doctrine): Response
    {
        $userName = $session->get("user");
        if (!$userName) {
            return $this->redirectToRoute("proj");
        }
        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository(User::class)->find($userName);
        $user->setImage($request->get("image"));
        $entityManager->flush();
        $this->addFlash("messages", "Successfully changed image");
        return $this->redirectToRoute("proj-user");
    }
    /**
     * @Route("/proj/register", name="proj-register", methods={"GET"})
     */
    public function register(SessionInterface $session): Response
    {
        if ($session->get("user")) {
            return $this->redirectToRoute("proj-user");
        }
        return $this->render("poker/register.html.twig");
    }
    /**
     * @Route("/proj/register", name="proj-register-process", methods={"POST"})
     */
    public function registerProcess(SessionInterface $session, ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
        $user = new User();
        $user->setName($request->get("username"));
        $user->setPassword($request->get("password"));
        $user->setAdmin(false);
        $user->setImage("");
        $user->setBalance(0);
        $entityManager->persist($user);
        $entityManager->flush();
        $this->addFlash("messages", "User created");
        return $this->redirectToRoute("proj-login");
    }
    /**
     * @Route("/proj/logout", name="proj-logout", methods={"POST"})
     */
    public function logout(SessionInterface $session): Response
    {
        $session->set("pokergame", null);
        $session->set("user", null);
        return $this->redirectToRoute("proj");
    }
    /**
     * @Route("/proj/login", name="proj-login", methods={"GET"})
     */
    public function login(SessionInterface $session): Response
    {
        if ($session->get("user")) {
            return $this->redirectToRoute("proj-user");
        }
        return $this->render("poker/login.html.twig");
    }
    /**
     * @Route("/proj/login", name="proj-login-process", methods={"POST"})
     */
    public function loginProcess(SessionInterface $session, ManagerRegistry $doctrine, Request $request): Response
    {
        $session->set("pokergame", null);
        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository(User::class)->find($request->get("username"));
        if($user && $user->getPassword() === $request->get("password")) {
            $session->set("user", $user->getName());
            $this->addFlash("messages", "Logged in successfully");
            return $this->redirectToRoute("proj");
        }
        $this->addFlash("messages", "Wrong username or password");
        return $this->redirectToRoute("proj-login");
    }
    /**
     * @Route("/proj/remove", name="proj-remove-user", methods={"POST"})
     */
    public function remove(SessionInterface $session, ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository(User::class)->find($session->get("user"));
        if (!$user->getAdmin()) {
            $this->addFlash("messages", "Only an admin can remove users");
            return $this->redirectToRoute("proj-user");
        }
        $entityManager->remove($entityManager->getRepository(User::class)->find($request->get("username")));
        $entityManager->flush();
        return $this->redirectToRoute("proj-user");
    }
    /**
     * @Route("/proj/edit", name="proj-edit-user", methods={"POST"})
     */
    public function edit(SessionInterface $session, ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository(User::class)->find($session->get("user"));
        if (!$user->getAdmin()) {
            $this->addFlash("messages", "Only an admin can edit other users");
            return $this->redirectToRoute("proj-user");
        }
        $userToEdit = $entityManager->getRepository(User::class)->find($request->get("username"));
        $userToEdit->setAdmin($request->get("admin") ?? false);
        $userToEdit->setImage($request->get("image"));
        $userToEdit->setBalance($request->get("balance"));
        $entityManager->flush();
        return $this->redirectToRoute("proj-user");
    }
    /**
     * @Route("/proj/reset", name="proj-reset")
     */
    public function reset(SessionInterface $session, ManagerRegistry $doctrine): Response
    {
        $session->set("user", null);
        $session->set("pokergame", null);
        $entityManager = $doctrine->getManager();
        $users = $entityManager->getRepository(User::class)->findAll();
        for ($i = 0; $i < count($users); $i++) {
            $entityManager->remove($users[$i]);
        }
        $entityManager->flush();
        $admin = new User();
        $admin->setName("admin");
        $admin->setPassword("admin");
        $admin->setAdmin(true);
        $admin->setImage("");
        $admin->setBalance(PHP_INT_MAX);
        $doe = new User();
        $doe->setName("doe");
        $doe->setPassword("doe");
        $doe->setAdmin(false);
        $doe->setImage("");
        $doe->setBalance(0);
        $entityManager->persist($admin);
        $entityManager->persist($doe);
        $entityManager->flush();
        return $this->redirectToRoute("proj-login");
    }
}
