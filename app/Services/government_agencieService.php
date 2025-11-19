<?php

namespace App\Services;

use App\Repositories\government_agencieRepository;
use App\Repositories\userRepository;

class government_agencieService
{
    protected $repository;

    public function __construct(government_agencieRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAll(){
        $government_agencie = $this->repository->getAll();
        $message='this are government_agencie';
            $data=$government_agencie;
            $code=200;

        return[            'data'=>$data,

            'message'=>$message,
            'code'=>$code,
        ];

}
}
