@extends('layouts.user.app')

@section('title', 'MWS - High-end computers, Laptops, PC Gaming, Genuine accessories')

@section('content')
    @include('components.user.carousel-banner')

    @include('components.user.category-grid')

    @if (isset($categories) && $categories->count() > 0)
        @foreach ($categories as $category)
            @if ($category->products->count() > 0)
                <x-user.category-product-carousel :category="$category" :products="$category->products" :limit="15" />
            @endif
        @endforeach
    @endif

    @include('components.user.top-selling', ['topSellingProducts' => $topSellingProducts])

    @include('components.user.brand-carousel')
@endsection

@push('styles')
    <style>
        html {
            scroll-behavior: smooth;
        }

        section {
            position: relative;
        }

        a {
            transition: all 0.3s ease;
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (min-width: 1200px) {
            .container {
                min-width: 1200px;
                max-width: 1440px;
                width: 100%;
            }

            section.py-5 {
                padding: 3rem 0;
            }
        }

        @media (max-width: 1199px) and (min-width: 768px) {
            .container {
                min-width: 768px;
                max-width: 100%;
                width: 100%;
            }

            section.py-5 {
                padding: 2.5rem 0;
            }
        }

        @media (max-width: 767px) {
            .container {
                min-width: 320px;
                max-width: 100%;
                width: 100%;
                padding-left: 10px;
                padding-right: 10px;
            }

            section {
                padding: 1.5rem 0 !important;
            }

            .py-5 {
                padding-top: 1.5rem !important;
                padding-bottom: 1.5rem !important;
            }

            .py-4 {
                padding-top: 1rem !important;
                padding-bottom: 1rem !important;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            function updateCountdown() {
                const targetDate = new Date();
                targetDate.setDate(targetDate.getDate() + 2);
                targetDate.setHours(10, 34, 60);

                const now = new Date().getTime();
                const distance = targetDate - now;

                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                const $timeBoxes = $('.countdown-timer .time-box h3');
                if ($timeBoxes.length === 4) {
                    $timeBoxes.eq(0).text(String(days).padStart(2, '0'));
                    $timeBoxes.eq(1).text(String(hours).padStart(2, '0'));
                    $timeBoxes.eq(2).text(String(minutes).padStart(2, '0'));
                    $timeBoxes.eq(3).text(String(seconds).padStart(2, '0'));
                }
            }

            setInterval(updateCountdown, 1000);
            updateCountdown();

            // Lazy load images
            const $images = $('img[data-src]');

            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const $img = $(entry.target);
                            $img.attr('src', $img.data('src'));
                            $img.removeAttr('data-src');
                            observer.unobserve(entry.target);
                        }
                    });
                });

                $images.each(function() {
                    imageObserver.observe(this);
                });
            } else {
                $images.each(function() {
                    const $img = $(this);
                    $img.attr('src', $img.data('src'));
                    $img.removeAttr('data-src');
                });
            }
        });
    </script>
@endpush
