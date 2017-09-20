<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="seed_data")
 */
class SeedData
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
     * @ORM\Column(type="float")
     */
    protected $oil_yield;

    /**
     * @ORM\Column(type="float")
     */
    protected $oilmeal_yield;

    /**
     * @ORM\Column(type="float")
     */
    protected $oil_price;

    /**
     * @ORM\Column(type="float")
     */
    protected $oilmeal_price;

    /**
     * @ORM\Column(type="float")
     */
    protected $processing_cost;

    /**
     * @ORM\Column(type="float")
     */
    protected $usdrub;

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
     * @return SeedData
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
     * Set oilYield
     *
     * @param float $oilYield
     *
     * @return SeedData
     */
    public function setOilYield($oilYield)
    {
        $this->oil_yield = $oilYield;

        return $this;
    }

    /**
     * Get oilYield
     *
     * @return float
     */
    public function getOilYield()
    {
        return $this->oil_yield;
    }

    /**
     * Set oilmealYield
     *
     * @param float $oilmealYield
     *
     * @return SeedData
     */
    public function setOilmealYield($oilmealYield)
    {
        $this->oilmeal_yield = $oilmealYield;

        return $this;
    }

    /**
     * Get oilmealYield
     *
     * @return float
     */
    public function getOilmealYield()
    {
        return $this->oilmeal_yield;
    }

    /**
     * Set oilPrice
     *
     * @param float $oilPrice
     *
     * @return SeedData
     */
    public function setOilPrice($oilPrice)
    {
        $this->oil_price = $oilPrice;

        return $this;
    }

    /**
     * Get oilPrice
     *
     * @return float
     */
    public function getOilPrice()
    {
        return $this->oil_price;
    }

    /**
     * Set processingCost
     *
     * @param float $processingCost
     *
     * @return SeedData
     */
    public function setProcessingCost($processingCost)
    {
        $this->processing_cost = $processingCost;

        return $this;
    }

    /**
     * Get processingCost
     *
     * @return float
     */
    public function getProcessingCost()
    {
        return $this->processing_cost;
    }

    /**
     * Set usdrub
     *
     * @param float $usdrub
     *
     * @return SeedData
     */
    public function setUsdrub($usdrub)
    {
        $this->usdrub = $usdrub;

        return $this;
    }

    /**
     * Get usdrub
     *
     * @return float
     */
    public function getUsdrub()
    {
        return $this->usdrub;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return SeedData
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

    /**
     * Set oilmealPrice
     *
     * @param float $oilmealPrice
     *
     * @return SeedData
     */
    public function setOilmealPrice($oilmealPrice)
    {
        $this->oilmeal_price = $oilmealPrice;

        return $this;
    }

    /**
     * Get oilmealPrice
     *
     * @return float
     */
    public function getOilmealPrice()
    {
        return $this->oilmeal_price;
    }
}
