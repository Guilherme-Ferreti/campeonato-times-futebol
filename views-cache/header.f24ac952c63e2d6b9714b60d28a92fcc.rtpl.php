<?php if(!class_exists('Rain\Tpl')){exit;}?><!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campeonato de Futebol</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>

    <header>
        <div class="row-season">
            <div>
                Estamos na <span>1</span>ª temporada!
            </div>
        </div>
        <div class="row-menu">
            <div class="menu-box">
                <img src="../assets/images/logo.png" alt="Logo" id="logo">
                <ul>
                    <li><a href="/">Campeonatos</a></li>
                    <li><a href="">Lista de Equipes</a></li>
                    <li><a href="">Estatísticas</a></li>
                    <li><a href="">Sobre</a></li>
                </ul>
            </div>
        </div>
    </header>

    <main>

    <?php if( $title!=null ){ ?>
        <div class="main-row-title">
            <h1><?php echo htmlspecialchars( $title, ENT_COMPAT, 'UTF-8', FALSE ); ?></h1>
            <hr>
        </div>
    <?php } ?>