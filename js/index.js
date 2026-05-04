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
    let retur ={
        tasks:[
            {id:1,
            time: "03:00",
            name:"PHP"
            },

            {id:2,
            time: "03:00",
            name:"Javascript"
            },

            {id:3,
            time: "03:00",
            name:"Databaser"
            },

            {id:4,
            time: "03:00",
            name:"HTML/CSS"
            },


            {id:5,
            time: "03:00",
            name:"Slöseri"
            }
        ]
    }
    fyllLista(retur)
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


