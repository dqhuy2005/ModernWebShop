@extends('layouts.user.app')

@section('title', 'MWS - High-end computers, Laptops, PC Gaming, Genuine accessories')

@section('content')
    @include('components.user.carousel-banner')

    @include('components.user.category-grid')

    @include('components.user.product-carousel', ['categories' => $categories])

    @include('components.user.top-selling', ['topSellingProducts' => $topSellingProducts])

    @include('components.user.brand-carousel')
@endsection

@push('styles')
    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }

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
