let aktiviteter = []
let aktuelltUppgiftId = null
window.onload = () => {
    let queryString = window.location.search
    let parameters = new URLSearchParams(queryString)

    getActivities()
        .finally(() => {
            if (parameters.has('id')) {
                aktuelltUppgiftId=parameters.get('id')
                fillForm(parameters.get('id'))
            } else {
                emptyForm()
            }
        })

        // händelselyssnare för  sparknappen

        document.getElementById('spara').addEventListener("click", sparaUppgift)

        document.getElementById('inputDatum').max = new Date().toISOString().substring(0, 10)
}

async function getActivities() {
    try {
        let response = await fetch("api/activity")
        if (response.ok) {
            let data = await response.json()
            aktiviteter = data.activities
            fillDropdown(aktiviteter)
        } else {
            let message = null
            try {
                message = await response.json()
            } finally {
                let fel = {
                    status: response.status,
                    text: response.statusText,
                    url: response.url,
                    message
                }

                throw fel
            }
        }
    } catch (error) {
        console.error(error)
    }
}

function fillDropdown(aktiviteter) {
    let dropdown = document.getElementById("inputAktivitet")
    // Töm dropdown
    dropdown.innerHTML=''

    // Fyll med data
    for(let i=0;i<aktiviteter.length;i++) {
        let option = document.createElement("option")
        option.value=aktiviteter[i].id
        option.text=aktiviteter[i].activity
        dropdown.append(option)
    }
}

async function fillForm(id) {
    // Hämta uppgifter, just nu alla och välj rätt sedan hämta rätt.
    try {
        let response = await fetch(`api/task/${id}`)
        if (response.ok) {
            let data = await response.json()
            
            
            document.getElementById('labelId').style.display = "initial"
            document.getElementById('valueId').innerText = post.id
            document.getElementById('inputDatum').value = post.date
            document.getElementById('inputVaraktighet').value = post.time
            document.getElementById('inputBeskrivning').innerHTML = post.description
            // Aktivitet är en dropdown!
            document.getElementById('inputAktivitet').value = post.activityId
        } else {
            let message = null
            try {
                message = await response.json()
            } finally {
                let fel = {
                    status: response.status,
                    text: response.statusText,
                    url: response.url,
                    message
                }

                throw fel
            }
        }
    } catch (error) {
        console.error(error)
    }
}

function emptyForm() {
    // Dölj ID-fältet
    document.getElementById('labelId').style.display = "none"

    // Töm alla inputs
    document.getElementById('inputDatum').value = (new Date).toISOString().substring(0, 10)
    document.getElementById('inputVaraktighet').value = '01:00'
    document.getElementById('inputBeskrivning').value = ''
    // Aktivitet är en dropdown!
    document.getElementById('inputAktivitet').value = -1
}

function sparaUppgift() {
    if(!valideraFormulär()) {
        alert('Fixa uppgifterna')
        return
    }
    alert('hurra, sparar direkt')

    if(aktuelltUppgiftId) {
        uppdateraBefintligUppgift()
    } else {
        sparaNyUppgift()
    }

}
function sparaNyUppgift(){
    let form = new FormData()
    form.append("date", document.getElementById('inputDatum').value)
    form.append("time", document.getElementById('inputVaraktighet').value)
    form.append("activityId", document.getElementById('labelAktivitet').value)
    form.append("description", document.getElementById('labelBeskrivning').value)
    form.append("action", "save")
    fetch("api/task", {
        method:"POST",
        body:form
    })
        .then(response => {
            if(response.ok) {
                return response.json()
            } else {
                throw response.json()
            }
        })
        .then (data =>{
            alert (`Ny post sparades med id=${data.id}`)
            window.location.href=`editUppgift.html?id${id}`
        })
        .catch(err => {
            alert("spara misslyckades, titta i konsolen")
            console.error(err)
        })

}

    // inmatningar i formuläret duger för att spara



function valideraFormulär() {
    let valid = true
    // inte i framtiden
    if(document.getElementById('inputDatum').value>(new Date()).toISOString().substring(0,10)) {
        valid=false
    }

    // Max 8h 
      if(document.getElementById('inputVaraktighet').value>"08:00"){
        valid=false
    }
    // min 15 minuter
        if(document.getElementById('inputVaraktighet').value<"00:15"){
        valid=false
    }
    //15 min intervall
    
        if(!["00","15","30","45"].includes(document.getElementById('inputVaraktighet').value.substring(3, 5))) {
        valid=false
    }
    //aktivitet ska finnas
        if(document.getElementById('inputAktivitet').selectedIndex<0){
        valid=false
    }

    return valid;
}