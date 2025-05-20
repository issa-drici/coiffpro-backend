<?php

namespace App\Http\Controllers\Salon;

use App\Application\Usecases\Salon\UpdateSalonUsecase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UpdateSalonController extends Controller
{
    public function __construct(
        private UpdateSalonUsecase $updateSalonUsecase
    ) {
    }

    public function __invoke(string $salonId, Request $request)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'address' => 'sometimes|nullable|string|max:255',
            'postal_code' => 'sometimes|nullable|string|max:10',
            'city' => 'sometimes|nullable|string|max:255',
            'city_slug' => 'sometimes|nullable|string|max:255',
            'name_slug' => 'sometimes|nullable|string|max:255',
            'type_slug' => 'sometimes|nullable|string|max:255',
            'phone' => 'sometimes|nullable|string|max:50',
            'logo' => 'sometimes|nullable|file|image|max:2048',
            'logo_url' => 'sometimes|nullable|string',
            'remove_logo' => 'sometimes|nullable|string',
            'social_links' => ['sometimes', 'nullable', function ($attribute, $value, $fail) {
                if ($value && !is_array(json_decode($value, true))) {
                    $fail('Le champ social_links doit être un JSON valide.');
                }
            }],
            'google_info' => ['sometimes', 'nullable', function ($attribute, $value, $fail) {
                if ($value && !is_array(json_decode($value, true))) {
                    $fail('Le champ google_info doit être un JSON valide.');
                }
            }],
        ]);

        // Décoder les champs JSON
        if (isset($data['social_links'])) {
            $data['social_links'] = json_decode($data['social_links'], true) ?? [];
        }
        if (isset($data['google_info'])) {
            $data['google_info'] = json_decode($data['google_info'], true) ?? [];
        }

        $salon = $this->updateSalonUsecase->execute($salonId, $data);

        return response()->json([
            'message' => 'Restaurant updated successfully',
            'data' => [
                'id' => $salon->getId(),
                'name' => $salon->getName(),
                'address' => $salon->getAddress(),
                'phone' => $salon->getPhone(),
                'logo_id' => $salon->getLogoId(),
                'social_links' => $salon->getSocialLinks(),
                'google_info' => $salon->getGoogleInfo(),
            ]
        ]);
    }
}