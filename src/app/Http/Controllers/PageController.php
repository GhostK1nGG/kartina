<?php

namespace App\Http\Controllers;

use App\Models\Painting;
use Illuminate\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        $aboutContent = $this->aboutContent();
        $portraitPath = public_path('assets/images/about/artist-photo.jpg');
        $portraitVersion = is_file($portraitPath) ? (string) filemtime($portraitPath) : (string) time();

        $works = Painting::query()
            ->active()
            ->orderByDesc('is_featured')
            ->orderByDesc('updated_at')
            ->limit(3)
            ->get()
            ->map(fn (Painting $painting) => [
                'title' => $painting->title,
                'subtitle' => collect([
                    $this->categoryLabel($painting->category?->slug, $painting->category?->name),
                    $painting->year,
                ])->filter()->implode(' · '),
                'description' => $painting->short_desc ?: $aboutContent['works_default_description'],
                'image_url' => $painting->main_image_url,
                'url' => route('paintings.show', $painting),
            ])
            ->all();

        if ($works === []) {
            $fallbackWorks = $aboutContent['fallback_works'];

            $works = [
                [
                    ...$fallbackWorks[0],
                    'image_url' => asset('assets/images/home/gallery-after-rain.jpg'),
                    'url' => route('paintings.index'),
                ],
                [
                    ...$fallbackWorks[1],
                    'image_url' => asset('assets/images/home/gallery-ember-field.jpg'),
                    'url' => route('paintings.index'),
                ],
                [
                    ...$fallbackWorks[2],
                    'image_url' => asset('assets/images/home/gallery-quiet-dust.jpg'),
                    'url' => route('paintings.index'),
                ],
            ];
        }

        return view('pages.about', [
            'pageKey' => 'about',
            'seoTitle' => __('site.meta.about.title'),
            'seoDescription' => __('site.meta.about.description'),
            'about' => $aboutContent + [
                'portrait_url' => asset('assets/images/about/artist-photo.jpg') . '?v=' . $portraitVersion,
            ],
            'aboutWorks' => $works,
        ]);
    }

    private function categoryLabel(?string $slug, ?string $fallback = null): ?string
    {
        if (!$slug) {
            return $fallback;
        }

        $key = "site.categories.{$slug}";
        $translated = __($key);

        return $translated === $key ? $fallback : $translated;
    }

    private function aboutContent(): array
    {
        if (app()->getLocale() === 'en') {
            return [
                'eyebrow' => 'about the artist',
                'title' => 'A dialogue between the Renaissance and the inner dream.',
                'name' => 'k.',
                'education' => 'Neo-Renaissance · oil, acrylic, gold leaf',
                'lead' => 'Classical form meets the sensation of a living soul. The visual language stays romantic, elevated, and quietly luminous.',
                'description' => 'The paintings are built around a Neo-Renaissance sensitivity: clouds, sunlight, stars, feathers, angels, antique silhouettes, and simple drapery become recurring signs of an intimate inner world.',
                'intro_blocks' => [
                    'The palette often leans into pink, sky blue, and gold, while oil, acrylic, and gold leaf create a surface that feels both earthly and ceremonial.',
                ],
                'cover_label' => 'cover portrait',
                'cover_title' => 'Neo-Renaissance mood',
                'cover_caption' => 'romanticism, light, and symbolic imagery',
                'details' => [
                    ['label' => 'Style', 'value' => 'Neo-Renaissance, romanticism, elevation'],
                    ['label' => 'Recurring motifs', 'value' => 'Clouds, sunlight, stars, feathers, angels, antique imagery'],
                    ['label' => 'Palette', 'value' => 'Pink, blue, gold'],
                    ['label' => 'Materials', 'value' => 'Oil, acrylic, gold leaf'],
                ],
                'principles_heading' => 'The language of the paintings',
                'principles_description' => null,
                'principles' => [
                    [
                        'title' => 'Form and dream',
                        'text' => 'The composition holds on to classical balance, yet each work is led by an inner, almost dreamlike state rather than a purely historical quotation.',
                    ],
                    [
                        'title' => 'Light as a symbol',
                        'text' => 'Sunlight, stars, and radiant skies are treated not just as scenery but as emotional signs that lift the image into a more intimate and elevated register.',
                    ],
                    [
                        'title' => 'Fabric and tenderness',
                        'text' => 'Simple drapery, feathers, antique figures, and soft material transitions make the surface feel human, fragile, and quietly ceremonial.',
                    ],
                ],
                'story_heading' => 'My path',
                'story_label' => 'artist journey',
                'story_blocks' => [
                    'In my very first diary, at the age of seven, I wrote: "I dream of becoming an artist." That dream came true not only because it was written down, but because I kept moving toward it with love.',
                    'Learning to draw never felt unbearably hard to me, because when you truly love something, the road becomes lighter. The conditions were different. At school I often drew with pencil stubs, worn-out brushes, with a mouse on an old laptop, and across the margins of my notebooks.',
                    'The world could be difficult, but inside there was always warmth. That warmth is what kept leading me forward.',
                ],
                'quote' => '"I dream of becoming an artist."',
                'quote_label' => 'first diary note',
                'quote_meta' => 'written at age seven',
                'works_heading' => 'Selected works',
                'works_description' => null,
                'works_default_description' => 'A work from the artist\'s current catalog.',
                'works_more' => 'More Details',
                'fallback_works' => [
                    [
                        'title' => 'Quiet Snow',
                        'subtitle' => 'Landscape · 2024',
                        'description' => 'Soft winter light, calm air, and close attention to the texture of space.',
                    ],
                    [
                        'title' => 'Warm Studio',
                        'subtitle' => 'Interior · 2024',
                        'description' => 'A work about color, object silence, and the rhythm of details inside an enclosed space.',
                    ],
                    [
                        'title' => 'Evening Horizon',
                        'subtitle' => 'Abstract · 2025',
                        'description' => 'A composed image with deep color and calm graphic movement of large planes.',
                    ],
                ],
            ];
        }

        return [
            'eyebrow' => 'о художнике',
            'title' => 'Диалог между Ренессансом и внутренним сном.',
            'name' => 'k.',
            'education' => 'Неоренессанс · масло, акрил, поталь',
            'lead' => 'Сочетание классической формы и ощущения души. Живопись строится на романтизме, возвышенности и тихом внутреннем свете.',
            'description' => 'Стиль неоренессанс соединяет в себе античную пластику, мягкую символику и почти сновидческое чувство образа. В картинах часто появляются облака, солнечный свет, звезды, перья, ангелы, античные мотивы и простые ткани.',
            'intro_blocks' => [
                'Частые оттенки: розовый, голубой и золотой. Основные материалы: масло, акрил и поталь, которые дают работе и плотность, и свечение одновременно.',
            ],
            'cover_label' => 'обложка художника',
            'cover_title' => 'Неоренессансное настроение',
            'cover_caption' => 'романтизм, свет и символический образ',
            'details' => [
                ['label' => 'Стиль', 'value' => 'Неоренессанс, романтизм, возвышенность'],
                ['label' => 'Частые элементы', 'value' => 'Облака, солнечный свет, звезды, перья, ангелы, античные образы'],
                ['label' => 'Оттенки', 'value' => 'Розовый, голубой, золотой'],
                ['label' => 'Материалы', 'value' => 'Масло, акрил, поталь'],
            ],
            'principles_heading' => 'Язык живописи',
            'principles_description' => null,
            'principles' => [
                [
                    'title' => 'Форма и внутренний сон',
                    'text' => 'Композиция держится на классической ясности, но внутри нее всегда остается чувство сна, тишины и едва уловимого душевного движения.',
                ],
                [
                    'title' => 'Свет как знак',
                    'text' => 'Солнечный свет, звезды и облака работают не только как пейзажные элементы, а как символы надежды, возвышения и внутреннего тепла.',
                ],
                [
                    'title' => 'Ткань и символ',
                    'text' => 'Простые ткани, перья, ангелы и античные силуэты делают образ одновременно нежным, телесным и почти церемониальным.',
                ],
            ],
            'story_heading' => 'Мой путь',
            'story_label' => 'путь художницы',
            'story_blocks' => [
                'В своем первом дневнике, в 7 лет, я написала: «Я мечтаю стать художником». Эта мечта сбылась не только потому, что была записана, а потому что я всегда шла к ней с любовью.',
                'Мне не было тяжело учиться рисовать, ведь когда ты любишь, путь становится легким. Но об условиях я не могу сказать так же. В школе я часто рисовала огрызками карандашей, изношенными кистями, мышкой на старом ноутбуке, рисовала на полях школьных тетрадей.',
                'Мир мог быть сложным, но внутри всегда было тепло, и именно оно вело меня дальше.',
            ],
            'quote' => '«Я мечтаю стать художником».',
            'quote_label' => 'первая запись',
            'quote_meta' => 'из дневника, 7 лет',
            'works_heading' => 'Работы из каталога',
            'works_description' => null,
            'works_default_description' => 'Работа из текущего каталога художницы.',
            'works_more' => 'Подробнее',
            'fallback_works' => [
                [
                    'title' => 'Тихий снег',
                    'subtitle' => 'Пейзаж · 2024',
                    'description' => 'Мягкий зимний свет, спокойный воздух и внимание к фактуре пространства.',
                ],
                [
                    'title' => 'Теплая мастерская',
                    'subtitle' => 'Интерьер · 2024',
                    'description' => 'Работа о цвете, предметной тишине и ритме деталей в замкнутом пространстве.',
                ],
                [
                    'title' => 'Вечерний горизонт',
                    'subtitle' => 'Абстракция · 2025',
                    'description' => 'Собранная композиция с глубиной цвета и спокойной графикой крупных плоскостей.',
                ],
            ],
        ];
    }
}
