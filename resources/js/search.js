const searchInput = document.querySelector('[name=s]');
const content = document.querySelector('#search-content');

searchInput.focus();
window.addEventListener('load', () => {
  searchInput.focus();
});
const debounce = (func, delay) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => {
            func(...args);
        }, delay);
    };
};

searchInput.addEventListener('input', debounce(async () => {
    const search = searchInput.value;

    const result = await fetch(`/s?query=${search}`);
    const html = await result.json()
    content.innerHTML = html;

    // trigger custom event
    const event = new CustomEvent('ajax-loaded');
    document.dispatchEvent(event);

}, 400));
