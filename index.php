<?php
session_start();
$titre = "Accueil - Projet PING";
include('header.inc.php');
include('menu.inc.php');
include('message.inc.php');
?>
<div class="row my-4">
    <div class="col-md-8 mx-auto text-center">
        <div class="jumbotron bg-light p-5 rounded">
            <h1 class="display-4">Bienvenue sur PING</h1>
            <p class="lead">Plateforme de gestion des projets ingénieur de l'ESIGELEC</p>
            <hr class="my-4">
            <p>Créez, consultez et gérez les sujets de projets pour les élèves de dernière année.</p>
            <?php if (!isset($_SESSION['email'])): ?>
                <a class="btn btn-primary btn-lg me-2" href="inscription.php" role="button">Créer un compte</a>
                <a class="btn btn-outline-primary btn-lg" href="connexion.php" role="button">Se connecter</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row my-4">
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Proposer un sujet</h5>
                <p class="card-text">En tant que tuteur entreprise, proposez un sujet de projet pour les étudiants.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Valider les sujets</h5>
                <p class="card-text">En tant que responsable PING, validez, modifiez ou refusez les sujets proposés.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Consulter les sujets</h5>
                <p class="card-text">Parcourez les sujets validés et disponibles pour les équipes d'étudiants.</p>
            </div>
        </div>
    </div>
</div>
<?php
include('footer.inc.php');
?>