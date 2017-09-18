<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="deal")
 */
class Deal
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $uid;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $deal_done;

    /**
     * @ORM\Column(type="float")
     */
    protected $seed_price;

    /**
     * @ORM\Column(type="float")
     */
    protected $delivery_price;

    /**
     * @ORM\Column(type="float")
     */
    protected $shipment_price;

    /**
     * @ORM\Column(type="float")
     */
    protected $storage_price;

    /**
     * @ORM\Column(type="float")
     */
    protected $oil_content;

    /**
     * @ORM\Column(type="datetimetz")
     */
    protected $updated_at;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set uid
     *
     * @param integer $uid
     *
     * @return Deal
     */
    public function setUid($uid)
    {
        $this->uid = $uid;

        return $this;
    }

    /**
     * Get uid
     *
     * @return integer
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Set dealDone
     *
     * @param boolean $dealDone
     *
     * @return Deal
     */
    public function setDealDone($dealDone)
    {
        $this->deal_done = $dealDone;

        return $this;
    }

    /**
     * Get dealDone
     *
     * @return boolean
     */
    public function getDealDone()
    {
        return $this->deal_done;
    }

    /**
     * Set seedPrice
     *
     * @param float $seedPrice
     *
     * @return Deal
     */
    public function setSeedPrice($seedPrice)
    {
        $this->seed_price = $seedPrice;

        return $this;
    }

    /**
     * Get seedPrice
     *
     * @return float
     */
    public function getSeedPrice()
    {
        return $this->seed_price;
    }

    /**
     * Set deliveryPrice
     *
     * @param float $deliveryPrice
     *
     * @return Deal
     */
    public function setDeliveryPrice($deliveryPrice)
    {
        $this->delivery_price = $deliveryPrice;

        return $this;
    }

    /**
     * Get deliveryPrice
     *
     * @return float
     */
    public function getDeliveryPrice()
    {
        return $this->delivery_price;
    }

    /**
     * Set shipmentPrice
     *
     * @param float $shipmentPrice
     *
     * @return Deal
     */
    public function setShipmentPrice($shipmentPrice)
    {
        $this->shipment_price = $shipmentPrice;

        return $this;
    }

    /**
     * Get shipmentPrice
     *
     * @return float
     */
    public function getShipmentPrice()
    {
        return $this->shipment_price;
    }

    /**
     * Set storagePrice
     *
     * @param float $storagePrice
     *
     * @return Deal
     */
    public function setStoragePrice($storagePrice)
    {
        $this->storage_price = $storagePrice;

        return $this;
    }

    /**
     * Get storagePrice
     *
     * @return float
     */
    public function getStoragePrice()
    {
        return $this->storage_price;
    }

    /**
     * Set oilContent
     *
     * @param float $oilContent
     *
     * @return Deal
     */
    public function setOilContent($oilContent)
    {
        $this->oil_content = $oilContent;

        return $this;
    }

    /**
     * Get oilContent
     *
     * @return float
     */
    public function getOilContent()
    {
        return $this->oil_content;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Deal
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }
}
