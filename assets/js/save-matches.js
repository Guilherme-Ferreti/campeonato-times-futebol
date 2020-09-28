function saveMatches( url ) {

    let arMatches = document.querySelectorAll('.match');

    let matchesToSave = [];

    arMatches.forEach((item, index) => {

        if (item.dataset.finished === "0") {

            let matchId = item.dataset.id;

            let input1 = document.getElementById('host-' + matchId).value;
            let input2 = document.getElementById('visitor-' + matchId).value;

            if (input1.trim() !== "" && input2.trim() !== "") {

                matchesToSave.push({
                    id: matchId,
                    team1: item.dataset.team1,
                    team2: item.dataset.team2,
                    goals1: input1,
                    goals2: input2,
                });
            }
        }
    }); 

    console.log(matchesToSave);

    document.getElementById("hidden-save").value = JSON.stringify(matchesToSave); 

    let form = document.querySelector("#hidden-form");

    let formData = new FormData( form );
    
    if (matchesToSave.length > 0) { 

        let arSaveBtn = document.querySelectorAll('.btn-save-matchlist');

        arSaveBtn.forEach( (item, index) => {

            item.innerHTML = `
                <img src="../assets/images/loading-gif.gif" alt="loading">
            `;
        });       

        fetch( url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(json => {

            console.log(json);
            
            if (json.result === true) {
                window.location.reload();
            }

        })
        .catch(e => {

            console.log(e);

        });
        
    }
}

document.getElementById('match-list-number-1').addEventListener('keypress', e => {

    if (e.key === "Enter") {

        toggleMatchList(Number(e.srcElement.value), 1,"input");
    }

});

document.getElementById('match-list-number-2').addEventListener('keypress', e => {

    if (e.key === "Enter") {

        toggleMatchList(Number(e.srcElement.value), 2, "input");
    }

});

function toggleMatchList(valueToToggle, stage, valueFrom = "") {

    let listInput = document.getElementById('match-list-number-'+ stage);

    let matchList = document.querySelectorAll('.match-list');

    let totalList = Number(document.getElementById('list-total-' + stage).dataset.totallist);
    let currentList = Number(document.getElementById('match-list-number-' + stage).dataset.currentlist);

    let newList = valueToToggle;

    if ( valueFrom !== "" ) {

        if ( valueToToggle > 0 && valueToToggle <= totalList ) {

            matchList.forEach((item, index) => {

                let listToDisplay = "list-" + stage + '-' + valueToToggle;
                
                if (item.id === listToDisplay) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }
        
    }
    else {

        newList = currentList + valueToToggle;

        if ( newList > 0 && newList <= totalList ) {

            matchList.forEach((item, index) => {

                let listToDisplay = "list-" + stage + '-' + newList;
                
                if (item.id === listToDisplay) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }

                listInput.value = newList;
            });
        }
    }
    
    if ( newList > 0 && newList <= totalList) document.getElementById('match-list-number-' + stage).dataset.currentlist = newList;
}

function toggleStage(id) {

    let arStages = document.querySelectorAll('.stage');

    let arBtnStages = document.querySelectorAll('.stage-item');

    arStages.forEach((item, index) => {

        if ( item.id === document.getElementById(id).dataset.stage ) {

            document.getElementById(item.id).style.display = "flex";
        } else {

            document.getElementById(item.id).style.display = "none";
        }     
    });

    arBtnStages.forEach((item, index) => {

        if ( item.id === id ) {
            document.getElementById(item.id).classList.add('selected-stage');
        } else {

            document.getElementById(item.id).classList.remove('selected-stage');
        }
    });

}