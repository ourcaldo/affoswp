/**
 * AffosWP - Premium UI/UX System v2
 * Varied animations per section, 3D effects, smooth interactions
 */

document.addEventListener('DOMContentLoaded', () => {

    // ========================
    // SCROLL-TRIGGERED ANIMATIONS
    // ========================

    const initAnimations = () => {
        const animatedSections = document.querySelectorAll('[data-anim]');

        const observerOptions = {
            threshold: 0.15,
            rootMargin: '0px 0px -50px 0px'
        };

        const animObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                }
            });
        }, observerOptions);

        animatedSections.forEach(section => {
            animObserver.observe(section);
        });
    };

    // ========================
    // 3D CARD TILT EFFECT
    // ========================

    const init3DCards = () => {
        // Product card hover is now handled by CSS (subtle translateY lift)
        // Only apply tilt to bento-cards if present
        const bentoCards = document.querySelectorAll('.bento-card');

        bentoCards.forEach(card => {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                const centerX = rect.width / 2;
                const centerY = rect.height / 2;

                const rotateX = (y - centerY) / 25;
                const rotateY = (centerX - x) / 25;

                card.style.transform = `
                    perspective(1000px) 
                    rotateX(${rotateX}deg) 
                    rotateY(${rotateY}deg) 
                    translateY(-4px)
                `;
            });

            card.addEventListener('mouseleave', () => {
                card.style.transform = '';
            });
        });
    };

    // ========================
    // HEADER SCROLL EFFECT
    // ========================

    const initHeaderEffect = () => {
        const header = document.querySelector('header');
        if (!header) return;

        let lastScroll = 0;
        header.style.transition = 'transform 0.3s ease, background 0.3s ease, box-shadow 0.3s ease';

        window.addEventListener('scroll', () => {
            const currentScroll = window.scrollY;

            // Background change
            if (currentScroll > 50) {
                header.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.08)';
                header.style.background = '#FFFFFF';
            } else {
                header.style.boxShadow = 'none';
                header.style.background = '#FFFFFF';
            }

            // Hide/show on scroll
            if (currentScroll > lastScroll && currentScroll > 300) {
                header.style.transform = 'translateY(-100%)';
            } else {
                header.style.transform = 'translateY(0)';
            }

            lastScroll = currentScroll;
        });
    };

    // ========================
    // SMOOTH ANCHOR SCROLLING
    // ========================

    const initSmoothScroll = () => {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;

                const target = document.querySelector(targetId);
                if (target) {
                    e.preventDefault();
                    const headerHeight = 100;
                    const targetPosition = target.getBoundingClientRect().top + window.scrollY - headerHeight;

                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });
    };

    // ========================
    // COMPARISON LOGIC
    // ========================

    const initComparisonSystem = () => {
        let compareState = JSON.parse(localStorage.getItem('affos_compare')) || [];

        // Get compare URL from body data attribute or default to /bandingkan/
        const compareUrl = document.body.dataset.compareUrl || '/bandingkan/';

        // Create compare bar if not exists
        let bar = document.querySelector('.compare-bar');
        if (!bar) {
            bar = document.createElement('div');
            bar.className = 'compare-bar';
            document.body.appendChild(bar);
        }

        updateCompareBar();

        // Handle compare buttons - support multiple selectors
        const compareBtns = document.querySelectorAll('.add-to-compare, .action-btn[title="Bandingkan"], [data-compare-id]');
        compareBtns.forEach((btn) => {
            // Get product ID from data attribute or closest product card
            let pid = btn.dataset.compareId || btn.dataset.id;
            if (!pid) {
                const card = btn.closest('.product-card, [data-product-id]');
                if (card) {
                    pid = card.dataset.productId || card.dataset.id;
                }
            }
            if (!pid) return;

            btn.dataset.compareId = pid;

            // Set initial state
            if (compareState.includes(pid)) {
                btn.classList.add('active');
                btn.style.color = 'var(--accent-color)';
                btn.style.background = 'var(--accent-light)';
            }

            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const id = btn.dataset.compareId;

                const idx = compareState.indexOf(id);
                if (idx > -1) {
                    compareState.splice(idx, 1);
                    btn.classList.remove('active');
                    btn.style.color = '';
                    btn.style.background = '';
                } else {
                    if (compareState.length >= 3) {
                        showToast('Maksimal 3 produk untuk dibandingkan!');
                        return;
                    }
                    compareState.push(id);
                    btn.classList.add('active');
                    btn.style.color = 'var(--accent-color)';
                    btn.style.background = 'var(--accent-light)';
                }

                localStorage.setItem('affos_compare', JSON.stringify(compareState));
                updateCompareBar();
            });
        });

        function updateCompareBar() {
            const state = JSON.parse(localStorage.getItem('affos_compare') || '[]');

            // Don't show compare bar if on compare page (already viewing comparison)
            const isComparePage = window.location.pathname.includes('/compare');
            if (isComparePage) {
                bar.classList.remove('active');
                return;
            }

            if (state.length > 0) {
                // Build initial thumbs (names will be fetched async)
                let thumbsHtml = state.map(id => `
                    <div class="mini-thumb" data-thumb-id="${id}" data-tooltip="Memuat...">
                        <i class="ri-smartphone-line"></i>
                    </div>
                `).join('');

                bar.innerHTML = `
                    <div class="container">
                        <div class="compare-items-preview">
                            <span style="font-weight:700;">${state.length} Produk</span>
                            ${thumbsHtml}
                        </div>
                        <div class="compare-actions">
                            <button class="btn btn-outline btn-sm" id="clear-compare">Reset</button>
                            <button class="btn btn-primary btn-sm" id="go-compare">Bandingkan</button>
                        </div>
                    </div>
                `;

                // Fetch product names for tooltips
                const ajaxUrl = typeof affosData !== 'undefined' ? affosData.ajaxUrl : '/wp-admin/admin-ajax.php';
                fetch(ajaxUrl + '?action=affos_get_compare_names&ids=' + state.join(','))
                    .then(r => r.json())
                    .then(data => {
                        if (data.success && data.data) {
                            Object.entries(data.data).forEach(([id, name]) => {
                                const thumb = bar.querySelector(`.mini-thumb[data-thumb-id="${id}"]`);
                                if (thumb) {
                                    thumb.setAttribute('data-tooltip', name);
                                    thumb.setAttribute('title', name);
                                }
                            });
                        }
                    })
                    .catch(() => { });

                document.getElementById('clear-compare')?.addEventListener('click', () => {
                    localStorage.setItem('affos_compare', '[]');
                    document.querySelectorAll('.add-to-compare, .action-btn[title="Bandingkan"], [data-compare-id]').forEach(b => {
                        b.classList.remove('active');
                        b.style.color = '';
                        b.style.background = '';
                    });
                    compareState = [];
                    updateCompareBar();
                });

                // Navigate to SEO-friendly compare URL
                document.getElementById('go-compare')?.addEventListener('click', () => {
                    if (state.length < 2) {
                        showToast('Pilih minimal 2 produk untuk dibandingkan');
                        return;
                    }
                    // Fetch SEO-friendly URL from server
                    fetch(ajaxUrl + '?action=affos_get_compare_slugs&ids=' + state.join(','))
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.data.url) {
                                window.location.href = data.data.url;
                            } else {
                                window.location.href = compareUrl;
                            }
                        })
                        .catch(() => {
                            window.location.href = compareUrl;
                        });
                });

                requestAnimationFrame(() => bar.classList.add('active'));
            } else {
                bar.classList.remove('active');
            }
        }
    };

    // ========================
    // TOAST NOTIFICATION
    // ========================

    const showToast = (message) => {
        let toast = document.querySelector('.toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.className = 'toast';
            toast.style.cssText = `
                position: fixed;
                bottom: 100px;
                left: 50%;
                transform: translateX(-50%) translateY(20px);
                background: #0F172A;
                color: white;
                padding: 14px 28px;
                border-radius: 12px;
                font-size: 0.9rem;
                font-weight: 500;
                z-index: 9999;
                opacity: 0;
                transition: all 0.3s ease;
                box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            `;
            document.body.appendChild(toast);
        }

        toast.textContent = message;
        requestAnimationFrame(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateX(-50%) translateY(0)';
        });

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(-50%) translateY(20px)';
        }, 3000);
    };

    // ========================
    // TYPING PLACEHOLDER
    // ========================

    const initTypingEffect = () => {
        const searchInput = document.querySelector('.search-input');
        if (!searchInput) return;

        const placeholders = [
            'Cari iPhone 15 Pro Max...',
            'Cari Samsung Galaxy S24...',
            'Cari MacBook Air M3...',
            'Cari Sony WH-1000XM5...',
        ];

        let currentIndex = 0;

        setInterval(() => {
            currentIndex = (currentIndex + 1) % placeholders.length;
            searchInput.style.transition = 'opacity 0.3s';
            searchInput.style.opacity = '0.5';

            setTimeout(() => {
                searchInput.placeholder = placeholders[currentIndex];
                searchInput.style.opacity = '1';
            }, 150);
        }, 4000);
    };

    // ========================
    // HORIZONTAL SCROLL DRAG
    // ========================

    const initDragScroll = () => {
        const scrollContainers = document.querySelectorAll('.product-scroll');

        scrollContainers.forEach(container => {
            let isDown = false;
            let startX;
            let scrollLeft;

            container.addEventListener('mousedown', (e) => {
                isDown = true;
                container.style.cursor = 'grabbing';
                startX = e.pageX - container.offsetLeft;
                scrollLeft = container.scrollLeft;
            });

            container.addEventListener('mouseleave', () => {
                isDown = false;
                container.style.cursor = 'grab';
            });

            container.addEventListener('mouseup', () => {
                isDown = false;
                container.style.cursor = 'grab';
            });

            container.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - container.offsetLeft;
                const walk = (x - startX) * 2;
                container.scrollLeft = scrollLeft - walk;
            });

            // Set initial cursor
            container.style.cursor = 'grab';
        });
    };

    // ========================
    // CATEGORY SHOWCASE TABS
    // ========================

    const initCategoryShowcase = () => {
        const tabs = document.querySelectorAll('.cat-tab');
        const showcaseDisplay = document.getElementById('category-display');
        const hotProduct = document.getElementById('hot-product');

        if (!tabs.length || !showcaseDisplay) return;

        // Color map for categories
        const colorMap = {
            smartphone: { bg: 'linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%)', accent: '#2563EB' },
            laptop: { bg: 'linear-gradient(135deg, #7C3AED 0%, #5B21B6 100%)', accent: '#7C3AED' },
            tablet: { bg: 'linear-gradient(135deg, #EA580C 0%, #C2410C 100%)', accent: '#EA580C' },
            audio: { bg: 'linear-gradient(135deg, #16A34A 0%, #15803D 100%)', accent: '#16A34A' },
            wearable: { bg: 'linear-gradient(135deg, #D97706 0%, #B45309 100%)', accent: '#D97706' }
        };

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove active from all tabs
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');

                // Get data from tab
                const category = tab.dataset.category;
                const icon = tab.dataset.icon;
                const color = tab.dataset.color;
                const title = tab.dataset.title;
                const desc = tab.dataset.desc;
                const hotTitle = tab.dataset.hotTitle;
                const hotSpecs = tab.dataset.hotSpecs;
                const hotPrice = tab.dataset.hotPrice;

                // Animate showcase out
                showcaseDisplay.style.opacity = '0';
                showcaseDisplay.style.transform = 'translateY(10px)';

                setTimeout(() => {
                    // Update showcase content
                    showcaseDisplay.style.background = colorMap[category].bg;

                    const showcaseTitle = showcaseDisplay.querySelector('.showcase-title');
                    const showcaseDescEl = showcaseDisplay.querySelector('.showcase-desc');
                    const showcaseProducts = showcaseDisplay.querySelectorAll('.sp-img i');

                    if (showcaseTitle) showcaseTitle.textContent = title;
                    if (showcaseDescEl) showcaseDescEl.textContent = desc;

                    // Update product icons
                    showcaseProducts.forEach(p => {
                        p.className = icon;
                    });

                    // Animate showcase back in
                    showcaseDisplay.style.opacity = '1';
                    showcaseDisplay.style.transform = 'translateY(0)';
                }, 250);

                // Update hot product card
                if (hotProduct) {
                    const hotProductImg = hotProduct.querySelector('.product-placeholder i');
                    const hotCategoryEl = hotProduct.querySelector('.hot-category');
                    const hotTitleEl = hotProduct.querySelector('.hot-title');
                    const hotSpecsEl = hotProduct.querySelector('.hot-specs');
                    const hotPriceEl = hotProduct.querySelector('.hot-price');

                    // Animate out
                    hotProduct.style.opacity = '0.5';
                    hotProduct.style.transform = 'scale(0.98)';

                    setTimeout(() => {
                        if (hotProductImg) hotProductImg.className = icon;
                        if (hotCategoryEl) hotCategoryEl.textContent = title;
                        if (hotTitleEl) hotTitleEl.textContent = hotTitle;
                        if (hotSpecsEl) hotSpecsEl.textContent = hotSpecs;
                        if (hotPriceEl) hotPriceEl.textContent = hotPrice;

                        // Update accent color
                        if (hotCategoryEl) hotCategoryEl.style.color = color;
                        if (hotProductImg) {
                            hotProductImg.parentElement.style.background = `linear-gradient(135deg, ${color}20 0%, ${color}10 100%)`;
                            hotProductImg.style.color = color;
                        }

                        // Animate back in
                        hotProduct.style.opacity = '1';
                        hotProduct.style.transform = 'scale(1)';
                    }, 200);
                }
            });
        });

        // Add transition to showcase
        showcaseDisplay.style.transition = 'all 0.3s ease';
        if (hotProduct) hotProduct.style.transition = 'all 0.3s ease';
    };

    // ========================
    // TABLE OF CONTENTS GENERATOR
    // ========================

    const initTableOfContents = () => {
        const tocContainer = document.querySelector('.toc-list');
        const articleContent = document.querySelector('.article-content');

        if (!tocContainer || !articleContent) return;

        // Find all headings in the article
        const headings = articleContent.querySelectorAll('h2, h3, h4');

        if (headings.length === 0) {
            // Hide TOC card if no headings
            const tocCard = document.querySelector('.toc-card');
            if (tocCard) tocCard.style.display = 'none';
            return;
        }

        // Clear existing TOC
        tocContainer.innerHTML = '';

        // Generate TOC items
        headings.forEach((heading, index) => {
            // Create unique ID if not exists
            if (!heading.id) {
                heading.id = `heading-${index + 1}`;
            }

            const tocItem = document.createElement('a');
            tocItem.href = `#${heading.id}`;
            tocItem.className = 'toc-item';
            tocItem.textContent = heading.textContent;

            // Add indent class for h3 and h4
            if (heading.tagName === 'H3') {
                tocItem.classList.add('toc-h3');
                tocItem.style.paddingLeft = '1rem';
            } else if (heading.tagName === 'H4') {
                tocItem.classList.add('toc-h4');
                tocItem.style.paddingLeft = '1.5rem';
            }

            tocContainer.appendChild(tocItem);
        });

        // Highlight active TOC item on scroll
        const observerOptions = {
            rootMargin: '-100px 0px -70% 0px',
            threshold: 0
        };

        const tocObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                const id = entry.target.id;
                const tocLink = tocContainer.querySelector(`a[href="#${id}"]`);

                if (entry.isIntersecting) {
                    // Remove active from all
                    tocContainer.querySelectorAll('.toc-item').forEach(item => {
                        item.classList.remove('active');
                    });
                    // Add active to current
                    if (tocLink) {
                        tocLink.classList.add('active');
                    }
                }
            });
        }, observerOptions);

        headings.forEach(heading => {
            tocObserver.observe(heading);
        });
    };

    // ========================
    // ARCHIVE CATEGORY FILTERS
    // ========================

    const initArchiveFilters = () => {
        const filterBtns = document.querySelectorAll('.filter-btn[data-filter]');
        const filterableCards = document.querySelectorAll('[data-category]');

        if (!filterBtns.length || !filterableCards.length) return;

        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const filter = btn.dataset.filter;

                // Update active state
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                // Filter cards
                filterableCards.forEach(card => {
                    const cardCategory = card.dataset.category;

                    if (filter === 'all' || cardCategory === filter) {
                        card.style.display = '';
                        card.style.animation = 'fadeInUp 0.4s ease forwards';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    };

    // ========================
    // MOBILE MENU TOGGLE
    // ========================

    const initMobileMenu = () => {
        const menuToggle = document.getElementById('mobile-menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        const closeMenu = document.getElementById('close-mobile-menu');
        const mobileLinks = document.querySelectorAll('.mobile-nav-links a');

        if (!menuToggle || !mobileMenu) return;

        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.add('active');
            document.body.style.overflow = 'hidden';
        });

        const closeMobileMenu = () => {
            mobileMenu.classList.remove('active');
            document.body.style.overflow = '';
        };

        if (closeMenu) {
            closeMenu.addEventListener('click', closeMobileMenu);
        }

        mobileLinks.forEach(link => {
            link.addEventListener('click', closeMobileMenu);
        });

        // Close on outside click
        mobileMenu.addEventListener('click', (e) => {
            if (e.target === mobileMenu) {
                closeMobileMenu();
            }
        });
    };

    // ========================
    // SEARCH TOGGLE
    // ========================

    const initSearchToggle = () => {
        const searchToggle = document.getElementById('search-toggle');
        const searchOverlay = document.getElementById('search-overlay');
        const searchClose = document.getElementById('search-close');
        const searchInput = document.querySelector('.search-overlay .search-input');

        if (!searchToggle) return;

        searchToggle.addEventListener('click', () => {
            if (searchOverlay) {
                searchOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
                if (searchInput) searchInput.focus();
            }
        });

        if (searchClose) {
            searchClose.addEventListener('click', () => {
                searchOverlay.classList.remove('active');
                document.body.style.overflow = '';
            });
        }

        // Close on ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && searchOverlay?.classList.contains('active')) {
                searchOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    };

    // ========================
    // HERO CAROUSEL
    // ========================

    const initHeroCarousel = () => {
        const carousel = document.querySelector('.hero-carousel');
        const slides = document.querySelectorAll('.hero-slide');
        const indicators = document.querySelectorAll('.hero-indicator');

        if (!carousel || slides.length <= 1) return;

        let currentSlide = 0;
        let autoplayInterval;

        const goToSlide = (index) => {
            slides.forEach((slide, i) => {
                slide.classList.toggle('active', i === index);
            });
            indicators.forEach((ind, i) => {
                ind.classList.toggle('active', i === index);
            });
            currentSlide = index;
        };

        const nextSlide = () => {
            const next = (currentSlide + 1) % slides.length;
            goToSlide(next);
        };

        // Click on indicators
        indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => {
                goToSlide(index);
                resetAutoplay();
            });
        });

        // Autoplay
        const startAutoplay = () => {
            autoplayInterval = setInterval(nextSlide, 5000);
        };

        const resetAutoplay = () => {
            clearInterval(autoplayInterval);
            startAutoplay();
        };

        startAutoplay();
    };

    // ========================
    // SHARE BUTTONS
    // ========================

    const initShareButtons = () => {
        const shareButtons = document.querySelectorAll('.share-btn');

        shareButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const platform = btn.dataset.platform;
                const url = encodeURIComponent(window.location.href);
                const title = encodeURIComponent(document.title);

                let shareUrl = '';

                switch (platform) {
                    case 'facebook':
                        shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
                        break;
                    case 'twitter':
                        shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
                        break;
                    case 'whatsapp':
                        shareUrl = `https://wa.me/?text=${title}%20${url}`;
                        break;
                    case 'linkedin':
                        shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${url}`;
                        break;
                    case 'copy':
                        navigator.clipboard.writeText(window.location.href).then(() => {
                            showToast('Link berhasil disalin!');
                        });
                        return;
                }

                if (shareUrl) {
                    window.open(shareUrl, '_blank', 'width=600,height=400');
                }
            });
        });
    };

    // ========================
    // INITIALIZE ALL
    // ========================

    initAnimations();
    init3DCards();
    initHeaderEffect();
    initSmoothScroll();
    initComparisonSystem();
    initTypingEffect();
    initDragScroll();
    initCategoryShowcase();
    initTableOfContents();
    initArchiveFilters();
    initMobileMenu();
    initSearchToggle();
    initHeroCarousel();
    initShareButtons();

    console.log('🚀 AffosWP UI/UX v2 Loaded');
});
