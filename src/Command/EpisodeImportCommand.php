<?php

namespace App\Command;

use App\Services\EpisodeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'episode:import',
    description: 'Load episodes to project',
)]
class EpisodeImportCommand extends Command
{
    public function __construct(private HttpClientInterface $client, private EntityManagerInterface $entityManager, private CacheInterface $cache)
    {
        parent::__construct();
    }

    protected function configure(): void
    {

//        $this
//            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $service = new EpisodeService($this->client, $this->entityManager, $this->cache);
        $result = $service->import();

        $io->success('Import episodes. All: '.$result['all'].', updated: '.$result['updated']);

        return Command::SUCCESS;
    }
}
