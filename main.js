// Als je <script type="module"> gebruikt, dan wordt je script automatisch deferred geladen (dus pas na het parsen van de HTML).
// Dat betekent dat je in principe DOMContentLoaded niet eens nodig hebt — maar het is wel veilig en future-proof.


document.addEventListener("DOMContentLoaded", () => {

  // -------------------------
  // Hamburger menu
  // -------------------------
  const mobileMenu = document.getElementById('mobile-menu');
  const navLinks = document.querySelector('.nav-links');
  const navItems = document.querySelectorAll('.nav-links li a');

  if (mobileMenu && navLinks) {
    mobileMenu.addEventListener('click', () => navLinks.classList.toggle('active'));
  }

  navItems.forEach(item => {
    item.addEventListener('click', () => navLinks.classList.remove('active'));
  });

  // -------------------------
  // Smooth scroll-to-top button
  // -------------------------
  const topButton = document.getElementById("topButton");

  function scrollFunction() {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
      topButton.style.display = "block";
    } else {
      topButton.style.display = "none";
    }
  }

  window.addEventListener("scroll", scrollFunction);

  topButton.addEventListener("click", () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
    setTimeout(() => {
      history.replaceState(null, null, window.location.pathname);
    }, 400);
  });

  // -------------------------
  // Generieke modal loader
  // -------------------------
  async function loadModal({ url, containerId, modalId, openBtnId, closeBtnId }) {
    try {
      const response = await fetch(url);
      if (!response.ok) throw new Error(`Kan modal niet laden: ${url}`);
      const html = await response.text();

      const container = document.getElementById(containerId);
      container.innerHTML = html;

      const modal = document.getElementById(modalId);
      const openBtn = document.getElementById(openBtnId);
      const closeBtn = document.getElementById(closeBtnId);

      if (!modal || !openBtn || !closeBtn) return;

      // Open modal
      openBtn.addEventListener("click", () => modal.classList.add("active"));

      // Sluit modal via X
      closeBtn.addEventListener("click", () => modal.classList.remove("active"));

      // Sluit modal bij klik buiten content
      modal.addEventListener("click", e => {
        if (e.target === modal) modal.classList.remove("active");
      });

      // Sluit modal via Escape
      document.addEventListener("keydown", e => {
        if (e.key === "Escape") modal.classList.remove("active");
      });

    } catch (err) {
      console.error(err);
    }
  }

  // -------------------------
  // Modals configuratie
  // -------------------------
  const modals = [
    {
      url: './modals/privacy.html',
      containerId: 'modalPrivacyContainer',
      modalId: 'privacyModal',
      openBtnId: 'openPrivacy',
      closeBtnId: 'closePrivacy'
    },
    {
      url: './modals/disclaimer.html',
      containerId: 'modalDisclaimerContainer',
      modalId: 'disclaimerModal',
      openBtnId: 'openDisclaimer',
      closeBtnId: 'closeDisclaimer'
    },
    {
      url: './modals/colofon.html',
      containerId: 'modalColofonContainer',
      modalId: 'colofonModal',
      openBtnId: 'openColofon',
      closeBtnId: 'closeColofon'
    }
  ];

  // Laad alle modals
  modals.forEach(loadModal);

});



const main = document.getElementById("main-content");

// voeg hier de sectienamen (=bestandsnaam) toe
const sections = [
  "intro",
  "expertise",
  "specialized_therapy",
  "education",
  "vestibular_therapy",
  "collaborations",
  "contact",
];

// voeg hier de modalnamen (=bestandsnaam) toe
const modals = [
  "privacy",
  "disclaimer",
  "colofon",
];

// Functie om sections te fetchen
async function loadSections() {
  for (const name of sections) {
    try {
      const res = await fetch(`sections/${name}.html`);
      const html = await res.text();

      const temp = document.createElement('div');
      temp.innerHTML = html;

      // Voeg <style> toe aan <head> (indien aanwezig)
      const style = temp.querySelector('style');
      if (style) {
        document.head.appendChild(style.cloneNode(true));
      }

      // Voeg <section> toe aan <main>
      const section = temp.querySelector('section');
      if (section) {
        main.appendChild(section);
      } else {
        console.warn(`Geen <section> gevonden in ${name}.html`);
      }

    } catch (err) {
      console.error(`Fout bij laden van ${name}.html:`, err);
    }
  }
}

loadSections();

