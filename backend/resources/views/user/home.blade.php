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

            const timeBoxes = document.querySelectorAll('.countdown-timer .time-box h3');
            if (timeBoxes.length === 4) {
                timeBoxes[0].textContent = String(days).padStart(2, '0');
                timeBoxes[1].textContent = String(hours).padStart(2, '0');
                timeBoxes[2].textContent = String(minutes).padStart(2, '0');
                timeBoxes[3].textContent = String(seconds).padStart(2, '0');
            }
        }

        setInterval(updateCountdown, 1000);
        updateCountdown();

        $(document).on('click', '.btn-outline-secondary', function(e) {
            e.preventDefault();
            const icon = $(this).find('i');

            if (icon.hasClass('far')) {
                icon.removeClass('far').addClass('fas');
                $(this).removeClass('btn-outline-secondary').addClass('btn-danger text-white');
                toastr.success('Added to wishlist!');
            } else {
                icon.removeClass('fas').addClass('far');
                $(this).removeClass('btn-danger text-white').addClass('btn-outline-secondary');
                toastr.info('Removed from wishlist!');
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('img[data-src]');

            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                        observer.unobserve(img);
                    }
                });
            });

            images.forEach(img => imageObserver.observe(img));
        });
    </script>
@endpush
