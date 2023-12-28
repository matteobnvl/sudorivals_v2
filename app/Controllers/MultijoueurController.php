<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\Game;
use App\Models\Sudoku;
use App\Models\User;
use App\Models\Multijoueur;

class MultijoueurController extends Controller
{
    private $sudoku;
    private $user;
    private $multijoueur;

    function __construct()
    {
        parent::__construct();
        if (!isConnected()) {
            redirect('app_login');
        }
        $this->sudoku = new Sudoku();
        $this->user = new User();
        $this->multijoueur = new Multijoueur();
    }

    public function multi()
    {
        $message = '';
        $id_multi = null;
        if ($_GET) {
            if ($_GET['mode'] === 'aleatoire') {
                if (!$this->multijoueur->checkUserHasNotDuel()) {
                    $message = 'recherche d\'un joueur';
                    $id_multi = $this->multijoueur->getIdMultiByJoueur();
                    $id_multi = $id_multi[0]['id_multi'];
                } else {
                    if (!$this->multijoueur->checkDuelInProgress()) {
                        $id_multi = $this->multijoueur->getIdDuelInProgress();
                        $id_multi = $id_multi[0]['id_multi'];
                        $this->multijoueur->createDuel($id_multi);
                        $this->multijoueur->updateComplet($id_multi);
                        redirect('app_game_multi', [], '?duel='.$id_multi);
                    } else {
                        // création du duel
                        $id_multi = $this->multijoueur->create();
                        $this->multijoueur->createDuel($id_multi);
                        $message = 'recherche d\'un joueur';
                    }
                }
            } else if ($_GET['mode'] === 'annuler') {
                $this->multijoueur->updateAnnuler($_GET['id']);
                redirect('app_multi');
            }
        }
        return $this->twig->render('multijoueur/index.html.twig', [
            'message' => $message,
            'id_multi' => $id_multi
        ]);
    }

    public function attente()
    {
        if ($_POST) {
            $duel = $this->multijoueur->checkDuelIsComplete($_POST['id_multi']);
            $complet = ($duel[0]['complet'] == 1);
            return $complet;
        }
    }

    public function gameMulti(int $id_duel, int $id_sudoku)
    {
        if (!$id_duel) {
            redirect('app_dashboard');
        }
        if (!$this->multijoueur->isDuelByIdJoueur($id_duel, $_SESSION['id_joueur'])) {
            redirect('app_dashboard');
        }
        if (!isset($_GET['sudoku']) && $this->multijoueur->notSudokuInDuel($id_duel)) {
            $sudoku = $this->sudoku->generateSudoku();
            $id_sudoku = $this->sudoku->createSudoku(
                json_encode($sudoku->{'sudoku'}),
                json_encode($sudoku->{'solution'}),
            );
            $this->multijoueur->updateSudokuDuel($id_sudoku, $_GET['duel']);
            redirect('app_game_multi', ['id_duel' => $id_duel, 'id_sudoku' => $id_sudoku]);
        }
        if (!$this->multijoueur->isCorrectIdSudoku($id_duel, $id_sudoku)) {
            redirect('app_dashboard');
        }
        $sudokuJoueur = $this->sudoku->getTableauByIdSudoku($id_sudoku);
        $statut = $this->multijoueur->getVieAndStatutByIdDuel($id_duel);
        $adversaire = $this->multijoueur->getAdversaireDuelByIdMulti($id_duel);
        return $this->twig->render('multijoueur/game_multi.html.twig', [
            'adversaire' => $adversaire[0],
            'sudoku' => json_decode($sudokuJoueur[0]['tableau']),
            'statut' => $statut[0],
            'id_multi' => $id_duel,
            'id_sudoku' => $id_sudoku
        ]);
    }

    public function sudokeAdverse()
    {
        if ($_POST) {
            $sudoku = $this->multijoueur->getSudokuAdverse($_POST['id']);
            $sudoku = $sudoku[0];
            return json_encode($sudoku['tableau']);
        }
    }

    public function insertMulti()
    {
        if ($_POST) {
            $index = array_map('intval', explode(',', $_POST['arrayCase']['key']));
            $value = $_POST['arrayCase']['value'];
            $id_sudoku = $_POST['id'];
            $sudoku = $this->sudoku->getSudokuById($id_sudoku);
            $this->sudoku->insertMulti($index, $value, $sudoku, $id_sudoku);
        }
    }

    public function deleteMulti()
    {
        if ($_POST) {
            $index = array_map('intval', explode(',',$_POST['attrCase']));
            $id_sudoku = intval($_POST['id']);
            $sudoku = $this->sudoku->getSudokuById($id_sudoku);
            $this->sudoku->deleteMulti($index, $sudoku, $id_sudoku);
        }
    }

    public function vie()
    {
        if ($_POST) {
            $vie = $this->multijoueur->getVieAdverseByIdMulti($_POST['id_duel']);
            return $vie[0]['vie'];
        }
    }

    public function checkVainqueur()
    {
        if ($_POST) {
            return $this->multijoueur->checkAdverseIsVainqueur($_POST['id_duel']);
        }
    }

    public function win()
    {
        if ($_POST) {
            $id_multi = $_POST['id_duel'];
            $id_sudoku = $_POST['id_sudoku'];
            $vie = $this->multijoueur->getVieAdverseByIdMulti($_POST['id_duel']);
            if ($vie[0]['vie'] == 0)
            {
                $this->multijoueur->UpdateJoueurVainqueurDuelById($id_multi);
                $this->user->addScore(50);
                $this->multijoueur->updateScorePartie($id_multi);
                $this->multijoueur->updateStatutDuelById($id_multi, 2);
                redirect('game_multi', [], '?duel='.$id_multi.'&sudoku='.$id_sudoku);
            }
        }
    }

    public function lose()
    {
        if ($_POST) {
            $id_multi = $_POST['id_duel'];
            $id_sudoku = $_POST['id_sudoku'];
            if ($this->multijoueur->checkAdverseIsVainqueur($_POST['id_duel']) == 'true') {
                $this->multijoueur->updateStatutDuelById($id_multi, 3);
            }
        }
    }

    public function verifMulti()
    {
        if ($_POST) {
            $id_multi = $_POST['id_duel'];
            $id_sudoku = $_POST['id_sudoku'];

            $vie = $this->multijoueur->getVieAndStatutByIdDuel($id_multi);
            $vie = $vie[0]['vie'] -1;
            $this->multijoueur->updateVieById($id_multi, $vie);
            if ($vie == 0) {
                $this->multijoueur->updateStatutDuelById($id_multi, 3);
                return 'false';
            }
            $solutionSudoku = $this->sudoku->getSolutionSudokuByIdSudoku($id_sudoku);
            $solutionSudoku = json_decode($solutionSudoku[0]['solution']);

            $sudoku = $this->sudoku->getTableauByIdSudoku($id_sudoku);
            $sudoku = json_decode($sudoku[0]['tableau']);

            $arrayVerif = [];

            foreach ($sudoku as $keyLigne => $ligne) {
                foreach ($ligne as $keyCase => $case) {
                    if ($case >= 10) {
                        $array = [
                            'key' => strval($keyLigne).','. strval($keyCase),
                            'value' => $case / 10 == $solutionSudoku[$keyLigne][$keyCase]
                        ];
                        $arrayVerif[] = $array;
                    }
                }
            }
            return json_encode($arrayVerif);
        }
    }


    public function finishMulti()
    {
        if ($_POST) {
            $id_multi = $_POST['id_duel'];
            $id_sudoku = $_POST['id_sudoku'];

            $solutionSudoku = $this->sudoku->getSolutionSudokuByIdSudoku($id_sudoku);
            $solutionSudoku = json_decode($solutionSudoku[0]['solution']);

            $sudoku = $this->sudoku->getSudokuById($id_sudoku);
            $sudoku = json_decode($sudoku[0]['tableau']);

            $arrayVerif = [];

            foreach ($sudoku as $keyLigne => $ligne) {
                foreach ($ligne as $keyCase => $case) {
                    if ($case >= 10) {
                        if (($case /10) != $solutionSudoku[$keyLigne][$keyCase]) {
                            $array = [
                                'key' => strval($keyLigne).','. strval($keyCase),
                                'value' => false
                            ];
                            $arrayVerif[] = $array;
                        }
                    }
                }
                
            }

            if (empty($arrayVerif)) {
                $this->multijoueur->UpdateJoueurVainqueurDuelById($id_multi);
                $this->user->addScore(50);
                $this->multijoueur->updateScorePartie($id_multi);
                $this->multijoueur->updateStatutDuelById($id_multi, 2);
                return json_encode(['key' => true, 'score' => 50]);
            }
            return json_encode($arrayVerif);
        }
    }
}