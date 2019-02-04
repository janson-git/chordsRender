<?php
define('APP_DIR', __DIR__);

require_once __DIR__ . '/src/Chord.php';
require_once __DIR__ . '/src/ChordsException.php';
require_once __DIR__ . '/src/ChordParser.php';
require_once __DIR__ . '/src/ChordRender.php';


$string = isset($_POST['chord_string']) ? (string) $_POST['chord_string'] : '002210';
$error = null;
$imgfile = null;

$parser = new ChordParser();
try {
    $chord = $parser->parse($string);

    $render = new ChordRender();
    $imgfile = $render->getChordImage($chord);

} catch (\ChordsException $e) {
    $error = $e->getMessage();
}



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
        .help-text {
            color: #999;
            font-size: 1em;
            font-style: italic;
        }
        .error-text {
            color: #f66;
        }
    </style>
</head>

<body>

<form method="post">
    <div class="form-row">
        <div class="help-text">
            Chord string is list of strings (from 6 to 1) with positions of user frets.
            For example: 002210 (for Am)<br>
        </div>
        <?php
        if ($error !== null) {
            ?>
            <div class="error-text">
                <?=$error?>
            </div>
            <?php
        }
        ?>
    </div>
    <div class="form-row">
        <div class="label">
            <label for="chord_string">Chord string</label>
        </div>
        <div class="input-field">
            <input type="text" name="chord_string" id="chord_string" value="<?=$string?>">
        </div>
    </div>

    <div class="form-row">
        <div class="input-field">
            <input type="submit" value="Generate image">
        </div>
    </div>
</form>

<div>
    <?= $string ?>
</div>
<?php
if ($imgfile !== null) {
    ?>
    <img src="/images/<?= $imgfile ?>">
    <?php
}
?>
</body>
</html>

