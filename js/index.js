window.onload=() =>{
    //rensa listan
    alert("tömmer listan!");
    rensaLista()
    // sätt standardvärderna
    //alert ("sätter standardvärden för perioden")
    setDateInterval()
    // hämta från Api
    getCompilation()
}


function rensaLista() {
    let lista  = document.getElementById("test"); // HTMLElement
    lista.innerHTML = "";
}

function setDateInterval() {
    let idag = new Date();
    let aktuellManad = idag.getMonth();

    let fromDatum = new Date(idag.getFullYear(), aktuellManad, 1, 24);
    let toDatum =new Date(idag.getFullYear(), aktuellManad+1, 0,24);

    document.getElementById("franDatum").value=fromDatum.toISOString().subtstring(0,10);
    document.getElementById("tillDatum").value=toDatum.toISOString().subtstring(0,10);

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
}

function fyllLista(retur) {

}