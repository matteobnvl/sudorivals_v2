<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\Game;
use App\Models\Sudoku;
use App\Models\User;

class GameController extends Controller
{
    private $game;
    private $sudoku;
    private $user;
    
    function __construct()
    {
        parent::__construct();
        $this->game = new Game();
        $this->sudoku = new Sudoku();
        $this->user = new User();
        if ($_GET) {
            if ($_SESSION) {
                if (!$this->game->verifGame($_GET['sudoku'], $_SESSION['id_joueur'])) {
                    redirect('Dashboard');
                }
            } else {
                $partie = $this->game->getGame($_GET['sudoku']);
                if (empty($partie) || $partie['id_joueur'] != null) {
                    redirect('Accueil');
                }
            }
        }
    }

    public function generate()
    {
        if ($_POST) {
            $niveau = $this->game->getIdNiveauByName($_POST['niveau']);
            $niveau = $niveau[0]['id_niveau'];
        } else {
            $niveau  = 1;
        }
        if ($_SESSION) {
            if ($this->game->create($niveau, $_SESSION['id_joueur'])) {
                $partie = $this->game->getLastGameCreate($_SESSION['id_joueur']);
            }
        } else {
            if ($this->game->create()) {
                $partie = $this->game->getLastGameCreate();
            }
        }
        $sudoku = ($_POST)
                    ? $this->sudoku->generateSudoku($_POST['niveau'])
                    : $this->sudoku->generateSudoku();
        $this->sudoku->createSudoku(
            json_encode($sudoku->{'sudoku'}),
            json_encode($sudoku->{'solution'}),
            $partie['id_partie']);
        redirect('app_game', ['id_partie' => $partie['id_partie']]);
    }

    public function index(int $id_partie)
    {
        $sudoku = $this->sudoku->getSudokuByPartie($id_partie);
        if (empty($sudoku)) {
            redirect('app_dashboard'); // ou alors 404/403 a voir 
        }
        $statut = $this->game->getStatutAndVieByIdPartie($id_partie);

        $arrayNiveau = [
            1 => 'Facile',
            2 => 'Moyen',
            3 => 'Difficile',
            4 => 'Aléatoire'
        ];
        $tableau = json_decode($sudoku[0]['tableau']);
        return $this->twig->render('game/index.html.twig', [
            'sudoku' => $tableau,
            'statut' => $statut[0],
            'niveau' => $arrayNiveau,
            'id_partie' => $id_partie
        ]);
    }

    public function insert()
    {
        if ($_POST) {
            $index = array_map('intval', explode(',', $_POST['arrayCase']['key']));
            $filteredIndex = array_filter($index, function($value) {
                return is_numeric($value) && $value >= 0 && $value <= 8;
            });
            if (count($filteredIndex) !== count($index)) {
                return json_encode(['message' => 'Erreur champs non valide 1']);
            }
            $value = intval($_POST['arrayCase']['value']);
            if (is_numeric($value) && $value < 1 && $value >10) {
                return json_encode(['message' => 'Erreur champs non valide 2']);
            }
            $id_partie = $_POST['id'];
            $sudoku = $this->sudoku->getSudokuByPartie($id_partie);
            if (empty($sudoku)) {
                return json_encode(['message' => 'Sudoku non valide 3']);
            }
            $this->sudoku->insert($index, $value, $sudoku, $id_partie);
        }
    }

    public function delete()
    {
        if ($_POST) {
            $index = array_map('intval', explode(',',$_POST['attrCase']));
            $filteredIndex = array_filter($index, function($value) {
                return is_numeric($value) && $value >= 0 && $value <= 8;
            });
            if (count($filteredIndex) !== count($index)) {
                return json_encode(['message' => 'Erreur champs non valide']);
            }
            $id_partie = intval($_POST['id']);
            $sudoku = $this->sudoku->getSudokuByPartie($id_partie);
            if (empty($sudoku)) {
                return json_encode(['message' => 'Sudoku non valide']);
            }
            $this->sudoku->delete($index, $sudoku, $id_partie);
        }
    }

    public function verif()
    {
        if ($_POST) {
            $id_partie = $_POST['id'];

            $score = $this->game->getVieById($id_partie);
            if (empty($score)) {
                return json_encode(['message' => 'Partie non autorisée']);
            }
            $score = $score[0]['vie'] -1;
            $this->game->updateScoreById($id_partie, $score);
            if ($score == 0) {
                $this->sudoku->updateStatutSudoku($id_partie, 3);
                return 'false';
            }
            $solutionSudoku = $this->sudoku->getSolutionSudokuByPartie($id_partie);
            $solutionSudoku = json_decode($solutionSudoku[0]['solution']);

            $sudoku = $this->sudoku->getSudokuByPartie($id_partie);
            if (empty($sudoku)) {
                return json_encode(['message' => 'Sudoku non valide']);
            }
            $sudoku = json_decode($sudoku[0]['tableau']);

            $arrayVerif = [];

            foreach ($sudoku as $keyLigne => $ligne) {
                foreach ($ligne as $keyCase => $case) {
                    if ($case >= 10) {
                        $array = [
                            'key' => strval($keyLigne).','. strval($keyCase),
                            'value' => ($case / 10) == $solutionSudoku[$keyLigne][$keyCase]
                        ];
                        $arrayVerif[] = $array;
                    }
                }
            }
            return json_encode($arrayVerif);
        }
    }


    public function finish()
    {
        if ($_POST) {
            $id_partie = $_POST['id'];

            $solutionSudoku = $this->sudoku->getSolutionSudokuByPartie($id_partie);
            $solutionSudoku = json_decode($solutionSudoku[0]['solution']);

            $sudoku = $this->sudoku->getSudokuByPartie($id_partie);
            if (empty($sudoku)) {
                return json_encode(['message' => 'Sudoku non valide']);
            }
            $sudoku = json_decode($sudoku[0]['tableau']);

            $arrayVerif = [];

            foreach ($sudoku as $keyLigne => $ligne) {
                foreach ($ligne as $keyCase => $case) {
                    if ($case >= 10) {
                        if (($case / 10) != $solutionSudoku[$keyLigne][$keyCase]) {
                            $array = [
                                'key' => strval($keyLigne).','. strval($keyCase),
                                'value' => false
                            ];
                            $arrayVerif[] = $array;
                        }
                    }
                }
                
            }
            
            // voir ici protection d'ajout de point
            if (empty($arrayVerif)) {
                $score = $this->sudoku->getScoreByNiveau($id_partie);
                $vie = $this->game->getStatutAndVieByIdPartie($id_partie);
                $vie = $vie[0]['vie'];
                $score = $score[0]['score'] + ($vie *2);
                $this->user->addScore($score);
                $this->game->addScore($score, $id_partie);
                $this->sudoku->updateStatutSudoku($id_partie, 2);

                return json_encode(['key' => true, 'score' => $score]);
            }
            return json_encode($arrayVerif);
        }
    }

    public function retry()
    {
        if ($_GET) {
            $id_partie = $_GET['sudoku'];

            $this->sudoku->updateStatutSudoku($id_partie, 1);
            $this->game->updateVieById($id_partie, 5);
            redirect('app_game', ['id_partie' => $id_partie]);
        }
    }
}