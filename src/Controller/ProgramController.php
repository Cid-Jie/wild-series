<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\CommentType;
use App\Form\ProgramType;
use App\Repository\CommentRepository;
use App\Repository\ProgramRepository;
use App\Service\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProgramRepository $programRepository, Request $request): Response
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData(['search']);
            $programs = $programRepository->findLikeName($search);
        } else {
             $programs = $programRepository->findAll();
        }
       
        return $this->render('program/index.html.twig', [
            'programs' => $programs,
            'form' => $form->createView(),
         ]);
    }

    #[Route('/list', name: 'list')]
    public function list(ProgramRepository $programRepository): Response
    {
        $programs = $programRepository->findAll();
        return $this->render('program/list.html.twig', [
            'programs' => $programs,
         ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/new', name: 'new')]
    public function new(Request $request, MailerInterface $mailer ,ProgramRepository $programRepository, Slugify $slugify)
    {
        $program = new Program();
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            $slug = $slugify->generate($program->getTitle());
            $program->setSlug($slug);
            $program->setOwner($this->getUser());
            $programRepository->add($program, true);
            $this->addFlash('success', 'La série a bien été ajoutée.');
            $email = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to('your_email@example.com')
                ->subject('Une nouvelle série vient d\'être publiée !')
                ->html($this->renderView('program/newProgramEmail.html.twig', ['program' => $program]));
            $mailer->send($email);
            return $this->redirectToRoute('program_index');
        }
        
        return $this->render('program/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{slug}/', methods: ['GET'], name: 'show')]
    public function show(Program $program): Response
    {
 
        if (!$program) {
            throw $this->createNotFoundException(
                'No program found with id ' . $program . ' found in program\'s table.'
            );
        }

        $seasons = $program->getSeasons();

        return $this->render('program/show.html.twig', [
            'program' => $program,
            'seasons' => $seasons,
        ]);
    }

    #[Route('/{id}/watchlist', name: 'watchlist', methods: ["GET","POST"])]
    public function addToWatchlist(Request $request, Program $program, EntityManagerInterface $entityManager): Response
    {
        /* if ($this->getUser()->isInWatchlist($program)) {
            $this->getUser()->removeFromWatchlist($program);
        } else {
            $this->getUser()->addToWatchlist($program);
        } */
        $this->getUser->addToWatchlist($program);
        $entityManager->flush();

        return $this->redirectToRoute('season_show', [
            'program' => $program,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{slug}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Program $program, ProgramRepository $programRepository): Response
    {
        /*if (!($this->getUser() == $program->getOwner())) {
            // If not the owner, throws a 403 Access Denied exception
            throw new AccessDeniedException('Only the owner can edit the program!');
        }*/
    
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $programRepository->add($program, true);

            return $this->redirectToRoute('program_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('program/edit.html.twig', [
            'program' => $program,
            'form' => $form,
        ]);
    }
    
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request,Program $program, ProgramRepository $programRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$program->getId(), $request->request->get('_token'))) {
            $programRepository->remove($program, true);
            $this->addFlash('warning', 'La série a bien été supprimée.');
        }

        return $this->redirectToRoute('program_list', [], Response::HTTP_SEE_OTHER);
    }

    #[Route("/{program_slug}/season/{season_id}", methods: 'GET', name: 'season_show')]
    #[ParamConverter('program', options: ['mapping' => ['program_slug' => 'slug']])]
    #[Entity('season', options: ['id' => 'season_id'])]
    public function showSeason(Program $program, Season $season)
    {
        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season,
        ]);
    }

    #[Route("/{program_slug}/season/{season_id}/episode/{episode_slug}", name: "episode_show")]
    #[ParamConverter('program', options: ['mapping' => ['program_slug' => 'slug']])]
    #[Entity('season', options: ['id' => 'season_id'])]
    #[ParamConverter('episode', options: ['mapping' => ['episode_slug' => 'slug']])]
    public function showEpisode(EntityManagerInterface $manager, Program $program, Season $season, Episode $episode, CommentRepository $commentRepository, Request $request )
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($this->getUser());
            $comment->setEpisode($episode);
            $manager->persist($comment);
            $commentRepository->add($comment, true);
            $manager->flush();
        }

        $comments = $commentRepository->findAll();

        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,
            'form' => $form->createView(),
            'comments' => $comments
        ]);
    }
}
