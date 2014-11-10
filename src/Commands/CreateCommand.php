<?php namespace Groovey\Migration\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Groovey\Migration\Models\Migration;
use Groovey\Migration\Adapters\Adapter;

class CreateCommand extends Command
{
    private $adapter;

    public function __construct(Adapter $adapter)
    {
        parent::__construct();

        $this->adapter = $adapter;

    }

    protected function configure()
    {
        $this
            ->setName('migration:create')
            ->setDescription('Creates a .yml migration file under database/migrations.')
             ->addArgument(
                'param',
                InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                'The database task description.'
            )
        ;
    }

    private function getDirectory()
    {
        return getcwd().'/database/migrations';
    }

    private function getFilename(InputInterface $input)
    {

        $argument = $this->getArguments($input);
        $version  = $this->getVersion();

        return $version . '_' . $argument . '.yml';
    }

    private function getVersion()
    {

        $finder = new Finder();
        $finder->files()->in($this->getDirectory());

        $versions = ['000'];

        foreach ($finder as $file) {

            $filename = $file->getRelativePathname();

            list($version, $description) = explode('_', $filename);
            $versions[] =  $version;
        }

        $last = end($versions);
        $new  = (int) $last + 1;

        return str_pad($new, 3, "0", STR_PAD_LEFT);
    }

    private function getArguments(InputInterface $input)
    {
        $argument = '';

        if ($names = $input->getArgument('param')) {
            $argument .= implode('_', $names);
        }

        return trim($argument);
    }

    private function getTemplate(InputInterface $input)
    {

        $names = $input->getArgument('param');

$yaml = <<<YML
# Run the migration
UP:


# Reverse the migration
DOWN:


YML;

        return $yaml;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $directory = $this->getDirectory();
        $filename  = $this->getFilename($input);
        $data      = $this->getTemplate($input);

        file_put_contents($directory . '/' . $filename, $data);
    }

}
