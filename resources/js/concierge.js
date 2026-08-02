
document.addEventListener('DOMContentLoaded', () => {
    initDarkToggle();
    initConcierge();
});

function initDarkToggle() {
    // Check local storage or system preference
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }

    // Toggle Button Logic (assuming button exists in FAB or Header)
    // We'll expose a global function for the button to call
    window.toggleTheme = function () {
        if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            localStorage.theme = 'light';
        } else {
            document.documentElement.classList.add('dark');
            localStorage.theme = 'dark';
        }
    }
}

function initConcierge() {
    // Disable on Admin/Titanium panels
    if (window.location.pathname.includes('/titanium') || window.location.pathname.includes('/admin')) {
        return;
    }

    const fab = document.createElement('div');
    fab.className = 'fixed bottom-6 right-6 z-[950] flex flex-col items-end gap-3 pointer-events-none';
    fab.innerHTML = `
        <!-- Actions Menu (Hidden by default) -->
        <div id="concierge-menu" class="bg-white/80 backdrop-blur-2xl dark:bg-slate-900/80 p-6 rounded-[2rem] shadow-2xl border border-white/20 mb-3 transform scale-90 opacity-0 pointer-events-none transition-all duration-500 origin-bottom-right w-72 pointer-events-auto">
            <div class="mb-6">
                <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-600 mb-1">Assistance Hub</h4>
                <p class="text-xs font-semibold text-slate-400">How may we assist you today?</p>
            </div>
            
            <ul class="space-y-3">
                <li>
                    <a href="/portal/dashboard" class="flex items-center gap-4 p-3 rounded-2xl hover:bg-white dark:hover:bg-slate-800 transition group">
                        <span class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-sm group-hover:scale-110 transition text-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" /></svg>
                        </span>
                        <div>
                            <span class="block text-sm font-bold text-slate-700 dark:text-slate-200">Guest Portal</span>
                            <span class="block text-[10px] text-slate-400 font-medium">Manage your stay</span>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="https://wa.me/${window.SITE_WHATSAPP || '1234567890'}" target="_blank" class="flex items-center gap-4 p-3 rounded-2xl hover:bg-white dark:hover:bg-slate-800 transition group">
                        <span class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-sm group-hover:scale-110 transition text-emerald-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" /></svg>
                        </span>
                        <div>
                            <span class="block text-sm font-bold text-slate-700 dark:text-slate-200">WhatsApp</span>
                            <span class="block text-[10px] text-slate-400 font-medium">Direct concierge Chat</span>
                        </div>
                    </a>
                </li>
                <li class="pt-2 border-t border-slate-100 dark:border-slate-800">
                    <button onclick="window.toggleTheme()" class="flex items-center gap-4 p-3 rounded-2xl hover:bg-white dark:hover:bg-slate-800 transition group w-full text-left">
                        <span class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-sm group-hover:scale-110 transition text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" /></svg>
                        </span>
                        <div>
                            <span class="block text-sm font-bold text-slate-700 dark:text-slate-200">Appearance</span>
                            <span class="block text-[10px] text-slate-400 font-medium">Toggle dark mode</span>
                        </div>
                    </button>
                </li>
            </ul>
        </div>

        <!-- Main Button -->
        <button id="concierge-btn" class="w-16 h-16 bg-slate-900 dark:bg-blue-600 text-white rounded-full shadow-2xl flex items-center justify-center hover:scale-105 active:scale-95 transition-all duration-300 group relative overflow-hidden pointer-events-auto">
            <div class="absolute inset-0 bg-gradient-to-tr from-blue-600 to-indigo-600 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <span class="relative z-10 text-2xl group-hover:rotate-12 transition-transform duration-500 flex items-center justify-center" id="concierge-icon">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.456-2.454L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l1.035.259a3.375 3.375 0 0 0 2.456 2.456L24 9l-1.035.259a3.375 3.375 0 0 0-2.456 2.456L20.25 12.75l-.259-1.035a3.375 3.375 0 0 0-2.456-2.456L17.25 9l1.035-.259a3.375 3.375 0 0 0 2.456-2.456L21 6l-1.035-.259a3.375 3.375 0 0 0-2.456-2.456L18.259 3.215Z" /></svg>
            </span>
        </button>
    `;

    document.body.appendChild(fab);

    // Interaction
    const btn = fab.querySelector('#concierge-btn');
    const menu = fab.querySelector('#concierge-menu');
    const icon = fab.querySelector('#concierge-icon');
    let isOpen = false;

    btn.addEventListener('click', () => {
        isOpen = !isOpen;
        if (isOpen) {
            menu.classList.remove('scale-90', 'opacity-0', 'pointer-events-none');
            icon.textContent = '×';
            icon.classList.add('text-3xl');
        } else {
            menu.classList.add('scale-90', 'opacity-0', 'pointer-events-none');
            icon.textContent = '✨';
            icon.classList.remove('text-3xl');
        }
    });

    // Close on click outside
    document.addEventListener('click', (e) => {
        if (isOpen && !fab.contains(e.target)) {
            btn.click();
        }
    });
}
