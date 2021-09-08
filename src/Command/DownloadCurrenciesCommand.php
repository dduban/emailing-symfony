<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Currency\CurrencyManager;

class DownloadCurrenciesCommand extends Command
{
    protected static $defaultName = 'app:download-currencies';
    protected static $defaultDescription = 'Command to download currencies';

    private CurrencyManager $currencyManager;

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
        ;
    }

    public function __construct(CurrencyManager $currencyManager){
        $this->currencyManager = $currencyManager;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->currencyManager->synchronize();

        $io->success('Currencies downloaded successfully');

        return 0;

    }
}
