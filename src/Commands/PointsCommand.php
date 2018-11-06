<?php


namespace Commands;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Slack\ChannelInterface;

class PointsCommand extends Command
{

    /**
     * The name of the command, or an array of aliases
     * @return string|array
     */
    public function command()
    {
        return ['addpoint', 'removepoint'];
    }

    /**
     * Run the command on the specified channel.
     * @param ChannelInterface $channel
     * @param array $message The text the user said, exploded by space.
     * @return mixed
     */
    public function run(ChannelInterface $channel, $message)
    {
        /* @var $entityManager EntityManager */
        $entityManager = \BagOfDooDoo::make(EntityManager::class);
        $cmd = str_replace('.', '', $message[0]);
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
}