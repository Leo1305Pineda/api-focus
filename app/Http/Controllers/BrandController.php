<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\BrandRepository;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class BrandController extends Controller
{
    public function __construct(BrandRepository $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    public function getAll(Request $request){
        return $this->getDataResponse($this->brandRepository->getAll($request), HttpFoundationResponse::HTTP_OK);
    }

    public function getById(Request $request, $id){
        return $this->getDataResponse($this->brandRepository->getById($request, $id), HttpFoundationResponse::HTTP_OK);
    }
}