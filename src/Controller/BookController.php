<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;

class BookController extends AbstractController
{
    /**
     * @Route("/book", name="book-index")
     */
    public function indexRoute(): Response
    {
        return $this->render("book/index.html.twig");
    }
    /**
     * @Route("/book/list", name="book-list")
     */
    public function bookListRoute(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findAll();
        return $this->render("book/booklist.html.twig", ["books" => $books]);
    }
    /**
     * @Route("/book/add", name="book-add")
     */
    public function addBookRoute(): Response
    {
        return $this->render("book/bookform.html.twig");
    }
    /**
     * @Route("/book/view/{id}", name="book-view")
     */
    public function viewBookRoute(int $id, BookRepository $bookRepository): Response
    {
        $book = $bookRepository->find($id);
        return $this->render("book/bookview.html.twig", ["book" => $book]);
    }
    /**
     * @Route("/book/edit/{id}", name="book-edit")
     */
    public function editBookRoute(int $id, BookRepository $bookRepository): Response
    {
        $book = $bookRepository->find($id);
        return $this->render("book/bookedit.html.twig", ["book" => $book]);
    }
    /**
     * @Route("/book/edit-process", name="book-edit-process")
     */
    public function editBookProcessRoute(Request $request, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $book = $entityManager->getRepository(Book::class)->find($request->get("id"));
        $book->setTitle($request->get("title"));
        $book->setIsbn($request->get("isbn"));
        $book->setAuthor($request->get("author"));
        $book->setDescription($request->get("description"));
        $book->setImage($request->get("image"));
        $entityManager->flush();
        $this->addFlash("messages", '"' . $book->getTitle() . '" har ändrats');
        return $this->redirectToRoute("book-list");
    }
    /**
     * @Route("/book/remove", name="book-remove")
     */
    public function removeBookRoute(Request $request, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $book = $entityManager->getRepository(Book::class)->find($request->get("id"));
        $this->addFlash("messages", '"' . $book->getTitle() . '" har tagits bort');
        $entityManager->remove($book);
        $entityManager->flush();
        return $this->redirectToRoute("book-list");
    }
    /**
     * @Route("/book/add/process", name="book-add-process", methods={"POST"})
     */
    public function addBookPostRoute(Request $request, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $book = new Book();
        $book->setTitle($request->get("title"));
        $book->setIsbn($request->get("isbn"));
        $book->setAuthor($request->get("author"));
        $book->setDescription($request->get("description"));
        $book->setImage($request->get("image"));
        $entityManager->persist($book);
        $entityManager->flush();
        $this->addFlash("messages", '"' . $book->getTitle() . '" har lagts till');
        return $this->redirectToRoute("book-add");
    }
}
