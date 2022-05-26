<?php

namespace App\Controller;

use App\Entity\Program;
use App\Entity\Season;
use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    #[Route('/{id<\d+>}/', methods: ['GET'], name: 'show')]
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
    
    #[Route("/{programId}/season/{seasonId}", methods: 'GET', name: 'season_show')]
    public function showSeason(int $programId, int $seasonId, Program $program, Season $season, ProgramRepository $programRepository)
    {
        $program = $programRepository->findAll($programId);
        $season = $program->getSeasons($seasonId);

        return $this->render('program/season_show.html.twig', [
            'programeId' => $programId,
            'seasonId' => $seasonId,
            'season' => $season,
        ]);
    }
}
