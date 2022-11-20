<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * PersonRepository
 * @extends EntityRepository<Person>
 */
class PersonRepository extends EntityRepository
{
    public function getNumberInDateRange(\DateTime $start, \DateTime $end)
    {
        $qb = $this->createQueryBuilder('e')
            ->select('COUNT(DISTINCT e.id)')
            ->innerJoin('e.roles', 'r')
            ->innerJoin('r.show', 's')
            ->innerJoin('s.performances', 'p')
            ->andWhere('p.start_at < :end')
            ->andWhere('p.repeat_until >= :start')
            ->setParameter('start', $start)
            ->setParameter('end', $end);

        $result = $qb->getQuery()->getOneOrNullResult();

        return current($result);
    }

    /**
     * findCanonicalPerson
     *
     * Find the canonical name for a Person, i.e. if the person is known on
     * Camdram by multiple names, return the preferred name.
     */
    public function findCanonicalPerson($name)
    {
        /* Use the latest instance of this name by ordering by id field in
         * descending order. Almost always we want the most recent person,
         * and to date we've not had two simultaneous users with the same name...
         */
        $person = $this->createQueryBuilder('p')
            ->leftJoin('p.mapped_to', 'm')
            ->where('p.name = :name')
            ->setParameter('name', $name)
            ->orderBy('p.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();
        if ($person && $person->getMappedTo() instanceof Person) {
            return $person->getMappedTo();
        } else {
            return $person;
        }
    }

     /**
     * Appears on /people
     *
     * Returns $limit people who are involved with shows between $start and $end,
     * sorted by their overall show count
     */
    public function getPeopleInDateRange(\DateTime $start, \DateTime $end, $limit)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT person, role, s, COUNT(role) AS HIDDEN range_show_count,
                (SELECT COUNT(sub_r) FROM Acts\\CamdramBundle\\Entity\\Role sub_r WHERE sub_r.person = person) AS HIDDEN total_show_count
            FROM Acts\\CamdramBundle\\Entity\\Person person
            INNER JOIN person.roles AS role INNER JOIN role.show AS s INNER JOIN s.performances AS perf
            WHERE perf.start_at < :end AND perf.repeat_until >= :start
            GROUP BY person HAVING range_show_count > 0
            ORDER BY total_show_count ASC, person.id DESC
        ');

        return $query->setParameter('start', $start)
                ->setParameter('end', $end)
                ->setMaxResults($limit)
                ->getResult();
    }
}
