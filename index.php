<?php

    // INSERSION DE LA BASE DE DONNEES
    require_once('bdd.php');

    //---------------------------------------------------------------------------------
    // -----------------------FONCTION DE CHANGEMENT DE PAGE---------------------------
    //---------------------------------------------------------------------------------

    // Prend en paramètres la base de données, l'id de la page sélectionnée, le nom de la page, et l'intitulé de l'id de la page demandée 
    function GoToPage(PDO $bdd, int $select, string $name, string $id) {
        try {
             
            // Je sécurise mon champ
            $select = htmlspecialchars($select);
        
            // Je sélectionne tous les éléments de la table référente à la page demandée
            $req = $bdd->prepare('SELECT * FROM '.$name.' WHERE `'.$id.'` = ?');
        
            // J'exécute avec l'id référent
            if (($req->execute([$select])) !== false) {
        
                if (($res = $req->fetch(PDO::FETCH_ASSOC)) !== false) {

                    // Puis je redirige l'utilisateur sur la page demandée en passant l'id correspondant dans l'url
                    header('Location: '.$name.'.php?'.$name.'='.$select);
                    exit();
                }
            }
            
    
        } catch(PDOException $e){
    
            die($e->getMessage());        
        }    
    }

    //---------------------------------------------------------------------------------
    // -----------------------TRAITEMENT DE CHANGEMENT DE PAGE-------------------------
    //---------------------------------------------------------------------------------

    // Si mon champ existe et qu'il n'est pas vide
    if (isset($_POST['choose_nain']) && !empty($_POST['choose_nain'])) {

        // je stocke les intitulés de la table concercnée dans des davriables
        $nain = 'nain';
        $nainId = 'n_id';
        
        // Puis j'appelle ma fonction GotoPage ci-dessus en passant en paramètres les informations demandées pour accéder à la page choisie
        goToPage($bdd, $_POST['choose_nain'], $nain, $nainId);
    }

    
    if (isset($_POST['choose_ville']) && !empty($_POST['choose_ville'])) {

        $ville = 'ville';
        $villeId = 'v_id';
        
        goToPage($bdd, $_POST['choose_ville'], $ville, $villeId);
    }

    if (isset($_POST['choose_groupe']) && !empty($_POST['choose_groupe'])) {

        $groupe = 'groupe';
        $groupeId = 'g_id';
        
        goToPage($bdd, $_POST['choose_groupe'], $groupe, $groupeId);
    }


    if (isset($_POST['choose_taverne']) && !empty($_POST['choose_taverne'])) {

        $taverne = 'taverne';
        $taverneId = 't_id';
        
        goToPage($bdd, $_POST['choose_taverne'], $taverne, $taverneId);
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

    <title>Page Principale</title>

    <style>
        h1 {
            text-align: center;
        }
        .container {
            display: flex;
        }
        .alert {
            margin-right: 10px;
        }
    </style>
  </head>
  <body>
    <h1 class="mt-5">Page Principale</h1>

    <div class="container mt-5">

    <!--------------------------------------------------------------------------------->
    <!---------------------------------SELECT NAIN------------------------------------->
    <!--------------------------------------------------------------------------------->
        <div class="alert alert-secondary col-md-3 form-group" >
            <form action="" method="POST">
                <label for="choose_nain">Choisir un nain</label>
                <select class="form-control" name="choose_nain" id="choose_nain">
                    <?php
                        $req = $bdd->prepare('SELECT * FROM `nain`');
                        if (($req->execute()) !== false) {
                           while ($res = $req->fetch(PDO::FETCH_ASSOC)) {
                               ?>
                                    <option value="<?php echo $res['n_id']; ?>"><?php echo $res['n_nom']; ?></option>
                               <?php
                           }
                        }
                    ?>
                    <input type="submit" class="btn btn-primary btn-lg btn-block mt-3" value="Valider">
                </select>
            </form>
        </div>

    <!--------------------------------------------------------------------------------->
    <!---------------------------------SELECT VILLE------------------------------------>
    <!--------------------------------------------------------------------------------->
        <div class="alert alert-secondary col-md-3" >
            <form action="" method="POST">
                    <label for="choose_ville">Choisir une ville</label>

                    <!-- Je fais un select pour chaque choix de page -->
                    <select class="form-control" name="choose_ville" id="choose_ville">
                        <?php

                            // Je sélectionne tous les éléments de la table demandée
                            $req = $bdd->prepare('SELECT * FROM `ville`');
                            if (($req->execute()) !== false) {
                            while ($res = $req->fetch(PDO::FETCH_ASSOC)) {
                                ?>
                                    <!-- J'affiche tous les éléments qui sont dans ma table, puis je passe en valeur l'id de l'élément sélectionner pour le traitrement du formulaire -->
                                    <option value="<?php echo $res['v_id']; ?>"><?php echo $res['v_nom']; ?></option>
                                <?php
                                }
                            }
                        ?>
                    </select>
                    <input type="submit" class="btn btn-primary btn-lg btn-block mt-3" value="Valider">
            </form>
        </div>

    <!--------------------------------------------------------------------------------->
    <!---------------------------------SELECT GROUPE----------------------------------->
    <!--------------------------------------------------------------------------------->
        <div class="alert alert-secondary col-md-3" >
            <form action="" method="POST">
                <label for="choose_groupe">Choisir un groupe</label>
                <select class="form-control" name="choose_groupe" id="choose_groupe">
                    <?php
                        $req = $bdd->prepare('SELECT * FROM `groupe`');
                        if (($req->execute()) !== false) {
                        while ($res = $req->fetch(PDO::FETCH_ASSOC)) {
                            ?>
                                <option value="<?php echo $res['g_id']; ?>"><?php echo $res['g_id']; ?></option>
                            <?php
                            }
                        }
                    ?>
                    </select>
                    <input type="submit" class="btn btn-primary btn-lg btn-block mt-3" value="Valider">
            </form>
        </div>

    <!--------------------------------------------------------------------------------->
    <!---------------------------------SELECT TAVERNE---------------------------------->
    <!--------------------------------------------------------------------------------->
        <div class="alert alert-secondary col-md-3" >
            <form action="" method="POST">
                <label for="choose_taverne">Choisir une taverne</label>
                <select class="form-control" name="choose_taverne" id="choose_taverne">
                    <?php
                        $req = $bdd->prepare('SELECT * FROM `taverne`');
                        if (($req->execute()) !== false) {
                        while ($res = $req->fetch(PDO::FETCH_ASSOC)) {
                            ?>
                                <option value="<?php echo $res['t_id']; ?>"><?php echo $res['t_nom']; ?></option>
                            <?php
                            }
                        }
                    ?>
                    </select>
                    <input type="submit" class="btn btn-primary btn-lg btn-block mt-3" value="Valider">
            </form>
        </div>

    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
  </body>
</html>