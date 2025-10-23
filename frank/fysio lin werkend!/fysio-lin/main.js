const sections = [
  // "intro",
  // "about",
  // "services",
  // "contact",
  "section1",
  "section2",
  "section3",
  "section4",
  "section5",
  "section6",
  "section7",
  "section8",
  // voeg hier andere sectienamen toe
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

  // Laad en voeg checkform.js toe nadat alle secties geladen zijn
  // komt netjes onder main.js te staan
  const script = document.createElement('script');
  script.src = 'sections/checkform.js'; // formulier validatie script
  script.defer = true;
  document.body.appendChild(script);
}

loadSections();
