<?php


class ChordRender
{
    protected $imagePadding = 10;
    protected $fretBoardLength = 5;
    protected $fretWidth = 8;
    protected $stringOffset = 5;

    protected $font = 'slkscr.ttf';
    protected $fontSize = 6;

    public function getChordImage(Chord $chord)
    {
        $positions = $chord->getPositions();
        $stringsCount = $chord->getStringsCount();

        $width = (2 * $this->imagePadding) + ($this->fretBoardLength * $this->fretWidth);
        $height = (2 * $this->imagePadding) + ($this->stringOffset * ($stringsCount - 1));

        $img = imagecreate($width, $height);

        $img = $this->generateFretboard($img, $width, $height, $stringsCount);
        $img = $this->generatePositions($img, $chord);

        $filename = md5( implode($positions) ) . '.png';
        imagepng($img, APP_DIR . '/images/' . $filename);
        return $filename;
    }

    protected function generateFretboard($img, $width, $height, $stringsCount)
    {
        $blackColor = imagecolorallocate($img, 0, 0, 0);
        $whiteColor = imagecolorallocate($img, 225, 225, 225);

        imagefill($img, 5,5, $whiteColor);

        $padding = $this->imagePadding;
        // strings
        for ($i = 0; $i < $stringsCount; $i++) {
            $stringPadding = $padding + ($i * $this->stringOffset);
            imageline($img,
                $padding,
                $stringPadding,
                $width - $padding,
                $stringPadding,
                $blackColor
            );
        }
        // frets
        for ($i = 0; $i < 6; $i++) {
            $fretPadding = $padding + ($i * $this->fretWidth);
            if ($i == 0) {
                imageline($img,
                    $fretPadding - 1,
                    $padding,
                    $fretPadding - 1,
                    $padding + (($stringsCount - 1) * $this->stringOffset),
                    $blackColor
                );
            }
            imageline($img,
                $fretPadding,
                $padding,
                $fretPadding,
                $padding + (($stringsCount - 1) * $this->stringOffset),
                $blackColor
            );
        }

        imageline($img,
            $padding,
            $padding,
            $padding - 3,
            $padding - 3,
            $blackColor
        );
        imageline($img,
            $padding,
            $height - $padding,
            $padding - 3,
            $height - $padding + 3,
            $blackColor
        );

        return $img;
    }

    protected function generatePositions($img, Chord $chord)
    {
        $stringsCount = $chord->getStringsCount();
        $hasBarre = $chord->hasBarre();
        $barrePosition = $chord->getBarrePosition();

        $blackColor = imagecolorallocate($img, 0, 0, 0);

        if ($hasBarre) {
            $barrePosition = $this->imagePadding + $this->fretWidth / 2;
            imageline($img,
                $barrePosition,
                $this->imagePadding - 2,
                $barrePosition,
                $this->imagePadding + 2 + (($stringsCount - 1) * $this->stringOffset),
                $blackColor
            );
            imageline($img,
                $barrePosition - 1,
                $this->imagePadding - 2,
                $barrePosition - 1,
                $this->imagePadding + 2 + (($stringsCount - 1) * $this->stringOffset),
                $blackColor
            );

            // need text mark of barre position
            // TODO: convert  barre position to romans fret number
            imagettftext($img,
                $this->fontSize,
                0,
                $barrePosition - 1,
                2*$this->imagePadding - 1 + (($stringsCount - 1) * $this->stringOffset),
                $blackColor,
                APP_DIR . '/fonts/' . $this->font,
                $barrePosition
            );
        }

        $firstPosition = $hasBarre ? $barrePosition : 1;

        // set positions marks
        foreach ($chord->getPositions() as $key => $val) {
            $key = $stringsCount - ($key + 1);

            if (is_string($val)) {
                switch ($val) {
                    case 'x':
                        // display 'x' on muted string
                        $x = $this->imagePadding - $this->fretWidth;
                        $y = $this->imagePadding + ($key * $this->stringOffset) + $this->stringOffset / 2 + 1;

                        imagettftext($img,
                            $this->fontSize - 1,
                            0,
                            $x,
                            $y,
                            $blackColor,
                            APP_DIR . '/fonts/' . $this->font,
                            'x'
                        );
                        break;
                    case 'o':
                        // display 'o' on open string
                        $x = $this->imagePadding - $this->fretWidth;
                        $y = $this->imagePadding + ($key * $this->stringOffset) + $this->stringOffset / 2 + 1;

                        imagettftext($img,
                            $this->fontSize,
                            0,
                            $x,
                            $y,
                            $blackColor,
                            APP_DIR . '/fonts/' . $this->font,
                            'O'
                        );
                        break;
                    default:
                        break;
                }
            } else {
                if ($hasBarre && $val === $barrePosition) {
                    continue;
                }

                if ($val !== 0) {
                    $x = $this->imagePadding + $this->fretWidth / 2 + ($val - $firstPosition) * $this->fretWidth;
                    $y = $this->imagePadding + ($key * $this->stringOffset);

                    imagefilledellipse($img,
                        $x,
                        $y,
                        5,
                        5,
                        $blackColor
                    );
                }
            }
        }

        return $img;
    }
}