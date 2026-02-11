<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Separator } from '@/components/ui/separator';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { ref } from 'vue';

interface Review {
    name: string | null;
    rating: number | null;
    text: string | null;
}

const url = ref('');
const rating = ref<string | null>(null);
const reviewCount = ref<number | null>(null);
const reviews = ref<Review[]>([]);
const error = ref<string | null>(null);
const loading = ref(false);

async function fetchRating() {
    rating.value = null;
    reviewCount.value = null;
    reviews.value = [];
    error.value = null;
    loading.value = true;

    try {
        const response = await axios.post('/rating', { url: url.value });
        rating.value = response.data.rating;
        reviewCount.value = response.data.reviewCount;
        reviews.value = response.data.reviews;
    } catch (e: any) {
        error.value = e.response?.data?.error ?? 'Произошла ошибка';
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <Head title="Рейтинг заведения" />

    <div
        class="flex min-h-[60vh] flex-col items-center justify-center gap-6 px-4"
    >
        <Card v-if="rating" class="w-full max-w-xs">
            <CardContent class="flex items-center justify-center gap-1 pt-6">
                <span class="text-lg font-semibold">{{ rating }}</span>
                <svg
                    v-for="star in 5"
                    :key="star"
                    class="h-6 w-6"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <defs>
                        <linearGradient :id="'star-fill-' + star">
                            <stop
                                :offset="
                                    Math.min(
                                        Math.max(
                                            parseFloat(rating!) - (star - 1),
                                            0,
                                        ),
                                        1,
                                    ) *
                                        100 +
                                    '%'
                                "
                                stop-color="#FACC15"
                            />
                            <stop
                                :offset="
                                    Math.min(
                                        Math.max(
                                            parseFloat(rating!) - (star - 1),
                                            0,
                                        ),
                                        1,
                                    ) *
                                        100 +
                                    '%'
                                "
                                stop-color="#D1D5DB"
                            />
                        </linearGradient>
                    </defs>
                    <path
                        :fill="'url(#star-fill-' + star + ')'"
                        d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"
                    />
                </svg>
            </CardContent>
            <Separator />
            <CardFooter
                v-if="reviewCount"
                class="justify-center pt-4 text-sm text-muted-foreground"
            >
                Всего {{ reviewCount }} отзывов
            </CardFooter>
        </Card>

        <h1 class="text-2xl font-bold">Рейтинг заведения</h1>

        <form @submit.prevent="fetchRating" class="flex w-full max-w-lg gap-2">
            <Input
                v-model="url"
                type="url"
                placeholder="https://yandex.ru/maps/org/..."
                required
            />
            <Button type="submit" :disabled="loading">
                {{ loading ? 'Загрузка...' : 'Получить' }}
            </Button>
        </form>

        <div v-if="error" class="text-sm text-destructive">
            {{ error }}
        </div>

        <div v-if="reviews.length" class="mt-4 flex w-full max-w-lg flex-col gap-4">
            <h2 class="text-lg font-semibold">Отзывы</h2>
            <Card v-for="(review, index) in reviews" :key="index">
                <CardContent class="pt-4">
                    <div class="mb-2 flex items-center justify-between">
                        <span class="font-medium">{{ review.name ?? 'Аноним' }}</span>
                        <div v-if="review.rating" class="flex items-center gap-0.5">
                            <svg
                                v-for="star in 5"
                                :key="star"
                                class="h-4 w-4"
                                :class="star <= review.rating ? 'text-yellow-400' : 'text-gray-300'"
                                fill="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                            </svg>
                        </div>
                    </div>
                    <p v-if="review.text" class="text-sm text-muted-foreground">{{ review.text }}</p>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
