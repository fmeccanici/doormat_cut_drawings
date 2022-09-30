<?php


namespace App\ResourcePlanning\Domain\Tasks\Cutting;

use App\SharedKernel\Geometry\Line;
use App\SharedKernel\Geometry\Position;
use Carbon\Carbon;
use DOMDocument;
use Illuminate\Support\Collection;
use XMLWriter;

abstract class CutDrawing
{
    /**
     * The roll width is without the rubber edge.
     */
    const ROLL_MARGIN = 100; // 100 = 10 cm

    protected XMLWriter $xmlWriter;
    protected string $fileName;
    protected string $filePath;
    protected string $cutMaterial;

    /**
     * @var ToBeCutRectangle[]
     */
    protected array $toBeCutShapes;
    protected float $length;
    protected float $width;
    protected OverlappingLinesRemover $overlappingLinesRemover;

    protected bool $removeOverlappingLines;
    protected array $lines;

    public function __construct(array $toBeCutShapes, string $fileName, float $width, float $length, bool $removeOverlappingLines = true)
    {
        $this->setToBeCutShapes($toBeCutShapes);
        $this->setWidth($width);
        $this->setLength($length);
        $this->removeOverlappingLines = $removeOverlappingLines;

        $this->fileName = $fileName;
        $this->filePath = getcwd()."/".$fileName;

        $this->overlappingLinesRemover = new OverlappingLinesRemover();
        $this->xmlWriter = new XMLWriter();
    }

    public function getLines(): array
    {
        return $this->lines;
    }

    public function removeOverlappingLines()
    {
        $this->removeOverlappingLines = true;
    }

    public function doNotRemoveOverlappingLines()
    {
        $this->removeOverlappingLines = false;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function setWidth(float $width)
    {
        $this->width = $width;
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function setLength(float $length)
    {
        $this->length = $length;
    }

    public function getLength(): float
    {
        return $this->length;
    }

    public function setCutMaterial(string $cutMaterial)
    {
        $this->cutMaterial = $cutMaterial;
    }

    public function getCutMaterial(): string
    {
        return $this->cutMaterial;
    }

    public function setToBeCutShapes(array $toBeCutShapes)
    {
        $this->toBeCutShapes = $toBeCutShapes;
    }

    public function getToBeCutShapes(): array
    {
        return $this->toBeCutShapes;
    }

    abstract function asString(): string;

    protected function addMethodsElementToXml(): void
    {
        $this->xmlWriter->startElement("Methods");

        $this->addCameraMethodElementToXml();
        $this->addThruCutMethodElementToXml();
        $this->addLabelMethodElementToXml();

        // Methods element
        $this->xmlWriter->endElement();
    }

    protected function addCameraMethodElementToXml(): void
    {
        $this->xmlWriter->startElement("Method");
        $this->xmlWriter->writeAttribute("Type", "Register");
        $this->xmlWriter->writeAttribute("Color", "000000");
        $this->xmlWriter->writeAttribute("RegistrationType", "borderFrontRight");
        $this->xmlWriter->endElement();
    }

    protected function addThruCutMethodElementToXml(): void
    {
        $this->xmlWriter->startElement("Method");
        $this->xmlWriter->writeAttribute("Type", "Thru-cut");
        $this->xmlWriter->writeAttribute("Color", "0000ff");
        $this->xmlWriter->writeAttribute("AllowReverseDirection", "false");
        $this->xmlWriter->writeAttribute("Name", "0");
        $this->xmlWriter->writeAttribute("CuttingMode", "standard");
        $this->xmlWriter->endElement();
    }

    protected function addLabelMethodElementToXml(): void
    {
        $this->xmlWriter->startElement("Method");
        $this->xmlWriter->writeAttribute("Type", "{none}");
        $this->xmlWriter->writeAttribute("Color", "808080");
        $this->xmlWriter->writeAttribute("AllowReverseDirection", "false");
        $this->xmlWriter->writeAttribute("Name", "TEXT");
        $this->xmlWriter->endElement();
    }

    protected function convertXmlStringToPrettyFormat(string $xmlString): string
    {
        $domxml = new DOMDocument('1.0');
        $domxml->preserveWhiteSpace = false;
        $domxml->formatOutput = true;
        $domxml->loadXML($xmlString);

        return $domxml->saveXml();
    }

    protected function addRootElementToXml()
    {
        $this->xmlWriter->startElement("ZCC_cmd");
        $this->xmlWriter->writeAttribute("MessageID", "541");
        $this->xmlWriter->writeAttribute("CommandID", "jobdescription");
        $this->xmlWriter->writeAttribute("xsi:noNamespaceSchemaLocation", "file:ZCC_cmd.xsd");
        $this->xmlWriter->writeAttribute("Version", "3026");
        $this->xmlWriter->writeAttribute("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
        $this->xmlWriter->writeAttribute("xmlns:zcc", "www.zund.com/ZCC");
    }

    protected function addJobElementToXml()
    {
        $this->xmlWriter->startElement("Job");
        $this->xmlWriter->writeAttribute("Name", $this->fileName.".zcc");
    }

    protected function addMetaElementToXml()
    {
        $this->xmlWriter->startElement("Meta");
        $this->xmlWriter->writeAttribute("Reserved", "9809");

        $this->xmlWriter->startElement("Description");
        $this->xmlWriter->text("Resource Planner");
        $this->xmlWriter->endElement();

        $this->xmlWriter->startElement("Priority");
        $this->xmlWriter->text("Low");
        $this->xmlWriter->endElement();

        $this->xmlWriter->startElement("Creation");
        $this->xmlWriter->writeAttribute("Name", "Cut Editor");
        $this->xmlWriter->writeAttribute("Version", "3.2.6.9");

        $this->xmlWriter->writeAttribute("Date", Carbon::now()->format("Y-m-d\Th:m:s"));
        $this->xmlWriter->endElement();

        // meta element
        $this->xmlWriter->endElement();
    }

    // the cutting machine automatically adds the settings for a material to the xml
    protected function addMaterialElementToXml()
    {
        $this->xmlWriter->startElement("Material");
        $this->xmlWriter->writeAttribute("Name", $this->cutMaterial);

        // material element
        $this->xmlWriter->endElement();
    }

    protected function addToBeCutLinesToXml(array $lines)
    {
        foreach ($lines as $line)
        {
            if ($this->isLineWithHeighestYValue($line) && sizeof($this->getToBeCutShapes()) > 1)
            {
                continue;
            }

            $this->xmlWriter->startElement("Outline");

            // rotate and multiply by 10 for Zund software
            $startPosition = new Position($line->startPosition()->y() * 10, $line->startPosition()->x() * 10);
            $endPosition = new Position($line->endPosition()->y() * 10, $line->endPosition()->x() * 10);

            $this->addMoveToToXml($startPosition->x(), $startPosition->y());
            $this->addLineToToXml($endPosition->x(), $endPosition->y());
            $this->addMethodToXml("Thru-cut", "0");

            $this->xmlWriter->endElement();
        }
    }

    protected function addToBeCutShapesToXml(array $toBeCutRectangles)
    {

        foreach ($toBeCutRectangles as $toBeCutRectangle)
        {
            $this->xmlWriter->startElement("Outline");

            $rectangle = new ToBeCutRectangle($toBeCutRectangle->id(), $toBeCutRectangle->width(), $toBeCutRectangle->length(), $toBeCutRectangle->customer(), $toBeCutRectangle->cutPosition());
            $rectangle->rotate(90);
            $rectangle->setWidth($rectangle->width() * 10);
            $rectangle->setLength($rectangle->length() * 10);
            $rectangle->setCutPosition(new Position($rectangle->cutPosition()->x() * 10, $rectangle->cutPosition()->y() * 10));

            $this->addRectangleToXml($rectangle);

            // outline element
            $this->xmlWriter->endElement();

        }

    }

    protected function addRectangleToXml(ToBeCutRectangle $rectangle)
    {
        $this->addMoveToToXml($rectangle->bottomLeft()->x(), $rectangle->bottomLeft()->y());
        $this->addLineToToXml($rectangle->bottomRight()->x(), $rectangle->bottomRight()->y());
        $this->addLineToToXml($rectangle->topRight()->x(), $rectangle->topRight()->y());
        $this->addLineToToXml($rectangle->topLeft()->x(), $rectangle->topLeft()->y());
        $this->addLineToToXml($rectangle->bottomLeft()->x(), $rectangle->bottomLeft()->y());

        $this->addMethodToXml("Thru-cut", "0");
    }

    /**
     * @param Collection<string> $labels Collection of labels
     * @param ToBeCutRectangle $rectangle
     * @return void
     */
    protected function addLabelsToXml(Collection $labels, ToBeCutRectangle $rectangle)
    {
        $x = $rectangle->bottomLeft()->x();
        $y = $rectangle->topLeft()->y();
        $height = 100;

        $labels->each(function (string $label) use (&$x, &$y, &$height) {
            $this->xmlWriter->startElement("Label");
            $this->xmlWriter->writeAttribute("Text", $label);
            $this->xmlWriter->writeAttribute("Height", $this->convertFloatToCorrectString($height));
            $this->xmlWriter->writeAttribute("Angle", "0.000");
            $this->xmlWriter->writeAttribute("Deformation", "1.000");

            $this->xmlWriter->startElement("Position");
            $this->xmlWriter->writeAttribute("X", $this->convertFloatToCorrectString($x));
            $this->xmlWriter->writeAttribute("Y", $this->convertFloatToCorrectString($y));

            // position element
            $this->xmlWriter->endElement();

            $this->addMethodToXml("{none}", "TEXT");

            // label element
            $this->xmlWriter->endElement();

            $y -= $height;
        });
    }

    protected function addMoveToToXml(float $x, float $y)
    {
        $this->xmlWriter->startElement("MoveTo");

        $this->xmlWriter->writeAttribute("X",$this->convertFloatToCorrectString($x));
        $this->xmlWriter->writeAttribute("Y", $this->convertFloatToCorrectString($y));

        $this->xmlWriter->endElement();
    }

    protected function addLineToToXml(float $x, float $y)
    {
        $this->xmlWriter->startElement("LineTo");
        $this->xmlWriter->writeAttribute("X", $this->convertFloatToCorrectString($x));
        $this->xmlWriter->writeAttribute("Y", $this->convertFloatToCorrectString($y));
        $this->xmlWriter->endElement();
    }

    protected function convertFloatToCorrectString(float $value): string
    {
        return number_format((string) $value, 3, ".", "");
    }

    protected function addMethodToXml(string $methodType, $methodName)
    {
        $this->xmlWriter->startElement("Method");
        $this->xmlWriter->writeAttribute("Type", $methodType);
        $this->xmlWriter->writeAttribute("Name", $methodName);
        $this->xmlWriter->endElement();
    }

    protected function getMostVerticalPosition(): float
    {
        $mostVerticalPosition = 0;

        foreach ($this->getToBeCutShapes() as $shape)
        {
            $verticalPositionOfShape = $shape->topLeft()->y();

            if ($verticalPositionOfShape > $mostVerticalPosition)
            {
                $mostVerticalPosition = $verticalPositionOfShape;
            }
        }

        return $mostVerticalPosition;
    }

    protected function addHorizontalCuttingLineOnTop()
    {
        $this->xmlWriter->startElement("Outline");

        // * 10 = to mm for cutting machine software
        $yStart = $this->getMostVerticalPosition() * 10;

        $xStart = 0;
        $yEnd = $yStart;

        // * 10 = to mm for cutting machine software
        $xEnd = $this->getWidth() * 10 + self::ROLL_MARGIN;

        // x/y swapped for cutting machine software
        $this->addMoveToToXml($yStart, $xStart);
        $this->addLineToToXml($yEnd, $xEnd);
        $this->addMethodToXml("Thru-cut", "0");

        $this->xmlWriter->endElement();
    }

    protected function isLineWithHeighestYValue(Line $line): bool
    {
        $heighestYValue = $this->getHeighestYValue();

        if ($line->startPosition()->y() == $heighestYValue && $line->endPosition()->y() == $heighestYValue)
        {
            return true;
        }
        else {
            return false;
        }
    }

    protected function getHeighestYValue(): float
    {
        $heighestYValue = 0;

        foreach ($this->toBeCutShapes as $shape)
        {
            $y = $shape->topLeft()->y();

            if ($y > $heighestYValue)
            {
                $heighestYValue = $y;
            }
        }

        return $heighestYValue;
    }

}

