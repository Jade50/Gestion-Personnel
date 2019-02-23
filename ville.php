<?php

    // INSERSION DE LA BASE DE DONNEES
    require_once('bdd.php');

    //---------------------------------------------------------------------------------
    // -------------------------REQUETE INFORMATIONS VILLE-----------------------------
    //---------------------------------------------------------------------------------
    try {

        // Je sélectionne tous les éléments de ma table ville
        $req = $bdd->prepare('SELECT * FROM `ville` WHERE `v_id` = ?');

         // J'exécute en passant l'id de la ville récupéré dans l'url
        if (($req->execute([$_GET['ville']])) !== false) {
            
            if (($res = $req->fetch(PDO::FETCH_ASSOC)) !== false) {
                
                // Puis je stocke les informations dont j'ai besoin dans une variable
                $villeNom = $res['v_nom'];
                $villeSuperficie = $res['v_superficie'];
            }
        }

    } catch(PDOException $e){
    
        die($e->getMessage());        
    }   


    //---------------------------------------------------------------------------------
    // --------------------REQUETE INFORMATIONS VILLES ATTENANTES----------------------
    //---------------------------------------------------------------------------------

    // Je sélectionne tous les élements de ma table `ville` en faisant une jointure avec ma table `tunnel`
    $req = $bdd->prepare('SELECT * FROM `ville`
                          JOIN `tunnel` ON `ville`.`v_id` = `tunnel`.`t_villedepart_fk`
                          WHERE `v_id` = ?');

     // J'exécute en passant l'id de la ville récupéré dans l'url
    if (($req->execute([$_GET['ville']])) !== false) {

        // Pour tous les résultats parcourus dans ma boucle (si il y a plusieurs villes attenantes) je stocke dans un tableau l'id des villes d'arrivées, donc des villes attenantes 
        // Puis je fais la même chose avec la progression du tunnel
        while (($res = $req->fetch(PDO::FETCH_ASSOC)) !== false) {
            // $villeId[] = $res['t_villedepart_fk'];
            $villeId[] = $res['t_villearrivee_fk'];
            $tunnelProg[] = $res['t_progres'];
        }
        
        // Je parcours mon tableau qui contient les id des villes attenantes
        foreach ($villeId as $key => $value) {
        
            // Je fais une nouvelle requpete pour récupérer le nom des villes correspondantes aux id
            $req = $bdd->prepare('SELECT `v_nom`, `v_id` FROM `ville` WHERE `v_id` = ?');

            // J'exécute en passant chaque clef de mon tableau si il en contient plusieurs
            $req->execute([$villeId[$key]]);
            while (($res = $req->fetch(PDO::FETCH_ASSOC)) !== false) {   
                
                // Puis je stocke le résultat des noms dans un nouveau tableau
                $villes[] = $res['v_nom'];   
            }
        }
        
        // foreach ($villes as $key => $value) {
        //     if ($value == $villeNom) {
        //         unset($villes[$key]);
        //     }
        // }

        // foreach ($villeId as $key => $value) {
        //     if ($value == $_GET['ville']) {
        //         unset($villeId[$key]);
        //     }
        // }
        
        // $villeId = array_values($villeId);
        // $villes = array_values($villes);
    }        
    


?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">

    <title>Page Ville</title>

    <style>
        h1, h2 {
            text-align: center;
        }
        div {
            margin: auto;
            text-align: center;
        }
        span {
            color: blue;
        }
        ul li {
            list-style-type: none;
        }
    </style>
  </head>
  <body>
    <h1 class="mt-5">Page Ville</h1>

   <!-- J'affiche le nom de la ville récupéré plus haut grace à mes requêtes -->
    <h2 class="mt-3">Bienvenue dans la ville de <span><?= $villeNom;?></span></h2>

    <div class="alert alert-secondary col-md-8 mt-5">

         <!-- J'affiche le nom de la superficie de la ville récupéré plus haut grace à mes requêtes -->
        <p>Superficie de la ville :<span> <?= $villeSuperficie.' km²'; ?></span></p>

        <p>
            <ul>  Liste des nains issus de cette ville : 
                <?php
                    try {
                    //-----------------------------------------------------------------------------
                    // ---------------------REQUETE NAINS ISSUS DE LA VILLE------------------------
                    //-----------------------------------------------------------------------------

                        // Je sélectionne les éléments de ma table `ville` puis je fais une jointure avec la table nain pour récupérer le nombre de nains issus de cette ville
                        $req = $bdd->prepare('SELECT * FROM `ville`
                                            JOIN `nain` ON `ville`.`v_id` = `nain`.`n_ville_fk`
                                            WHERE `v_id` = ?');

                         // J'exécute en passant l'id de la ville récupéré dans l'url
                        if (($req->execute([$_GET['ville']])) !== false) {                               
                            // Puis je parcours dans ma boucle tous mes résultats 
                            while (($res = $req->fetch(PDO::FETCH_ASSOC)) !== false) {
                                ?>
                                    <!-- Et j'affiche tous les nains issus de la ville -->
                                    <li><a href="<?= 'nain.php?nain='.$res['n_id'] ?>"><?= $res['n_nom']; ?></a></li>
                                <?php           
                            }
                        }               
                    } catch(PDOException $e){                       
                        die($e->getMessage());        
                    }   
                ?>

            </ul>

            <ul> Liste des Tavernes issus de cette ville : 
                <?php
                    //-----------------------------------------------------------------------------
                    // -------------------REQUETE TAVERNES ISSUS DE LA VILLE-----------------------
                    //-----------------------------------------------------------------------------
                
                    try {
                         // Je sélectionne les éléments de ma table `ville` puis je fais une jointure avec la table taverne pour récupérer le nombre de tavernes issues de cette ville                   
                        $req = $bdd->prepare('SELECT * FROM `ville`
                                            JOIN `taverne` ON `ville`.`v_id` = `taverne`.`t_ville_fk`
                                            WHERE `v_id` = ?');

                        // J'exécute en passant l'id de la ville récupéré dans l'url
                        if (($req->execute([$_GET['ville']])) !== false) {                               
                            // Puis je parcours dans ma boucle tous mes résultats 
                            while (($res = $req->fetch(PDO::FETCH_ASSOC)) !== false) {
                                ?>
                                    <!-- Et j'affiche tous les nains issus de la ville -->
                                    <li><a href="<?= 'taverne.php?taverne='.$res['t_id'] ?>"><?= $res['t_nom']; ?></a></li>
                                <?php           
                            }
                        }               
                    } catch(PDOException $e){                       
                        die($e->getMessage());        
                    }   
                ?>
            </ul>

            <p> Ville(s) correspondante(s) via le tunnel : 
                <?php
                    //-----------------------------------------------------------------------------
                    // ----------------------AFFICHAGE VILLES ATTENANTES---------------------------
                    //-----------------------------------------------------------------------------

                    // Je parcours mon tableau $villes qui vontient les villes attenantes (information que j'ai récupérée plus haut grâce à mes requêtes. Pour pouvoir afficher toutes les villes attenantes si il y en a plusieurs
                    foreach ($villes as $key => $value) {
                        // Puis je place dans mon lien l'id de la ville récupéré dans mon tableau $villeId
                        echo '<span><a href="ville.php?ville='.$villeId[$key].'">'.$value.'</a></span> ';                    
                    }

                    // Je parcours également mon tableau $tunnelProg pour afficher la progression des tunnels attenants 
                    foreach ($tunnelProg as $key => $value) {
                        if ($value == 100) {
                            echo 'Tunnel Ouvert / ';
                        } else {
                            echo 'Tunnel en construction à '.$value.' % / ';
                        }
                    }

                ?>
            </p>
        </p>

    </div>
    

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
  </body>
</html>