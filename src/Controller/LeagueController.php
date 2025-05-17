<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use App\Entity\Stage;
use App\Repository\StageRepository;
use App\Entity\Team;
use App\Repository\TeamRepository;
use App\Entity\Game;
use App\Repository\GameRepository;



#[Route("/league", name: "league")]

class LeagueController extends AbstractController
{
	private $gameRepository;
	private $teamRepository;
	private $stageRepository;
	public function __construct(GameRepository $gameRepository, TeamRepository $teamRepository, StageRepository $stageRepository)
	{
		$this->gameRepository = $gameRepository;
		$this->teamRepository = $teamRepository;
		$this->stageRepository = $stageRepository;
	}


	#[Route("/clasificacion", name: "_clasificacion")]

	public function clasificacion(): Response
	{
		$teams = $this->teamRepository->findAll();

		$clasificacion = array();
		foreach ($teams as $team) {
			$clasificacion[] = ["equipo" => $team->getTeam(), "puntos" => 0];

			foreach ($team->getGamesGuest() as $game) {
				if ($game->getScoreHost() < $game->getScoreGuest())
					$clasificacion[array_key_last($clasificacion)]["puntos"]  += 3;

				if ($game->getScoreHost() == $game->getScoreGuest())
					$clasificacion[array_key_last($clasificacion)]["puntos"]  += 1;
			}


			foreach ($team->getGamesHost() as $game) {
				if ($game->getScoreGuest() < $game->getScoreHost())
					$clasificacion[array_key_last($clasificacion)]["puntos"]  += 3;

				if ($game->getScoreGuest() == $game->getScoreHost())
					$clasificacion[array_key_last($clasificacion)]["puntos"]  += 1;
			}
		}

		asort($clasificacion);


		return $this->render('league/clasificacion.html.twig', [
			'clasificacion' => $clasificacion,
		]);
	}


	#[Route("/stage/{id?0}", name: "_stage")]

	public function stage($id, Request $request): Response
	{
		$stages = $this->stageRepository->findAll();
		// Valor max stage 
		if ($id == 0) {
			foreach ($stages as $stage)
				if ($id < $stage->getStage()) $id = $stage->getStage();
		}
		// Jornada vector choices con jornadas select
		$jornadas = array();
		foreach ($stages as $stage)
			$jornadas[$stage->getStage()] = $stage->getStage();



		$stage = $this->stageRepository->find($id);
		$games  = $stage->getGames();

		$form = $this->createFormBuilder()
			->add('stage', ChoiceType::class, ['choices'  => $jornadas, 'data' => $id])
			->add('Send', SubmitType::class)
			->getForm();

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			// array con valores form field => value
			$data = $form->getData();
			return $this->redirectToRoute('league_stage', ['id' => $data['stage']]);
		} else {
			$stage = $this->stageRepository->find($id);

			return $this->render('league/stage.html.twig', array('form' => $form->createView(), "games" => $stage->getGames()));
		}
	}


	#[Route("/backend", name: "_backend")]

	public function backend(Request $request): Response
	{

		$stages = $this->stageRepository->findAll();
		// Valor max stage 
		$id = 0;
		foreach ($stages as $stage)
			if ($id < $stage->getStage()) $id = $stage->getStage();

		$stage = $this->stageRepository->find($id);
		$games  = $stage->getGames();




		$formBuilder = $this->createFormBuilder();

		foreach ($games as $game) {
			$formBuilder->add('game_host' . $game->getId(), TextType::class, ['data' => $game->getScoreHost(), 'label' => $game->getHost()->getTeam()]);

			$formBuilder->add('game_guest' . $game->getId(), TextType::class, ['data' => $game->getScoreGuest(), 'label' => $game->getGuest()->getTeam()]);
		}


		$formBuilder->add('Send', SubmitType::class);
		$form =  $formBuilder->getForm();

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			// array con valores form field => value
			$data = $form->getData();


			$data = [ 'game_host3' =>5, 'game_guest3' =>1, 'game_host4' =>5, 'game_guest3' =>1];
			foreach ($games as $game) {
				if (isset($data['game_host' . $game->getId()])) {
					$game->setScoreHost($data['game_host' . $game->getId()]);
				}

				if (isset($data['game_guest' . $game->getId()])) {
					$game->setScoreGuest($data['game_guest' . $game->getId()]);
				}
				$this->gameRepository->save($game);
				
				//$this->gameRepository->flush();

			}

			return $this->redirectToRoute('league_backend');
		} else {
			return $this->render('league/actualizacion.html.twig', array('form' => $form->createView()));
		}
	}
}
