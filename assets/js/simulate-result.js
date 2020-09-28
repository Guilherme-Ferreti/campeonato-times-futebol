console.log(simulateResult("Paradão", "5", "Moca", "5"));

function simulateResult(teamA, ratingA, teamB, ratingB) {
    ratingA = Number(ratingA) * 2;
    ratingB = Number(ratingB) * 2;

    let draws = (ratingA > ratingA) ? ratingA : ratingB; 

    let chances = [];

    let scoreboardDraw = [];
    scoreboardDraw.push(
        "0x0", "0x0", "0x0", "0x0", 
        "1x1", "1x1", "1x1", "1x1", 
        "2x2", "2x2", "2x2", "2x2",
        "3x3", "3x3", 
        "4x4"
    );

    let scoreboardWin = [];
    scoreboardWin.push(
        "1x0", "1x0", "1x0", "1x0",
        "2x0", "2x0", "2x0",
        "2x1", "2x1",
        "3x1", "3x1",
        "3x2",
        "4x2",
        "4x3",
        "5x0"
    );

    // Adiciona chances no array
    for (let i = 0; i < ratingA; i++) chances.push(teamA);

    for(let i = 0; i < ratingB; i++) chances.push(teamB);

    for(let i = 0; i < draws; i++) chances.push("Empate");  

    // Sorteia resultado da partida
    let result = Math.floor(Math.random() * chances.length);

    let finalScoreboard = "";

    // Decide o placar da partida
    if (chances[result] === "Empate") {
        let a = Math.floor(Math.random() * scoreboardDraw.length);
        finalScoreboard = scoreboardDraw[a];
    }
    else {
        let dif = Math.abs(ratingA - ratingB);
        if (dif === 2 ) scoreboardWin.push("3x2", "4x2", "3x0", "3x0");

        let a = Math.floor(Math.random() * scoreboardWin.length);
        finalScoreboard = scoreboardWin[a];
    }

    let goals = finalScoreboard.split("x");

    if (chances[result] === teamB) goals.reverse();
console.log(chances[result]);
    return goals;
}

