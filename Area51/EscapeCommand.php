<?php declare(strict_types = 1);

namespace Area51;

use Area51\Api\RobotApiClient;
use Area51\Api\RobotOfflineException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;

#[AsCommand(
    name: 'area51:escape',
    description: 'Attempt to escape with robot',
    hidden: false,
    aliases: ['escape']
)]
class EscapeCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $extraction = new RobotExtraction(new RobotApiClient(HttpClient::create()));

        try
        {
            $extracted = $extraction->extractRobot();
        }
        catch (RobotOfflineException $e)
        {
            $output->writeln("Robot is offline");

            return Command::FAILURE;
        }

        if ($extracted)
        {
            $output->writeln("Robot saved");

            return Command::SUCCESS;
        }

        $output->writeln("Robot destroyed");

        return Command::FAILURE;
    }
}