<?php
namespace Vuravel\Form\Tests\Models;
use Illuminate\Database\Eloquent\Model;
use Vuravel\Form\Tests\Models\Obj;

class Imagemtm extends Model
{	
	public function objs() 
	{
		return $this->morphedByMany(Obj::class, 'model', 'obj_imagemtm'); 
	}

}
