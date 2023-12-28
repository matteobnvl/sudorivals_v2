<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\User;
use App\Models\Game;

class ProfilController extends Controller
{
    private $user;
    private $game;

    public function __construct()
    {
        parent::__construct();
        if(!isConnected()) {
            redirect('app_login');
        }
        $this->user = new User();
        $this->game = new Game();
    }

    public function index()
    {
        if ($_POST){
            if (isset($_POST['email']) && $_POST['email'] !== '' ||
                isset($_POST['pseudo']) && $_POST['pseudo'] !== '') {
                    $this->user->update($_SESSION['id_joueur'] ,$_POST['pseudo'], $_POST['email']);
            }
        }
        $amis = $this->user->getAmis($_SESSION['id_joueur']);

        $nbWin = $this->game->countPartieByStatut(2);
        $nbLose = $this->game->countPartieByStatut(3);
        $nbInProgress = $this->game->countPartieByStatut(1);

        $scores = $this->user->getScores();
        $scoresAmis = $this->user->getScoresWithFriends();
        
        return $this->twig->render('profil/index.html.twig', [
            'amis' => $amis,
            'nbWin' => $nbWin[0]['nbgame'],
            'nbLose' => $nbLose[0]['nbgame'],
            'nbInProgress' => $nbInProgress[0]['nbgame'],
            'scores' => $scores,
            'scores_amis' => $scoresAmis
        ]);
    }

    public function delete()
    {
        $this->user->deleteJoueur();
        redirect('app_logout');
    }


}