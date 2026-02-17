<?php

namespace App\Infrastructure\Controller;

use App\Application\ShippingService;
use App\Domain\Model\ShippingRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/api/shipping')]
class ShippingController extends AbstractController
{
    private ShippingService $shippingService;

    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
    }

    #[Route('/calculate', name: 'api_shipping_calculate', methods: ['POST'])]
    #[OA\Post(
        path: '/api/shipping/calculate',
        summary: 'Calculate shipping cost',
        tags: ['Shipping'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'carrier', type: 'string', example: 'transcompany'),
                    new OA\Property(property: 'weightKg', type: 'number', format: 'float', example: 10.5)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful calculation',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'cost', type: 'number', format: 'float', example: 100.0)
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid input or unsupported carrier'
            )
        ]
    )]
    public function calculate(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['carrier']) || !isset($data['weightKg'])) {
            return $this->json(['error' => 'Missing carrier or weightKg'], 400);
        }

        try {
            $shippingRequest = new ShippingRequest($data['carrier'], (float) $data['weightKg']);
            $cost = $this->shippingService->calculate($shippingRequest);

            return $this->json(['cost' => $cost]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }
}
