<?php 

namespace App\Models;

class Multijoueur extends Model
{
    public function checkUserHasNotDuel()
    {
        $qry = "SELECT *
                FROM Multijoueur
                LEFT JOIN Duel ON Multijoueur.id_multi = Duel.id_multi
                WHERE Duel.id_joueur = :id_joueur AND Multijoueur.complet = :complet";
        $stt = $this->db->prepare($qry);
        $stt-> execute([
            ':id_joueur' => $_SESSION['id_joueur'],
            ':complet' => 0
        ]);

        $duel = $stt->fetchAll(\PDO::FETCH_ASSOC);

        return (empty($duel)) ? true : false;
    }

    public function checkDuelInProgress()
    {
        $qry = "SELECT Multijoueur.id_multi
                FROM Multijoueur
                LEFT JOIN Duel ON Multijoueur.id_multi = Duel.id_multi
                WHERE complet = :complet AND termine = :termine AND Duel.id_joueur != :id_joueur
                LIMIT 1";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':complet' => 0,
            ':termine' => 0,
            ':id_joueur' => $_SESSION['id_joueur']
        ]);
        $duel = $stt->fetchAll(\PDO::FETCH_ASSOC);
        return (empty($duel)) ? true : false;
    }

    public function create()
    {
        $qry = "INSERT INTO Multijoueur
                (date_start, complet, termine)
                VALUES (:date_start, :complet, :termine)";
        $stt = $this->db->prepare($qry);
        $date = date('Y-m-d H:i:s');
        $stt->execute([
            ':date_start' => $date,
            ':complet' => 0,
            ':termine' => 0
        ]);

        return $this->db->lastInsertId();
    }


    public function createDuel($id_multi)
    {
        $qry = "INSERT INTO Duel
                (id_joueur, id_multi)
                VALUES (:id_joueur, :id_multi)";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':id_joueur' => $_SESSION['id_joueur'],
            ':id_multi' => $id_multi
        ]);
    }

    public function updateAnnuler($id_multi)
    {
        $qry = "UPDATE Multijoueur
                SET complet = :complet
                WHERE id_multi = :id_multi";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':complet' => 1,
            ':id_multi' => $id_multi
        ]);
    }

    public function getIdMultiByJoueur()
    {
        $qry = "SELECT Multijoueur.id_multi
                FROM Multijoueur
                LEFT JOIN Duel ON Multijoueur.id_multi = Duel.id_multi
                WHERE Duel.id_joueur = :id_joueur AND Multijoueur.complet = :complet";
        $stt = $this->db->prepare($qry);
        $stt-> execute([
            ':id_joueur' => $_SESSION['id_joueur'],
            ':complet' => 0
        ]);

        return $stt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function updateComplet($id_multi)
    {
        $qry = "UPDATE Multijoueur
                SET complet = :complet
                WHERE id_multi = :id_multi";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':complet' => 1,
            ':id_multi' => $id_multi
        ]);
    }

    public function getIdDuelInProgress()
    {
        $qry = "SELECT Multijoueur.id_multi
                FROM Multijoueur
                LEFT JOIN Duel ON Multijoueur.id_multi = Duel.id_multi
                WHERE complet = :complet AND termine = :termine AND Duel.id_joueur != :id_joueur
                LIMIT 1";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':complet' => 0,
            ':termine' => 0,
            ':id_joueur' => $_SESSION['id_joueur']
        ]);
        return $stt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function checkDuelIsComplete($id_multi)
    {
        $qry = "SELECT *
                FROM Multijoueur
                WHERE id_multi = :id_multi";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':id_multi' => $id_multi
        ]);
        return $stt->fetchAll(\PDO::FETCH_ASSOC);
    }


    public function getAdversaireDuelByIdMulti($id_multi)
    {
        $qry = "SELECT Joueur.pseudo, Duel.vie, Duel.id_sudoku
                FROM Duel
                INNER JOIN Joueur ON Joueur.id_joueur = Duel.id_joueur
                WHERE id_multi = :id_multi AND Duel.id_joueur != :id_joueur";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':id_multi' => $id_multi,
            ':id_joueur' => $_SESSION['id_joueur']
        ]);
        return $stt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function updateSudokuDuel($id_sudoku, $id_multi)
    {
        $qry = "UPDATE Duel
                SET id_sudoku = :id_sudoku
                WHERE id_multi = :id_multi AND id_joueur = :id_joueur";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':id_sudoku' => $id_sudoku,
            ':id_multi' => $id_multi,
            ':id_joueur' => $_SESSION['id_joueur']
        ]);
    }

    public function getVieAndStatutByIdDuel($id_multi)
    {
        $qry = "SELECT vie, statut, score
                FROM Duel
                WHERE id_multi = :id_multi AND id_joueur = :id_joueur";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':id_multi' => (int) $id_multi,
            ':id_joueur' => $_SESSION['id_joueur']
        ]);
        return $stt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getSudokuAdverse($id_sudoku)
    {
        $qry = "SELECT tableau
                FROM Sudoku
                WHERE id_sudoku = :id_sudoku";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':id_sudoku' => $id_sudoku
        ]);
        return $stt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getVieAdverseByIdMulti($id_multi)
    {
        $qry = "SELECT vie
                FROM Duel
                WHERE id_multi = :id_multi AND id_joueur != :id_joueur";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':id_multi' => $id_multi,
            ':id_joueur' => $_SESSION['id_joueur']
        ]);
        return $stt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function checkAdverseIsVainqueur($id_multi)
    {
        $qry = "SELECT vainqueur
                FROM Duel
                WHERE id_multi = :id_multi AND id_joueur != :id_joueur";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':id_multi' => $id_multi,
            ':id_joueur' => $_SESSION['id_joueur']
        ]);
        $vainqueur = $stt->fetchAll(\PDO::FETCH_ASSOC);
        return ($vainqueur[0]['vainqueur'] == 1) ? 'true' : 'false';
    }

    public function updateVieById($id_multi, $vie)
    {
        $qry = "UPDATE Duel
                SET vie = :vie
                WHERE id_multi = :id_multi AND id_joueur = :id_joueur";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':vie' => $vie,
            ':id_multi' => $id_multi,
            ':id_joueur' => $_SESSION['id_joueur']
        ]);
    }

    public function updateStatutDuelById($id_multi, $statut)
    {
        $qry = "UPDATE Duel
                SET statut = :statut
                WHERE id_multi = :id_multi AND id_joueur = :id_joueur";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':statut' => $statut,
            ':id_multi' => $id_multi,
            ':id_joueur' => $_SESSION['id_joueur']
        ]);
    }

    public function UpdateJoueurVainqueurDuelById($id_multi)
    {
        $qry = "UPDATE Duel
                SET vainqueur = :vainqueur
                WHERE id_multi = :id_multi AND id_joueur = :id_joueur";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':vainqueur' => 1,
            ':id_multi' => $id_multi,
            ':id_joueur' => $_SESSION['id_joueur']
        ]);
    }

    public function updateScorePartie($id_multi)
    {
        $qry = "UPDATE Duel
                SET score = :score
                WHERE id_multi = :id_multi AND id_joueur = :id_joueur";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':score' => 50,
            ':id_multi' => $id_multi,
            ':id_joueur' => $_SESSION['id_joueur']
        ]);
    }

    public function isDuelByIdJoueur($id_duel, $id_joueur)
    {
        $qry = "SELECT * 
                FROM Duel 
                WHERE id_multi = :id_duel 
                    AND id_joueur = :id_joueur";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':id_duel' => $id_duel,
            ':id_joueur' => $id_joueur
        ]);
        $result = $stt->fetchAll(\PDO::FETCH_ASSOC);
        return !empty($result);
    }

    public function notSudokuInDuel($id_duel)
    {
        $qry = "SELECT *
                FROM Duel
                WHERE id_multi = :id_duel AND id_sudoku is not NULL";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':id_duel' => $id_duel
        ]);
        $result = $stt->fetchAll(\PDO::FETCH_ASSOC);
        return empty($result);
    }

    public function isCorrectIdSudoku($id_duel, $id_sudoku)
    {
        $qry = "SELECT *
                FROM Duel
                WHERE id_multi = :id_duel 
                    AND id_sudoku = :id_sudoku
                    AND id_joueur = :id_joueur";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':id_duel' => $id_duel,
            ':id_sudoku' => $id_sudoku,
            ':id_joueur' => $_SESSION['id_joueur']
        ]);
        $result = $stt->fetchAll(\PDO::FETCH_ASSOC);
        return !empty($result);
    }
}