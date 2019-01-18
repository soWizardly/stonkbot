<?php


namespace App\Commands;


use Doctrine\ORM\EntityManager;
use Slack\ChannelInterface;

class KarmaCommand extends Command
{

    /**
     * The name of the command, or an array of aliases
     * @return string|array
     */
    public function command()
    {
        return ['addpoint', 'removepoint', 'karma', 'scoreboard'];
    }

    /**
     * Run the command on the specified channel.
     * @param ChannelInterface $channel
     * @param array $message The text the user said, exploded by space.
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function run(ChannelInterface $channel, $message)
    {
        /* @var $entityManager EntityManager */
        $entityManager = \Container::make(EntityManager::class);
        $cmd = str_replace('.', '', $message[0]);


        if ($cmd == 'scoreboard') {
            var_dump('ok');
            $qb = $entityManager->createQueryBuilder();
            $query = $qb->select([
                'u',
                $qb->expr()->diff('u.plus', 'u.minus') . ' as total'
            ])
                ->from('UserKarma', 'u')
                ->orderBy('total', 'DESC')
                ->setMaxResults(5)->getQuery();
            try {
                $result = $query->getArrayResult();
                $scoreboard = '';
                $i = 1;
                foreach ($result as $item) {
                    $scoreboard .= $i . '. ' . $item[0]['name'] . ': +' . $item[0]['plus'] . '/-' . $item[0]['minus'] . ' ' . ($item[0]['plus'] - $item[0]['minus']);
                    if ($i != 5) {
                        $scoreboard .= ' | ';
                    }
                    $i++;
                }
                $message = $this->client->getMessageBuilder()
                    ->setText($scoreboard)
                    ->setChannel($channel)
                    ->create();
                $this->client->postMessage($message);
            } catch (\Exception $e) {
                var_dump($e->getMessage());
            }

            return;
        }

        $userKarma = $entityManager->getRepository('UserKarma')->findBy([
            'name' => $message[1]
        ]);
        if (empty($userKarma)) {
            $userKarma = new \UserKarma();
            $userKarma->setName($message[1]);
            $userKarma->setPlus(0);
            $userKarma->setMinus(0);
        } else {
            $userKarma = $userKarma[0];
        }
        
        if ($cmd == 'addpoint') {
            $userKarma->setPlus($userKarma->getPlus() + 1);
        }
        if ($cmd == 'removepoint') {
            $userKarma->setMinus($userKarma->getMinus() + 1);
        }

        $message = $this->client->getMessageBuilder()
            ->setText($userKarma->getName() . ": +" . $userKarma->getPlus() . "/-" . $userKarma->getMinus() . " " . ($userKarma->getPlus() - $userKarma->getMinus()))
            ->setChannel($channel)
            ->create();
        $this->client->postMessage($message);

        $entityManager->persist($userKarma);
        $entityManager->flush();
    }

    public function description(): string
    {
        return 'Scoreboard for plebs';
    }
}