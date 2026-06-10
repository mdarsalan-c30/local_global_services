/**
 * Local Global Services (LGS) - Core Application Logic
 * Integrates database-driven dynamic navbar rendering, translation systems, stats counting, and AJAX contact logs.
 */

document.addEventListener('DOMContentLoaded', () => {
    // Global variable to hold CMS configurations
    let cmsSettings = null;

    // -------------------------------------------------------------
    // 1. Dynamic Database-driven Navbar Rendering
    // -------------------------------------------------------------
    const navbarContent = document.getElementById('navbarContent');
    let dynamicMenuData = []; // Stores database menu entries
    
    if (navbarContent) {
        loadDynamicNavbar();
    }
    
    async function loadDynamicNavbar() {
        try {
            const response = await fetch('api/get_menu.php');
            const result = await response.json();
            
            if (result.success && result.data) {
                dynamicMenuData = result.data;
                renderNavbar(result.data);
            }
        } catch (err) {
            console.error("Failed to load database menus, falling back to static structure:", err);
        }
    }
    
    function renderNavbar(menuTree) {
        navbarContent.innerHTML = '';
        
        // Helper to format navbar link with icon
        const getLinkContent = (item) => {
            let iconHtml = '';
            if (item.icon && item.icon.trim() !== '') {
                iconHtml = `<i class="fa-solid ${item.icon} me-2 text-danger" style="font-size: 0.8rem; width: 14px; text-align: center; display: inline-block; vertical-align: middle;"></i>`;
            }
            return `${iconHtml}<span style="vertical-align: middle;">${item.name}</span>`;
        };
        
        // Build primary ul list
        const ul = document.createElement('ul');
        ul.className = 'navbar-nav mx-auto mb-2 mb-lg-0';
        
        // Always add Home first
        const homeLi = document.createElement('li');
        homeLi.className = 'nav-item';
        homeLi.innerHTML = `<a class="nav-link" href="index.html#home" data-lang-key="nav_home">Home</a>`;
        ul.appendChild(homeLi);
        
        menuTree.forEach(menu => {
            // Filter out 'News & Resources' from the main navbar since we will shift it to the top bar
            if (menu.name.toLowerCase().includes('news') || menu.name.toLowerCase().includes('resource')) {
                return;
            }
            const li = document.createElement('li');
            
            if (menu.submenus && menu.submenus.length > 0) {
                // If it is Product & Services (Mega Menu Column Layout)
                if (menu.id === 2 || menu.name.toLowerCase().includes('product') || menu.name.toLowerCase().includes('service')) {
                    li.className = 'nav-item dropdown mega-menu-parent';
                    
                    // Mega menu container structure
                    let megaHtml = `
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            ${menu.name}
                        </a>
                        <div class="dropdown-mega-menu">
                            <div class="container-fluid">
                                <div class="row g-4">
                    `;
                    
                    // High-fidelity groupings mapping directly to the client's Excel sheet divisions
                    const columnsData = [
                        {
                            title: 'SAP Core ERP & Cloud',
                            icon: 'fa-cube',
                            groups: [
                                {
                                    name: 'Integrated SAP & Digital Transformation',
                                    category_key: 'sap-digital-transform',
                                    keys: ['sap-s4hana', 'rise-sap', 'grow-sap', 'continuous-ams', 'sap-digital-transform', 'sap-migration']
                                },
                                {
                                    name: 'Digital Innovation',
                                    category_key: 'sap-innovation',
                                    keys: ['sap-innovation', 'sap-btp', 'sap-cloud']
                                },
                                {
                                    name: 'SAP Cloud Services',
                                    category_key: 'sap-cloud-services',
                                    keys: ['sap-cloud-services', 's4-hana-cloud', 'sap-successfactors', 'sap-ariba']
                                }
                            ]
                        },
                        {
                            title: 'Analytics & Specialized Modules',
                            icon: 'fa-layer-group',
                            groups: [
                                {
                                    name: 'Business Analytics',
                                    category_key: 'sap-analytics',
                                    keys: ['sap-analytics', 'sap-analytics-cloud']
                                },
                                {
                                    name: 'FIORI Enablement',
                                    category_key: 'fiori-enablement',
                                    keys: ['fiori-enablement']
                                },
                                {
                                    name: 'Specialized SAP Module Services',
                                    category_key: 'sap-specialized-modules',
                                    keys: ['sap-specialized-modules', 'sap-hcm', 'sap-refx', 'sap-dms', 'sap-ehs', 'sap-opentext']
                                }
                            ]
                        },
                        {
                            title: 'Internet of Things (IoT)',
                            icon: 'fa-wifi',
                            groups: [
                                {
                                    name: 'IoT Production & Logistics',
                                    category_key: 'iot-core',
                                    keys: ['iot-core', 'iot-production', 'iot-vehicle', 'iot-fuel', 'iot-bus', 'iot-temp', 'iot-equipment', 'iot-rfid']
                                }
                            ]
                        },
                        {
                            title: 'Custom Applications & AI',
                            icon: 'fa-microchip',
                            groups: [
                                {
                                    name: 'Resource Augmentation',
                                    category_key: 'resource-augmentation',
                                    keys: ['resource-augmentation']
                                },
                                {
                                    name: 'Enterprise Cyber Security',
                                    category_key: 'cyber-security',
                                    keys: ['cyber-security']
                                }
                            ]
                        }
                    ];
                    
                    // Track items already placed to prevent duplicates
                    const placedIds = new Set();
                    const columnsHtml = ['', '', '', ''];
                    
                    columnsData.forEach((col, index) => {
                        let colHtml = '';
                        
                        col.groups.forEach(grp => {
                            const matchedItems = menu.submenus.filter(sub => {
                                const key = sub.service_key.toLowerCase();
                                return grp.keys.some(k => key.includes(k)) && !placedIds.has(sub.id);
                            });
                            
                            if (matchedItems.length > 0) {
                                // Add Group Header and list wrapped in a collapsible group container
                                colHtml += `<div class="mega-menu-group">`;
                                colHtml += `<div class="mega-menu-subheading"><a href="service.html?id=${grp.category_key}" class="subheading-link text-decoration-none">${grp.name}</a> <i class="fa-solid fa-chevron-down ms-2 small transition-icon toggle-trigger"></i></div>`;
                                colHtml += `<ul class="mega-menu-list">`;
                                matchedItems.forEach(item => {
                                    colHtml += `<li><a href="service.html?id=${item.service_key}">${getLinkContent(item)}</a></li>`;
                                    placedIds.add(item.id);
                                });
                                colHtml += `</ul>`;
                                colHtml += `</div>`;
                            }
                        });
                        
                        columnsHtml[index] = colHtml;
                    });
                    
                    // Final universal fallback for any remaining items (appended directly to Column 2!)
                    const finalUnmatched = menu.submenus.filter(sub => !placedIds.has(sub.id));
                    if (finalUnmatched.length > 0) {
                        let fallbackHtml = `<div class="mega-menu-group">`;
                        fallbackHtml += `<div class="mega-menu-subheading"><a href="service.html?id=ai-core" class="subheading-link text-decoration-none">Enterprise Services</a> <i class="fa-solid fa-chevron-down ms-2 small transition-icon toggle-trigger"></i></div>`;
                        fallbackHtml += `<ul class="mega-menu-list">`;
                        finalUnmatched.forEach(item => {
                            fallbackHtml += `<li><a href="service.html?id=${item.service_key}">${getLinkContent(item)}</a></li>`;
                        });
                        fallbackHtml += `</ul>`;
                        fallbackHtml += `</div>`;
                        
                        columnsHtml[1] += fallbackHtml;
                    }
                    
                    // Render columns HTML
                    columnsHtml.forEach((html, index) => {
                        if (html !== '') {
                            const col = columnsData[index];
                            megaHtml += `
                                <div class="col-md-3">
                                    <div class="mega-menu-title"><i class="fa-solid ${col.icon}"></i> ${col.title}</div>
                                    ${html}
                                </div>
                            `;
                        }
                    });
                    
                    megaHtml += `
                                </div>
                            </div>
                        </div>
                    `;
                    li.innerHTML = megaHtml;
                } else {
                    // Standard Bootstrap Dropdown
                    li.className = 'nav-item dropdown';
                    let dropdownHtml = `
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            ${menu.name}
                        </a>
                        <ul class="dropdown-menu">
                    `;
                    menu.submenus.forEach(sub => {
                        dropdownHtml += `<li><a class="dropdown-item" href="service.html?id=${sub.service_key}">${getLinkContent(sub)}</a></li>`;
                    });
                    dropdownHtml += `</ul>`;
                    li.innerHTML = dropdownHtml;
                }
            } else {
                // Standard single link
                li.className = 'nav-item';
                li.innerHTML = `<a class="nav-link" href="index.html#${menu.name.toLowerCase().replace(/[^a-z0-9]/g, '')}">${menu.name}</a>`;
            }
            
            ul.appendChild(li);
        });
        
        navbarContent.appendChild(ul);
        
        // Append visual CTA button
        const ctaBtn = document.createElement('a');
        ctaBtn.href = 'index.html#contact';
        ctaBtn.className = 'btn btn-accent-brand';
        ctaBtn.id = 'cta-get-in-touch';
        ctaBtn.setAttribute('data-lang-key', 'btn_get_started');
        ctaBtn.textContent = translations[currentLang]?.btn_get_started || 'Get in Touch';
        navbarContent.appendChild(ctaBtn);
        
        // Re-apply language translations to new elements
        applyLanguage(currentLang);
        
        // Highlight active navbar selection
        highlightActiveNavItem();
    }
    
    function highlightActiveNavItem() {
        const path = window.location.pathname;
        const search = window.location.search;
        const navLinks = document.querySelectorAll('.navbar-nav .nav-link, .dropdown-item, .mega-menu-list li a');
        
        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href) {
                if (href.includes(path) && (search === '' || href.includes(search))) {
                    link.classList.add('active');
                    // Add active to parent dropdown as well
                    const parentDropdown = link.closest('.dropdown');
                    if (parentDropdown) {
                        const toggle = parentDropdown.querySelector('.dropdown-toggle');
                        if (toggle) toggle.classList.add('active');
                    }
                } else {
                    link.classList.remove('active');
                }
            }
        });
    }



    // -------------------------------------------------------------
    // 2. Language Switching Engine
    // -------------------------------------------------------------
    const currentLangBtn = document.getElementById('currentLangBtn');
    const langDropdownItems = document.querySelectorAll('.lang-dropdown-menu li a');
    
    let currentLang = localStorage.getItem('site_language') || 'en';
    applyLanguage(currentLang);
    
    langDropdownItems.forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            applyLanguage(item.getAttribute('data-lang'));
        });
    });
    
    function applyLanguage(lang) {
        if (!translations[lang]) return;
        
        currentLang = lang;
        localStorage.setItem('site_language', lang);
        
        const dictionary = translations[lang];
        document.documentElement.setAttribute('dir', dictionary.dir);
        document.documentElement.setAttribute('lang', lang);
        
        const langNames = {
            en: 'English (EN)', ar: 'العربية (AR)', de: 'Deutsch (DE)', es: 'Español (ES)', fr: 'Français (FR)'
        };
        if (currentLangBtn) currentLangBtn.textContent = langNames[lang];
        
        document.querySelectorAll('[data-lang-key]').forEach(el => {
            const key = el.getAttribute('data-lang-key');
            let textValue = dictionary[key];
            
            // Override with CMS value if current language is English and CMS has the key
            if (lang === 'en' && cmsSettings) {
                if (key === 'about_title' && cmsSettings.about_title) {
                    textValue = cmsSettings.about_title;
                } else if (key === 'about_subtitle' && cmsSettings.about_subtitle) {
                    textValue = cmsSettings.about_subtitle;
                } else if (key === 'about_desc_1' && cmsSettings.about_desc_1) {
                    textValue = cmsSettings.about_desc_1;
                } else if (key === 'about_desc_2' && cmsSettings.about_desc_2) {
                    textValue = cmsSettings.about_desc_2;
                }
            }
            
            if (textValue) {
                if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
                    el.setAttribute('placeholder', textValue);
                } else if (el.tagName === 'SELECT') {
                    const firstOption = el.options[0];
                    if (firstOption) firstOption.text = textValue;
                } else {
                    el.innerHTML = textValue;
                }
            }
        });
    }

    // -------------------------------------------------------------
    // 3. Sticky Header Control
    // -------------------------------------------------------------
    const mainNav = document.getElementById('mainNav');
    if (mainNav) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                mainNav.classList.add('scrolled');
            } else {
                mainNav.classList.remove('scrolled');
            }
        });
    }

    // -------------------------------------------------------------
    // 4. Statistics Count-Up Animator
    // -------------------------------------------------------------
    const statBoxes = document.querySelectorAll('.stat-box');
    if (statBoxes.length > 0) {
        const observerOptions = { threshold: 0.5, rootMargin: '0px' };
        
        const countObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const numElement = entry.target.querySelector('.stat-number');
                    const targetValStr = numElement.getAttribute('data-target');
                    
                    if (targetValStr.toUpperCase().includes('X')) {
                        numElement.textContent = targetValStr;
                    } else {
                        const targetVal = parseInt(targetValStr.replace(/[^0-9]/g, ''));
                        const hasPlus = targetValStr.includes('+');
                        const hasComma = targetValStr.includes(',');
                        const hasPercent = targetValStr.includes('%');
                        
                        animateCount(numElement, targetVal, hasPlus, hasComma, hasPercent);
                    }
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);
        
        statBoxes.forEach(box => countObserver.observe(box));
    }
    
    function animateCount(el, target, plus, comma, percent) {
        let current = 0;
        const duration = 1500;
        const increment = target / (duration / 16);
        
        const counter = setInterval(() => {
            current += increment;
            if (current >= target) {
                clearInterval(counter);
                current = target;
            }
            
            let displayVal = Math.floor(current);
            if (comma) displayVal = displayVal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            if (plus) displayVal = displayVal + '+';
            if (percent) displayVal = displayVal + '%';
            
            el.textContent = displayVal;
        }, 16);
    }

    // -------------------------------------------------------------
    // 5. AJAX Form Submission
    // -------------------------------------------------------------
    const contactForm = document.getElementById('contactForm');
    const formNotice = document.getElementById('formNotice');
    
    if (contactForm) {
        contactForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            formNotice.className = 'alert alert-info d-block';
            formNotice.innerHTML = `<i class="fa-solid fa-arrows-spin fa-spin me-2"></i> <span data-lang-key="msg_submitting">${translations[currentLang].msg_submitting}</span>`;
            
            try {
                const response = await fetch('api/contact.php', {
                    method: 'POST',
                    body: new FormData(contactForm)
                });
                const result = await response.json();
                
                if (result.success) {
                    formNotice.className = 'alert alert-success d-block';
                    formNotice.innerHTML = `<i class="fa-regular fa-circle-check me-2"></i> <span data-lang-key="msg_success">${translations[currentLang].msg_success}</span>`;
                    contactForm.reset();
                } else {
                    formNotice.className = 'alert alert-danger d-block';
                    formNotice.innerHTML = `<i class="fa-solid fa-circle-exclamation me-2"></i> <span data-lang-key="msg_error">${translations[currentLang].msg_error}</span>`;
                }
            } catch (err) {
                console.error(err);
                formNotice.className = 'alert alert-danger d-block';
                formNotice.innerHTML = `<i class="fa-solid fa-circle-exclamation me-2"></i> <span data-lang-key="msg_error">${translations[currentLang].msg_error}</span>`;
            }
        });
    }

    // Toggle collapsible submenus on click/tap (excellent for mobile and touch support)
    document.addEventListener('click', (e) => {
        const toggleTrigger = e.target.closest('.toggle-trigger');
        if (toggleTrigger) {
            const subheading = toggleTrigger.closest('.mega-menu-subheading');
            if (subheading) {
                const group = subheading.closest('.mega-menu-group');
                if (group) {
                    // Toggle active state
                    group.classList.toggle('active');
                    
                    // Toggle rotation of caret
                    if (group.classList.contains('active')) {
                        toggleTrigger.style.transform = 'rotate(180deg)';
                    } else {
                        toggleTrigger.style.transform = '';
                    }
                }
            }
        }
    });

    // -------------------------------------------------------------
    // 6. Dynamic Website Settings Synchronization
    // -------------------------------------------------------------
    async function loadDynamicContactSettings() {
        try {
            // Adjust API path depending on current sub-path
            const apiPath = window.location.pathname.includes('/admin/') ? '../api/get_settings.php' : 'api/get_settings.php';
            const response = await fetch(apiPath);
            const result = await response.json();
            
            if (result.success && result.data) {
                cmsSettings = result.data;
                
                const phone = result.data.contact_phone;
                const email = result.data.contact_email;
                
                if (phone) {
                    document.querySelectorAll('.js-contact-phone').forEach(el => {
                        const icon = el.querySelector('i');
                        if (icon) {
                            el.innerHTML = '';
                            el.appendChild(icon);
                            el.appendChild(document.createTextNode(' ' + phone));
                        } else {
                            el.textContent = phone;
                        }
                        if (el.tagName === 'A') {
                            el.setAttribute('href', 'tel:' + phone.replace(/[^+\d]/g, ''));
                        }
                    });
                }
                
                if (email) {
                    document.querySelectorAll('.js-contact-email').forEach(el => {
                        const icon = el.querySelector('i');
                        if (icon) {
                            el.innerHTML = '';
                            el.appendChild(icon);
                            el.appendChild(document.createTextNode(' ' + email));
                        } else {
                            el.textContent = email;
                        }
                        if (el.tagName === 'A') {
                            el.setAttribute('href', 'mailto:' + email);
                        }
                    });
                }
                
                // Hero Background Image and Opacity overlay
                const homeEl = document.getElementById('home');
                if (homeEl) {
                    const bgImage = result.data.hero_bg_image;
                    const opacity = result.data.hero_bg_opacity !== undefined ? parseFloat(result.data.hero_bg_opacity) : 0.20;
                    const overlayOpacity = 1 - opacity;
                    if (bgImage && bgImage.trim() !== '') {
                        homeEl.style.backgroundImage = `linear-gradient(180deg, rgba(255, 255, 255, ${overlayOpacity}) 0%, rgba(255, 255, 255, ${overlayOpacity}) 100%), url('${bgImage}')`;
                    } else {
                        homeEl.style.backgroundImage = `linear-gradient(180deg, rgba(255, 255, 255, ${overlayOpacity}) 0%, rgba(255, 255, 255, ${overlayOpacity}) 100%)`;
                    }
                }
                
                // About Section Image
                const aboutImage = result.data.about_image;
                if (aboutImage && aboutImage.trim() !== '') {
                    const el = document.getElementById('about-section-img');
                    if (el) el.src = aboutImage;
                }
                
                // Re-apply language translations to use the CMS overrides
                applyLanguage(currentLang);
            }
        } catch (err) {
            console.error("Failed to load settings dynamically:", err);
        }
    }
    loadDynamicContactSettings();

    // Load dynamic testimonials
    loadTestimonials();

    // Load dynamic industries
    loadDynamicIndustries();

    // Check and show popup
    checkAndShowPopup();

    // -------------------------------------------------------------
    // 7. Load Testimonials
    // -------------------------------------------------------------
    async function loadTestimonials() {
        const track = document.getElementById('testimonial-track-container');
        if (!track) return;
        try {
            const response = await fetch('api/get_testimonials.php');
            const result = await response.json();
            if (result.success && result.data && result.data.length > 0) {
                track.innerHTML = '';
                
                const renderCard = (t) => {
                    const card = document.createElement('div');
                    card.className = 'testimonial-card';
                    
                    // Generate avatar or fallback
                    let avatarHtml = '';
                    if (t.image_url && t.image_url.trim() !== '') {
                        avatarHtml = `<img class="testimonial-avatar" src="${t.image_url}" alt="${t.client_name}">`;
                    } else {
                        // Create initials
                        const initials = t.client_name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
                        avatarHtml = `<div class="testimonial-fallback-avatar">${initials}</div>`;
                    }
                    
                    card.innerHTML = `
                        <div class="testimonial-quote">
                            ${t.testimonial_text}
                        </div>
                        <div class="testimonial-client">
                            ${avatarHtml}
                            <div class="testimonial-info">
                                <h5>${t.client_name}</h5>
                                <span>${t.service_name}</span>
                            </div>
                        </div>
                    `;
                    return card;
                };
                
                // Append cards multiple times (at least 3 sets) to ensure infinite scroll fills any screen width
                const cardCount = result.data.length;
                for (let i = 0; i < 3; i++) {
                    result.data.forEach(t => track.appendChild(renderCard(t)));
                }
                
                // Start the horizontal scroll loop
                initTestimonialAutoScroll(cardCount);
            }
        } catch (err) {
            console.error("Failed to load testimonials:", err);
        }
    }

    function initTestimonialAutoScroll(numCards) {
        const container = document.querySelector('.testimonial-scroll-container');
        const track = document.getElementById('testimonial-track-container');
        if (!container || !track || track.children.length <= numCards) return;
        
        let scrollSpeed = 1; // speed: 1px per step
        let intervalTime = 30; // step every 30ms (approx 33fps)
        let timer = null;
        let isHovered = false;
        
        container.addEventListener('mouseenter', () => { isHovered = true; });
        container.addEventListener('mouseleave', () => { isHovered = false; });
        
        // Touch events for mobile support
        container.addEventListener('touchstart', () => { isHovered = true; }, { passive: true });
        container.addEventListener('touchend', () => { isHovered = false; }, { passive: true });
        
        // Calculate the exact wrap width (the offset distance of one full set of cards)
        const firstCardSecondSet = track.children[numCards];
        const firstCardFirstSet = track.children[0];
        const wrapWidth = firstCardSecondSet.offsetLeft - firstCardFirstSet.offsetLeft;
        
        function step() {
            if (!isHovered) {
                if (container.scrollLeft >= wrapWidth) {
                    container.scrollLeft = container.scrollLeft - wrapWidth;
                }
                container.scrollLeft += scrollSpeed;
            }
        }
        
        timer = setInterval(step, intervalTime);
    }

    // -------------------------------------------------------------
    // 8. Load Dynamic Industries
    // -------------------------------------------------------------
    async function loadDynamicIndustries() {
        const pills = document.getElementById('industryPills');
        const content = document.getElementById('industryPillsContent');
        if (!pills || !content) return;
        
        try {
            const response = await fetch('api/get_industries.php');
            const result = await response.json();
            if (result.success && result.data && result.data.length > 0) {
                pills.innerHTML = '';
                content.innerHTML = '';
                
                result.data.forEach((ind, index) => {
                    const isActive = index === 0;
                    
                    // Create Pill button
                    const btn = document.createElement('button');
                    btn.className = `nav-link ${isActive ? 'active' : ''}`;
                    btn.id = `industry-tab-${ind.id}`;
                    btn.setAttribute('data-bs-toggle', 'pill');
                    btn.setAttribute('data-bs-target', `#industry-pane-${ind.id}`);
                    btn.setAttribute('type', 'button');
                    btn.setAttribute('role', 'tab');
                    btn.setAttribute('aria-controls', `industry-pane-${ind.id}`);
                    btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
                    btn.innerHTML = `<i class="fa-solid ${ind.icon} me-2"></i> ${ind.name}`;
                    pills.appendChild(btn);
                    
                    // Parse Features Pointers
                    let featuresHtml = '';
                    if (ind.features && ind.features.trim() !== '') {
                        const featuresList = ind.features.split('\n').map(f => f.trim()).filter(f => f !== '');
                        if (featuresList.length > 0) {
                            featuresHtml = '<ul class="list-unstyled mt-4 d-flex flex-column gap-2">';
                            featuresList.forEach(feat => {
                                featuresHtml += `
                                    <li class="d-flex align-items-start text-muted" style="font-size: 1rem;">
                                        <i class="fa-solid fa-circle-check text-danger me-2 mt-1" style="font-size: 1rem;"></i>
                                        <span>${feat}</span>
                                    </li>
                                `;
                            });
                            featuresHtml += '</ul>';
                        }
                    }
                    
                    // Create Pane div
                    const pane = document.createElement('div');
                    pane.className = `tab-pane fade ${isActive ? 'show active' : ''}`;
                    pane.id = `industry-pane-${ind.id}`;
                    pane.setAttribute('role', 'tabpanel');
                    pane.setAttribute('aria-labelledby', `industry-tab-${ind.id}`);
                    
                    pane.innerHTML = `
                        <div class="tab-pane-content">
                            <div class="row align-items-center g-4">
                                <div class="col-md-7">
                                    <h3 class="fw-bold mb-3" style="color: var(--primary-blue);">${ind.title}</h3>
                                    <p class="text-muted mb-0 lead" style="font-size: 1.05rem;">${ind.description}</p>
                                    ${featuresHtml}
                                </div>
                                <div class="col-md-5 text-center">
                                    ${ind.image_url ? `<img src="${ind.image_url}" class="img-fluid rounded border shadow-sm" style="max-height: 250px; width: 100%; object-fit: cover;" alt="${ind.title}">` : `<div style="font-size: 8rem; color: rgba(11, 53, 109, 0.08);"><i class="fa-solid ${ind.icon}"></i></div>`}
                                </div>
                            </div>
                        </div>
                    `;
                    content.appendChild(pane);
                });
            }
        } catch (err) {
            console.error("Failed to load dynamic industries:", err);
        }
    }

    // -------------------------------------------------------------
    // 9. Center Popup Handler
    // -------------------------------------------------------------
    async function checkAndShowPopup() {
        const overlay = document.getElementById('popupOverlay');
        const content = document.getElementById('popupContent');
        const closeBtn = document.getElementById('popupCloseBtn');
        if (!overlay || !content || !closeBtn) return;
        
        try {
            const response = await fetch('api/get_settings.php');
            const result = await response.json();
            if (result.success && result.data) {
                const s = result.data;
                const status = s.popup_status;
                const type = s.popup_type;
                const title = s.popup_title || '';
                const text = s.popup_text || '';
                const image = s.popup_image || '';
                
                if (status === 'show') {
                    // Check sessionStorage
                    if (!sessionStorage.getItem('lgs_popup_shown')) {
                        content.innerHTML = '';
                        
                        let hasImg = image && image.trim() !== '';
                        
                        if (type === 'image' && hasImg) {
                            content.innerHTML = `<img src="${image}" class="popup-img-only" alt="${title || 'Announcement'}">`;
                        } else if (type === 'text') {
                            content.innerHTML = `
                                <div class="popup-text-body">
                                    <h3>${title}</h3>
                                    <p>${text}</p>
                                </div>
                            `;
                        } else if (type === 'both') {
                            if (hasImg) {
                                content.innerHTML = `
                                    <img src="${image}" class="popup-img-only" alt="${title}">
                                    <div class="popup-text-body">
                                        <h3>${title}</h3>
                                        <p>${text}</p>
                                    </div>
                                `;
                            } else {
                                // Fallback to text only if image is not set
                                content.innerHTML = `
                                    <div class="popup-text-body">
                                        <h3>${title}</h3>
                                        <p>${text}</p>
                                    </div>
                                `;
                            }
                        } else {
                            // If invalid configuration or type is image but no image URL
                            return;
                        }
                        
                        // Show overlay smoothly
                        overlay.style.display = 'flex';
                        // Force a reflow
                        overlay.offsetHeight;
                        overlay.classList.add('show');
                        
                        // Close event handlers
                        const closePopup = () => {
                            sessionStorage.setItem('lgs_popup_shown', 'true');
                            overlay.classList.remove('show');
                            setTimeout(() => {
                                overlay.style.display = 'none';
                            }, 400); // match transition duration
                        };
                        
                        closeBtn.addEventListener('click', closePopup);
                        overlay.addEventListener('click', (e) => {
                            if (e.target === overlay) {
                                closePopup();
                            }
                        });
                    }
                }
            }
        } catch (err) {
            console.error("Failed to check popup status:", err);
        }
    }
});
