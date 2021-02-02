<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Author;

class AuthorController extends AbstractController
{
    /**
     * @Route("/author", name="author_index", methods={"GET"})
     */
    public function index(request $r): Response
    {
        // $authors = $this->getDoctrine()
        //     ->getRepository(Author::class)
        //     ->findAll();

        $authors = $this->getDoctrine()
            ->getRepository(Author::class);
            if ($r->query->get('sort') == 'name_az') $authors = $authors->findBy([], ['name' => 'asc']);
            elseif ($r->query->get('sort') == 'name_za') $authors = $authors->findBy([], ['name' => 'desc']);
            elseif ($r->query->get('sort') == 'surname_az') $authors = $authors->findBy([], ['surname' => 'asc']);
            elseif ($r->query->get('sort') == 'surname_za') $authors = $authors->findBy([], ['surname' => 'desc']);
            else $authors = $authors->findAll();

        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
            'authors' => $authors,
            'sortBy' => $r->query->get('sort') ?? 'default'
        ]);
    }

    /**
     * @Route("/author/create", name="author_create", methods={"GET"})
     */
    public function create(request $r): Response
    {
        return $this->render('author/create.html.twig', [
            'errors' => $r->getSession()->getFlashBag()->get('errors', [])
        ]);
    }

    /**
     * @Route("/author/store", name="author_store", methods={"POST"})
     */
    public function store(request $r, ValidatorInterface $validator): Response
    {
        $author = new Author;
        $author
            ->setName($r->request->get('author_name'))
            ->setSurname($r->request->get('author_surname'));

        $errors = $validator->validate($author);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $r->getSession()->getFlashBag()->add('errors', $error->getMessage());
            }
            return $this->redirectToRoute('author_create');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($author);
        $entityManager->flush();

        return $this->redirectToRoute('author_index');
    }

    /**
     * @Route("/author/edit/{id}", name="author_edit", methods={"GET"})
     */
    public function edit(int $id): Response
    {
        $author = $this->getDoctrine()
            ->getRepository(Author::class)
            ->find($id);

        return $this->render('author/edit.html.twig', [
            'author' => $author
        ]);
    }

    /**
     * @Route("/author/update/{id}", name="author_update", methods={"POST"})
     */
    public function update(request $r, $id): Response
    {
        $author = $this->getDoctrine()
            ->getRepository(Author::class)
            ->find($id);

        $author
            ->setName($r->request->get('author_name'))
            ->setSurname($r->request->get('author_surname'));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($author);
        $entityManager->flush();

        return $this->redirectToRoute('author_index');
    }

    /**
     * @Route("/author/delete/{id}", name="author_delete", methods={"POST"})
     */
    public function delete($id): Response
    {
        $author = $this->getDoctrine()
            ->getRepository(Author::class)
            ->find($id);

        if ($author->getBooks()->count() > 0) {
            return new Response('Selected Author (#id: '.$author->getId().') can not be deleted, because '.$author->getName().' '.$author->getSurname().' has '.$author->getBooks()->count().' books.');
        }
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($author);
        $entityManager->flush();

        return $this->redirectToRoute('author_index');
    }
}
