window.onload=() =>{
    //rensa listan
    alert("tömmer listan!");
    rensaLista()
    // sätt standardvärderna
    //alert ("sätter standardvärden för perioden")
    setDateInterval()
    // hämta från Api
    getTaskList()
}

function rensaLista() {
    let lista  = document.getElementById("uppgifter"); // HTMLElement
    lista.innerHTML = "";
}

//function


function setDateInterval() {
    let idag = new Date();
    let aktuellManad = idag.getMonth();

    let fromDatum = new Date(idag.getFullYear(), aktuellManad, 1, 24);
    let toDatum =new Date(idag.getFullYear(), aktuellManad+1, 0,24);

    document.getElementById("franDatum").value=fromDatum.toISOString().substring(0,10);
    document.getElementById("tillDatum").value=toDatum.toISOString().substring(0,10);

}

function getTaskList() {


}



function fyllLista(data) {
let lista = document.getElementById("uppgifter")
for (let i=0; i<data.tasks.length; i++) {
    let rad=document.createElement("ul")
    rad.className="lista"
    rad.innerHTML=`<li>${data.tasks[i].date}</li>
    <li>${data.tasks[i].activity}</li>
    <li>${data.tasks[i].description}</li>
    <li class="right">${data.tasks[i].time}</li>`
    
    lista.appendChild(rad)
}

}
