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
            $req = $bdd->prepare('SELECT `v_nom` FROM `ville` WHERE `v_id` = ?');
    
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
    // --------------------------REQUETE INFORMATION NAIN------------------------------
    //---------------------------------------------------------------------------------
    
    try {
        // Je sélectionne tous les éléments de ma table nain en faisant toutes les jointures pour récupérer toutes les informations dont j'ai besoin
        $req = $bdd->prepare('SELECT * FROM `nain`
                              JOIN `ville` ON `nain`.`n_ville_fk` = `ville`.`v_id`
                              LEFT JOIN `groupe` ON `nain`.`n_groupe_fk` = `groupe`.`g_id`
                              LEFT JOIN `taverne` ON `groupe`.`g_taverne_fk` = `taverne`.`t_id`
                              LEFT JOIN `tunnel` ON `groupe`.`g_tunnel_fk` = `tunnel`.`t_id`
                              WHERE `n_id` = ?');

        if (($req->execute([$_GET['nain']])) !== false) {
            
            if ($res = $req->fetch(PDO::FETCH_ASSOC)) { 

                // Je stocke toutes les informations dont j'ai besoin dans des variables
                $nainNom = $res['n_nom'];
                $nainBarbe = $res['n_barbe'];
                $nainVilleId = $res['v_id'];
                $nainVille = $res['v_nom'];
                $nainDebutTravail = $res['g_debuttravail'];
                $nainFinTravail = $res['g_fintravail'];
                $nainGroupe = $res['g_id'];
                $nainTaverne = $res['t_nom'];
                $villeDepartId = $res['t_villedepart_fk'];
                $villeArriveeId = $res['t_villearrivee_fk'];

                // Avec cette requête je ne récupère que l'id de la ville de départ du tunnel et de la ville d'arrivée, via ma table `tunnel` je fais donc appel à ma fonction villeTunnel() pour récupérer leur nom et pouvoir les afficher 
                $nainVilleDepart = villeTunnel($bdd, $res['t_villedepart_fk']);
                $nainVilleArrivee = villeTunnel($bdd, $res['t_villearrivee_fk']);
            }
        }

    } catch(PDOException $e){
    
        die($e->getMessage());        
    }   

//---------------------------------------------------------------------------------
// --------------------------------REQUETE TAVERNE ID------------------------------
//---------------------------------------------------------------------------------

    try {

        // Je sélectionne l'id de la taverne
        $req = $bdd->prepare('SELECT `t_id` FROM `taverne` WHERE `t_nom` = ?');

        // et j'exécute ma requête en passant en paramètre le nom de la taverne que j'ai récupérée dans ma requpete précédente 
        if (($req->execute([$nainTaverne])) !== false) {
            
            if (($res = $req->fetch(PDO::FETCH_ASSOC)) !== false) {

                // Je stocke l'id de la taverne assignée à ce nain pour pouvoir la placer dans mon lien 'taverne'
               $taverneId = $res['t_id'];
            }
        }
    } catch(PDOException $e){

        die($e->getMessage());        
    }   


//---------------------------------------------------------------------------------
// ------------------TRAITEMENT FORMULAIRE CHANGEMENT DE GROUPE--------------------
//---------------------------------------------------------------------------------
    
    // Si mon champ existe et qu'il n'est pas vide
    if (isset($_POST['new_group']) && !empty($_POST['new_group'])) {

        try {

            // Je prépare ma requete à modifier le groupe du nain 
            $req = $bdd->prepare('UPDATE `nain` SET `n_groupe_fk` = ? WHERE `n_id` = ?');

            // J'exécute ma requête en passant le champ rempli et ma variable $_GET['nain'], pour cibler l'id du nain placée dans l'url, qui est donc l'id du nain de la page
            if (($req->execute([$_POST['new_group'], $_GET['nain']])) !== false) {

                // je stocke le résultat de ma requete dans une variable pour informer l'utilisateur du résultat du traitement 
                $msg = $nainNom.' a bien été affecté au nouveau groupe';
            } else {
                $msg = 'une erreur est survenue lors de la modification';
            }

        } catch(PDOException $e){
    
            die($e->getMessage());        
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

    <title>Page Nain</title>

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
    </style>
  </head>
  <body>
    <h1 class="mt-5">Page Nain</h1>

    <!-- J'affiche le nom du nain récupéré plus haut grace à mes requêtes -->
    <h2 class="mt-3">Bienvenue sur le profil de <span> <?= $nainNom; ?> </span></h2>

    <!-- Si l'utilisateur a demandé à effectuer un changement de groupe du nain, j'affiche la variable $msg dans une div pour informer l'utilisateur du résultat du traitement -->
    <?php 
        if (isset($msg)) {
            ?>
                <div class="alert alert-primary col-md-8 mt-5"><?= $msg; ?></div>
            <?php
        }
    ?>

    <div class="alert alert-secondary col-md-8 mt-5">

        <!-- J'affiche touts les informations demandées en passant dans le lien l'id de chaque information pour le faire passer en $_GET et accéder à la page référente -->
        <p>Ville d'origine :<span><a href="ville.php?ville=<?= $nainVilleId; ?>"> <?= $nainVille; ?></a></span></p>

        <p>Longueur de barbe :<span> <?= $nainBarbe.' cm'; ?></span></p>

        <p>Boit dans :<span><a href="taverne.php?taverne=<?= $taverneId; ?>"> <?= $nainTaverne; ?></a></span></p>

        <p>Travaille de :<span> <?= $nainDebutTravail.'</span> à <span>'.$nainFinTravail.'</span> de <span><a href="ville.php?ville='.$villeDepartId.'">'.$nainVilleDepart.'</a></span> à <span><a href="ville.php?ville='.$villeArriveeId.'">'.$nainVilleArrivee; ?></a></span></p>

        <p>Membre du groupe n° :<span> <?= $nainGroupe; ?></span></p>

        <!--------------------------------------------------------------------------------->
        <!---------------------FORMULAIRE DE MODIFICATION DU GROUPE------------------------>
        <!--------------------------------------------------------------------------------->

        <form action="" method="POST" class="form-group mt-5">

            <label for="new_group">Voulez vous affecter <span> <?= $nainNom; ?> </span> à un autre groupe ?</label>

            <!-- Je fais un select pour afficher tous les groupes-->
            <select class="form-control" name="new_group" id="new_group">
                        <?php
                            // Je sélectionne tous les éléments de ma table `groupe`
                            $req = $bdd->prepare('SELECT * FROM `groupe`');
                            if (($req->execute()) !== false) {
                            while ($res = $req->fetch(PDO::FETCH_ASSOC)) {
                                ?>
                                     <!-- Puis pour chaque résultat j'affiche une option avec le numéro du groupe, en passant également l'id dans la 'value=""''" de la balise pour pouvoir le traiter avec un $_POST -->
                                    <option value="<?php echo $res['g_id']; ?>"><?php echo $res['g_id']; ?></option>
                                <?php
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