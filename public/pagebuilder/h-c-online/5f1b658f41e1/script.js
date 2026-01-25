// ===== SCROLL ANIMATIONS =====
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const animateOnScroll = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('in-view');
        }
    });
}, observerOptions);

// Observe all elements with data-animate attribute
document.addEventListener('DOMContentLoaded', () => {
    const animatedElements = document.querySelectorAll('[data-animate]');
    animatedElements.forEach(el => animateOnScroll.observe(el));
});

// ===== FAQ ACCORDION =====
function initFAQ() {
    const faqItems = document.querySelectorAll('.faq-item');
    
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        
        question.addEventListener('click', () => {
            const isActive = item.classList.contains('active');
            
            // Close all FAQ items
            faqItems.forEach(faq => faq.classList.remove('active'));
            
            // Open clicked item if it wasn't active
            if (!isActive) {
                item.classList.add('active');
            }
        });
    });
}

// ===== COUNTDOWN TIMER =====
function initCountdown() {
    // Set target date to Feb 28, 2026
    const targetDate = new Date('2026-02-28T23:59:59').getTime();
    
    function updateCountdown() {
        const now = new Date().getTime();
        const distance = targetDate - now;
        
        if (distance < 0) {
            document.getElementById('countdown').innerHTML = '<p>Đợt tuyển sinh đã kết thúc!</p>';
            return;
        }
        
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        document.getElementById('days').textContent = days;
        document.getElementById('hours').textContent = hours;
        document.getElementById('minutes').textContent = minutes;
        document.getElementById('seconds').textContent = seconds;
    }
    
    // Update immediately
    updateCountdown();
    
    // Update every second
    setInterval(updateCountdown, 1000);
}

// ===== SMOOTH SCROLL TO FORM =====
function scrollToForm() {
    const form = document.getElementById('registration-form');
    if (form) {
        form.scrollIntoView({ 
            behavior: 'smooth',
            block: 'start'
        });
        
        // Add a subtle shake animation to draw attention
        const formContainer = form.querySelector('.form-container');
        formContainer.style.animation = 'none';
        setTimeout(() => {
            formContainer.style.animation = 'shake 0.5s ease-in-out';
        }, 100);
    }
}

// Shake animation keyframes
const shakeKeyframes = `
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }
`;

// Add shake animation to stylesheet
const styleSheet = document.createElement('style');
styleSheet.textContent = shakeKeyframes;
document.head.appendChild(styleSheet);

// Make scrollToForm available globally
window.scrollToForm = scrollToForm;

// ===== FORM HANDLING =====
function initForm() {
    const form = document.getElementById('mainForm');
    
    if (form) {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            
            // Show success message
            showSuccessMessage();
            
            // Log form data (in production, you'd send this to a server)
            console.log('Form submitted:', data);
            
            // Reset form
            form.reset();
        });
    }
}

function showSuccessMessage() {
    // Create success overlay
    const overlay = document.createElement('div');
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        animation: fadeIn 0.3s ease-out;
    `;
    
    const messageBox = document.createElement('div');
    messageBox.style.cssText = `
        background: white;
        padding: 3rem;
        border-radius: 24px;
        max-width: 500px;
        text-align: center;
        animation: scaleIn 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    `;
    
    messageBox.innerHTML = `
        <div style="font-size: 4rem; margin-bottom: 1rem;">🎉</div>
        <h2 style="color: #003D6F; margin-bottom: 1rem; font-family: 'Outfit', sans-serif;">Đăng Ký Thành Công!</h2>
        <p style="color: #4A4A4A; margin-bottom: 2rem; line-height: 1.6;">
            Cảm ơn bạn đã đăng ký! Tư vấn viên sẽ liên hệ với bạn trong vòng 2 giờ.
        </p>
        <button onclick="this.closest('div[style*=fixed]').remove()" style="
            background: linear-gradient(135deg, #FF6B35, #FF8C5F);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 100px;
            font-size: 1.125rem;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Outfit', sans-serif;
        ">Đóng</button>
    `;
    
    overlay.appendChild(messageBox);
    document.body.appendChild(overlay);
    
    // Add animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes scaleIn {
            from { transform: scale(0.7); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
    `;
    document.head.appendChild(style);
    
    // Auto close after 5 seconds
    setTimeout(() => {
        overlay.style.animation = 'fadeOut 0.3s ease-out forwards';
        setTimeout(() => overlay.remove(), 300);
    }, 5000);
}

// ===== HEADER SCROLL EFFECT =====
function initHeaderScroll() {
    const header = document.querySelector('.header');
    let lastScroll = 0;
    
    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll > 100) {
            header.style.boxShadow = '0 4px 20px rgba(0, 61, 111, 0.15)';
        } else {
            header.style.boxShadow = '0 2px 8px rgba(0, 61, 111, 0.08)';
        }
        
        // Hide header on scroll down, show on scroll up
        if (currentScroll > lastScroll && currentScroll > 500) {
            header.style.transform = 'translateY(-100%)';
        } else {
            header.style.transform = 'translateY(0)';
        }
        
        lastScroll = currentScroll;
    });
}

// ===== MARQUEE DUPLICATION FOR INFINITE SCROLL =====
function initMarquee() {
    const marqueeContent = document.querySelector('.marquee-content');
    if (marqueeContent) {
        // Clone the content to create seamless loop
        const clone = marqueeContent.cloneNode(true);
        marqueeContent.parentElement.appendChild(clone);
    }
}

// ===== PHONE NUMBER FORMATTING =====
function initPhoneFormat() {
    const phoneInput = document.getElementById('phone');
    
    if (phoneInput) {
        phoneInput.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            
            if (value.length > 10) {
                value = value.slice(0, 10);
            }
            
            // Format as: 0901 234 567
            if (value.length > 4 && value.length <= 7) {
                value = value.slice(0, 4) + ' ' + value.slice(4);
            } else if (value.length > 7) {
                value = value.slice(0, 4) + ' ' + value.slice(4, 7) + ' ' + value.slice(7);
            }
            
            e.target.value = value;
        });
    }
}

// ===== PARALLAX EFFECT =====
function initParallax() {
    const hero = document.querySelector('.hero');
    
    if (hero) {
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const parallaxElements = hero.querySelectorAll('.hero-title, .hero-subtitle, .hero-badges');
            
            parallaxElements.forEach((el, index) => {
                const speed = (index + 1) * 0.1;
                el.style.transform = `translateY(${scrolled * speed}px)`;
            });
        });
    }
}

// ===== TESTIMONIAL AUTO-ROTATE =====
function initTestimonialRotate() {
    const testimonials = document.querySelectorAll('.testimonial-card');
    let currentIndex = 0;
    
    function highlightTestimonial() {
        testimonials.forEach((card, index) => {
            if (index === currentIndex) {
                card.style.transform = 'scale(1.05)';
                card.style.boxShadow = '0 12px 48px rgba(0, 61, 111, 0.2)';
            } else {
                card.style.transform = 'scale(1)';
                card.style.boxShadow = '0 4px 20px rgba(0, 61, 111, 0.12)';
            }
        });
        
        currentIndex = (currentIndex + 1) % testimonials.length;
    }
    
    // Initial highlight
    if (testimonials.length > 0) {
        highlightTestimonial();
        // Rotate every 5 seconds
        setInterval(highlightTestimonial, 5000);
    }
}

// ===== FORM INPUT ANIMATIONS =====
function initFormAnimations() {
    const inputs = document.querySelectorAll('.form-group input, .form-group select');
    
    inputs.forEach(input => {
        input.addEventListener('focus', () => {
            input.parentElement.style.transform = 'translateY(-2px)';
            input.parentElement.style.transition = 'transform 0.3s ease';
        });
        
        input.addEventListener('blur', () => {
            input.parentElement.style.transform = 'translateY(0)';
        });
    });
}

// ===== TYPING EFFECT FOR HERO =====
function initTypingEffect() {
    const highlightText = document.querySelector('.title-highlight');
    if (!highlightText) return;
    
    const text = highlightText.textContent;
    highlightText.textContent = '';
    highlightText.style.display = 'block';
    
    let index = 0;
    function type() {
        if (index < text.length) {
            highlightText.textContent += text.charAt(index);
            index++;
            setTimeout(type, 50);
        }
    }
    
    // Start typing after initial animations
    setTimeout(type, 1500);
}

// ===== CURSOR TRAIL EFFECT (OPTIONAL) =====
function initCursorTrail() {
    const hero = document.querySelector('.hero');
    if (!hero) return;
    
    hero.addEventListener('mousemove', (e) => {
        const trail = document.createElement('div');
        trail.style.cssText = `
            position: fixed;
            width: 8px;
            height: 8px;
            background: rgba(255, 107, 53, 0.6);
            border-radius: 50%;
            pointer-events: none;
            left: ${e.clientX}px;
            top: ${e.clientY}px;
            animation: trailFade 1s ease-out forwards;
            z-index: 9999;
        `;
        
        document.body.appendChild(trail);
        
        setTimeout(() => trail.remove(), 1000);
    });
    
    // Add trail animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes trailFade {
            to {
                transform: scale(2);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
}

// ===== LAZY LOAD IMAGES =====
function initLazyLoad() {
    const images = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                imageObserver.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
}

// ===== PERFORMANCE OPTIMIZATION =====
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// ===== INITIALIZE ALL FUNCTIONS =====
document.addEventListener('DOMContentLoaded', () => {
    console.log('🚀 Esuhai Landing Page Initialized');
    
    // Core functionality
    initFAQ();
    initCountdown();
    initForm();
    initHeaderScroll();
    initMarquee();
    initPhoneFormat();
    initFormAnimations();
    initLazyLoad();
    
    // Enhanced effects
    setTimeout(() => {
        initParallax();
        initTestimonialRotate();
        // initTypingEffect(); // Optional: can make page feel slower
        // initCursorTrail(); // Optional: can be distracting
    }, 1000);
});

// ===== SMOOTH SCROLL FOR ALL ANCHOR LINKS =====
document.addEventListener('click', (e) => {
    if (e.target.matches('a[href^="#"]')) {
        e.preventDefault();
        const targetId = e.target.getAttribute('href').slice(1);
        const targetElement = document.getElementById(targetId);
        
        if (targetElement) {
            targetElement.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }
});

// ===== PAGE LOAD PERFORMANCE =====
window.addEventListener('load', () => {
    document.body.classList.add('loaded');
    console.log('✅ Page fully loaded');
});

// ===== EASTER EGG: KONAMI CODE =====
let konamiCode = [];
const konamiSequence = ['ArrowUp', 'ArrowUp', 'ArrowDown', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'ArrowLeft', 'ArrowRight', 'b', 'a'];

document.addEventListener('keydown', (e) => {
    konamiCode.push(e.key);
    konamiCode.splice(-konamiSequence.length - 1, konamiCode.length - konamiSequence.length);
    
    if (konamiCode.join('') === konamiSequence.join('')) {
        activateEasterEgg();
    }
});

function activateEasterEgg() {
    // Add rainbow animation to entire page
    document.body.style.animation = 'rainbow 5s ease-in-out';
    
    const style = document.createElement('style');
    style.textContent = `
        @keyframes rainbow {
            0% { filter: hue-rotate(0deg); }
            100% { filter: hue-rotate(360deg); }
        }
    `;
    document.head.appendChild(style);
    
    // Show secret message
    const message = document.createElement('div');
    message.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 2rem;
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        z-index: 10000;
        text-align: center;
        animation: bounce 1s ease-in-out;
    `;
    message.innerHTML = `
        <h2 style="color: #003D6F; font-family: 'Outfit', sans-serif;">🎌 Subarashii! 🎌</h2>
        <p style="color: #4A4A4A;">Bạn đã tìm thấy Easter Egg!</p>
        <p style="color: #FF6B35; font-weight: 700;">Giảm thêm 5% khi đăng ký!</p>
    `;
    document.body.appendChild(message);
    
    setTimeout(() => {
        message.remove();
        document.body.style.animation = '';
    }, 3000);
}

// ===== EXPORT FOR TESTING =====
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        scrollToForm,
        initFAQ,
        initCountdown,
        initForm
    };
}
