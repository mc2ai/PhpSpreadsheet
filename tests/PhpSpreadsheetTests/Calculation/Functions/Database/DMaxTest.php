<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Database;

use PhpOffice\PhpSpreadsheet\Calculation\Database\DMax;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class DMaxTest extends SetupTeardownDatabases
{
    /**
     * @dataProvider providerDMax
     *
     * @param mixed $expectedResult
     * @param mixed $database
     * @param mixed $field
     * @param mixed $criteria
     */
    public function testDirectCallToDMax($expectedResult, $database, $field, $criteria): void
    {
        $result = DMax::evaluate($database, $field, $criteria);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-12);
    }

    /**
     * @dataProvider providerDMax
     *
     * @param mixed $expectedResult
     * @param mixed $database
     * @param mixed $field
     * @param mixed $criteria
     */
    public function testDMaxAsWorksheetFormula($expectedResult, $database, $field, $criteria): void
    {
        $this->prepareWorksheetWithFormula('DMAX', $database, $field, $criteria);

        $result = $this->getSheet()->getCell(self::RESULT_CELL)->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-12);
    }

    public function providerDMax(): array
    {
        return [
            [
                96,
                $this->database1(),
                'Profit',
                [
                    ['Tree', 'Height', 'Height'],
                    ['=Apple', '>10', '<16'],
                    ['=Pear', null, null],
                ],
            ],
            [
                340000,
                $this->database2(),
                'Sales',
                [
                    ['Quarter', 'Area'],
                    [2, 'North'],
                ],
            ],
            [
                460000,
                $this->database2(),
                'Sales',
                [
                    ['Sales Rep.', 'Quarter'],
                    ['Carol', '>1'],
                ],
            ],
            'omitted field name' => [
                ExcelError::VALUE(),
                $this->database1(),
                null,
                $this->database1(),
            ],
            'field column number okay' => [
                18,
                $this->database1(),
                2,
                $this->database1(),
            ],
            /* Excel seems to return #NAME? when column number
               is too high or too low. This makes so little sense
               to me that I'm not going to bother coding that up,
               content to return #VALUE! as an invalid name would */
            'field column number too high' => [
                ExcelError::VALUE(),
                $this->database1(),
                99,
                $this->database1(),
            ],
            'field column number too low' => [
                ExcelError::VALUE(),
                $this->database1(),
                0,
                $this->database1(),
            ],
        ];
    }
}
