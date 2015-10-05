<?php

class ChordsException extends \Exception {}

class Chords
{
    protected $validationError = '';
    protected $listOfPositions = [0,0,0,0,0,0];
    protected $hasBarre = false;
    protected $barrePosition = 0;
    protected $stringsCount = 6;

//    public function __construct($chordString)
//    {
//        if (!$this->isValidChordString($chordString)) {
//            throw new ChordsException('Invalid chord string value given');
//        }
//    }

    public function setBarrePosition($position)
    {
        if (!is_numeric($position)) {
            throw new ChordsException('Barre position must be a numeric integer value');
        }

        $position = (integer) $position;
        $this->barrePosition = $position;
    }

    public function setStringsCount($count)
    {
        if ($count < 2 || $count > 12) {
            throw new ChordsException('Strings count must be in range from 2 to 12.');
        }
        $this->stringsCount = (integer) $count;
        $this->listOfPositions = [];
        array_pad($this->listOfPositions, $this->stringsCount, 0);
    }

    protected function isValidChordString($chordString)
    {
        $isValidChars = preg_match('#^[0-9x|o]{6,17}$#i', $chordString);
        if (!$isValidChars) {
            $this->validationError = 'Chord string has invalid chars.';
            return false;
        }
        // если есть символы '|' - то делим строку по ним. В противном случае - делим посимвольно.
        if ($pos = strpos($chordString, '|') !== false) {
            if ($pos === 0) {
                $this->validationError = 'Chord string starts with \'|\' char.';
                return false;
            }
            $parts = explode('|', $chordString);
        } else {
            $parts = str_split($chordString);
        }

        if (count($parts) !== $this->stringsCount) {
            $this->validationError = 'Chord string has invalid strings count.';
            return false;
        }

        return true;
    }

    protected function getPositionsFromString($chordString, $delimiter = null)
    {
        if (!is_null($delimiter)) {
            $parts = explode($delimiter, $chordString);
        } else {
            $parts = str_split($chordString);
        }
        return $parts;
    }

    public function parseChordString($chordString)
    {
        if (!$this->isValidChordString($chordString)) {
            throw new ChordsException($this->validationError);
        }

        $chordString = strtolower($chordString);

        // начинаем парсинг. Нужно получить позицию барре, список зажатых ладов.
        $delimiter = null;
        if (strpos($chordString, '|') !== false) {
            $delimiter = '|';
        }
        $parts = $this->getPositionsFromString($chordString, $delimiter);
        if (count($parts) !== $this->stringsCount) {
            throw new ChordsException('Chord string has invalid strings count.');
        }

        $this->listOfPositions = [];

        $min = null;
        $max = null;
        $hasMuted = false;
        $hasOpened = false;
        foreach ($parts as $val) {
            switch ($val) {
                case 'x':
                    $hasMuted = true;
                    break;
                case 'o':
                    $hasOpened = true;
                    break;
                default:
                    $val = (integer) $val;
                    if (is_null($min)) {
                        $min = $val;
                    }
                    if (is_null($max)) {
                        $max = $val;
                    }
                    if ($val < $min) {
                        $min = $val;
                    }
                    if ($val > $max) {
                        $max = $val;
                    }
                    break;
            }

            $this->listOfPositions[] = $val;
        }

        if (!is_null($min) && !is_null($max)) {
            $diff = $max - $min;
            if ($diff > 4) {
                throw new ChordsException("Unbelievable finger positions! Frets from {$min} to {$max}");
            }
        }

        if (!$hasMuted && !$hasOpened && $min > 0) {
            $this->hasBarre = true;
            $this->barrePosition = $min;
        }
    }


    public function getChordImage()
    {
        $img = imagecreate(50, 50);
        $color = imagecolorallocate($img, 0,0,0);

        imagefill($img, 5,5,$color);

        $filename = md5( implode($this->listOfPositions) ) . '.png';
        imagepng($img, APP_DIR . '/images/' . $filename);
        return $filename;
    }
}