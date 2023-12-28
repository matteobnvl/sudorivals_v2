<?php

namespace App\Models;

class User extends Model
{
    public function checkMailAndPseudo($email, $pseudo)
    {
        $qry = "SELECT * FROM Joueur WHERE email = :email OR pseudo = :pseudo";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':email' => $email,
            ':pseudo' => $pseudo
        ]);
        
        return $stt->fetch(\PDO::FETCH_ASSOC) > 0 ? false : true;
    }

    public function verifAskResetPassword($mail) 
    {
        $qry = "SELECT token_recovery_password
                FROM Joueur
                WHERE email = :email";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':email' => htmlentities($mail)
        ]);
        $token = $stt->fetchAll(\PDO::FETCH_ASSOC);
        if ($token[0]['token_recovery_password'] !== null) {
            $dateToken = $this->checkDatePassword($mail);
            $dateDay = new \DateTime();
            $dateDay = $dateDay->format('Y-m-d H:i:s');
            $dateToken = $dateToken[0]['date_recovery_password'];
            
            if (datediff($dateDay,$dateToken) < 5000) {
                return false;
            }
        }
        return true;
    }

    public function checkDatePassword($mail) 
    {
        $qry = "SELECT date_recovery_password
                FROM Joueur
                WHERE email = :email";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':email' => $mail,
        ]);
        return $stt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function InsertTokenDate($mail, $token) 
    {
        $qry = "UPDATE Joueur
                SET token_recovery_password = :token_recovery_password, date_recovery_password = :date_recovery_password
                WHERE email = :email";
        $stt = $this->db->prepare($qry);
        $date = new \DateTime();
        $date =$date->format('Y-m-d-H-i-s');
        $stt->execute([
            ':token_recovery_password' => hash('sha256', $token),
            ':date_recovery_password' => $date,
            ':email' => $mail
        ]);
    }

    public function deleteCookie()
    {
        $qry = "UPDATE Joueur SET token_remember_me = :token_remember WHERE id_joueur = :id_joueur";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':token_remember' => null,
            ':id_joueur' => $_SESSION['id_joueur']
        ]);
    }

    public function register($post)
    {
        $qry = "INSERT INTO Joueur (pseudo, email, mot_de_passe, created_at)
                VALUES (:pseudo, :email, :mdp, NOW())";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':pseudo' => htmlentities($post['pseudo']),
            ':email' => htmlentities($post['email']),
            ':mdp' => hash(env('HASH'),$post['password'])
        ]);
        return true;
    }

    public function login($email, $mdp, $token = false) 
    {
        $qry = "SELECT * FROM Joueur
            WHERE (email = :email OR pseudo = :email) AND mot_de_passe = :mdp";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':email' => htmlentities($email),
            ':mdp' => ($token == true)? $mdp : hash(env('HASH'), $mdp)
        ]);

        $user = $stt->fetch(\PDO::FETCH_ASSOC);

        if ($stt->rowCount() > 0) {

            foreach ($user as $key => $value) {
                $_SESSION[$key] = $value;
            }
            return true;
        }
        return false;
    }

    public function UserByToken($token) 
    {
        $qry = "SELECT * 
                FROM Joueur
                WHERE token_recovery_password = :token";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':token' => hash('sha256', $token)
        ]);
        return $stt->fetchAll(\PDO::FETCH_ASSOC);
    }


    public function ResetPassword($post, $token) 
    {
        $qry = "UPDATE Joueur
                SET token_recovery_password = :token_recovery_password, 
                    date_recovery_password = :date_recovery_password, 
                    `mot_de_passe` = :pwd
                WHERE token_recovery_password = :token";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':token_recovery_password' => null,
            ':date_recovery_password' => null,
            ':pwd' => hash('sha256', $post['password']),
            ':token' => hash('sha256', $token)
        ]);
    }

    public function VerifToken($token) 
    {
        $qry = "SELECT *
                FROM Joueur
                WHERE token_recovery_password = :token";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':token' => hash('sha256', $token)
        ]);
        $user = $stt->fetchAll(\PDO::FETCH_ASSOC);
        if($stt->rowCount() > 0) {
            $dateDay = new \DateTime();
            $dateDay = $dateDay->format('Y-m-d H:i:s');
            $dateToken = $user[0]['date_recovery_password'];
            if (datediff($dateDay,$dateToken) < 5000) {
                return true;
            }
        }
        return false;
    }

    public function insertToken($token)
    {
        $qry = "UPDATE Joueur SET token_remember_me = :token_remember WHERE id_joueur = :id_joueur";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':token_remember' => $token,
            ':id_joueur' => $_SESSION['id_joueur']
        ]);
    }

    public function getListRequestFriends()
    {
        $qry = "SELECT Amis.id, Joueur.id_joueur, Joueur.pseudo
                FROM Amis
                INNER JOIN Joueur ON Joueur.id_joueur = Amis.id_amis
                WHERE Amis.id_amis_1 = :id_joueur AND statut = :statut";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':id_joueur' => $_SESSION['id_joueur'],
            ':statut' => 0
        ]);
        return $stt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getCheckDemandeAmis($id)
    {
        $qry = "SELECT *
                FROM Amis 
                WHERE id_amis = :id_joueur 
                    AND id_amis_1 = :id_demande
                    AND statut = :statut";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':id_joueur' => $_SESSION['id_joueur'],
            ':id_demande' => $id,
            ':statut' => 0
        ]);
        return empty($stt->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function acceptFriendsRequest($id)
    {
        $qry = "UPDATE Amis 
                SET statut = :statut 
                WHERE id = :id";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':id' => $id,
            ':statut' => 1
        ]);
    }

    public function deleteFriendsRequest($id)
    {
        $qry = "DELETE FROM Amis
                WHERE id = :id";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':id' => $id,
            ':statut' => 1
        ]);
    }

    public function addFriends($id_joueur)
    {
        $qry = "INSERT INTO Amis
                (id_amis, id_amis_1, statut, `date`)
                VALUES (:id_amis, :id_amis_1, :statut, :date)";
        $stt = $this->db->prepare($qry);
        $date = new \DateTime();
        $date =$date->format('Y-m-d');
        $stt->execute([
            ':id_amis' => $_SESSION['id_joueur'],
            ':id_amis_1' => $id_joueur,
            ':statut' => 0,
            ':date' => $date
        ]);
    }

    public function getJoueur($id_joueur)
    {
        $qry = "SELECT *
                FROM Joueur
                WHERE id_joueur = :id_joueur";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':id_joueur' => $id_joueur
        ]);
        return $stt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function searchUserByPseudo($pseudo)
    {
        $qry = "SELECT J.id_joueur, J.pseudo
                FROM Joueur AS J
                WHERE pseudo LIKE :pseudo 
                    AND pseudo != :pseudo_user";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':pseudo' => '%'.htmlentities($pseudo).'%',
            ':pseudo_user' => $_SESSION['pseudo']
        ]);
        return $stt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function checkMail($post) {
        $qry = "SELECT email
                FROM Joueur
                WHERE email = :email";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':email' => $post['mail']
        ]);
        if ($stt->rowCount() === 0){
            return false;
        }
        return true;
    }

    public function getAmis($id_joueur)
    {
        $qry = "SELECT pseudo, score, Amis.date
                FROM Joueur 
                JOIN Amis ON (Joueur.id_joueur = Amis.id_amis OR Joueur.id_joueur = Amis.id_amis_1) 
                WHERE (Amis.id_amis = :id_joueur OR Amis.id_amis_1 = :id_joueur) 
                AND Joueur.id_joueur <> :id_joueur AND Joueur.deleted = 0 AND Amis.statut = 1";
        $stt = $this->db->prepare($qry);
        $stt->bindParam(':id_joueur', $id_joueur, \PDO::PARAM_INT);
        $stt->execute();
        $amis = $stt->fetchAll(\PDO::FETCH_OBJ);
        return $amis;
    }

    public function getScores()
    {
        $qry = "SELECT pseudo, score FROM Joueur WHERE deleted = 0 ORDER BY score DESC LIMIT 10";
        $stt = $this->db->prepare($qry);
        $stt->execute();
        $scores = $stt->fetchAll(\PDO::FETCH_OBJ);
        return $scores;
    }

    public function update($id_joueur, $pseudo, $email)
    {
        $qry = "UPDATE Joueur SET pseudo = :pseudo, email = :email  WHERE id_joueur = :id_joueur";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':pseudo' => htmlentities($pseudo),
            ':email' => htmlentities($email),
            ':id_joueur' => $id_joueur
        ]);
        $_SESSION['pseudo'] = htmlentities($pseudo);
        $_SESSION['email'] = htmlentities($email);
        return true;
    }

    public function deleteJoueur()
    {
        $qry = "UPDATE Joueur SET
                deleted = :deleted, email = :email, pseudo = :pseudo
                WHERE id_joueur = :id_joueur";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':id_joueur' => $_SESSION['id_joueur'],
            ':deleted' => 1,
            ':email' => null,
            ':pseudo' => null
        ]);
    }

    public function getScoresWithFriends()
    {
        $qry = "SELECT j.pseudo, j.score 
                FROM Joueur j 
                INNER JOIN Amis a ON (j.id_joueur = a.id_amis) 
                WHERE (a.id_amis = :id_joueur 
                    OR a.id_amis_1 = :id_joueur 
                    OR j.id_joueur = :id_joueur)
                    AND j.deleted = 0
                GROUP BY j.pseudo, j.score
                ORDER BY score DESC";
        $stt = $this->db->prepare($qry);
        $stt->bindParam(':id_joueur', $_SESSION['id_joueur'], \PDO::PARAM_INT);
        $stt->execute();
        $amis = $stt->fetchAll(\PDO::FETCH_OBJ);
        return $amis;
    }

    public function addScore($score)
    {
        $newScore = $_SESSION['score'] + $score;
        $qry = "UPDATE Joueur
                SET score = :score
                WHERE id_joueur = :id_joueur";
        $stt = $this->db->prepare($qry);
        $stt->execute([
            ':score' => $newScore,
            ':id_joueur' => $_SESSION['id_joueur']
        ]);
        $_SESSION['score'] = $newScore;
    }
}