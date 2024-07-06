import.meta.glob(['../images/**']);


const getCookie = key => document.cookie.split('; ').find(cookie => cookie.startsWith(key))?.split('=')[1];

const like = document.querySelector('.like');
if (like) {
  like.addEventListener('click', () => {
    let userLikes = getCookie('userLikes')?.split(',') || [];
    like.classList.toggle('liked');
    userLikes = like.classList.contains('liked')
      ? [...userLikes, window.currentPostId.toString()]
      : userLikes.filter(like => like !== window.currentPostId.toString());

    const maxAge = 60 * 60 * 24 * 365; // 1 year in seconds
    document.cookie = `userLikes=${userLikes.join(',')}; path=/; max-age=${maxAge}`;
  });

  if (window.currentPostId && getCookie('userLikes')?.split(',').includes(window.currentPostId)) {
    like.classList.add('liked');
  }
}

function isWebpSupported() {
  const elem = document.createElement('canvas');
  return elem.getContext && elem.getContext('2d') && elem.toDataURL('image/webp').startsWith('data:image/webp');
}

// image loafing
window.addEventListener('load', function () {
  function loadFullImage(img) {
    const fullImageSrc = img.getAttribute(isWebpSupported() ? 'data-webp' : 'data-image');
    if (fullImageSrc && !img.dataset.startedLoading) {
      img.dataset.startedLoading = "true";
      const fullImage = new Image();
      fullImage.src = fullImageSrc;
      fullImage.onload = () => {
        img.src = fullImageSrc;
        img.parentNode.classList.add('loaded');
      };
    }
  }

  function initializeObserver() {
    const observer = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const img = entry.target;
          if (!img.dataset.startedLoading) {
            loadFullImage(img);
          }
          observer.unobserve(img);
        }
      });
    });

    const images = document.querySelectorAll('img[data-image]');
    images.forEach(img => {
      observer.observe(img);
    });
  }

  // Initial run
  initializeObserver();

  // Re-run after AJAX load
  document.addEventListener('ajax-loaded', initializeObserver);
});


window.addEventListener('scroll', function () {
  const infiniteScrollElement = document.querySelector('.infinity-scroll');
  if (infiniteScrollElement) {
    let isLoading = false;
    const observer = new IntersectionObserver(entries => {
      entries.forEach(async entry => {
        if (!entry.isIntersecting || isLoading) {
          return;
        }
        observer.unobserve(infiniteScrollElement);
        isLoading = true;
        let response = await fetch('/api/infinity?offset=' + infiniteScrollElement.getAttribute('data-offset'));
        response = await response.json();
        isLoading = false;
        if (!response.html) {
          return;
        }

        infiniteScrollElement.insertAdjacentHTML('beforeend', response.html);
        infiniteScrollElement.setAttribute('data-offset', response.offset);

        // set offset to url
        const url = new URL(window.location);
        url.searchParams.set('offset', response.offset);
        window.history.pushState({}, '', url);

        const event = new CustomEvent('ajax-loaded');
        document.dispatchEvent(event);
      });
    }, {
      rootMargin: '500px',
    });
    observer.observe(document.querySelector('.infinity-scroll-end'));
  }
}, {once: true});

