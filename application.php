<?php

use App\Auction;
use App\Model\Buyer;
use App\Model\Session;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Cursor;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\SingleCommandApplication;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validation;

require_once __DIR__ . '/vendor/autoload.php';

(new SingleCommandApplication())
    ->setCode(function (InputInterface $input, OutputInterface $output) {
        $helper = $this->getHelper('question');
        $validator = Validation::createValidator();

        // Reserve price
        do {
            $reservePrice = $helper->ask($input, $output, new Question('Reserve price: '));

            $violations = $validator->validate($reservePrice, [
                new Type(type: 'numeric'),
                new NotBlank(),
            ]);

            foreach ($violations as $violation) {
                (new Cursor($output))
                    ->moveToPosition(1, 1)
                    ->clearOutput();

                $output->writeln('<error>' . $violation->getMessage() . '</error>');
            }
        } while (count($violations) > 0);

        $session = new Session($reservePrice);
        $auction = new Auction($session);

        (new Cursor($output))
            ->moveToPosition(1, 1)
            ->clearOutput();

        // Add buyers
        do {
            $buyers = $session->getBuyers();

            if ($buyers) {
                // list buyers
                $table = new Table($output);
                $table
                    ->setHeaders(['Buyer Name'])
                    ->setRows(array_map(fn (Buyer $buyer) => [$buyer->getName()], $buyers));
                $table->render();
            }

            $buyerName = $helper->ask($input, $output, new Question('Type buyer name (to continue hit enter): '));

            $cleaned = false;

            $violations = $validator->validate($buyerName, [
                new Type(type: 'alpha'),
            ]);

            foreach ($violations as $violation) {
                (new Cursor($output))
                    ->moveToPosition(1, 1)
                    ->clearOutput();

                $cleaned = true;

                $output->writeln('<error>' . $violation->getMessage() . '</error>');
            }

            if ($buyerName && count($violations) === 0) {
                $session->addBuyer(new Buyer($buyerName));

                (new Cursor($output))
                    ->moveToPosition(1, 1)
                    ->clearOutput();
            } elseif (!$buyerName) {
                foreach ($validator->validate($session->getBuyers(), [
                    new Count(min: 2),
                ]) as $violation) {
                    if (!$cleaned) {
                        (new Cursor($output))
                            ->moveToPosition(1, 1)
                            ->clearOutput();
                    }

                    $output->writeln('<error>' . $violation->getMessage() . '</error>');
                }
            }
        } while ($buyerName || !$session->getBuyers());

        // Bidding
        do {
            $buyers = $session->getBuyers();

            $output->writeln('<comment>Reserve price is <options=bold,underscore>' . $session->getReservePrice() . '</></comment>');
            $table = new Table($output);
            $table
                ->setHeaders(['Buyer Name', 'Bids'])
                ->setRows(array_map(fn (Buyer $buyer) => [
                    $buyer->getName(),
                    implode(', ', $buyer->getBids()),
                ], $buyers));
            $table->render();

            $buyerName = $helper->ask($input, $output, new ChoiceQuestion(
                'Select buyer (to get winner hit enter): ',
                array_merge(['' => ''], array_map(fn (Buyer $buyer) => $buyer->getName(), $buyers)),
            ));

            if ($buyerName) {
                do {
                    $bid = $helper->ask($input, $output, new Question('Bid: '));

                    $violations = $validator->validate($bid, [
                        new Type(type: 'numeric'),
                        new NotBlank(),
                    ]);

                    foreach ($violations as $violation) {
                        $output->writeln('<error>' . $violation->getMessage() . '</error>');
                    }
                } while (count($violations) > 0);

                try {
                    (new Cursor($output))
                        ->moveToPosition(1, 1)
                        ->clearOutput();

                    $auction->bid($auction->getBuyer($buyerName), $bid);
                } catch (Exception $exception) {
                    $output->writeln('<error>' . $exception->getMessage() . '</error>');
                }
            }
        } while ($buyerName);

        // Print winner and price
        try {
            $winner = $auction->winner();
            $winningPrice = $auction->winningPrice($winner);
            $output->writeln('<info>Buyer <options=bold,underscore>' . $winner->getName() . '</> is the winner and has to pay <options=bold,underscore>' . $winningPrice . '</></info>');
        } catch (Exception $exception) {
            $output->writeln('<error>' . $exception->getMessage() . '</error>');
        }

        return Command::SUCCESS;
    })
    ->run();
