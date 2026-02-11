<?php

namespace App\Http\Controllers;

use App\Services\YandexReviewsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __construct(
        private YandexReviewsService $yandexReviews,
    ) {}

    public function index(): Response
    {
        return Inertia::render('Home');
    }

    public function getRating(Request $request): JsonResponse
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        $url = $request->input('url');

        try {
            $data = $this->yandexReviews->fetchViaApi($url);

            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Parsing error: ' . $e->getMessage());

            return response()->json(['error' => 'Не удалось получить рейтинг'], 422);
        }
    }
}