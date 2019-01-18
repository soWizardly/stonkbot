<?php


namespace App\Commands;


use App\Communication\Message;
use App\Models\UserKarma;
use Doctrine\ORM\EntityManager;

class UserKarmaCommand extends Command
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
     * @param Message $message The text the user said, exploded by space.
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function run(Message $message): Message
    {
        /* @var $entityManager EntityManager */
        $entityManager = resolve(EntityManager::class);
        $msg = explode(' ', $message->getMessage());
        $cmd = str_replace('.', '', $msg[0]);

        if ($cmd == 'scoreboard') {
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
                $message->setMessage($scoreboard);
                return $message;
            } catch (\Exception $e) {
                var_dump($e->getMessage());
            }
        }

        $userKarma = $entityManager->getRepository('UserKarma')->findBy([
            'name' => $msg[1]
        ]);
        if (empty($userKarma)) {
            $userKarma = new UserKarma();
            $userKarma->setName($msg[1]);
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

        $message->setMessage($userKarma->getName() . ": +" . $userKarma->getPlus() . "/-" . $userKarma->getMinus() . " " . ($userKarma->getPlus() - $userKarma->getMinus()));
        $entityManager->persist($userKarma);
        $entityManager->flush();
        return $message;
    }

    public function description(): string
    {
        return 'Scoreboard for plebs';
    }
}