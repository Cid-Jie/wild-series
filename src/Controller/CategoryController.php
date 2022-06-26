<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\ProgramRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;

#[Route('/category', name: 'category_')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        return $this->render('category/index.html.twig', [
            'categories' => $categories,
         ]);
    }

    #[Route('/list', name: 'list')]
    public function list(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        return $this->render('category/list.html.twig', [
            'categories' => $categories,
         ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/new', name:'new')]
    public function new(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();

        // Create the form, linked with $category
        $form = $this->createForm(CategoryType::class, $category);
        
        // Get data from HTTP request
        $form->handleRequest($request);

        //Was the form submitted ?
        if ($form->isSubmitted() && $form->isValid()) {
            // Deal with the submitted data
            // For example : persiste & flush the entity
            $categoryRepository->add($category, true);
            // And redirect to a route that display the result
            return $this->redirectToRoute('category_list');
        }

        // Render the form
        //return $this->renderForm('category/new.html.twig', [
         //  'form' => $form,
        //]);

        // Another alternative
        return $this->render('category/new.html.twig', [
           'form' => $form->createView(),
        ]);
    }

    #[Route('/{categoryName}', name: 'show')]
    public function show(string $categoryName, CategoryRepository $categoryRepository, ProgramRepository $programRepository): Response
    {
        
        if (!$categoryName) {
            throw $this->createNotFoundException(
                'No category found with ' . $categoryName . ' found in category\'s table.'
            );
        }else {
            $category = $categoryRepository->findOneBy(
                ['name' => $categoryName],
            );
            $programs = $programRepository->findBy(
                ['category' => $category],
                ['id' => 'DESC'], 3
            );

        }
        return $this->render('category/show.html.twig', [
            'category' => $category,
            'programs' => $programs,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->add($category, true);

            return $this->redirectToRoute('category_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $categoryRepository->remove($category, true);
        }

        return $this->redirectToRoute('category_list', [], Response::HTTP_SEE_OTHER);
    }

}
