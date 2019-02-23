<?php

    // INSERSION DE LA BASE DE DONNEES
    require_once('bdd.php');

    //---------------------------------------------------------------------------------
    // -------------------------REQUETE INFORMATIONS TAVERNE---------------------------
    //---------------------------------------------------------------------------------
    try {

        // Je sélectionne les éléments de ma table taverne en faisant une jointure avec ma table `ville`
        $req = $bdd->prepare('SELECT * FROM `taverne`
                              JOIN `ville` ON `ville`.`v_id` = `taverne`.`t_ville_fk`
                              WHERE `t_id` = ?');

        // J'exécute en passant l'id de la taverne que j'ai récupéré dans l'url
        if (($req->execute([$_GET['taverne']])) !== false) {
            
            if (($res = $req->fetch(PDO::FETCH_ASSOC)) !== false) {
                
                // Puis je stocke les informations dont j'ai besoin 
                $taverneNom = $res['t_nom'];
                $taverneSuperficie = $res['t_ville_fk'];
                $taverneChambres = $res['t_chambres'];
            }
        }

    } catch(PDOException $e){
    
        die($e->getMessage());        
    }   


    //---------------------------------------------------------------------------------
    // --------------------CALCUL NOMBRE DE NAINS DANS LA TAVERNE----------------------
    //---------------------------------------------------------------------------------

    try {

        // Je sélectionne les éléments de ma table taverne n faisant des jointures avec les tables `groupe` et `nains`
        $req = $bdd->prepare('SELECT * FROM `taverne`
                              JOIN `groupe` ON `taverne`.`t_id` = `groupe`.`g_taverne_fk`
                              JOIN `nain` ON `groupe`.`g_id` = `nain`.`n_groupe_fk`
                              WHERE `t_id` = ?');

        // J'exécute en passant l'id de la taverne que j'ai récupéré dans l'url
        if (($req->execute([$_GET['taverne']])) !== false) {

            // Puis je stocke dans une variable le résultat de ma méthode rowcount() sur ma requete
            // j'ai donc récupéré le nombre de nain dans chaque groupe qui occupe la taverne
            $nbNains = $req->rowcount();    
        }

    } catch(PDOException $e){
    
        die($e->getMessage());        
    }   

    // je stocke dans une variable le résultat du nombre de nains présents dans la taverne, au nombre de chambres que celle-ci contient pour avoir le nombre de chambres disponibles
    $chambresDispos = $taverneChambres - $nbNains;


?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">

    <title>Page Taverne</title>

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
    <h1 class="mt-5">Page Taverne</h1>

    <!-- J'affiche la taverne récupéré plus haut grace à mes requêtes -->
    <h2 class="mt-3">Bienvenue dans la taverne de <span><?= $taverneNom;?></span></h2>

    <div class="alert alert-secondary col-md-8 mt-5">

        <ul> Bières disponibles : 
            <?php
                //---------------------------------------------------------------------------------
                // -----------------------------REQUETE LISTE BIERES-------------------------------
                //---------------------------------------------------------------------------------
                try {

                    // Je sélectionne les éléments de ma table `taverne`
                    $req = $bdd->prepare('SELECT * FROM `taverne` WHERE `t_id` = ?');
            
                    // J'exécute en passant l'id de la taverne que j'ai récupéré dans l'url
                    if (($req->execute([$_GET['taverne']])) !== false) {
                        
                        if (($res = $req->fetch(PDO::FETCH_ASSOC)) !== false) {
                            
                            // Puis pour chaque bière je l'affiche dans ma liste si elle est bien vrai
                            // (les bières sont stockées en booléens dans ma base de données donc si elles ne sont pas stockées à 0 c'est qu'elles sont disponibles,)
                            // Donc si elles sont disponibles je les affiche dans ma liste 
                            if ($res['t_blonde']) {
                                echo '<li><span>Blonde</span></li>';
                            }
                            if ($res['t_brune']) {
                                echo '<li><span>Brune</span></li>';
                            }
                            if ($res['t_rousse']) {
                                echo '<li><span>Rousse</span></li>';
                            }
                        }
                    }
                } catch(PDOException $e){
                
                    die($e->getMessage());        
                }   
            ?>
        </ul>

        <!-- J'affiche le nombre de chambres grace aux informations que j'ai récupéré plus haut -->
        <p>Nombre de chambres :<span> <?= $taverneChambres; ?></span></p>

        <!-- J'affiche le nombre de chambres dispos grace aux informations que j'ai récupéré plus tôt -->
        <p>Nombre de chambres disponibles : <span><?= $chambresDispos; ?></span></p>
    </div>
    

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
  </body>
</html>