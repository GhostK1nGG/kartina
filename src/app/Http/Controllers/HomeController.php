<?php

namespace App\Http\Controllers;

use App\Models\Painting;
use App\Models\Review;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $featuredPaintings = Painting::query()
            ->with(['category', 'gallery'])
            ->active()
            ->featured()
            ->orderByDesc('updated_at')
            ->orderBy('title')
            ->limit(5)
            ->get();

        $gallerySizes = ['large', 'medium', 'small', 'small', 'small'];

        $galleryPaintings = $featuredPaintings->values()->map(function (Painting $painting, int $index) use ($gallerySizes) {
            $images = collect([$painting->main_image_url])
                ->merge($painting->gallery->pluck('image_url'))
                ->filter()
                ->unique()
                ->values()
                ->all();

            return [
                'slug' => $painting->slug,
                'size' => $gallerySizes[$index] ?? 'small',
                'title' => $painting->title,
                'excerpt' => $painting->short_desc ?: Str::limit(strip_tags((string) $painting->full_desc), 110),
                'chip' => trim(implode(' · ', array_filter([
                    $this->categoryLabel($painting->category?->slug, $painting->category?->name),
                    $painting->year,
                    $painting->size,
                ]))),
                'story' => $painting->full_desc ?: $painting->short_desc ?: __('site.messages.description_coming_soon'),
                'story_preview' => Str::limit($painting->full_desc ?: $painting->short_desc ?: __('site.messages.description_coming_soon'), 180),
                'image_url' => $painting->main_image_url,
                'detail_url' => route('paintings.show', $painting),
                'images' => $images,
            ];
        })->all();

        $publishedReviews = Review::query()
            ->published()
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn (Review $review) => [
                'author' => $review->author_name,
                'city' => $review->author_city,
                'text' => Str::limit($review->text, 220),
                'rating' => $review->rating,
                'date' => $review->created_at?->format('d.m.Y'),
                'image_url' => $review->image_url,
            ])
            ->values()
            ->all();

        return view('home', [
            'pageKey' => 'home',
            'seoTitle' => __('site.meta.home.title'),
            'seoDescription' => __('site.meta.home.description'),
            'hero' => [
                'eyebrow' => null,
                'title' => app()->getLocale() === 'en'
                    ? 'Paintings that make a room feel rare, intimate, and unforgettable.'
                    : 'Картины, которые делают пространство редким, живым и незабываемым.',
                'description' => app()->getLocale() === 'en'
                    ? 'Original works for interiors with character: quiet luxury, inner light, and a visual presence you feel the moment you enter the room.'
                    : 'Оригинальные работы для пространств с характером: тихая роскошь, внутренний свет и то самое ощущение, которое чувствуется сразу, как только вы входите в комнату.',
                'metrics' => [],
                'floating_cards' => [
                    [
                        'class' => 'large',
                        'title' => $featuredPaintings->get(0)?->title ?? __('site.home.hero.defaults.featured.title'),
                        'subtitle' => $featuredPaintings->get(0)?->size ?? __('site.home.hero.defaults.featured.subtitle'),
                        'tag' => 'featured',
                        'image' => $featuredPaintings->get(0)?->main_image_url ?? asset('assets/images/home/hero-night-bloom.jpg'),
                    ],
                    [
                        'class' => 'tall',
                        'title' => $featuredPaintings->get(1)?->title ?? __('site.home.hero.defaults.gallery.title'),
                        'subtitle' => $featuredPaintings->get(1)?->category?->name
                            ? $this->categoryLabel($featuredPaintings->get(1)?->category?->slug, $featuredPaintings->get(1)?->category?->name)
                            : __('site.home.hero.defaults.gallery.subtitle'),
                        'tag' => null,
                        'image' => $featuredPaintings->get(1)?->main_image_url ?? asset('assets/images/home/hero-amber-trace.jpg'),
                    ],
                    [
                        'class' => 'wide',
                        'title' => $featuredPaintings->get(2)?->title ?? __('site.home.hero.defaults.collection.title'),
                        'subtitle' => $featuredPaintings->get(2)?->year ?? __('site.home.hero.defaults.collection.subtitle'),
                        'tag' => null,
                        'image' => $featuredPaintings->get(2)?->main_image_url ?? asset('assets/images/home/hero-soft-horizon.jpg'),
                    ],
                    [
                        'class' => 'mini',
                        'title' => $featuredPaintings->get(3)?->title ?? __('site.home.hero.defaults.featured.title'),
                        'subtitle' => $featuredPaintings->get(3)?->year ?? __('site.home.hero.defaults.collection.subtitle'),
                        'tag' => null,
                        'image' => $featuredPaintings->get(3)?->main_image_url ?? asset('assets/images/home/hero-night-bloom.jpg'),
                    ],
                ],
                'note' => app()->getLocale() === 'en'
                    ? [
                        'title' => 'Chosen with intention',
                        'description' => 'Each work is created to become the emotional center of the space, not just a decorative detail.',
                    ]
                    : [
                        'title' => 'Выбрано с намерением',
                        'description' => 'Каждая работа создаётся не как декор, а как эмоциональный центр пространства.',
                    ],
            ],
            'concept' => $this->conceptContent(),
            'gallery' => [
                'heading' => __('site.home.gallery.heading'),
                'description' => null,
                'paintings' => $galleryPaintings,
                'flip_label' => app()->getLocale() === 'en' ? 'tap to flip' : 'нажмите, чтобы открыть',
                'catalog_cta_label' => app()->getLocale() === 'en' ? 'More Works in the Catalog' : 'Больше работ в каталоге',
            ],
            'reviewsShowcase' => [
                'heading' => __('site.home.reviews.heading'),
                'description' => null,
                'items' => $publishedReviews,
                'cta_label' => __('site.home.reviews.cta'),
                'cta_url' => route('reviews'),
            ],
            'contact' => $this->contactContent(),
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

    private function conceptContent(): array
    {
        if (app()->getLocale() === 'en') {
            return [
                'heading' => 'Dialogue with Space',
                'description' => null,
                'panel' => [
                    'eyebrow' => 'for your space',
                    'lead' => 'These paintings resonate with people who feel more deeply than they usually say out loud, and who value silence, beauty, symbols, and inner light.',
                    'title' => 'A painting becomes a point of gravity for the heart.',
                    'description' => 'It does not argue with the interior. Instead, it gathers atmosphere around itself: quiet, softened light, symbols, and a sense of inner presence.',
                    'items' => [
                        'For people who seek tenderness rather than noise.',
                        'For interiors where atmosphere matters as much as furniture.',
                        'For rooms that are meant to restore, inspire, and hold memory.',
                    ],
                ],
                'features' => [
                    [
                        'eyebrow' => 'space',
                        'title' => 'Creative Studios',
                        'description' => 'Works that hold concentration, imagination, and the feeling of a private inner world.',
                    ],
                    [
                        'eyebrow' => 'space',
                        'title' => 'Light Living Rooms',
                        'description' => 'A calm focal point that brings softness and symbolic depth into everyday life.',
                    ],
                    [
                        'eyebrow' => 'space',
                        'title' => 'Bedrooms and Private Rooms',
                        'description' => 'Paintings that belong near silence, personal rituals, and emotional intimacy.',
                    ],
                    [
                        'eyebrow' => 'space',
                        'title' => 'Meditation and Recovery Spaces',
                        'description' => 'Images that support slowing down, practice, restoration, and inward attention.',
                    ],
                ],
            ];
        }

        return [
            'heading' => 'Диалог с пространством',
            'description' => null,
            'panel' => [
                'eyebrow' => 'для вашего пространства',
                'lead' => 'Мои картины находят отклик у людей, которые чувствуют глубже, чем принято говорить вслух, и ценят тишину, красоту, символы и внутренний свет.',
                'title' => 'Картина становится точкой притяжения для сердца.',
                'description' => 'Она не спорит с интерьером, а собирает вокруг себя атмосферу: мягкий свет, тишину, внутреннее присутствие и ощущение личного смысла.',
                'items' => [
                    'Для тех, кто выбирает не шумный акцент, а глубокий эмоциональный отклик.',
                    'Для пространств, где важна не только красота, но и состояние.',
                    'Для людей, которым близки символы, свет и тихая внутренняя поэзия.',
                ],
            ],
            'features' => [
                [
                    'eyebrow' => 'пространство',
                    'title' => 'Творческие студии',
                    'description' => 'Там, где важны вдохновение, сосредоточенность и чувство собственного внутреннего мира.',
                ],
                [
                    'eyebrow' => 'пространство',
                    'title' => 'Светлые гостиные',
                    'description' => 'Картина даёт мягкую точку фокуса и делает атмосферу дома более тонкой и живой.',
                ],
                [
                    'eyebrow' => 'пространство',
                    'title' => 'Спальни и личные комнаты',
                    'description' => 'Работы особенно естественно раскрываются рядом с тишиной, личными ритуалами и интимным пространством.',
                ],
                [
                    'eyebrow' => 'пространство',
                    'title' => 'Места для практик и восстановления',
                    'description' => 'Они хорошо звучат там, где нужны замедление, медитация, отдых и возвращение к себе.',
                ],
            ],
        ];
    }

    private function contactContent(): array
    {
        if (app()->getLocale() === 'en') {
            return [
                'process' => [
                    'eyebrow' => 'how we create together',
                    'title' => 'From a feeling to a finished painting',
                    'description' => 'The process stays personal and transparent: we move from mood and symbols to sketches, composition, and the final work, and I can share intermediate stages along the way.',
                    'steps' => [
                        [
                            'index' => '01',
                            'title' => 'Shaping the idea',
                            'description' => 'You share your idea, mood, or story. We discuss imagery, symbols, color, and atmosphere. I prepare several sketch directions and, after approval, begin the painting.',
                        ],
                        [
                            'index' => '02',
                            'title' => 'Conversation and start',
                            'description' => 'To begin, write through Instagram, email, or the form on the site. Tell me the idea or request, desired size, and timeline if it matters. Then I reply with clarifying questions, we agree on format and cost, and start the work.',
                        ],
                    ],
                ],
                'form' => [
                    'eyebrow' => 'project request',
                    'front_title' => 'Tell me the mood, image, or story',
                    'front_description' => 'A good project starts with a feeling. You can describe an image, a symbol, a palette, or simply the atmosphere you want the painting to hold.',
                    'front_list_label' => 'To start, include:',
                    'front_list' => [
                        'the idea or request',
                        'the desired size',
                        'timeline, if it matters',
                    ],
                    'front_button' => 'Open Form',
                    'back_title' => 'Send a request',
                    'back_description' => 'You can begin through Instagram, email, or directly through the form below. After the message I respond with clarifying questions, we discuss the details, agree on format and cost, and begin creating the painting.',
                    'back_list_label' => 'After your request:',
                    'back_list' => [
                        'I reply with clarifications',
                        'we discuss the details',
                        'we agree on format and cost',
                        'and then begin the painting',
                    ],
                    'submit_label' => 'Send Request',
                ],
            ];
        }

        return [
            'process' => [
                'eyebrow' => 'как мы создаём проект вместе',
                'title' => 'От чувства и идеи к готовой картине',
                'description' => 'Процесс остаётся личным и понятным: мы идём от настроения, символов и истории к эскизам, композиции и готовой работе, а по пути я могу делиться промежуточными этапами.',
                'steps' => [
                    [
                        'index' => '01',
                        'title' => 'Воплощение идеи',
                        'description' => 'Вы делитесь своей идеей, настроением или историей. Мы обсуждаем образы, символы, цвета и ощущения. Я предлагаю художественное видение и несколько эскизных направлений, а после согласования начинаю работу над картиной.',
                    ],
                    [
                        'index' => '02',
                        'title' => 'Сотрудничество и старт',
                        'description' => 'Чтобы начать, напишите мне в Instagram, на email или через форму на сайте. Укажите идею или запрос, желаемый размер и сроки, если они важны. После этого я отвечу с уточнениями, мы согласуем формат и стоимость и начнём создание картины.',
                    ],
                ],
            ],
            'form' => [
                'eyebrow' => 'заявка на картину',
                'front_title' => 'Расскажите идею, настроение или историю',
                'front_description' => 'Хороший проект начинается с чувства. Можно описать образ, символ, цветовую атмосферу или просто состояние, которое вы хотите сохранить в картине.',
                'front_list_label' => 'Чтобы начать, укажите:',
                'front_list' => [
                    'идею или запрос',
                    'желаемый размер',
                    'сроки, если они важны',
                ],
                'front_button' => 'Открыть форму',
                'back_title' => 'Оставить заявку',
                'back_description' => 'Можно начать через Instagram, email или форму ниже. После сообщения я вернусь с уточнениями, мы обсудим детали, согласуем формат и стоимость, а затем перейдём к созданию работы.',
                'back_list_label' => 'После вашей заявки:',
                'back_list' => [
                    'я отвечу с уточнениями',
                    'мы обсудим детали',
                    'согласуем формат и стоимость',
                    'и начнём создание картины',
                ],
                'submit_label' => 'Отправить запрос',
            ],
        ];
    }
}
