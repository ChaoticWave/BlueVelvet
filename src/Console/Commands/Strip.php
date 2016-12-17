<?php namespace ChaoticWave\BlueVelvet\Console\Commands;

use ChaoticWave\BlueVelvet\Enums\GlobFlags;
use ChaoticWave\BlueVelvet\Traits\ConsoleHelper;
use ChaoticWave\BlueVelvet\Utility\Disk;

class Strip extends BaseCommand
{
    use ConsoleHelper;

    //******************************************************************************
    //* Members
    //******************************************************************************

    /** @inheritdoc */
    protected $signature = 'bv:strip {pattern : A file glob pattern} {--pretty : If present, the resulting code will be prettified} {--recursive : Recurse into subdirectories}';
    /** @inheritdoc */
    protected $description = 'Strips comments from a PHP file, optionally prettifying';
    /**
     * @var string The php-cs-fixer path
     */
    protected $fixer;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Handle the work
     */
    public function handle()
    {
        $this->writeHeader();

        $_pattern = $this->argument('pattern');
        $_pretty = $this->option('pretty');
        $_flags = GlobFlags::GLOB_NODOTS | GlobFlags::GLOB_PATH;
        $this->option('recursive') and $_flags |= GlobFlags::GLOB_RECURSE;

        if (false === ($_files = Disk::glob($_pattern, $_flags))) {
            $this->writeln('The pattern "<comment>' . $_pattern . '</comment>" caused an error.');

            return -1;
        }

        if (empty($_files)) {
            $this->writeln('No files were found that match the pattern "<comment>' . $_pattern . '</comment>"');

            return 1;
        }

        //  Find php-cs-fixer
        if (file_exists($_fixer = getcwd() . '/vendor/bin/php-cs-fixer')) {
            $this->fixer = $_fixer;
        }

        //  Find php-cs-fixer
        if ($_pretty && !$this->fixer) {
            $this->writeln('The <comment>php-cs-fixer</comment> is not installed or not executable. No prettifying.');
            $_pretty = false;
        }

        foreach ($_files as $_file) {
            $this->write($_file . ': ');
            if (false === $this->stripFile($_file, $_pretty)) {
                $this->writeln('error');
            }
        }

        return 0;
    }

    /**
     * @param string $file
     * @param bool   $pretty
     *
     * @return bool|int
     */
    protected function stripFile($file, $pretty = false)
    {
        if (!is_readable($file)) {
            $this->error('File not readable');

            return false;
        }

        if (false === ($_contents = file_get_contents($file))) {
            $this->error('Error reading file');

            return false;
        }

        if ($pretty) {
            $_cmd = $this->fixer . ' fix --quiet ' . escapeshellarg(realpath($file)) . ' ';
            $this->write(' (' . $_cmd . ') ');
            exec($_cmd, $_output, $_return);

            if (0 === $_return) {
                $_contents = $_output;
            }
        }

        if (false !== file_put_contents($file . '.strip', $this->stripComments($_contents))) {
            $this->comment('wrote .strip');
        }

        return true;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    protected function stripComments($string)
    {
        $_stripped = null;

        $_search = array(T_COMMENT);
        defined('T_DOC_COMMENT') && $_search[] = T_DOC_COMMENT;
        defined('T_ML_COMMENT') && $_search[] = T_ML_COMMENT;
        is_array($string) && $string = implode(PHP_EOL, $string);

        $_tokens = token_get_all($string);

        foreach ($_tokens as $_token) {
            if (is_array($_token)) {
                if (in_array($_token[0], $_search)) {
                    continue;
                }

                $_token = $_token[1];
            }

            $_stripped .= $_token;
        }

        return $_stripped;
    }
}
