<?php
namespace App\Http\Controllers;
use App\Helpers\ResponseHelper;
use App\Services\EmployeeService;
use App\Services\government_agencieService;
use Illuminate\Http\Request;
class government_agencie extends Controller
{
    private $service;

    public function __construct(
        government_agencieService $service
    ) {
        $this->service = $service;
    }

    public function index(){
        $data=[];
        try{
            $data=$this->service->getAll();
            return ResponseHelper::Success($data['data'],$data['message']);}
        catch( \Throwable $th)
        {
            $message=$th->getMessage();
            return ResponseHelper::Error($data,$message);}
    }




}
