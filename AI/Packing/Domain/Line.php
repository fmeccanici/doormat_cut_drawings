<?php


namespace App\AI\Packing\Domain;

// TODO: Make separate geometry package: shared kernel for packing and resource planning and possibly other subdomains

class Line
{
    public Position $start;
    public Position $end;

    public function __construct(Position $start, Position $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function overlapsWithOtherLine(Line $other): bool
    {
        return $this->completelyOverlaps($other) && $this->partiallyOverlapsType1($other) && $this->partiallyOverlapsType2($other);
    }

    # this line
    # --------
    # other line
    # ----------------
    private function completelyOverlaps(Line $other): bool
    {
        # rounding is needed
        # otherwise lines with same y values are not detected properly
        # because there can be a slight difference of e.g. 0.0001

        $start_1_x = round($this->start->x, 2);
        $start_1_y = round($this->start->y, 2);

        $end_1_x = round($this->end->x, 2);
        $end_1_y = round($this->end->y, 2);

        $start_2_x = round($other->start->x, 2);
        $start_2_y = round($other->start->y, 2);

        $end_2_x = round($other->end->x, 2);
        $end_2_y = round($other->end->y, 2);

        if (($start_1_x >= $start_2_x) && ($end_1_x <= $end_2_x))
        {
            if ($start_1_y == $start_2_y && $end_1_y == $end_2_y)
            {
                return true;
            }
        }
        else if (($start_1_y >= $start_2_y) && ($end_1_y <= $end_2_y))
        {
            if ($start_1_x == $start_2_x && $end_1_x == $end_2_x)
            {
                return true;
            }
        }

        return false;
    }

    # this line
    #         ---------------------
    # other line
    # ----------------
    private function partiallyOverlapsType1(Line $other): bool
    {
        if ( ($this->start->x > $other->start->x && $this->start->x < $other->end->x) && ($this->end->x > $other->end) )
        {
            return true;
        }

        if ( ($this->start->y > $other->start->y && $this->start->y < $other->end->y) && ($this->end->y > $other->end->y) )
        {
            return true;
        }

        return false;
    }

    # this line
    # ----------------
    # other line
    #            ----------------
    private function partiallyOverlapsType2(Line $other): bool
    {
        if ( ($this->start->x < $other->start->x) && ($this->end->x > $other->start->x))
        {
            return true;
        }

        if ( ($this->start->y < $other->start->y) && ($this->end->y > $other->start->y) )
        {
            return true;
        }

        return false;
    }
}
