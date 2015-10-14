<?php 
define('APP_DIR', __DIR__);
header('Content-type: text/html; charset=utf-8');
?>
<html>
<head>
    <title>Chords</title>
    <style>
        .label {
            width: 200px;
            float: left;
        }
        .input-field {
            margin-left: 210px;
        }
    </style>
</head>

<body>

<form method="post">
    <div class="form-row">
    <div class="label">
        <label for="chord_string">Обозначение аккорда</label>
    </div>
    <div class="input-field">
        <input type="text" id="chord_string" value="">
    </div>
    </div>

    <div class="form-row">
    <div class="input-field">
        <input type="submit" value="Сгенерировать изображение">
    </div>
    </div>
</form>
<?php

require_once __DIR__ . '/Chords.php';


$string = isset($_POST['chord_string']) ? $_POST['chord_string'] : '000000';

$chords = new Chords();
//$string = 'X22oXX';
//$string = '577655';

$chords->parseChordString($string);

$imgfile = $chords->getChordImage();

?>
<img src="/images/<?= $imgfile ?>">
</body>
</html>

