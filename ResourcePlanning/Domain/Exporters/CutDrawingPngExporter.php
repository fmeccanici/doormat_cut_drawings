<?php


namespace App\ResourcePlanning\Domain\Exporters;

use App\ResourcePlanning\Domain\Tasks\Cutting\CutDrawing;

class CutDrawingPngExporter implements CutDrawingExporterInterface
{
    public function export(CutDrawing $cutDrawing, string $fileName): string
    {
        $fileNameWithExtensions = $fileName.'.png';
        $img_width = 800;
        $img_height = 1300;
        $img = imagecreatetruecolor($img_width, $img_height);

        foreach ($cutDrawing->getToBeCutShapes() as $toBeCutShape)
        {
            $color = imagecolorallocate($img, rand(0, 255), rand(0, 255), rand(0, 255));
            $topLeft = $toBeCutShape->topLeft();
            $bottomRight = $toBeCutShape->bottomRight();
            $x1 = $topLeft->x();
            $y1 = $topLeft->y();
            $x2 = $bottomRight->x();
            $y2 = $bottomRight->y();
            imagefilledrectangle($img, $x1, $y1, $x2, $y2, $color);
        }

        $filePath = storage_path().'/app/cut_drawings/'.$fileNameWithExtensions;
        $file = fopen($filePath, 'w');
        fclose($file);

        chmod($filePath,0755);
        imagepng($img, $filePath, 9, null);

        return $filePath;
    }
}
