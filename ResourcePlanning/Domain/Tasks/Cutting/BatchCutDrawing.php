<?php


namespace App\ResourcePlanning\Domain\Tasks\Cutting;


use App\SharedKernel\Geometry\Position;

class BatchCutDrawing extends CutDrawing
{

    function asString(): string
    {
        $this->xmlWriter->openMemory();

        $this->xmlWriter->startDocument("1.0", 'UTF-8');

        $this->addRootElementToXml();
        $this->addJobElementToXml();
        $this->addMetaElementToXml();
        $this->addMaterialElementToXml();

        $this->xmlWriter->startElement("Geometry");

        if ($this->removeOverlappingLines)
        {
            $this->lines = $this->overlappingLinesRemover->remove($this->toBeCutShapes);
            $this->addToBeCutLinesToXml($this->lines);
        }
        else {
            $this->addToBeCutShapesToXml($this->toBeCutShapes);
        }

        $this->addHorizontalCuttingLineOnTop();

        foreach ($this->toBeCutShapes as $toBeCutRectangle)
        {
            $rectangle = new ToBeCutRectangle($toBeCutRectangle->id(), $toBeCutRectangle->width(), $toBeCutRectangle->length(), $toBeCutRectangle->customer(), $toBeCutRectangle->cutPosition());
            $rectangle->rotate(90);
            $rectangle->setWidth($rectangle->width() * 10);
            $rectangle->setLength($rectangle->length() * 10);
            $rectangle->setCutPosition(new Position($rectangle->cutPosition()->x() * 10, $rectangle->cutPosition()->y() * 10));

            $labels = collect([$rectangle->customer()]);

            $this->addLabelsToXml($labels, $rectangle);
        }

        // the rest (machine setting etc.) is filled in automatically by the cutting machine

        // geometry element
        $this->xmlWriter->endElement();

        $this->addMethodsElementToXml();

        // job element
        $this->xmlWriter->endElement();

        // root element
        $this->xmlWriter->endElement();

        $this->xmlString = $this->xmlWriter->flush();
        return $this->convertXmlStringToPrettyFormat($this->xmlString);
    }
}
