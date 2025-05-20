<?php

namespace App\Http\Controllers\Salon;

use App\Application\Usecases\Salon\FindSalonByIdUsecase;
use App\Http\Controllers\Controller;

class FindSalonByIdController extends Controller
{
    public function __construct(
        private FindSalonByIdUsecase $findSalonByIdUsecase
    ) {}

    public function __invoke(string $salonId)
    {
        $salon = $this->findSalonByIdUsecase->execute($salonId);

        return response()->json([
            'message' => 'Salon retrieved successfully',
            'data' => [
                'id' => $salon->id,
                'name' => $salon->name,
                'address' => $salon->address,
                'phone' => $salon->phone,
                'logo' => $salon->logo,
                'postal_code' => $salon->postal_code,
                'city' => $salon->city,
                'city_slug' => $salon->city_slug,
                'name_slug' => $salon->name_slug,
                'type_slug' => $salon->type_slug,
                'social_links' => $salon->social_links,
                'google_info' => $salon->google_info,
                'owner' => $salon->owner
            ]
        ]);
    }
}
