<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Author;
use App\Entity\Book;

class BookController extends AbstractController
{
    /**
     * @Route("/book", name="book_index")
     */
    public function index(request $r): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // $books = $this->getDoctrine()
        //     ->getRepository(Book::class)
        //     ->findAll();

        $authors = $this->getDoctrine()
            ->getRepository(Author::class)
            ->findBy([],['surname'=>'asc']);

        $books = $this->getDoctrine()
            ->getRepository(Book::class);
            if ($r->query->get('author_id') !== null && $r->query->get('author_id') != 0) 
                $books = $books->findBy(['author_id' => $r->query->get('author_id')], ['title' => 'asc']);
            elseif ($r->query->get('author_id') == 0) $books = $books->findAll();
            else $books = $books->findAll();

        return $this->render('book/index.html.twig', [
            'books' => $books,
            'authors' => $authors,
            'authorId' => $r->query->get('author_id') ?? 0,
            'success' => $r->getSession()->getFlashBag()->get('success', [])
        ]);
    }

    /**
     * @Route("/book/create", name="book_create", methods={"GET"})
     */
    public function create(request $r): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $authors = $this->getDoctrine()
            ->getRepository(Author::class)
            ->findBy([],['surname'=>'asc']);

        $book_title = $r->getSession()->getFlashBag()->get('book_title', []);
        $book_isbn = $r->getSession()->getFlashBag()->get('book_isbn', []);
        $book_pages = $r->getSession()->getFlashBag()->get('book_pages', []);
        $book_about = $r->getSession()->getFlashBag()->get('book_about', []);
        $book_author_id = $r->getSession()->getFlashBag()->get('book_author_id', []);

        return $this->render('book/create.html.twig', [
            'authors' => $authors,
            'book_title' => $book_title[0] ?? '',
            'book_isbn' => $book_isbn[0] ?? '',
            'book_pages' => $book_pages[0] ?? '',
            'book_about' => $book_about[0] ?? '',
            'book_author_id' => $book_author_id[0] ?? '',
            'errors' => $r->getSession()->getFlashBag()->get('errors', [])
        ]);
    }

    /**
     * @Route("/book/store", name="book_store", methods={"POST"})
     */
    public function store(request $r, ValidatorInterface $validator): Response
    {
        $submittedToken = $r->request->get('token');
        if (!$this->isCsrfTokenValid('', $submittedToken)) $r->getSession()->getFlashBag()->add('errors', 'Invalid token.');

        $author = $this->getDoctrine()
            ->getRepository(Author::class)
            ->find($r->request->get('book_author_id'));

        if($author == null) $r->getSession()->getFlashBag()->add('errors', 'Author must be selected.');
        
        $book = new Book;
        $book
            ->setTitle($r->request->get('book_title'))
            ->setIsbn($r->request->get('book_isbn'))
            ->setPages((int)$r->request->get('book_pages'))
            ->setAbout($r->request->get('book_about'))
            ->setAuthor($author);

        $errors = $validator->validate($book);
        if (count($errors) > 0 or $author == null) {
            foreach ($errors as $error) {
                $r->getSession()->getFlashBag()->add('errors', $error->getMessage());
            }
            $r->getSession()->getFlashBag()->add('book_title', $r->request->get('book_title'));
            $r->getSession()->getFlashBag()->add('book_isbn', $r->request->get('book_isbn'));
            $r->getSession()->getFlashBag()->add('book_pages', $r->request->get('book_pages'));
            $r->getSession()->getFlashBag()->add('book_about', $r->request->get('book_about'));
            $r->getSession()->getFlashBag()->add('book_author_id', $r->request->get('book_author_id'));
            return $this->redirectToRoute('book_create');
        }
        if (!$this->isCsrfTokenValid('', $submittedToken)) return $this->redirectToRoute('book_create');
        if($author == null) return $this->redirectToRoute('book_create');

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($book);
        $entityManager->flush();

        $r->getSession()->getFlashBag()->add('success', 'Book was added.');

        return $this->redirectToRoute('book_index');
    }

    /**
     * @Route("/book/edit/{id}", name="book_edit", methods={"GET"})
     */
    public function edit(int $id, request $r): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $book = $this->getDoctrine()
            ->getRepository(Book::class)
            ->find($id);

        $authors = $this->getDoctrine()
            ->getRepository(Author::class)
            ->findBy([],['surname'=>'asc']);

        return $this->render('book/edit.html.twig', [
            'book' => $book,
            'authors' => $authors,
            'errors' => $r->getSession()->getFlashBag()->get('errors', []),
            'success' => $r->getSession()->getFlashBag()->get('success', [])
        ]);
    }

    /**
     * @Route("/book/update/{id}", name="book_update", methods={"POST"})
     */
    public function update(request $r, $id, ValidatorInterface $validator): Response
    {
        $submittedToken = $r->request->get('token');
        if (!$this->isCsrfTokenValid('', $submittedToken)) $r->getSession()->getFlashBag()->add('errors', 'Invalid token.');

        $book = $this->getDoctrine()
            ->getRepository(Book::class)
            ->find($id);

        $author = $this->getDoctrine()
            ->getRepository(Author::class)
            ->find($r->request->get('books_author'));
        
        $book
            ->setTitle($r->request->get('book_title'))
            ->setIsbn($r->request->get('book_isbn'))
            ->setPages($r->request->get('book_pages'))
            ->setAbout($r->request->get('book_about'))
            ->setAuthor($author);
            
        $errors = $validator->validate($book);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $r->getSession()->getFlashBag()->add('errors', $error->getMessage());
            }
            return $this->redirectToRoute('book_edit', ['id'=>$book->getId()]);
        }
        if (!$this->isCsrfTokenValid('', $submittedToken)) return $this->redirectToRoute('book_edit', ['id'=>$book->getId()]);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($book);
        $entityManager->flush();

        $r->getSession()->getFlashBag()->add('success', 'Book was updated.');

        return $this->redirectToRoute('book_index');
    }

    /**
     * @Route("/book/delete/{id}", name="book_delete", methods={"POST"})
     */
    public function delete($id): Response
    {
        $book = $this->getDoctrine()
            ->getRepository(Book::class)
            ->find($id);
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($book);
        $entityManager->flush();

        return $this->redirectToRoute('book_index');
    }
}
