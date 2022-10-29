<?php

namespace App\Controller\Api;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

// Test API for Swagger UI
class ApiRecipeController extends AbstractController
{
    private RecipeRepository $recipeRepository;

    public function __construct(RecipeRepository $recipeRepository) {
        $this->recipeRepository = $recipeRepository;
    }

    /**
     * @Route("api/v1/recipe/list", methods={"GET"}))
     * @OA\Parameter(
     *     name="userId",
     *     in="query",
     *     required=true,
     *     @OA\Schema(type="integer")
     * ),
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $id = $request->query->get('userId');
        $data = $this->recipeRepository->findBy(['userId' => $id]);

        $dto = [];

        foreach ($data as $recipe) {
            $dto[] = [
                'id' => $recipe->getId(),
                'name' => $recipe->getName(),
                'description' => $recipe->getDescription(),
                'createdAt' => $recipe->getCreatedAt(),
            ];
        }

        return $this->json($dto, Response::HTTP_OK);
    }
}
