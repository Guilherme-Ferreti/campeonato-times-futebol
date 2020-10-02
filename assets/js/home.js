let container = document.querySelector("#card-container");

let card = [
    {location: "Brasil - Nacional", name: "Campeonato Brasileiro Série A", imageFile: "main-cup.png", color: "card-brazil", route: "/campeonato-brasileiro-serie-a"},
    {location: "Brasil - Nacional", name: "Campeonato Brasileiro Série B", imageFile: "brazil-b.png", color: "card-brazil", route: "/campeonato-brasileiro-serie-b"},
    {location: "Brasil - Nacional", name: "Campeonato Brasileiro Série C", imageFile: "brazil-c.png", color: "card-brazil", route: "/campeonato-brasileiro-serie-c"},
    {location: "Brasil - Nacional", name: "Copa do Brasil", imageFile: "eliminatory-cup.png", color: "card-brazil", route: "/copa-do-brasil"},
    {location: "Brasil - Estadual", name: "Torneio São Bernardo", imageFile: "pre-tournament.png", color: "card-brazil", route: "/torneio-sao-bernardo"},
    {location: "Brasil - Estadual", name: "Torneio São Paulo", imageFile: "pre-tournament.png", color: "card-brazil", route: "/torneio-sao-paulo"},
    {location: "Brasil - Nacional", name: "Recopa - Brasil", imageFile: "recup.png", color: "card-brazil", route: "/recopa/7"},
    {location: "Internacional", name: "Copa Libertadores", imageFile: "libertadores-cup.png", color: "card-international", route: ""},
    {location: "Internacional", name: "Copa Sul-Americana", imageFile: "trofeu-03.png", color: "card-international", route: ""},
    {location: "Internacional", name: "Recopa - Internacional", imageFile: "recup.png", color: "card-international", route: ""},
    {location: "Internacional", name: "Liga Internacional", imageFile: "trofeu-10.png", color: "card-international", route: ""},
    {location: "Argentina", name: "Campeonato Argentino", imageFile: "main-cup.png", color: "card-argentina", route: "/campeonato-argentina"},
    {location: "Argentina", name: "Copa da Argentina", imageFile: "eliminatory-cup.png", color: "card-argentina", route: ""},
    {location: "Argentina", name: "Recopa - Argentina", imageFile: "recup.png", color: "card-argentina", route: ""},
    {location: "México", name: "Campeonato Mexicano", imageFile: "main-cup.png", color: "card-mexico", route: ""},
    {location: "México", name: "Copa do México", imageFile: "eliminatory-cup.png", color: "card-mexico", route: ""},
    {location: "México", name: "Recopa - México", imageFile: "recup.png", color: "card-mexico", route: ""},
    {location: "Estados Unidos", name: "Campeonato Americano", imageFile: "main-cup.png", color: "card-usa", route: ""},
    {location: "Estados Unidos", name: "Copa Americana", imageFile: "eliminatory-cup.png", color: "card-usa", route: ""},
    {location: "Estados Unidos", name: "Recopa - Estados Unidos", imageFile: "recup.png", color: "card-usa", route: ""},
];

for(let i = 0; i < card.length; i++) {
    
    let div = document.createElement('div');
    div.classList = "league-card";
    div.setAttribute("route", card[i].route)
    div.innerHTML = `
        
            <div class="league-card-title ${card[i].color}">
                <span class="league-location">${card[i].location}</span>
            </div>
            <div class="league-card-name">
                <span class="league-name">${card[i].name}</span>
            </div>
            <div class="league-card-image ${card[i].color}">
                <img src="../assets/images/${card[i].imageFile}" alt="Troféu">
            </div>
        
    `;

    container.insertAdjacentElement('beforeend', div);
    
    div.addEventListener('click', e => {

        let route = div.getAttribute("route");

        console.log(route);

        window.location.href = route;
    });
}