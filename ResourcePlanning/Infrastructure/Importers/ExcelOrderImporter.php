<?php


namespace App\ResourcePlanning\Infrastructure\Importers;

use App\ResourcePlanning\Domain\FinishedGoods\Doormat;
use App\ResourcePlanning\Domain\Importers\OrderImporterInterface;
use App\ResourcePlanning\Domain\Orders\Order;
use App\ResourcePlanning\Domain\Orders\OrderLine;
use App\ResourcePlanning\Domain\Resources\Coupage;
use App\ResourcePlanning\Domain\Resources\Roll;
use App\ResourcePlanning\Infrastructure\Exceptions\InvalidOrderImporterOperationException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class ExcelOrderImporter implements OrderImporterInterface
{
    private const ALLOWED_FILE_EXTENSIONS = ["xlsm", "xlsx", "xls"];
    private const REQUIRED_COLUMN_NAMES = ["Aantal", "Merk", "Materiaal", "Artikelnaam", "Omschrijving", "Breedte", "Lengte", "Orderdatum", "Coupage/Batch", "Ordernummer", "Klantnaam", "Soort", "Rolbreedte", "Locatie", "Registratienummer"];
    private array $excelSheetRows;
    private array $orderLines = [];
    private array $failedOrderLines = [];

    /**
     * @param string $fileContent
     * @param string $fileName
     * @return Order
     * @throws InvalidExcelTemplateException
     * @throws InvalidOrderImporterOperationException
     */
    public function convertToOrder(string $fileContent, string $fileName): Order
    {
        $filePath = $this->storeFileAndGetFilePath($fileContent, $fileName);

        $this->readExcelFile($filePath);
        $this->validate();

        $this->convertExcelFileToOrderLines();
        $this->processDuplicateOrderNumbersFromOrderLines();
        $this->processMultipleQuantitiesFromOrderLines();

        return new Order(collect($this->orderLines));
    }

    // TODO: Kolom object, rapporteren (missing data, out of range etc.)
    // TODO: Template object
    /**
     * @throws InvalidExcelTemplateException
     */
    private function validate()
    {
        // Zijn alle kolommen aanwezig
        $columnNames = array_filter($this->excelSheetRows[0]);

        if ($columnNames !== self::REQUIRED_COLUMN_NAMES) {
            $invalidColumns = array_diff($columnNames, self::REQUIRED_COLUMN_NAMES);

            throw new InvalidExcelTemplateException("Invalid columns: " . implode(',', $invalidColumns));
        }

        // TODO: It should handle quantities more than 1 and orderlines with same order number
        for ($i = 1; $i < sizeof($this->excelSheetRows); $i++)
        {

            $row = $this->excelSheetRows[$i];
            $numberOfColumns = sizeof($columnNames);

            // Empty row
            if (sizeof(array_filter($row)) === 0)
            {
                continue;
            }

            if (sizeof($row) < $numberOfColumns)
            {
                $row = array_pad($row, $numberOfColumns - sizeof($row), null);
            }

            if (sizeof($row) > $numberOfColumns)
            {

                $row = array_slice($row, 0, $numberOfColumns);
            }

            $row = array_combine($columnNames, $row);

            $row["Breedte"] = str_replace(",", ".", $row["Breedte"]);
            $row["Lengte"] = str_replace(",", ".", $row["Lengte"]);
            $row["Aantal"] = filter_var($row["Aantal"], FILTER_VALIDATE_INT) !== false ? (int) $row["Aantal"] : $row["Aantal"];
            $row["Breedte"] = filter_var($row["Breedte"], FILTER_VALIDATE_FLOAT | FILTER_VALIDATE_INT) ? (float) $row["Breedte"] : $row["Breedte"];
            $row["Lengte"] = filter_var($row["Lengte"], FILTER_VALIDATE_FLOAT | FILTER_VALIDATE_INT) ? (float) $row["Lengte"] : $row["Lengte"];
            $row["Rolbreedte"] = filter_var($row["Rolbreedte"], FILTER_VALIDATE_INT) !== false ? (int) $row["Rolbreedte"] : $row["Rolbreedte"];
            $row["Ordernummer"] = (string) $row["Ordernummer"];
            $row["Klantnaam"] = (string) $row["Klantnaam"];
            $row["Soort"] = (string) $row["Soort"];
            $row["Locatie"] = (string) $row["Locatie"];
            $row["Registratienummer"] = (string) $row["Registratienummer"];

            // TODO: Pas aan naar pass, validator->passed()
            $validator = Validator::make($row, [
                "Aantal" => "required|int|between:1,999",
                "Merk" => "required|string",
                "Materiaal" => "required|string",
                "Artikelnaam" => "required|string",
                "Omschrijving" => "required|string",
                "Breedte" => "required|numeric|between:1,99999999.99",
                "Lengte" => "required|numeric|between:1,99999999.99",
                "Orderdatum" => "required|date_format:d-m-Y",
                "Coupage/Batch" => "required|string|in:Batch,Coupage",
                "Ordernummer" => "required|string",
                "Klantnaam" => "required|string",
                "Soort" => "required|string",
                "Rolbreedte" => "required|int|between:1, 1000",
                "Locatie" => "nullable|string|required_with:Registratie",
                "Registratienummer" => "nullable|string|required_with:Locatie",
            ]);

            if ($validator->fails())
            {
                $fieldName = $validator->errors()->keys()[0];
                throw new InvalidExcelTemplateException("Required field ".$fieldName. " not present in row ".(string) ($i + 1));
            }
        }
    }

    private function storeFileAndGetFilePath(string $fileContent, string $fileName): string
    {
        // TODO: Refactor so this class doesn't need to store: Violates single responsibility principle
        $directory = storage_path().'/app/orderlists/';
        $filePath = $directory.$fileName;
        if (! File::isDirectory($directory))
        {
            File::makeDirectory($directory);
        }

        file_put_contents($filePath, $fileContent);

        return $filePath;
    }

    public function getFailedOrderLines(): array
    {
        return $this->failedOrderLines;
    }

    private function readExcelFile(string $filepath)
    {
        $fileExtension = pathinfo($filepath, PATHINFO_EXTENSION);

        if (! in_array($fileExtension, self::ALLOWED_FILE_EXTENSIONS, true))
        {
            throw new InvalidArgumentException("File should have extension ".implode(', ', self::ALLOWED_FILE_EXTENSIONS));
        }

        $excelReader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadSheet = $excelReader->load($filepath);
        $this->excelSheetRows = $spreadSheet->getSheet(0)->toArray();
    }

    /**
     * @throws InvalidOrderImporterOperationException
     */
    private function convertExcelFileToOrderLines()
    {
        $i = 0;

        $salesOrderLines = [];

        unset($this->excelSheetRows[0]);

        // TODO: It should handle quantities more than 1 and orderlines with same order number
        foreach ($this->excelSheetRows as $row)
        {
            if ($this->isExcelRowEmpty($row))
            {
                continue;
            }

            $quantity = (int) $row[0];
            $brand = $row[1];
            $material = $row[2];
            $productName = $row[3];
            $description = $row[4];

            $width = $this->stringNumberToFloatWithCorrectDecimalSeparator($row[5]);
            $length = $this->stringNumberToFloatWithCorrectDecimalSeparator($row[6]);

            $orderDate = $row[7];
            $cuttingMethod = $row[8];
            $orderNumber = $row[9];

            $customer = $row[10];
            $type = $row[11];
            $rollWidth = (float) $row[12];
            $location = $row[13];
            $registrationNumber = $row[14];

            // TODO: Do not hard code height
            $finishedGood = new Doormat($productName, $width, $length, 0, $material, $brand);

            if ($cuttingMethod === 'Batch')
            {
                // TODO: Do not hard code 1500 length
                $resource = new Roll(uniqid(), $rollWidth, 1500, $productName, $material);
            }
            elseif ($cuttingMethod === 'Coupage')
            {
                $resource = new Coupage($material, $length, $width);
            }
            else
            {
                throw new InvalidOrderImporterOperationException("Cutting method should be batch or coupage");
            }

            $orderLine = new OrderLine($quantity, $finishedGood, $resource, $customer, $orderNumber, $orderDate, $description, $type, null, $location, $registrationNumber);

            if ($width > $rollWidth && $length > $rollWidth && $brand != 'Ondervloer')
            {
                $this->failedOrderLines[]["orderLine"] = $orderLine;
                $this->failedOrderLines[]["errorMessage"] = "Width and length of doormat sales order are larger than the specified roll width";
            }
            else {
                $salesOrderLines[] = $orderLine;
            }

            $i++;
        }

        $this->orderLines = $salesOrderLines;
    }

    private function stringNumberToFloatWithCorrectDecimalSeparator(string $stringNumber): float
    {
        return (float) str_replace(",", ".", $stringNumber);
    }

    private function isExcelRowEmpty($row): bool
    {
        return ($row[0] == null) && ($row[1] == null) && ($row[2] == null) && ($row[3] == null) && ($row[4] == null) && ($row[5] == null)
                && ($row[6] == null) && ($row[7] == null) && ($row[8] == null) && ($row[9] == null) && ($row[10] == null)
                && ($row[11] == null);
    }

    private function processDuplicateOrderNumbersFromOrderLines()
    {
        $orderNumbers = [];
        foreach ($this->orderLines as $orderLine)
        {
            $orderNumbers[] = $orderLine->orderNumber();
        }

        $uniqueOrderNumbers = array_unique($orderNumbers);

        $parsedSalesOrderLines = [];

        foreach ($uniqueOrderNumbers as $uniqueOrderNumber)
        {
            $orderLinesWithSameOrderNumber = [];

            foreach ($this->orderLines as $orderLine)
            {
                if ($orderLine->orderNumber() == $uniqueOrderNumber)
                {
                    $orderLinesWithSameOrderNumber[] = $orderLine;
                }
            }


            if (sizeof($orderLinesWithSameOrderNumber) > 1)
            {
                for ($i = 0; $i < sizeof($orderLinesWithSameOrderNumber); $i++)
                {
                    $orderNumberParsed = $uniqueOrderNumber."-".$i + 1;
                    $orderLinesWithSameOrderNumber[$i]->changeOrderNumber($orderNumberParsed);
                }
            }


            $parsedSalesOrderLines = array_merge($parsedSalesOrderLines, $orderLinesWithSameOrderNumber);
        }

        $this->orderLines = $parsedSalesOrderLines;
    }

    private function processMultipleQuantitiesFromOrderLines()
    {
        $parsedSalesOrderLines = [];

        foreach ($this->orderLines as $orderLine)
        {
            $quantity = $orderLine->quantity();
            $orderNumber = $orderLine->orderNumber();

            if ($quantity > 1)
            {
                for ($i = 0; $i < $quantity; $i++)
                {

                    $parsedOrderNumber = $orderNumber."-".$i + 1;

                    $orderLine = clone $orderLine;
                    $orderLine->changeOrderNumber($parsedOrderNumber);

                    $parsedSalesOrderLines[] = $orderLine;
                }
            } else {
                $parsedSalesOrderLines[] = $orderLine;
            }
        }

        $this->orderLines = $parsedSalesOrderLines;
    }
}
