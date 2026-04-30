<?php
$x = 5;

echo $x . '<br>';

var_dump($x);

$s = 'Meireles da Silva';

echo $s . '<br>';

var_dump($s) . '<br>';

$carros = ['BMW', 'Mercedes', 'Audi'];

var_dump($carros);

echo ('<br>' . $carros[2] . '<br>');

// Declarar um array das 3 melhores equipas de futebol em pt

$equipas = ['Sporting', 'SCP', 'Equipa de Alvalade'];
var_dump($equipas);
echo ('<br>' . $equipas[1] . '<br>');

$comidas = [
    "Bacalhau á Brás", 
    "Souflé de Bacalhau", 
    "Pastel de Bacalhau", 
    "Bacalhau á lagareiro", 
    "Bacalhau com natas", 
    "Bacalhau à Gomes de Sá", 
    "Bacalhau à Zé do Pipo"];
var_dump($comidas);
echo ('<br>' . $comidas[1] . '<br>');

$isEven = (2 % 2) == 0;
    var_dump($isEven);

$users = ['Capibara King', 'Hard', 'Twenty Percent'];
var_dump($users);
echo ('<br>' . $users[1] . '<br>');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $carros[2] ?></title>
</head>
<body>
    <style>
    .admin {
        background-color: green;
      text-decoration: underline;
      font-size: 3em;
    }
 
    .regular {
      background-color: red;
      text-decoration: line-through;
      font-size: 1.5em;
    }
    .activo {
      background-color: green;
      color: red;
    }
 
    .inactivo {
      background-color: red;
      color: green;
    }
  </style>
    <p style="color: #275EF5; font-size: 3em;">
    <?php 
    echo $x;
    ?>
    </p>

    <p style="color: #275EF5; font-size: 3em;">
    <?php 
    echo $x;
    ?>
    </p>
    <h1>Eu Adoro o Sporting SCP</h1>

    <?php for ($x = 0; $x < 10; $x++): ?>
        <p>O Sporting é o melhor clube do mundo <?= $x ?></p>
    <?php endfor ?>

    <?php for ($ic = 0; $ic < count($carros); $ic++): ?>
        <p>Carro: <?= $carros[$ic] ?></p>
    <?php endfor ?>

    <!-- Iterar e imprimir as 3 melhores equipas de futebol em Portugal -->
    <?php for ($ie = 0; $ie < count($equipas); $ie++): ?>
        <p>Equipa: 
            <span style="color: #275EF5; font-size: 1.5em;"><?= $ie ?></span>
            <?= $equipas[$ie] ?>
        </p>
    <?php endfor ?>

    <?php for ($ic = 0; $ic < count($comidas); $ic++): ?>
        <p><?php if(($ic % 2) == 0): ?>
            Prato:
            <span style="color: green; font-size: 1.5em;"><?= $ic ?></span>
            <?php else: ?> 
                Prato:
            <span style="color: red; font-size: 1.5em;"><?= $ic ?></span>
            <?php endif ?>
            <?= $comidas[$ic] ?>
        </p>
    <?php endfor ?>

    <?php for ($icomidas2 = 0; $icomidas2 < count($comidas); $icomidas2++): ?>
    <p>
        <span class="<?= ($icomidas2 % 2) == 0 ? 'activo' : 'inactivo' ?>"><?= $icomidas2 ?></span>
 
      <?= $comidas[$icomidas2] ?>
    </p>
  <?php endfor ?>

  <?php for ($iu = 0; $iu < count($users); $iu++): ?>
  <p>
    <span class="<?= $users[$iu][0] === 'H' ? 'admin' : 'regular' ?>">
      <?= $iu ?>
    </span>

    <?= $users[$iu] ?>
  </p>
<?php endfor ?>

</body>
</html>

