document.documentElement.classList.add('js');

const navToggle = document.querySelector('[data-nav-toggle]');
const siteNav = document.querySelector('[data-site-nav]');

if (navToggle && siteNav) {
  navToggle.addEventListener('click', () => {
    const isOpen = navToggle.getAttribute('aria-expanded') === 'true';
    navToggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
    siteNav.classList.toggle('is-open', !isOpen);
    document.body.classList.toggle('nav-open', !isOpen);
  });

  siteNav.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => {
      navToggle.setAttribute('aria-expanded', 'false');
      siteNav.classList.remove('is-open');
      document.body.classList.remove('nav-open');
    });
  });
}

const heroSlider = document.querySelector('[data-hero-slider]');

if (heroSlider) {
  const slides = Array.from(heroSlider.querySelectorAll('[data-hero-slide]'));
  const dots = Array.from(heroSlider.querySelectorAll('[data-hero-dot]'));
  let currentIndex = slides.findIndex((slide) => slide.classList.contains('is-active'));
  if (currentIndex < 0) currentIndex = 0;

  const setSlide = (index) => {
    slides.forEach((slide, i) => slide.classList.toggle('is-active', i === index));
    dots.forEach((dot, i) => dot.classList.toggle('is-active', i === index));
    currentIndex = index;
  };

  dots.forEach((dot, index) => {
    dot.addEventListener('click', () => {
      setSlide(index);
    });
  });

  if (slides.length > 1) {
    setInterval(() => {
      setSlide((currentIndex + 1) % slides.length);
    }, 4500);
  }
}

document.querySelectorAll('[data-menu-group]').forEach((group, index) => {
  const toggle = group.querySelector('[data-menu-toggle]');
  const storageKey = `jh-admin-menu-group-${index}`;
  const savedState = window.localStorage ? localStorage.getItem(storageKey) : null;

  if (savedState === 'collapsed' || savedState === null) {
    group.classList.add('is-collapsed');
    toggle?.setAttribute('aria-expanded', 'false');
  }

  toggle?.addEventListener('click', () => {
    const isCollapsed = group.classList.toggle('is-collapsed');
    toggle.setAttribute('aria-expanded', isCollapsed ? 'false' : 'true');

    if (window.localStorage) {
      localStorage.setItem(storageKey, isCollapsed ? 'collapsed' : 'expanded');
    }
  });
});
