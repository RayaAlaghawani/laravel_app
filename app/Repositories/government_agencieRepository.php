<?php

namespace App\Repositories;

use App\Models\Government_agencie;

class government_agencieRepository
{
public function getAll(){
   return Government_agencie::all() ;


}
}
