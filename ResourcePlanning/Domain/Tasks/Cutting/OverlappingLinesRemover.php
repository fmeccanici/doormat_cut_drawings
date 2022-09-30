<?php


namespace App\ResourcePlanning\Domain\Tasks\Cutting;

// TODO: Write unit tests for this class
use App\SharedKernel\Geometry\Line;
use App\SharedKernel\Geometry\Position;

class OverlappingLinesRemover
{
    private array $xUnique;
    private array $yUnique;

    /**
     * @var Line[]
     */
    private array $lines;

    public function __construct()
    {
        $this->lines = [];
    }

    /**
     * @param ToBeCutRectangle[] $rectangles
     * @return Line[]
     */
    public function remove(array $rectangles): array
    {
        $this->lines = $this->convertRectanglesToLines($rectangles);

        $positions = $this->convertLinesToPositions($this->lines);
        $x = $this->getXValuesFromPositions($positions);
        $y = $this->getYValuesFromPositions($positions);

        $this->xUnique = array_unique($x);
        $this->yUnique = array_unique($y);

        $horizontalLinesWithoutOverlap = $this->removeOverlappingHorizontalLines();
        $verticalLinesWithoutOverlap = $this->removeOverlappingVerticalLines();

        $linesWithoutOverlap = array_merge($horizontalLinesWithoutOverlap, $verticalLinesWithoutOverlap);

        return $linesWithoutOverlap;
    }

    private function convertRectanglesToLines(array $rectangles): array
    {
        $lines = [];

        foreach ($rectangles as $rectangle)
        {
            $lines = array_merge($lines, $rectangle->convertToLines());
        }

        return $lines;
    }

    /**
     * @param Line[] $lines
     * @return Position[]
     */
    private function convertLinesToPositions(array $lines): array
    {
        $positions = [];

        foreach ($lines as $line)
        {
            $positions[] = $line->startPosition();
            $positions[] = $line->endPosition();
        }

        return $positions;
    }

    /**
     * @param Position[] $positions
     * @return array
     */
    private function getXValuesFromPositions(array $positions): array
    {
        $xValues = [];

        foreach ($positions as $position)
        {
            $xValues[] = $position->x();
        }

        return $xValues;
    }

    /**
     * @param Position[] $positions
     * @return array
     */
    private function getYValuesFromPositions(array $positions): array
    {
        $yValues = [];

        foreach ($positions as $position)
        {
            $yValues[] = $position->y();
        }

        return $yValues;
    }

    private function removeOverlappingHorizontalLines(): array
    {
        $horizontalLinesWithoutOverlap = [];

        foreach ($this->yUnique as $y)
        {
            $horizontalLines = $this->getHorizontalLinesForYValue($y);

            $i = 0;
            $j = 1;

            while ($this->thereAreOverlappingLines($horizontalLines))
            {
                $line_1 = $horizontalLines[$i];
                $line_2 = $horizontalLines[$j];

                if ($i != $j)
                {
                    if ($line_1->overlapsWith($line_2))
                    {
                        if ($line_2->endPosition()->x() > $line_1->endPosition()->x())
                        {
                            $line_1->changeEndPosition($line_2->endPosition());
                        }

                        if ($line_2->startPosition()->x() < $line_2->startPosition()->x())
                        {
                            $line_1->changeStartPosition($line_2->startPosition());
                        }

                        unset($horizontalLines[$j]);

                        $horizontalLines = array_values($horizontalLines);

                        $i = 0;
                        $j = 1;
                    }
                    else {
                        if ($j + 1 >= sizeof($horizontalLines))
                        {
                            if ($i + 1 >= sizeof($horizontalLines))
                            {
                                break;
                            }
                            else {
                                $i += 1;
                                $j = 0;
                            }
                        }
                        else {
                            $j += 1;
                        }
                    }
                }
                else {
                    if ($j + 1 >= sizeof($horizontalLines))
                    {
                        if ($i + 1 >= sizeof($horizontalLines))
                        {
                            break;
                        }
                        else {
                            $i += 1;
                            $j = 0;
                        }
                    }
                    else {
                        $j += 1;
                    }
                }
            }

            $horizontalLinesWithoutOverlap = array_merge($horizontalLinesWithoutOverlap, $horizontalLines);
        }

        return $horizontalLinesWithoutOverlap;
    }

    private function removeOverlappingVerticalLines(): array
    {
        $verticalLinesWithoutOverlap = [];

        foreach ($this->xUnique as $x)
        {
            $verticalLines = $this->getVerticalLinesForXValue($x);

            $i = 0;
            $j = 1;

            while ($this->thereAreOverlappingLines($verticalLines))
            {
                $line_1 = $verticalLines[$i];
                $line_2 = $verticalLines[$j];

                if ($i != $j)
                {
                    if ($line_1->overlapsWith($line_2))
                    {
                        if ($line_2->endPosition()->y() > $line_1->endPosition()->y())
                        {
                            $line_1->changeEndPosition($line_2->endPosition());
                        }

                        if ($line_2->startPosition()->y() < $line_1->startPosition()->y())
                        {
                            $line_1->changeStartPosition($line_2->startPosition());
                        }

                        unset($verticalLines[$j]);

                        $verticalLines = array_values($verticalLines);

                        $i = 0;
                        $j = 1;
                    }
                    else {
                        if ($j + 1 >= sizeof($verticalLines))
                        {
                            if ($i + 1 >= sizeof($verticalLines))
                            {
                                break;
                            }
                            else {
                                $i += 1;
                                $j = 0;
                            }
                        }
                        else {
                            $j += 1;
                        }
                    }
                }
                else {
                    if ($j + 1 >= sizeof($verticalLines))
                    {
                        if ($i + 1 >= sizeof($verticalLines))
                        {
                            break;
                        }
                        else {
                            $i += 1;
                            $j = 0;
                        }
                    }
                    else {
                        $j += 1;
                    }
                }
            }

            $verticalLinesWithoutOverlap = array_merge($verticalLinesWithoutOverlap, $verticalLines);
        }

        return $verticalLinesWithoutOverlap;
    }

    /**
     * @param mixed $y
     * @return Line[]
     */
    private function getHorizontalLinesForYValue(mixed $y): array
    {
        $result = [];

        foreach ($this->lines as $line)
        {
            if ($line->startPosition()->y() == $y && $line->endPosition()->y() == $y)
            {
                $result[] = $line;
            }
        }

        return $result;
    }

    /**
     * @param mixed $x
     * @return Line[]
     */
    private function getVerticalLinesForXValue(mixed $x): array
    {
        $result = [];

        foreach ($this->lines as $line)
        {
            if ($line->startPosition()->x() == $x && $line->endPosition()->x() == $x)
            {
                $result[] = $line;
            }
        }

        return $result;
    }

    private function thereAreOverlappingLines(array $lines): bool
    {
        if (sizeof($lines) === 1)
        {
            return false;
        }

        foreach ($lines as $l1)
        {
            foreach ($lines as $l2)
            {
                if (! ($l1 === $l2))
                {
                    if ($l1->overlapsWith($l2))
                    {
                        return true;
                    }
                }
            }
        }

        return false;
    }

}
