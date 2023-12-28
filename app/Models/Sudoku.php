<?php

namespace App\Models;

class Sudoku extends Model
{
    public function generateSudoku($niveau = 'Easy')
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, env('API_SUDOKU'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        $result = curl_exec($curl);
        curl_close($curl);
        $sudoku = json_decode($result);            
        return $sudoku;
    }

    public function generateSolutionSudoku($grid)
    {
        $grid = self::solveSudoku(json_decode($grid)->{'board'});
        return json_encode($grid);
    }

    public function createSudoku($tableau, $solution, $id_partie = null)
    {
        $qry = "INSERT INTO Sudoku (tableau, solution, id_partie)
                VALUES (:tableau, :solution, :id_partie)";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':tableau' => $tableau,
            ':solution' => $solution,
            ':id_partie' => $id_partie
        ]);
        return $this->db->lastInsertId();
    }

    public function getSudokuByPartie($id_partie)
    {
        $qry = "SELECT tableau FROM Sudoku as s
                INNER JOIN Partie as p ON p.id_partie = s.id_partie
                WHERE s.id_partie = :id_partie AND p.id_joueur = :id_joueur";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':id_partie' => $id_partie,
            'id_joueur' => $_SESSION['id_joueur']
        ]);
        return $stt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getAllSudokuJoueur($id_joueur)
    {
        $qry = "SELECT Partie.id_partie, Partie.statut, Niveau.difficulte 
                FROM Partie
                INNER JOIN Niveau ON Niveau.id_niveau = Partie.id_niveau
                WHERE id_joueur = :id_joueur";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':id_joueur' => $id_joueur
        ]);
        return $stt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findEmpty($grid, &$row, &$col) {
        for ($row = 0; $row < 9; $row++) {
            for ($col = 0; $col < 9; $col++) {
                if ($grid[$row][$col] == 0) {
                    return true;
                }
            }
        }
        return false;
    }
    
    public function usedInRow($grid, $row, $num) {
        for ($col = 0; $col < 9; $col++) {
            if ($grid[$row][$col] == $num) {
                return true;
            }
        }
        return false;
    }
    
    public function usedInCol($grid, $col, $num) {
        for ($row = 0; $row < 9; $row++) {
            if ($grid[$row][$col] == $num) {
                return true;
            }
        }
        return false;
    }
    
    public function usedInBox($grid, $boxStartRow, $boxStartCol, $num) {
        for ($row = 0; $row < 3; $row++) {
            for ($col = 0; $col < 3; $col++) {
                if ($grid[$row + $boxStartRow][$col + $boxStartCol] == $num) {
                    return true;
                }
            }
        }
        return false;
    }
    
    public function isSafe($grid, $row, $col, $num) {
        return !self::usedInRow($grid, $row, $num) &&
               !self::usedInCol($grid, $col, $num) &&
               !self::usedInBox($grid, $row - ($row % 3), $col - ($col % 3), $num);
    }

    public function findUnassignedLocation($grid) {
        for ($row = 0; $row < 9; $row++) {
            for ($col = 0; $col < 9; $col++) {
                if ($grid[$row][$col] === 0) {
                    return [$row, $col];
                }
            }
        }
        return null;
    }
    
    public function solveSudoku(&$grid) {
        $row = 0;
        $col = 0;
    
        if (!self::findEmpty($grid, $row, $col)) {
            return $grid;
        }
    
        for ($num = 1; $num <= 9; $num++) {
            if (self::isSafe($grid, $row, $col, $num)) {
                $grid[$row][$col] = $num;
                if (self::solveSudoku($grid)) {
                    return $grid;
                }
                $grid[$row][$col] = 0;
            }
        }
    
        return false;
    }

    public function insert($index, $value, $sudoku, $id_partie)
    {
        $sudoku = json_decode($sudoku[0]['tableau']);
        $sudoku[$index[0]][$index[1]] = $value * 10;
        $sudoku = json_encode($sudoku);
        $qry = "UPDATE Sudoku
                SET tableau = :tableau
                WHERE id_partie = :id_partie";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':tableau' => $sudoku,
            ':id_partie' => $id_partie
        ]);
    }

    public function delete($index, $sudoku, $id_partie)
    {
        $sudoku = json_decode($sudoku[0]['tableau']);
        $sudoku[$index[0]][$index[1]] = 0;
        $sudoku = json_encode($sudoku);

        $qry = "UPDATE Sudoku
                SET tableau = :tableau
                WHERE id_partie = :id_partie";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':tableau' => $sudoku,
            ':id_partie' => $id_partie
        ]);
    }

    public function getSolutionSudokuByPartie($id)
    {
        $qry = "SELECT solution
                FROM Sudoku
                INNER JOIN Partie ON Partie.id_partie = Sudoku.id_partie
                WHERE Sudoku.id_partie = :id_partie AND Partie.id_joueur = :id_joueur";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':id_partie' => $id,
            ':id_joueur' => $_SESSION['id_joueur']
        ]);

        return $stt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getScoreByNiveau($id)
    {
        $qry = "SELECT Niveau.score
                FROM Partie
                INNER JOIN Niveau ON Niveau.id_niveau = Partie.id_niveau
                WHERE Partie.id_partie = :id_partie";
            
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':id_partie' => $id
        ]);

        return $stt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function updateStatutSudoku($id_partie, $statut)
    {
        $qry = "UPDATE Partie
                SET statut = :statut
                WHERE id_partie = :id_partie";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':statut' => $statut,
            'id_partie' => $id_partie
        ]);
    }

    public function getTableauByIdSudoku($id_sudoku)
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

    public function insertMulti($index, $value, $sudoku, $id_sudoku)
    {
        $sudoku = json_decode($sudoku[0]['tableau']);
        $sudoku[$index[0]][$index[1]] = $value * 10;
        $sudoku = json_encode($sudoku);

        $qry = "UPDATE Sudoku
                SET tableau = :tableau
                WHERE id_sudoku = :id_sudoku";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':tableau' => $sudoku,
            ':id_sudoku' => $id_sudoku
        ]);
    }

    public function deleteMulti($index, $sudoku, $id_sudoku)
    {
        $sudoku = json_decode($sudoku[0]['tableau']);
        $sudoku[$index[0]][$index[1]] = 0;
        $sudoku = json_encode($sudoku);

        $qry = "UPDATE Sudoku
                SET tableau = :tableau
                WHERE id_sudoku = :id_sudoku";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':tableau' => $sudoku,
            ':id_sudoku' => $id_sudoku
        ]);
    }

    public function getSudokuById($id_sudoku)
    {
        $qry = "SELECT tableau FROM Sudoku
                WHERE id_sudoku = :id_sudoku";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':id_sudoku' => $id_sudoku
        ]);
        return $stt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getSolutionSudokuByIdSudoku($id_sudoku)
    {
        $qry = "SELECT solution
                FROM Sudoku
                WHERE id_sudoku = :id_sudoku";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':id_sudoku' => $id_sudoku
        ]);

        return $stt->fetchAll(\PDO::FETCH_ASSOC);
    }
}