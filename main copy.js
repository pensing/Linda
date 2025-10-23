const sections = [
  "intro",
  "about",
  "services",
  "contact",
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
      // 1. Laad en voeg CSS toe (alleen als nog niet aanwezig)
      const cssHref = `sections/${name}.css`;
      if (!document.querySelector(`link[href="${cssHref}"]`)) {
        const link = document.createElement("link");
        link.rel = "stylesheet";
        link.href = cssHref;
        document.head.appendChild(link);
      }

      // 2. Laad HTML bestand
      const res = await fetch(`sections/${name}.html`);
      const html = await res.text();

      // Parse de HTML-tekst en haal alleen de <section> eruit
      const temp = document.createElement("div");
      temp.innerHTML = html;

      const section = temp.querySelector("section");

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
