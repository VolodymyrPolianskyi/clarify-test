<?php

namespace App\Controller;

use App\DTO\ShippingCalculateRequest;
use App\Exception\UnsupportedCarrierException;
use App\Service\ShippingCalculatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api')]
class ShippingController extends AbstractController
{
    public function __construct(
        private readonly ShippingCalculatorService $calculatorService,
        private readonly ValidatorInterface $validator,
    ) {}

    #[Route('/carriers', methods: ['GET'])]
    public function carriers(): JsonResponse
    {
        return $this->json([
            'carriers' => $this->calculatorService->getAvailableCarriers(),
        ]);
    }

    #[Route('/shipping/calculate', methods: ['POST', 'OPTIONS'])]
    public function calculate(Request $request): JsonResponse
    {
        // Обработка preflight CORS запроса
        if ($request->getMethod() === 'OPTIONS') {
            return new JsonResponse(null, Response::HTTP_NO_CONTENT, [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'POST, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type',
            ]);
        }

        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json(['error' => 'Invalid JSON body'], Response::HTTP_BAD_REQUEST);
        }

        $dto = new ShippingCalculateRequest(
            carrier: (string) ($data['carrier'] ?? ''),
            weightKg: (float) ($data['weightKg'] ?? 0),
        );

        $violations = $this->validator->validate($dto);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }
            return $this->json(['error' => 'Validation failed', 'details' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $price = $this->calculatorService->calculate($dto->carrier, $dto->weightKg);
        } catch (UnsupportedCarrierException $e) {
            return $this->json(['error' => 'Unsupported carrier'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->json([
            'carrier' => $dto->carrier,
            'weightKg' => $dto->weightKg,
            'currency' => 'EUR',
            'price' => $price,
        ]);
    }
}
