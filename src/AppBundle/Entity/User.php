<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $boss;

    /**
     * @ORM\Column(type="string")
     */
    protected $border;

    /**
     * Set boss
     *
     * @param string $boss
     *
     * @return User
     */
    public function setBoss($boss)
    {
        $this->boss = $boss;

        return $this;
    }

    /**
     * Get boss
     *
     * @return string
     */
    public function getBoss()
    {
        return $this->boss;
    }

    /**
     * Set border
     *
     * @param string $border
     *
     * @return User
     */
    public function setBorder($border)
    {
        $this->border = $border;

        return $this;
    }

    /**
     * Get border
     *
     * @return string
     */
    public function getBorder()
    {
        return $this->border;
    }
}
