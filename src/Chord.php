<?php

/** Chord data transfer object */
class Chord
{
    /** @var array List of string positions */
    protected $positions;
    /** @var bool Is chord with barre? */
    protected $hasBarre = false;
    /** @var integer Position of barre */
    protected $barrePosition = 0;

    public function __construct(array $positions, bool $hasBarre, int $barrePosition)
    {
        $this->positions = $positions;
        $this->hasBarre = $hasBarre;
        $this->barrePosition = $barrePosition;
    }

    /**
     * @return array
     */
    public function getPositions() : array
    {
        return $this->positions;
    }

    /**
     * @return int
     */
    public function getStringsCount() : int
    {
        return count($this->positions);
    }

    /**
     * @return bool
     */
    public function hasBarre() : bool
    {
        return $this->hasBarre;
    }

    /**
     * @return int
     */
    public function getBarrePosition() : int
    {
        return $this->barrePosition;
    }
}