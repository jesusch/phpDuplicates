<?php

namespace jesusch\phpDuplicates\Entity;

/**
 * @Entity 
 * @Table(name="files")
 **/
class File
{
	
	/**
	 * 
	 * @ManyToOne(targetEntity="MD5Sum")
	 * @var MD5Sum
	 */
	protected $md5sum;
	
	/** 
	 * @Id 
	 * @Column(type="string") 
	 **/
    protected $name;


    public function getMd5sum()
    {
    	return $this->md5sum;
    }
    
    public function setMd5sum(MD5Sum $md5sum)
    {
    	$this->md5sum = $md5sum;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }

}