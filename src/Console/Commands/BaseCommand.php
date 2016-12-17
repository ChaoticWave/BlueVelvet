<?php namespace ChaoticWave\BlueVelvet\Console\Commands;

use ChaoticWave\BlueVelvet\Utility\Json;
use ChaoticWave\BlueVelvet\Shapers\XmlShape;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Adds some additional functionality to the Command class
 */
abstract class BaseCommand extends Command
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type bool Overridden --quiet indicator
     */
    protected $wasQuiet = false;
    /**
     * @type string The output format
     */
    protected $format;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Handle the work
     */
    abstract public function handle();

    /**
     * Hijack execute to turn off quiet
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //  No being quiet allowed...
        if (true === ($this->wasQuiet = (OutputInterface::VERBOSITY_QUIET === $output->getVerbosity()))) {
            $output->setVerbosity(OutputInterface::VERBOSITY_NORMAL);
        }

        //  Get the output format, if any
        if ($input->hasOption('format')) {
            if (empty($this->format = strtolower(trim($this->option('format'))))) {
                $this->format = null;
            }
        }

        //  Do the execute
        $_result = parent::execute($input, $output);

        //  Restore verbosity and return
        $this->wasQuiet && $output->setVerbosity(OutputInterface::VERBOSITY_NORMAL);

        return $_result;
    }

    /**
     * Using the --format option (if specified) format an array of data for output in that format
     *
     * @param array       $array
     * @param bool        $pretty
     * @param string|null $rootNode Enclose transformed data inside a $rootNode
     * @param string      $type     Inner node name prefix. Defaults to 'item'. Used only for XML
     *
     * @return string
     */
    protected function formatArray(array $array, $pretty = true, $rootNode = 'root', $type = 'item')
    {
        switch ($this->format) {
            case 'json':
                return Json::encode($array, ($pretty ? JSON_PRETTY_PRINT : 0) | JSON_UNESCAPED_SLASHES);

            case 'xml':
                return XmlShape::transform($array, ['root' => $rootNode, 'item-type' => $type, 'ugly' => !$pretty]);
        }

        //  Default is to use print_r format
        return print_r($array, true);
    }
}
