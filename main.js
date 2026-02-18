// Als je <script type="module"> gebruikt, dan wordt je script automatisch deferred geladen (dus pas na het parsen van de HTML).
// Dat betekent dat je in principe DOMContentLoaded niet eens nodig hebt — maar het is wel veilig en future-proof.


document.addEventListener("DOMContentLoaded", () => {

    // Hamburger menu
    const mobileMenu = document.getElementById('mobile-menu');
    const navLinks = document.querySelector('.nav-links');
    const navItems = document.querySelectorAll('.nav-links li a');

    if (mobileMenu && navLinks) {
        mobileMenu.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });
    }

    // Close menu on click whatever menu-item-link
    navItems.forEach(item => {
        item.addEventListener('click', () => {
            navLinks.classList.remove('active');
        });
    });

    // GoTopButton
    const topbutton = document.getElementById("topButton");

    function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            topbutton.style.display = "block";
        } else {
            topbutton.style.display = "none";
        }
    }

    window.addEventListener("scroll", scrollFunction);

    // SEO-friendly smooth scroll to top
    window.topFunction = function() {
        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });

        setTimeout(() => {
            history.replaceState(null, null, window.location.pathname);
        }, 400);
    };

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

// Functie om modals te fetchen
async function loadModal(url) {
try {
const response = await fetch(url);
if (!response.ok) throw new Error('Kan modal niet laden.');
const html = await response.text();
document.getElementById('modalContainer').innerHTML = html;


// Voeg functionaliteit toe nadat de modal is geladen
const modal = document.getElementById('privacyModal');
const openBtn = document.getElementById('openPrivacy');
const closeBtn = document.getElementById('closePrivacy');


openBtn.addEventListener('click', () => modal.classList.add('active'));
closeBtn.addEventListener('click', () => modal.classList.remove('active'));
modal.addEventListener('click', (e) => { if (e.target === modal) modal.classList.remove('active'); });
document.addEventListener('keydown', (e) => { if (e.key === 'Escape') modal.classList.remove('active'); });
} catch(err) {
console.error(err);
}
}

async function loadDisclaimerModal(url) {
try {
const response = await fetch(url);
if (!response.ok) throw new Error('Kan modal niet laden.');
const html = await response.text();
document.getElementById('modalDisclaimerContainer').innerHTML = html;


// Voeg functionaliteit toe nadat de modal is geladen
const modal = document.getElementById('disclaimerModal');
const openBtn = document.getElementById('openDisclaimer');
const closeBtn = document.getElementById('closeDisclaimer');


openBtn.addEventListener('click', () => modal.classList.add('active'));
closeBtn.addEventListener('click', () => modal.classList.remove('active'));
modal.addEventListener('click', (e) => { if (e.target === modal) modal.classList.remove('active'); });
document.addEventListener('keydown', (e) => { if (e.key === 'Escape') modal.classList.remove('active'); });
} catch(err) {
console.error(err);
}
}

async function loadColofonModal(url) {
try {
const response = await fetch(url);
if (!response.ok) throw new Error('Kan modal niet laden.');
const html = await response.text();
document.getElementById('modalColofonContainer').innerHTML = html;


// Voeg functionaliteit toe nadat de modal is geladen
const modal = document.getElementById('colofonModal');
const openBtn = document.getElementById('openColofon');
const closeBtn = document.getElementById('closeColofon');


openBtn.addEventListener('click', () => modal.classList.add('active'));
closeBtn.addEventListener('click', () => modal.classList.remove('active'));
modal.addEventListener('click', (e) => { if (e.target === modal) modal.classList.remove('active'); });
document.addEventListener('keydown', (e) => { if (e.key === 'Escape') modal.classList.remove('active'); });
} catch(err) {
console.error(err);
}
}


// Modals fetchen en toevoegen aan pagina
loadModal('./modals/privacy.html');
loadDisclaimerModal('./modals/disclaimer.html');
loadColofonModal('./modals/colofon.html');



