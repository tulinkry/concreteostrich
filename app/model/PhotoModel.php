<?php

namespace Model;

use Tulinkry\Model\Doctrine\BaseModel;

class PhotoModel extends BaseModel
{
	public function s()
	{
		return $this ->em->createQueryBuilder('perm')
        ->select('p')
        ->from('Entity\Photo', 'p')
        ->where('p.parent IS NULL');
	}
}