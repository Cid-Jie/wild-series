<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\ProgramType;
use App\Repository\ProgramRepository;
use App\Service\Slugify;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProgramRepository $programRepository): Response
    {
        $programs = $programRepository->findAll();
        return $this->render('program/index.html.twig', [
            'programs' => $programs,
         ]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, ProgramRepository $programRepository, Slugify $slugify)
    {
        $program = new Program();
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            $slug = $slugify->generate($program->getTitle());
            $program->setSlug($slug);
            $programRepository->add($program, true);
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
    public function showEpisode(Program $program, Season $season, Episode $episode)
    {
        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode
        ]);
    }
}
