<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;

class HomeController extends Controller
{
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

        $client = new Client([
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'Accept-Language' => 'ru-RU,ru;q=0.9',
            ]
        ]);

        try {
            $response = $client->get($url);
            $html = $response->getBody()->getContents();

            $crawler = new Crawler($html);
            
            // Рейтинг
            $rating = $crawler->filter('.business-summary-rating-badge-view__rating-text')->each(fn(Crawler $node) => $node->text());
            $rating = implode('', $rating);
            
            // Количество отзывов
            $reviewCountText = $crawler->filter('div.business-reviews-card-view__header h2.card-section-header__title')->text();
            preg_match('/\d+/', $reviewCountText, $matches);
            $reviewCount = (int) ($matches[0] ?? 0);
            
            // Сами отзывы
            $reviews = $crawler->filter('.business-reviews-card-view__review')->each(function (Crawler $node) {
                $name = $node->filter('span[itemprop="name"]')->count()
                    ? $node->filter('span[itemprop="name"]')->text()
                    : null;

                $ratingValue = null;
                $badgeDiv = $node->filter('div.business-rating-badge-view > div');
                if ($badgeDiv->count()) {
                    $ariaLabel = $badgeDiv->attr('aria-label');
                    if ($ariaLabel && preg_match('/(\d+)/', $ariaLabel, $m)) {
                        $ratingValue = (int) $m[1];
                    }
                }

                $text = $node->filter('span.spoiler-view__text-container')->count()
                    ? $node->filter('span.spoiler-view__text-container')->text()
                    : null;

                return [
                    'name' => $name,
                    'rating' => $ratingValue,
                    'text' => $text,
                ];
            });

            return response()->json(['rating' => $rating, 'reviewCount' => $reviewCount, 'reviews' => $reviews]);
        } catch (\Exception $e) {
            Log::error('Parsing error: ' . $e->getMessage());

            return response()->json(['error' => 'Не удалось получить рейтинг'], 422);
        }
    }
}
