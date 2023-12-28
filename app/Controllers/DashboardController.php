<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\User;
use App\Models\Mail;
use App\Models\Game;

class DashboardController extends Controller
{
    private $game;
    private $user;
    private $mail;

    public function __construct()
    {
        parent::__construct();
        if (!isConnected()) {
            redirect('app_login');
        }
        $this->game = new Game();
        $this->user = new User();
        $this->mail = new Mail();
    }

    public function index()
    {
        $sudokus = $this->game->getLastFiveSudokusByUser($_SESSION['id_joueur']);
        $arrayNiveau = [
            1 => 'Facile',
            2 => 'Moyen',
            3 => 'Difficile',
            4 => 'Aléatoire'
        ];
        foreach ($sudokus as &$sudoku) {
            $sudoku['tableau'] = json_decode($sudoku['tableau']);
        }
        return $this->twig->render('dashboard/index.html.twig',[
            'sudokus' => $sudokus,
            'niveau' => $arrayNiveau,
        ]);
    }

    public function addFriends(){

        $message_valid = '';
        $listes_demandes_amis = $this->user->getListRequestFriends();
        if (isset($_GET['id'])) {
            if ($this->user->getCheckDemandeAmis($_GET['id'])) {
                $this->user->addFriends($_GET['id']);
                $amis = $this->user->getJoueur($_GET['id']);
                $this->mail->sendMailDemandeAmis($amis[0]);
                $message_valid = ' amis ajouté';
            } else {
                $message_valid = 'déjà ajouté';
            }
            redirect('add_friends',[], '?message='.$message_valid);
        }
        return $this->twig->render('friends/add_friends.html.twig',[
            'message_valid' => $message_valid,
            'liste_demande_amis' => $listes_demandes_amis
        ]);
    }

    public function search()
    {
        if ($_POST) {
            if ($_POST['data'] != '') {
                $users = $this->user->searchUserByPseudo($_POST['data']);
                return json_encode($users);
            }
        }
    }

    public function accept()
    {
        if ($_GET) {
            $this->user->acceptFriendsRequest($_GET['id']);
            $message_valid = 'demande d\'amis acceptée !';
            redirect('add_friends',[], '?message='.$message_valid);
        }
    }

    public function refuse()
    {
        if ($_GET) {
            $this->user->deleteFriendsRequest($_GET['id']);
            $message_valid = 'demande d\'amis refusée !';
            redirect('add_friends',[], '?message='.$message_valid);
        }
    }

    public function allSudoku()
    {
        if ($_POST) {
            $offset = $_POST['offset'];
            $sudokus = $this->game->getGameByJoueurLimit($offset, 5);
            return json_encode($sudokus);
        }


        return $this->twig->render('sudoku/all_sudoku.html.twig', []);
    }
}