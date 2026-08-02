
import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

document.addEventListener('DOMContentLoaded', () => {

    // HERO TEXT REVEAL (Staggered)
    const heroText = document.querySelectorAll('.animate-fade-in-up > *');
    if (heroText.length) {
        gsap.from(heroText, {
            y: 50,
            opacity: 0,
            duration: 1.2,
            stagger: 0.2,
            ease: "power3.out",
            delay: 0.2
        });
    }

    // SEARCH BAR (Float Up)
    const searchBar = document.getElementById('hero-search-bar');
    if (searchBar) {
        gsap.from(searchBar, {
            y: 30,
            opacity: 0,
            duration: 1,
            ease: "power2.out",
            delay: 1
        });
    }

    // SCROLL REVEALS (Generic Class)
    const reveals = document.querySelectorAll('.reveal');
    reveals.forEach(el => {
        gsap.fromTo(el,
            { y: 50, opacity: 0 },
            {
                y: 0,
                opacity: 1,
                duration: 1,
                ease: "power3.out",
                scrollTrigger: {
                    trigger: el,
                    start: "top 85%", // Trigger when top of element hits 85% of viewport height
                    toggleActions: "play none none reverse"
                }
            }
        );
    });

    // TITANIUM CARDS (Staggered Grid)
    // We target the container to stagger children if possible, or just individual cards
    const cards = document.querySelectorAll('.titanium-card');
    if (cards.length) {
        ScrollTrigger.batch(cards, {
            onEnter: batch => gsap.fromTo(batch,
                { y: 60, opacity: 0, scale: 0.95 },
                { y: 0, opacity: 1, scale: 1, stagger: 0.15, duration: 0.8, ease: "back.out(1.7)" }
            ),
            start: "top 90%"
        });
    }

    // LIFESTYLE IMAGES (Parallaxish)
    const lifestyleImages = document.querySelectorAll('.lifestyle-img');
    lifestyleImages.forEach(img => {
        gsap.to(img, {
            yPercent: -20,
            ease: "none",
            scrollTrigger: {
                trigger: img,
                scrub: true
            }
        });
    });

});
