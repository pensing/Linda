
//form validatie  

(function () {
    const form = document.getElementById("checkform");

    if (!form) return;

    form.addEventListener("submit", function (e) {
      const errors = [];

      const email = form.email.value.trim();
      const name = form.name.value.trim();
      const phone = form.phone.value.trim();
      const message = form.message.value.trim();
      const tos = form.tos.checked;
      const honeypot = form.website.value.trim();
      //foutmeldingen nog even aanpassen
      // e-mail check
      const emailPattern = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
      if (!emailPattern.test(email)) {
        errors.push("Hé, dat e-mailadres lijkt niet helemaal te kloppen. Even checken?");
      }

      // naam check
      if (name.length < 2) {
        errors.push("Je naam mag iets langer zijn, minstens 2 tekens graag!");
      }

      // telefoonnummer check
      const phonePattern = /^\+?\d{7,15}$/;
      if (!phonePattern.test(phone)) {
        errors.push("Dat telefoonnummer ziet er raar uit. Kun je het nog eens proberen?");
      }

      // bericht check
      if (message.length < 10) {
        errors.push("We horen graag iets meer van je, minstens 10 tekens.");
      }

      // TOS checkbox check
      if (!tos) {
        errors.push("Je moet even akkoord gaan met de voorwaarden, anders kunnen we helaas niet verder.");
      }

      // honeypot check
      if (honeypot !== "") {
        errors.push("Opdonderen");
      }

      if (errors.length > 0) {
        e.preventDefault(); // stop verzending
        alert("Er zijn fouten gevonden:\n\n" + errors.join("\n"));
      }
    });
  })();
// alles word server side ook nog check gedaan
console.log("NOU HIJ DOET HET PAUL!!!!!!");