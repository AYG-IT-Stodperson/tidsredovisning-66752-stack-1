window.onload=() =>{
    //rensa listan
  //  alert("tömmer listan!");
    rensaLista()
    // sätt standardvärderna
    //alert ("sätter standardvärden för perioden")
    setDateInterval()
    // hämta från Api
    getCompilation()
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

function getCompilation() {
     let franDatum = document.getElementById("franDatum").value;
    let tillDatum = document.getElementById("tillDatum").value;
    fetch(`api/compilation/${franDatum}/${tillDatum}`)
        .then(response =>{
            if(response.ok) {
                return response.json()
            }

            // response är inte ok...
            return response.json()
                .catch(()=>null) // Är svaret inte json händer inget
                .then(message =>{
                    let fel ={status:response.status,
                        text: response.statusText,
                        url: response.url,
                        message
                    }

                    throw fel
                })
        })
        .then(data =>{
            fyllLista(data)
        })
        .catch(error => {
            console.error(error)
        })

}

function fyllLista(data) {
let lista = document.getElementById("aktiviteter")
for (let i=0; i<data.tasks.length; i++) {
    let rad=document.createElement("ul")
    rad.className="lista"
    rad.innerHTML=`<li>${data.tasks[i].name}</li><li class="right">${data.tasks[i].time}</li>`
        lista.appendChild(rad)
}

}

function hamtaNyData(){
    rensaLista()
    getCompilation()
}


