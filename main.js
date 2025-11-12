// voeg hier de sectienamen (=bestandsnaam) toe
const sections = [
  "intro",
  "expertise",
  "specialized_therapy",
  "education",
  "individual_therapy",
  "orofacial_therapy",
  "vestibular_therapy",
  "collaborations",
  "contact",
];

const main = document.getElementById("main-content");

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

