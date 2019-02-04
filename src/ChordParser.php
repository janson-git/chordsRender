<?php


class ChordParser
{
    /** @var string  */
    protected $validationError = '';
    /** @var int */
    protected $stringsCount;
    protected $listOfPositions;

    /**
     * ChordParser constructor.
     * @param int $stringsCount
     */
    public function __construct($stringsCount = 6)
    {
        $this->stringsCount = $stringsCount;
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

    /**
     * @param $chordString
     * @return Chord
     * @throws ChordsException
     */
    public function parse($chordString) : Chord
    {
        if (!$this->isValidChordString($chordString)) {
            throw new ChordsException($this->validationError);
        }

        $chordString = strtolower($chordString);

        // lets parse: get barre position and list of finger positions
        $delimiter = null;
        if (strpos($chordString, '|') !== false) {
            $delimiter = '|';
        }
        $parts = $this->getPositionsFromString($chordString, $delimiter);
        if (count($parts) !== $this->stringsCount) {
            throw new ChordsException('Chord value has invalid strings count.');
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
                    if ($min === null) {
                        $min = $val;
                    }
                    if ($max === null) {
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

        if (!($min === null) && !($max === null)) {
            $diff = $max - $min;
            if ($diff > 4) {
                throw new ChordsException("Unbelievable finger positions! Frets from {$min} to {$max}");
            }
        }

        $hasBarre = false;
        $barrePosition = 0;
        if (!$hasMuted && !$hasOpened && $min > 0) {
            $hasBarre = true;
            $barrePosition = $min;
        }

        return new Chord($this->listOfPositions, $hasBarre, $barrePosition);
    }

    /**
     * @return string
     */
    public function getValidationError() : string
    {
        return $this->validationError;
    }

    /**
     * @param $chordString
     * @return bool
     */
    protected function isValidChordString($chordString) : bool
    {
        $isValidChars = preg_match('#^[0-9x|o]{6,17}$#i', $chordString);
        if (!$isValidChars) {
            $this->validationError = 'Chord string has invalid chars.';
            return false;
        }
        // если есть символы '|' - то делим строку по ним. В противном случае - делим посимвольно.
        if ($pos = (strpos($chordString, '|') !== false)) {
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

}