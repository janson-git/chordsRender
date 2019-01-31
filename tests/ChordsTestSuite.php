<?php

require_once APP_DIR . '/src/Chords.php';

class ChordsTestSuite
{
    /** @var  \Chords */
    protected $chords;

    public function setUp()
    {
        $this->chords = new \Chords();
    }

    public function tearDown()
    {
        $this->chords = null;
    }


    protected $validChordStrings = ['000000', '123456', '999000', '111XXX', 'XXXXXX', 'XXX0oX', '1234xo'];

    public function testValidChordStringValidation()
    {
        $validationMethod = new ReflectionMethod('Chords', 'isValidChordString');
        $validationMethod->setAccessible(true);

        foreach ($this->validChordStrings as $string) {
            $isValid = $validationMethod->invoke($this->chords, $string);

            assert($isValid === true, "String {$string} must be valid chord string");
        }
    }

    protected $invalidChordStrings = ['00000', '12a456', '99[]00', '1|11XX', '|XXXXX', '||12345'];

    public function testInvalidChordStringValidation()
    {
        $validationMethod = new ReflectionMethod('Chords', 'isValidChordString');
        $validationMethod->setAccessible(true);

        foreach ($this->invalidChordStrings as $string) {
            $isValid = $validationMethod->invoke($this->chords, $string);

            assert($isValid === false, "String {$string} must be invalid chord string");
        }
    }


    public function testChordStringSplit()
    {
        $method = new ReflectionMethod('Chords', 'getPositionsFromString');
        $method->setAccessible(true);

        $string = '12345';
        $parts = $method->invoke($this->chords, $string);
        assert(count($parts) === 5, "String {$string} has 5 parts");

        $string = '123xo0';
        $parts = $method->invoke($this->chords, $string);
        assert(count($parts) === 6, "String {$string} has 5 parts");

        $string = '12|12|12|12|12|12|12';
        $parts = $method->invoke($this->chords, $string, '|');
        assert(count($parts) === 7, "String {$string} has 7 parts");
    }
}