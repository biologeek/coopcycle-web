<?php

namespace AppBundle\Entity\Sylius;

use AppBundle\DataType\TsRange;
use AppBundle\Sylius\Order\OrderInterface;
use Gedmo\Timestampable\Traits\Timestampable;

class OrderTimeline
{
    use Timestampable;

    protected $id;

    protected $order;

    /**
     * The time the order is expected to be dropped.
     */
    protected $dropoffExpectedAt;

    /**
     * The time the order is expected to be picked up.
     */
    protected $pickupExpectedAt;

    /**
     * The time the order preparation should start.
     */
    protected $preparationExpectedAt;

    public function getId()
    {
        return $this->id;
    }

    public function getOrder(): ?OrderInterface
    {
        return $this->order;
    }

    public function setOrder(OrderInterface $order): void
    {
        $this->order = $order;
    }

    public function getDropoffExpectedAt()
    {
        return $this->dropoffExpectedAt;
    }

    public function setDropoffExpectedAt(?\DateTime $dropoffExpectedAt)
    {
        $this->dropoffExpectedAt = $dropoffExpectedAt;

        return $this;
    }

    public function getPickupExpectedAt()
    {
        return $this->pickupExpectedAt;
    }

    public function setPickupExpectedAt(\DateTime $pickupExpectedAt)
    {
        $this->pickupExpectedAt = $pickupExpectedAt;

        return $this;
    }

    public function getPreparationExpectedAt()
    {
        return $this->preparationExpectedAt;
    }

    public function setPreparationExpectedAt(\DateTime $preparationExpectedAt)
    {
        $this->preparationExpectedAt = $preparationExpectedAt;

        return $this;
    }

    public static function create(OrderInterface $order, TsRange $range, string $preparationTime, ?string $shippingTime = null)
    {
        $timeline = new self();

        $order->setTimeline($timeline);

        if ('collection' === $order->getFulfillmentMethod()) {

            $preparation = clone $range->getUpper();
            $preparation->sub(date_interval_create_from_date_string($preparationTime));

            $timeline->setPickupExpectedAt($range->getUpper());
            $timeline->setPreparationExpectedAt($preparation);

        } else {

            $pickup = clone $range->getUpper();
            $pickup->sub(date_interval_create_from_date_string($shippingTime));

            $preparation = clone $pickup;
            $preparation->sub(date_interval_create_from_date_string($preparationTime));

            $timeline->setDropoffExpectedAt($range->getUpper());
            $timeline->setPickupExpectedAt($pickup);
            $timeline->setPreparationExpectedAt($preparation);
        }

        return $timeline;
    }
}
