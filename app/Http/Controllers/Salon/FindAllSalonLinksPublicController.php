<?php

namespace App\Http\Controllers\Salon;

use App\Http\Controllers\Controller;
use App\Application\Usecases\Salon\FindAllSalonLinksPublicUsecase;

class FindAllSalonLinksPublicController extends Controller
{
    public function __construct(
        private FindAllSalonLinksPublicUsecase $findAllSalonLinksPublicUsecase
    ) {}

    public function __invoke()
    {
        $links = $this->findAllSalonLinksPublicUsecase->execute();

        return response()->json([
            'data' => $links
        ]);
    }
}
