/* KZS language toggle — বাংলা / EN */
(function () {
  var LANG_KEY = 'kzs_lang';

  function getLang() { return localStorage.getItem(LANG_KEY) || 'bn'; }

  function applyLang(lang) {
    var isBn = lang === 'bn';
    /* swap text */
    document.querySelectorAll('[data-en]').forEach(function (el) {
      var en = el.getAttribute('data-en');
      if (!el.getAttribute('data-bn')) el.setAttribute('data-bn', el.textContent.trim());
      el.textContent = isBn ? el.getAttribute('data-bn') : en;
    });
    /* swap placeholders */
    document.querySelectorAll('[data-en-ph]').forEach(function (el) {
      var enPh = el.getAttribute('data-en-ph');
      if (!el.getAttribute('data-bn-ph')) el.setAttribute('data-bn-ph', el.placeholder || '');
      el.placeholder = isBn ? el.getAttribute('data-bn-ph') : enPh;
    });
    /* update ONLY lang toggle buttons (not theme buttons) */
    document.querySelectorAll('.lang-btn[data-lang]').forEach(function (btn) {
      btn.setAttribute('aria-pressed', btn.getAttribute('data-lang') === lang ? 'true' : 'false');
    });
  }

  function setLang(lang) {
    localStorage.setItem(LANG_KEY, lang);
    applyLang(lang);
    /* persist to backend if logged in */
    var csrf = document.querySelector('meta[name="csrf-token"]');
    if (csrf) {
      fetch('/settings/lang', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf.content },
        body: JSON.stringify({ lang: lang }),
      }).catch(function () {});
    }
  }

  /* wire buttons */
  document.addEventListener('DOMContentLoaded', function () {
    applyLang(getLang());
    document.querySelectorAll('.lang-btn[data-lang]').forEach(function (btn) {
      btn.addEventListener('click', function () { setLang(btn.getAttribute('data-lang')); });
    });
  });

  window.kzsSetLang = setLang;
  window.kzsGetLang = getLang;
})();
