<section class="brand-carousel py-5" style="background-color: #F8F9FA;">
    <div class="container">
        <div class="section-header text-center mb-4">
            <h2 class="section-title">THƯƠNG HIỆU NỔI BẬT</h2>
        </div>

        <div class="brand-carousel-wrapper">
            <div class="brand-track-container">
                <div class="brand-track" id="brandTrack">
                    @php
                        $brands = [
                            ['name' => 'ASUS ROG', 'logo' => 'rog_logo.png'],
                            ['name' => 'ASUS', 'logo' => 'asus_logo.png'],
                            ['name' => 'Lenovo Legion', 'logo' => 'lenovo_logo.png'],
                            ['name' => 'MSI', 'logo' => 'msi_logo.png'],
                            ['name' => 'Dell', 'logo' => 'dell_logo.png'],
                            ['name' => 'Acer', 'logo' => 'acer_logo.png'],
                            ['name' => 'Razer', 'logo' => 'razer_logo.png'],
                            ['name' => 'Gigabyte', 'logo' => 'gigabyte_logo.png'],
                        ];
                    @endphp

                    @foreach ($brands as $brand)
                        <div class="brand-item">
                            <div class="brand-card">
                                <img src="{{ asset('assets/imgs/thumbnail/' . $brand['logo']) }}"
                                    alt="{{ $brand['name'] }}" loading="lazy">
                            </div>
                        </div>
                    @endforeach

                    {{-- Duplicate for seamless loop --}}
                    @foreach ($brands as $brand)
                        <div class="brand-item">
                            <div class="brand-card">
                                <img src="{{ asset('assets/imgs/thumbnail/' . $brand['logo']) }}"
                                    alt="{{ $brand['name'] }}" loading="lazy">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .brand-carousel {
        background-color: #F8F9FA;
    }

    .section-title {
        font-size: 2rem;
        font-weight: 700;
        color: #202732;
        margin-bottom: 0.5rem;
        letter-spacing: 1px;
    }

    .brand-carousel-wrapper {
        position: relative;
        overflow: hidden;
        padding: 0;
    }

    .brand-track-container {
        overflow: hidden;
        width: 100%;
    }

    .brand-track {
        display: flex;
        gap: 2rem;
        width: fit-content;
        will-change: transform;
    }

    .brand-item {
        flex-shrink: 0;
        width: 200px;
    }

    .brand-card {
        background-color: #FFFFFF;
        border: 2px solid #E9ECEF;
        border-radius: 8px;
        padding: 2rem 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 120px;
        transition: all 0.3s ease;
    }

    .brand-card img {
        max-width: 100%;
        max-height: 80px;
        object-fit: contain;
        filter: grayscale(100%);
        opacity: 0.7;
        transition: all 0.3s ease;
    }

    .brand-card:hover img {
        filter: grayscale(0%);
        opacity: 1;
        transform: scale(1.05);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .brand-item {
            width: 160px;
        }

        .brand-card {
            height: 100px;
            padding: 1.5rem 0.5rem;
        }

        .brand-card img {
            max-height: 60px;
        }

        .section-title {
            font-size: 1.5rem;
        }
    }

    @media (max-width: 576px) {
        .brand-item {
            width: 140px;
        }

        .brand-track {
            gap: 1.5rem;
        }

        .section-title {
            font-size: 1.3rem;
        }
    }
</style>

<script>
    (function() {
        'use strict';

        class BrandCarousel {
            constructor(trackId) {
                this.track = document.getElementById(trackId);
                if (!this.track) return;

                this.container = this.track.parentElement;
                this.scrollPosition = 0;
                this.isPaused = false;
                this.pixelsPerSecond = 50;
                this.animationId = null;
                this.lastTimestamp = null;

                this.init();
            }

            init() {
                this.ensureSufficientItems();

                this.startAnimation();

                this.container.addEventListener('mouseenter', () => this.pause());
                this.container.addEventListener('mouseleave', () => this.resume());

                document.addEventListener('visibilitychange', () => {
                    if (document.hidden) {
                        this.pause();
                    } else {
                        this.resume();
                    }
                });
            }

            ensureSufficientItems() {
                const items = this.track.children;
                const totalWidth = this.track.scrollWidth;
                const containerWidth = this.container.offsetWidth;

                if (totalWidth < containerWidth * 3) {
                    const fragment = document.createDocumentFragment();
                    for (let i = 0; i < items.length; i++) {
                        fragment.appendChild(items[i].cloneNode(true));
                    }
                    this.track.appendChild(fragment);
                }
            }

            startAnimation() {
                const animate = (timestamp) => {
                    if (!this.lastTimestamp) {
                        this.lastTimestamp = timestamp;
                    }

                    const deltaTime = (timestamp - this.lastTimestamp) / 1000;
                    this.lastTimestamp = timestamp;

                    if (!this.isPaused) {
                        const pixelsToMove = this.pixelsPerSecond * deltaTime;
                        this.scrollPosition += pixelsToMove;

                        const itemsCount = this.track.children.length / 2;
                        const singleSetWidth = Array.from(this.track.children)
                            .slice(0, itemsCount)
                            .reduce((sum, item) => {
                                const style = window.getComputedStyle(item);
                                const margin = parseFloat(style.marginRight) || 0;
                                return sum + item.offsetWidth + margin;
                            }, 0);

                        if (this.scrollPosition >= singleSetWidth) {
                            this.scrollPosition = 0;
                        }

                        this.track.style.transform = `translateX(-${this.scrollPosition}px)`;
                    }

                    this.animationId = requestAnimationFrame(animate);
                };

                this.animationId = requestAnimationFrame(animate);
            }

            pause() {
                this.isPaused = true;
            }

            resume() {
                this.isPaused = false;
            }

            destroy() {
                if (this.animationId) {
                    cancelAnimationFrame(this.animationId);
                }
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                new BrandCarousel('brandTrack');
            });
        } else {
            new BrandCarousel('brandTrack');
        }
    })();
</script>
