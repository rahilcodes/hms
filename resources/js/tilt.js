
import VanillaTilt from 'vanilla-tilt';

document.addEventListener('DOMContentLoaded', () => {
    // Mobile Throttling: Disable on touch devices or small screens
    if (window.matchMedia("(min-width: 769px)").matches && !('ontouchstart' in window)) {
        VanillaTilt.init(document.querySelectorAll(".titanium-card"), {
            max: 5,               // subtle tilt
            speed: 400,           // smooth
            glare: true,          // shiny effect
            "max-glare": 0.2,     // subtle shine
            scale: 1.02           // slight zoom
        });
    }
});
