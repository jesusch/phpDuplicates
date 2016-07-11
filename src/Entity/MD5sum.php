<?php

namespace jesusch\phpDuplicates\Entity;

/**
 * @Entity
 * @Table(name="md5sums")
 **/
class MD5Sum
{
	/**
	 * @Id
	 * @Column(type="integer")
	 * @GeneratedValue
	 **/
	protected $id;

	/**
	 * @Column(type="string", unique=true)
	 **/
    protected $md5;

    public function getId()
    {
        return $this->name;
    }

    public function getMd5()
    {
    	return $this->md5;
    }

    public function setMd5($md5)
    {
    	$this->md5 = $md5;
    }
}