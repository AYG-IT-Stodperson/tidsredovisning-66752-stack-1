window.onload=() =>{
    document.getElementById('hamtaDatum').addEventListener("click", hamtaDatum)
    document.getElementById('hamtaSida').addEventListener("click", hamtaSida)

    //rensa listan
    alert("tömmer listan!");
    rensaLista()
    // sätt standardvärderna
    //alert ("sätter standardvärden för perioden")
    setDateInterval()
    // hämta från Api
    getActivites()
}


function rensaLista() {
    let lista  = document.getElementById("aktiviteter"); // HTMLElement
    lista.innerHTML = "";
}

function setDateInterval() {
    let idag = new Date();
    let aktuellManad = idag.getMonth();

    let fromDatum = new Date(idag.getFullYear(), aktuellManad, 1, 24);
    let toDatum =new Date(idag.getFullYear(), aktuellManad+1, 0,24);

    document.getElementById("franDatum").value=fromDatum.toISOString().substring (0,10);
    document.getElementById("tillDatum").value=toDatum.toISOString().substring (0,10);

}

async function getActivites() {
    try {
        let response= await fetch("dummy/sammanställning.json")
        if(response.ok) {
            let data = await response.json()
            fyllLista(data)
        } else {
            let message = null
            try{
                message=await response.json()
            } finally {
                let fel = {status:response.status,
                text: response.statusText,
                url: response.url,
                message
            }
            throw fel
        }
    }

    } catch (error) {
        console.log(error)
    }

}

function fyllLista(data) {
let lista = document.getElementById("aktiviteter")
for (let i=0; i<data.tasks.length; i++) {
    let rad=document.createElement("ul")
    rad.className="lista"
    rad.innerHTML=`<li>${data.tasks[i].activity}</li><li class="right">${data.tasks[i].time}</li>`
        lista.appendChild(rad)
}

}

function aktiveraAlternativ(ev){
    try {

        if(ev.target.value==='sida') {
            // aktivera rätt kontroller
            document.getElementById('sidnr').disabled = false;
            document.getElementById('hamtaSida').disabled = false;
            // avaktivera övriga kontroller

            document.getElementById('franDatum').disabled = true;
            document.getElementById('tillDatum').disabled = true;
            document.getElementById('hamtaDatum').disabled = true;
        } else {

            document.getElementById('franDatum').disabled = false;
            document.getElementById('tillDatum').disabled = false;
            document.getElementById('hamtaDatum').disabled = false;
            
            document.getElementById('sidnr').disabled = true;
            document.getElementById('hamtaSida').disabled = true;
        }
            
    
        } catch (error) {
            console.log(error)
        }
    }
