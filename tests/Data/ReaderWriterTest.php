<?php namespace ChaoticWave\BlueVelvet\Tests\Data;

use ChaoticWave\BlueVelvet\Data\LineWriter;
use ChaoticWave\BlueVelvet\Data\ParsingLineReader;
use ChaoticWave\BlueVelvet\Enums\Delimiters;
use ChaoticWave\BlueVelvet\Enums\Escapes;
use ChaoticWave\BlueVelvet\Enums\Wrappers;
use ChaoticWave\BlueVelvet\Tests\TestCase;

class ReaderWriterTest extends TestCase
{
    /**
     * @return \ChaoticWave\BlueVelvet\Data\ParsingLineReader
     */
    protected function getCsvReader()
    {
        return new ParsingLineReader(__DIR__ . '/test-data.csv', null, Delimiters::COMMA, Wrappers::DOUBLE_QUOTE, Escapes::DOUBLED);
    }

    /**
     * @return \ChaoticWave\BlueVelvet\Data\ParsingLineReader
     */
    protected function getTsvReader()
    {
        return new ParsingLineReader(__DIR__ . '/test-data.tsv', null, Delimiters::TAB, Wrappers::DOUBLE_QUOTE, Escapes::DOUBLED);
    }

    /**
     * @return \ChaoticWave\BlueVelvet\Data\ParsingLineReader
     */
    protected function getPsvReader()
    {
        return new ParsingLineReader(__DIR__ . '/test-data.psv', null, Delimiters::PIPE, Wrappers::NONE, Escapes::NONE);
    }

    public function testLineReader()
    {
        $_lines = 0;
        $_reader = $this->getCsvReader();

        foreach ($_reader as $_row) {
            $_lines++;
            $this->assertEquals(9, count($_row));
        }

        $this->assertEquals(14, $_lines);
    }

    public function testReadCsv()
    {
        $_lines = 0;
        $_reader = $this->getCsvReader();

        foreach ($_reader as $_row) {
            $_lines++;
            $this->assertEquals(9, count($_row));
        }

        $this->assertEquals(14, $_lines);
    }

    public function testReadPsv()
    {
        $_lines = 0;
        $_reader = $this->getPsvReader();

        foreach ($_reader as $_row) {
            $_lines++;
            $this->assertEquals(9, count($_row));
        }

        $this->assertEquals(14, $_lines);
    }

    public function testReadTsv()
    {
        $_lines = 0;
        $_reader = $this->getTsvReader();

        /** @noinspection PhpUnusedLocalVariableInspection */
        foreach ($_reader as $_row) {
            $_lines++;
//			echo implode( ', ', $_row ) . PHP_EOL;
        }

//		echo PHP_EOL;
//		echo 'Read ' . $_lines . ' rows (not including header).' . PHP_EOL;

        $this->assertEquals(14, $_lines);
    }

    public function testWriteCsv()
    {
        $_reader = $this->getCsvReader();
        $_keys = $_reader->getKeys();

        $_tsvWriter = new LineWriter(__DIR__ . '/write-test-out-test-data.tsv', $_keys, Delimiters::TAB, Wrappers::DOUBLE_QUOTE, Escapes::DOUBLED);
        $_csvWriter = new LineWriter(__DIR__ . '/write-test-out-test-data.csv', $_keys, Delimiters::COMMA, Wrappers::DOUBLE_QUOTE, Escapes::DOUBLED);
        $_psvWriter = new LineWriter(__DIR__ . '/write-test-out-test-data.psv', $_keys, Delimiters::PIPE, Wrappers::NONE, Escapes::NONE);

        $_lines = 0;

        foreach ($_reader as $_row) {
            $_lines++;
            $_csvWriter->writeRow($_row);
            $_tsvWriter->writeRow($_row);
            $_psvWriter->writeRow($_row);
        }

//		echo PHP_EOL;
//		echo 'Read ' . $_lines . ' rows (not including header).' . PHP_EOL;
//		echo PHP_EOL;
//		echo 'Wrote ' . $_csvWriter->getRowsOut() . ' CSV rows (including header).' . PHP_EOL;
//		echo 'Wrote ' . $_tsvWriter->getRowsOut() . ' TSV rows (including header).' . PHP_EOL;
//		echo 'Wrote ' . $_psvWriter->getRowsOut() . ' PSV rows (including header).' . PHP_EOL;

        $this->assertEquals(14, $_lines);
        $this->assertEquals(14, $_csvWriter->getRowsOut());
        $this->assertEquals(14, $_tsvWriter->getRowsOut());
        $this->assertEquals(14, $_psvWriter->getRowsOut());
    }
}
