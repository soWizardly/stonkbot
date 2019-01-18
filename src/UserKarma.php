<?php
namespace App;
/**
 * @Entity @Table(name="user_karma",uniqueConstraints={@UniqueConstraint(name="name_unique",columns={"name"})})
 **/
class UserKarma
{
    /** @Id @Column(type="integer") @GeneratedValue * */
    protected $id;

    /** @Column(type="string") * */
    protected $name;

    /** @Column(type="integer") * */
    protected $plus;

    /** @Column(type="integer") * */
    protected $minus;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getPlus()
    {
        return $this->plus;
    }

    public function setPlus($plus)
    {
        $this->plus = $plus;
    }

    public function getMinus()
    {
        return $this->minus;
    }

    public function setMinus($minus)
    {
        $this->minus = $minus;
    }
}