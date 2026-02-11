<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Symfony\Component\DomCrawler\Crawler;

class YandexReviewsService
{
    private function createClient(CookieJar $jar = null): Client
    {
        return new Client([
            'cookies' => $jar ?? new CookieJar(),
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'Accept-Language' => 'ru-RU,ru;q=0.9',
                'Referer' => 'https://yandex.ru/maps/',
            ],
        ]);
    }

    /**
     * Парсинг HTML-страницы организации.
     * Получает рейтинг и только те отзывы, которые есть в начальном HTML (обычно ~10).
     */
    public function fetchViaHtml(string $url): array
    {
        $client = $this->createClient();

        $response = $client->get($url);
        $html = $response->getBody()->getContents();
        $crawler = new Crawler($html);

        $rating = $crawler->filter('.business-summary-rating-badge-view__rating-text')
            ->each(fn(Crawler $node) => $node->text());
        $rating = implode('', $rating);

        $reviewCountText = $crawler->filter('div.business-reviews-card-view__header h2.card-section-header__title')->text();
        preg_match('/\d+/', $reviewCountText, $matches);
        $reviewCount = (int) ($matches[0] ?? 0);

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

        return [
            'rating' => $rating,
            'reviewCount' => $reviewCount,
            'reviews' => $reviews,
        ];
    }

    /**
     * Прямые запросы к внутреннему API Яндекс Карт.
     * Рейтинг берётся из HTML, отзывы — через API постранично (по 50 за запрос).
     */
    public function fetchViaApi(string $url): array
    {
        $businessId = $this->extractBusinessId($url);

        $jar = new CookieJar();
        $client = $this->createClient($jar);

        // Рейтинг из HTML (API отзывов его не возвращает)
        $response = $client->get($url);
        $html = $response->getBody()->getContents();
        $crawler = new Crawler($html);

        $rating = $crawler->filter('.business-summary-rating-badge-view__rating-text')
            ->each(fn(Crawler $node) => $node->text());
        $rating = implode('', $rating);

        $reviewCountText = $crawler->filter('div.business-reviews-card-view__header h2.card-section-header__title')->text();
        preg_match('/\d+/', $reviewCountText, $matches);
        $reviewCount = (int) ($matches[0] ?? 0);

        // CSRF-токен через POST (без параметров)
        $tokenResponse = $client->post('https://yandex.ru/maps/api/business/fetchReviews');
        $tokenData = json_decode($tokenResponse->getBody()->getContents(), true);
        $csrfToken = $tokenData['csrfToken'] ?? null;

        if (!$csrfToken) {
            throw new \RuntimeException('Не удалось получить CSRF-токен от Яндекса');
        }

        // Постраничная загрузка отзывов
        $allReviews = [];
        $page = 1;
        $pageSize = 50;
        $maxPages = 20;

        do {
            $params = [
                'ajax' => '1',
                'businessId' => $businessId,
                'csrfToken' => $csrfToken,
                'locale' => 'ru_RU',
                'page' => (string) $page,
                'pageSize' => (string) $pageSize,
                'ranking' => 'by_time',
            ];

            ksort($params);
            $queryString = http_build_query($params);
            $s = $this->djb2Hash($queryString);

            $apiUrl = 'https://yandex.ru/maps/api/business/fetchReviews?' . $queryString . '&s=' . $s;
            $reviewsResponse = $client->get($apiUrl);
            $reviewsData = json_decode($reviewsResponse->getBody()->getContents(), true);

            if (!$reviewsData) {
                break;
            }

            if (isset($reviewsData['csrfToken'])) {
                $csrfToken = $reviewsData['csrfToken'];
            }

            $data = $reviewsData['data'] ?? $reviewsData;
            $reviews = $data['reviews'] ?? [];

            foreach ($reviews as $review) {
                $allReviews[] = [
                    'name' => $review['author']['name'] ?? null,
                    'rating' => $review['rating'] ?? null,
                    'text' => $review['text'] ?? null,
                ];
            }

            $page++;
        } while (count($reviews) >= $pageSize && $page <= $maxPages);

        return [
            'rating' => $rating,
            'reviewCount' => $reviewCount,
            'reviews' => $allReviews,
        ];
    }

    public function extractBusinessId(string $url): string
    {
        if (!preg_match('/\/org\/[^\/]+\/(\d+)/', $url, $matches)) {
            throw new \InvalidArgumentException('Не удалось извлечь ID организации из URL');
        }

        return $matches[1];
    }

    /**
     * DJB2-хеш для параметра s (верификация запроса к API Яндекс Карт).
     * Точное воспроизведение JavaScript-алгоритма из фронтенда Яндекса.
     */
    private function djb2Hash(string $str): string
    {
        $n = 5381;
        for ($i = 0, $len = strlen($str); $i < $len; $i++) {
            $n = (33 * $n) ^ ord($str[$i]);
            $n &= 0xFFFFFFFF;
            if ($n >= 0x80000000) {
                $n -= 0x100000000;
            }
        }

        return (string) ($n & 0xFFFFFFFF);
    }
}