<?php

    // INSERSION DE LA BASE DE DONNEES
    require_once('bdd.php');

    //---------------------------------------------------------------------------------
    // ------------------------------FONCTION VILLE NOM--------------------------------
    //---------------------------------------------------------------------------------

    // prend en paramètres la base de données et l'id de la ville demandée
    function villeTunnel(PDO $bdd, int $villeId) : string {
        try {
        
             // Je sélectionne le nom de la ville qui aura l'id passé en paramètres
            $req = $bdd->prepare('SELECT * FROM `ville` WHERE `v_id` = ?');
    
            if (($req->execute([$villeId])) !== false) {
                
                // Je stocke le résultat dans une variable $ville
                if ($res = $req->fetch(PDO::FETCH_ASSOC)) {
                    $ville = $res['v_nom'];
                }
            }
    
        } catch(PDOException $e){
        
            die($e->getMessage());        
        }
        
        // Je retourne le nom de la ville demandée
        return $ville;
    }



    //---------------------------------------------------------------------------------
    // -------------------REQUETE NOMBRE DE NAINS DANS LE GROUPE-----------------------
    //---------------------------------------------------------------------------------

    try {

        // Je sélectionne tous les éléments de ma table `groupe` en faisant une jointure avec ma table `nain` 
        $req = $bdd->prepare('SELECT * FROM `groupe` 
                              JOIN `nain` ON `groupe`.`g_id` = `nain`.`n_groupe_fk`
                              WHERE `g_id` = ?');
    
        // J'exécute en passant l'id du groupe que j'ai récupéré dans l'url
        if (($req->execute([$_GET['groupe']])) !== false) {
               
            // Puis je stocke dans une variable le résultat de la méthode rowcount() sur ma requpête pour récupérer le nombre du nains que compose le groupe
            $nbNains = $req->rowcount();
        }

    } catch(PDOException $e){
        
        die($e->getMessage());        
    }

    //---------------------------------------------------------------------------------
    // --------------------CALCUL NOMBRE DE NAINS DANS LA TAVERNE----------------------
    //---------------------------------------------------------------------------------


    // try {

    //     $reqIdTaverne = $bdd->prepare('SELECT * FROM `taverne`');

    //     if (($reqIdTaverne->execute()) !== false) {
    //         while(($resIdTaverne = $reqIdTaverne->fetch(PDO::FETCH_ASSOC))) {
    //             $tavId[] = $resIdTaverne['t_id'];
    //         }
    //     }

    //     foreach ($tavId as  $value) {
    //         $req = $bdd->prepare('SELECT * FROM `taverne`
    //         LEFT JOIN `groupe` ON `taverne`.`t_id` = `groupe`.`g_taverne_fk`
    //         LEFT JOIN `nain` ON `groupe`.`g_id` = `nain`.`n_groupe_fk`
    //         WHERE `t_id` = ?');
        
    //         if (($req->execute([$tavId[$value]])) !== false) {

    //         $nbNains += $req->rowcount();    
    //         }
    //     }

    //     echo $nbNains;

    //     echo '<pre>';
    //     var_dump($tavId);
    //     echo '</pre>';


    // } catch(PDOException $e){
    
    //     die($e->getMessage());        
    // }   

    //---------------------------------------------------------------------------------
    // ---------------REQUETE INFORMATONS TAVERNE ET HORAIRES DE TRAVAIL---------------
    //---------------------------------------------------------------------------------
    
    try {

        // Je sélectionne tous les éléments de" ma table `groupe` en faisant une jointure avec ma table `taverne`
        $req = $bdd->prepare('SELECT * FROM `groupe`
                              LEFT JOIN `taverne` ON `taverne`.`t_id` = `groupe`.`g_taverne_fk`
                              WHERE `g_id` = ?');

        // J'exécute en passant l'id du groupe que j'ai récupéré dans l'url
        if (($req->execute([$_GET['groupe']])) !== false) {
            
            if (($res = $req->fetch(PDO::FETCH_ASSOC)) !== false) {
                
                // Puis je stocke dans des variables toutes les informations dont j'ai besoin pour l'affichage
                $taverneId = $res['t_id'];
                $groupe = $res['g_id'];
                $groupeDebutTravail = $res['g_debuttravail'];
                $groupeFinTravail = $res['g_fintravail'];
                $groupeTaverne = $res['t_nom'];
            }
        }

    } catch(PDOException $e){
    
        die($e->getMessage());        
    }   


    //---------------------------------------------------------------------------------
    // --------------------------REQUETE INFORMATONS TUNNEL----------------------------
    //---------------------------------------------------------------------------------

    try {

        // Je sélectionne tous les éléments de" ma table `groupe` en faisant une jointure avec ma table `tunnel`
        $req = $bdd->prepare('SELECT * FROM `groupe`
                              LEFT JOIN `tunnel` ON `tunnel`.`t_id` = `groupe`.`g_tunnel_fk`
                              WHERE `g_id` = ?');

        // J'exécute en passant l'id du groupe que j'ai récupéré dans l'url
        if (($req->execute([$_GET['groupe']])) !== false) {
            
            if (($res = $req->fetch(PDO::FETCH_ASSOC)) !== false) {

                 // Puis je stocke dans des variables toutes les informations dont j'ai besoin pour l'affichage
                $tunnelNum = $res['t_id'];
                $tunnelProg = $res['t_progres'];
                $villeDepartId = $res['t_villedepart_fk'];
                $villeArriveeId = $res['t_villearrivee_fk'];

                // Avec cette requête je ne récupère que l'id de la ville de départ du tunnel et de la ville d'arrivée, via ma table `tunnel` je fais donc appel à ma fonction villeTunnel() pour récupérer leur nom et pouvoir les afficher 
                $groupeVilleDepart = villeTunnel($bdd, $res['t_villedepart_fk']);
                $groupeVilleArrivee = villeTunnel($bdd, $res['t_villearrivee_fk']);

                // Si la progression de mon tunnel est différent de '100 %' j'affiche le pourcentage de progression, sinon je spécifie que celui-ci est en entretien
                if ($res['t_progres'] == 100) {
                    $tunnelProg = '<span> Tunnel en entretient </span>';
                } else {
                    $tunnelProg = 'Tunnel creusé à <span>'.$res['t_progres'].' %</span>';
                }
            }
        }

    } catch(PDOException $e){
    
        die($e->getMessage());        
    }   


    //---------------------------------------------------------------------------------
    // -----------------------FONCTION MODIFICATIONS DU GROUPE-------------------------
    //---------------------------------------------------------------------------------

    // Fonction de traitement du formulaire de modification des informations du groupe
    function updateGroupe($bdd, $groupe, $horaireDebut, $horaireFin, $tunnel, $taverne) {

            try {
    
                // Je fais une requpete préparée en passant en UPDATE tous les champs de ma table `groupe` que je veux modifier 
                $req = $bdd->prepare('UPDATE `groupe` SET `g_debuttravail` = ?, `g_fintravail` = ?, `g_tunnel_fk` = ?, `g_taverne_fk` = ? WHERE `g_id` = ?');
    
                // Je passe en paramètres de mon exécution toutes mes variables $_POST et ma variable $_GET passée en url, qui correspond à l'id du groupe ciblé
                if (($req->execute([$horaireDebut, $horaireFin, $tunnel, $taverne, $groupe])) !== false) {         
                    // je stocke le résultat de ma requete dans une variable pour informer l'utilisateur du résultat du traitement           
                    $msg = 'Les informations du groupe ont bien été changées';                
                } else {
                    $msg = 'Echec de la modification des données';
                }
    
            } catch(PDOException $e){
        
                die($e->getMessage());        
            }   
            return $msg;
    }

    //---------------------------------------------------------------------------------
    // ------------------TRAITEMENT FORMULAIRE MODIFICATIONS DU GROUPE-----------------
    //---------------------------------------------------------------------------------

    // Si tous mes champs de formulaire existent et qu'ils ne sont pas vides
    if (isset($_POST['new_horaire_debut']) && isset($_POST['new_horaire_fin']) && isset($_POST['new_tunnel']) && isset($_POST['new_taverne'])) {

        if (!empty($_POST['new_horaire_debut']) && !empty($_POST['new_horaire_fin']) && !empty($_POST['new_tunnel']) && !empty($_POST['new_taverne'])) {

            // Je stocke dans une variable le résultat de ma fonction de traitement de formulaire en passant en paramètre ma base de données, l'id du groupe de nains récupéré dans l'url puis toutes mes variables de formulaire
            $msg = updateGroupe($bdd, $_GET['groupe'], $_POST['new_horaire_debut'], $_POST['new_horaire_fin'], $_POST['new_tunnel'],  $_POST['new_taverne']);
        }
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

    <title>Page Groupe</title>

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
        form {
            margin: auto;
        }
    </style>
  </head>
  <body>

    <h1 class="mt-5">Page Groupe</h1>

    <!-- J'affiche le numéro récupéré plus haut grace à mes requêtes -->
    <h2 class="mt-3">Bienvenue sur la page du groupe<span> n°  <?= $groupe; ?> </span></h2>

    <!-- Si l'utilisateur a demandé à effectuer des changements sur le groupe, j'affiche la variable $msg dans une div pour informer l'utilisateur du résultat du traitement -->
    <?php 
        if (isset($msg)) {
            ?>
                <div class="alert alert-primary col-md-8 mt-5"><?= $msg; ?></div>
            <?php
        }
    ?>

    <div class="alert alert-secondary col-md-8 mt-5">

        <ul> Liste des nains du groupe :
            <?php
                try {
                    // Je sélectione les élements de ma table `groupe` en faisant une jointure avec ma table `nain` pour récupéré tous les nains qui composent le groupe
                    $req = $bdd->prepare('SELECT * FROM `groupe`
                                          JOIN `nain` ON `nain`.`n_groupe_fk` = `groupe`.`g_id`
                                          WHERE `g_id` = ?');
            
                    // J'exécute en passant l'id du groupe que j'ai récupéré dans l'url
                    if (($req->execute([$_GET['groupe']])) !== false) {
                        
                        // Puis avec une boucle j'affiche dans des <li> et des <a> le nom de tous les nains référents à ce groupe, en passant dans mes balises <a> l'id de chaque nain pour accéder à la bonne page 
                        while (($res = $req->fetch(PDO::FETCH_ASSOC)) !== false) {
                            ?>
                                <li><a href="<?= 'nain.php?nain='.$res['n_id'] ?>"><?= $res['n_nom']; ?></a></li>
                            <?php
                        }
                    }
            
                } catch(PDOException $e){
                
                    die($e->getMessage());        
                }   
            ?>

        </ul>

        <!-- J'affiche la taverne assignée au groupe, récupérée plus haut grace à mes requêtes -->
        <a href="taverne.php?taverne=<?= $taverneId; ?>"><p><?= $groupeTaverne; ?></p></a>

         <!-- J'affiche les horaires du groupe et les villes attenantes au tunnel dans lequel ils travaillent en passant les id des villes dans mes liens pour accéder aux pages correspondantes au click -->
        <p>Ce groupe creuse de <span><?= $groupeDebutTravail; ?></span> à <span><?= $groupeFinTravail; ?></span> de <span><a href="ville.php?ville=<?= $villeDepartId; ?>"><?= $groupeVilleDepart; ?></a></span> à <span><a href="ville.php?ville=<?= $villeArriveeId; ?>"><?= $groupeVilleArrivee; ?></a></span></p>

        <!-- J'affiche la progression du tunnel -->
        <p><?= $tunnelProg; ?></p>


        <form action="" method="POST" class="form-group mt-5 col-md-8">

        <!--------------------------------------------------------------------------------->
        <!------------------SELECT DE MODIFICATION DES HOREIRES DE DEBUT------------------->
        <!--------------------------------------------------------------------------------->

            <label for="new_horaire_debut">Modifier l'horaire de commencement :</label>

            <!-- Je fais un select pour afficher toutes les horaires-->
            <select class="form-control" name="new_horaire_debut" id="new_horaire_debut">
                <?php
                // Je sélectionne tous les éléments de ma table `groupe`
                    $req = $bdd->prepare('SELECT * FROM `groupe`');
                    if (($req->execute()) !== false) {
                        while ($res = $req->fetch(PDO::FETCH_ASSOC)) {
                                ?>

                                <!-- Puis pour chaque résultat j'affiche une option avec le numéro du groupe, en passant également l'id dans la 'value=""''" de la balise pour pouvoir le traiter avec un $_POST -->
                                <option
                                    <?php
                                        // Je fais une condition pour vérifier pour chaque horaire si elle correspond à celle de base du groupe, et si c'est le cas je place un 'selected' dans ma balise <option> pour placer ces horaires par défaut à l'affichage
                                        if ($groupeDebutTravail == $res['g_debuttravail']) {
                                            echo 'selected="selected"';
                                        }
                                    ?>
                                 value="<?= $res['g_debuttravail']; ?>"><?= $res['g_debuttravail']; ?></option>
                            <?php  
                            
                      
                        }
                    }
                ?>
            </select>

        <!--------------------------------------------------------------------------------->
        <!-------------------SELECT DE MODIFICATION DES HORAIRES DE FIN-------------------->
        <!--------------------------------------------------------------------------------->

            <label for="new_horaire_fin">Modifier l'horaire de fin :</label>

            <select class="form-control" name="new_horaire_fin" id="new_horaire_fin">
                <?php
                    $req = $bdd->prepare('SELECT * FROM `groupe`');
                    if (($req->execute()) !== false) {
                        while ($res = $req->fetch(PDO::FETCH_ASSOC)) {
                                ?>
                                <option
                                <?php
                                    if ($groupeFinTravail == $res['g_fintravail']) {
                                        echo 'selected="selected"';
                                    }
                                ?>
                                value="<?= $res['g_fintravail']; ?>"><?= $res['g_fintravail']; ?></option>
                            <?php  
                            
                    
                        }
                    }
                ?>
            </select>
            
        <!--------------------------------------------------------------------------------->
        <!--------------------------SELECT DE MODIFICATION DU TUNNEL----------------------->
        <!--------------------------------------------------------------------------------->
            <label for="new_tunnel">Modifier le tunnel :</label>

            <select class="form-control" name="new_tunnel" id="new_tunnel">
                <?php
                    $req = $bdd->prepare('SELECT * FROM `tunnel`');
                    if (($req->execute()) !== false) {
                        while ($res = $req->fetch(PDO::FETCH_ASSOC)) {
                            ?>
                                <option
                                <?php
                                    if ($tunnelNum == $res['t_id']) {
                                        echo 'selected="selected"';
                                    }
                                ?>
                                value="<?= $res['t_id']; ?>"><?= $res['t_id']; ?></option>
                            <?php
                        }
                    }
                ?>
            </select>

        <!--------------------------------------------------------------------------------->
        <!----------------------SELECT DE MODIFICATION DE LA TAVERNE----------------------->
        <!--------------------------------------------------------------------------------->
            
            <label for="new_taverne">Modifier la taverne :</label>

            <select class="form-control" name="new_taverne" id="new_taverne">
                <?php
                    $req = $bdd->prepare('SELECT * FROM `taverne`');
                    if (($req->execute()) !== false) {
                        while ($res = $req->fetch(PDO::FETCH_ASSOC)) {
                            $nbChambres = $res['t_chambres'];
                            // Si le nbombre de chambres est supérieur au nombre de nains du groupe j'affiche la taverne en option, sinon je ne l'affiche pas
                            if ($nbChambres > $nbNains) {
                                ?>
                                    <option
                                    <?php
                                        if ($groupeTaverne == $res['t_nom']) {
                                            echo 'selected="selected"';
                                        }
                                    ?>
                                    value="<?= $res['t_id']; ?>"><?= $res['t_nom']; ?></option>
                                <?php
                            }                        
                        }
                    }
                ?>
            </select>

            <input type="submit" class="btn btn-primary btn-lg btn-block mt-3" value="Valider">

        </form>

    </div>
    

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
  </body>
</html>