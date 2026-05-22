window.onload=() =>{
    //Skapa händelselyssnare



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

function hamtaDatum() {
        rensaLista()
    let franDatum = document.getElementById("franDatum").value;
    let tillDatum = document.getElementById("tillDatum").value;
    fetch(`api/tasklist/$(tillDatum)`)
    .then(response =>{
        if(response.ok) {
            return response.json()
        }

        //respone är inte ok...
        return response.json() 
           .catch(() => null) // är inte svaret json händer inget
           .then(message =>{
               let fel = {status:response.status,
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
    .catch(error =>{
        console.error(error)
    })

}

function hamtaSida() {
    rensaLista()
    fetch(`api/tasklist/${sidnr}`)
    .then(response =>{
        if(response.ok) {
            return response.json()
        }

        //respone är inte ok...
        return response.json() 
           .catch(() => null) // är inte svaret json händer inget
           .then(message =>{
               let fel = {status:response.status,
                text: response.statusText,
                url: response.url,
                message
               }
               throw fel
           })
           
    })
    .then(data =>{
        fyllLista(data)
        let select=document.getElementById("sidnr");
         select.innerHTML='';
            for(let i=0;i<data.pages;i++) {
                let opt=document.createElement("option");
                opt.text=`${i+1}`
                select.appendChild(opt)
            }
        select.value = sidnr;
    })
    .catch(error =>{
        console.error(error)
    })

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

function aktiveraAlternativ(ev){
    try {

        if(ev.target.value==='sida') {
            // aktivera rätt kontroller
            document.getElementById('sidnr').disabled = false;
            document.getElementById('hamtaSida').disabled = false;
            hamtaSida()
            // avaktivera övriga kontroller

            document.getElementById('franDatum').disabled = true;
            document.getElementById('tillDatum').disabled = true;
            document.getElementById('hamtaDatum').disabled = true;
        } else {

            document.getElementById('franDatum').disabled = false;
            document.getElementById('tillDatum').disabled = false;
            document.getElementById('hamtaDatum').disabled = false;
            hamtaDatum()
            
            document.getElementById('sidnr').disabled = true;
            document.getElementById('hamtaSida').disabled = true;
        }
            
    
        } catch (error) {
            console.log(error)
            
            document.getElementById('franDatum').disabled = false;
            document.getElementById('tillDatum').disabled = false;
            document.getElementById('hamtaDatum').disabled = false;
            hamtaDatum()

                        document.getElementById('sidnr').disabled = true;
            document.getElementById('hamtaSida').disabled = true;
        }
    }


    // inte i framtiden

    // inte mer än 8 timmar

    // jämna kvartar

   /* function alertDelete(id) {
    if (confirm('Vill du radera posten med id=' + id + '?')) {
        let form: = new FormData()
        form.append("action", 'delete')
        fetch(`api/task/${id}`, {
        
        method: "POST",
        body: from
        })
        .then(respomse =>{
            if(response.ok) {
            return response.json()
            }else {
            throw response.json()
                }
                })
            .then(data =>{
                if(data.result) {
                alert('radera lyckades')
                } else {
                    alert ("radera missluckades, kontrollera konsolen")
                console.log(data)
                    }
                })
                    .catch(error => {
                        alert("något gick fel vid radering, kontrollera konsolen")
                        console.error(error);
                })
    }
}
    */