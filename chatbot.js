document.addEventListener("DOMContentLoaded", function () {
  const boton = document.getElementById("boton-preguntar");
  if (!boton) return;

  boton.addEventListener("click", function () {
    const input = document.getElementById("pregunta");
    const chatbox = document.getElementById("chatbox");

    if (!input || !chatbox) return;

    let pregunta = input.value.trim();
    if (!pregunta) return;

    const normalizar = (texto) => {
      return texto
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "");
    };

    const preguntaNormalizada = normalizar(pregunta);

    chatbox.innerHTML += `<div><strong>T  :</strong> ${pregunta}</div>`;

    let respuesta = "Lo siento, no tengo una respuesta para esa pregunta.";

    if (
      preguntaNormalizada.includes("donde") &&
      (preguntaNormalizada.includes("instituto") || preguntaNormalizada.includes("centro"))
    ) {
      respuesta = "El instituto Font de Sant Lluis esta en Valencia, en la Av. dels Germans Maristes, 25";
    } else if (
      preguntaNormalizada.includes("inscripcion") ||
      preguntaNormalizada.includes("matricula") ||
      preguntaNormalizada.includes("fecha")
    ) {
      respuesta = "La inscripcion esta abierta desde abril hasta junio. Puedes inscribirte desde la seccion de matricula en la pagina principal.";
    } else if (
      preguntaNormalizada.includes("web") ||
      preguntaNormalizada.includes("error") ||
      preguntaNormalizada.includes("ayuda") ||
      preguntaNormalizada.includes("problema") ||
      preguntaNormalizada.includes("no entiendo")
    ) {
      respuesta = "Si tienes complicaciones, por favor contacta con nosotros desde el apartado de contacto.";
    } else if (
      preguntaNormalizada.includes("horario") ||
      preguntaNormalizada.includes("hora")
    ) {
      respuesta = "El horario del centro es de lunes a viernes, de 8:00 a 14:00.";
    }

    chatbox.innerHTML += `<div><strong>Bot:</strong> ${respuesta}</div>`;
    input.value = "";
    chatbox.scrollTop = chatbox.scrollHeight;
  });
});
