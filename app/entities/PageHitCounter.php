<?php


namespace entities;

use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

#[ORM\Entity]
#[ORM\Table(name: 'page_hit_counter')]
#[ORM\HasLifecycleCallbacks]
class PageHitCounter
{
    #[ORM\Id, ORM\Column(type: 'integer'), ORM\GeneratedValue]
    private int|null $id = null;

    #[ORM\Column(type: 'string')]
    private string|null $ip = null;

    #[ORM\Column(type: 'string')]
    private string|null $city = null;

    #[ORM\Column(type: 'string')]
    private string|null $device = null;

    #[ORM\Column(type: 'datetime')]
    private string|DateTime|null $created_at = null;

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $this->created_at = new DateTime();
    }

    public function getCreatedAd(): ?string
    {
        return $this->created_at->format("m.d.Y H:i");
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): self
    {
        $this->ip = $ip;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getDevice(): ?string
    {
        return $this->device;
    }

    public function setDevice(?string $device): self
    {
        $this->device = $device;
        return $this;
    }

    public function getUniqueVisitsByHoursForToday($entityManager)
    {
        /* @var $entityManager EntityManager */
        $currentDate = new DateTime();

        $currentDate->setTime(0, 0, 0);

        $endDate = clone $currentDate;
        $endDate->modify('+1 day')->modify('-1 second');

        $sql = "SELECT COUNT(DISTINCT ip) AS visits, HOUR(created_at) AS hour
        FROM page_hit_counter
        WHERE created_at BETWEEN :startDate AND :endDate
        GROUP BY hour";

        $rsm = new ResultSetMappingBuilder($entityManager);
        $rsm->addScalarResult('visits', 'visits', 'integer');
        $rsm->addScalarResult('hour', 'hour', 'integer');

        $query = $entityManager->createNativeQuery($sql, $rsm);

        $query->setParameter('startDate', $currentDate->format('Y-m-d H:i:s'))
            ->setParameter('endDate', $endDate->format('Y-m-d H:i:s'));

        return $query->getResult();

//        $qb = $entityManager->createQueryBuilder();
//
//        $query = $qb->select('COUNT(DISTINCT phc.ip) AS visits, DATE_FORMAT(phc.created_at, "%H") AS hour')
//            ->from(PageHitCounter::class, 'phc')
//            ->where($qb->expr()->between('phc.created_at', ':startDate', ':endDate'))
//            ->groupBy('hour')
//            ->setParameter('startDate', $currentDate->format("Y-m-d H:i:s"))
//            ->setParameter('endDate', $endDate->format("Y-m-d H:i:s"))
//            ->getQuery();
//
//        $results = $query->getResult();
//
//        $visitsPerHour = [];
//        foreach ($results as $result) {
//            $visitsPerHour[$result['hour']] = $result['visits'];
//        }
//
//        return $visitsPerHour;
//        foreach ($visitsPerHour as $hour => $visits) {
//            echo "Час $hour: $visits уникальных посещений\n";
//        }
    }

    public function getVisitsByCity($entityManager)
    {
        /* @var $entityManager EntityManager */
        $currentDate = new DateTime();

        $currentDate->setTime(0, 0, 0);

        $endDate = clone $currentDate;
        $endDate->modify('+1 day')->modify('-1 second');

        $sql = "SELECT city, COUNT(ip) AS visits
        FROM page_hit_counter
        GROUP BY city;";

        $rsm = new ResultSetMappingBuilder($entityManager);
        $rsm->addScalarResult('visits', 'visits', 'integer');
        $rsm->addScalarResult('city', 'city', 'string');

        $query = $entityManager->createNativeQuery($sql, $rsm);

        return $query->getResult();
    }
}