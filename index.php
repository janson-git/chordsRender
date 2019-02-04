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
            <input type="text" name="chord_string" id="chord_string">
        </div>
    </div>

    <div class="form-row">
        <div class="input-field">
            <input type="submit" value="Сгенерировать изображение">
        </div>
    </div>
</form>

<?php
define('APP_DIR', __DIR__);

require_once __DIR__ . '/src/Chords.php';


$string = isset($_POST['chord_string']) ? $_POST['chord_string'] : '002210';

$chords = new Chords();
//$string = 'X22oXX';
//$string = '577655';

$chords->parseChordString($string);

$imgfile = $chords->getChordImage();

?>
<div>
    <?= $string ?>
</div>
<img src="/images/<?= $imgfile ?>">
</body>
</html>

