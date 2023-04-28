<?php

namespace Vagebnd\EnvatoThemecheckCli\Commands;

use Composer\Autoload\ClassLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Vagebnd\EnvatoThemecheckCli\Support\ThemeCheck;

class RunChecks extends Command
{
    protected static $defaultName = 'run-checks';

    protected static $defaultDescription = 'Run the envato theme check against a theme or files.';

    protected ClassLoader $classLoader;

    public function __construct(ClassLoader $classLoader)
    {
        parent::__construct();

        // Pass in the classloader so we can use the classmap to find checks.
        $this->classLoader = $classLoader;
    }

    protected function configure(): void
    {
        $this->addArgument('source', InputArgument::OPTIONAL, 'The input file or directory to check.');
    }

    /**
     * Execute the command
     *
     * @return int 0 if everything went fine, or an exit code.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = $input->getArgument('source') ?? getcwd();

        $results = ThemeCheck::make($source)->run($this->classLoader);
        $io = new SymfonyStyle($input, $output);

        $results
            ->filter(fn ($result) => $result->hasErrors())
            ->each(function ($result) use ($io) {
                $result->getErrors()->each(fn ($error) => $io->error($error->getMessage()));
                $result->getWarnings()->each(fn ($error) => $io->warning($error->getMessage()));
                $result->getInfos()->each(fn ($error) => $io->info($error->getMessage()));
            });

        return Command::SUCCESS;
    }
}
