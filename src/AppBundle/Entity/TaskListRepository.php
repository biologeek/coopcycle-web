<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Task;
use AppBundle\Entity\TaskCollectionItem;
use FOS\UserBundle\Model\UserInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;

class TaskListRepository extends EntityRepository
{
    public function findOneByTaskAndUser(Task $task, UserInterface $user)
    {
        // SELECT *
        // FROM task_list
        // JOIN task_collection_item ON task_list.id = task_collection_item.parent_id
        // WHERE task_collection_item.task_id = :task AND courier_id = :user
        $qb = $this->createQueryBuilder('tl')
            ->join(TaskCollectionItem::class, 'tci', Expr\Join::WITH, 'tci.parent = tl.id')
            ->andWhere('tci.task = :task')
            ->andWhere('tl.courier = :user')
            ->setParameter('task', $task)
            ->setParameter('user', $user);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findOneByUserAndDate(UserInterface $user, \DateTime $date)
    {
        $qb = $this->createQueryBuilder('o')
            ->andWhere('o.courier = :user')
            ->andWhere('DATE(o.date) = :date')
            ->setParameter('user', $user)
            ->setParameter('date', $date->format('Y-m-d'))
            ;

        return $qb->getQuery()->getOneOrNullResult();
    }
}
