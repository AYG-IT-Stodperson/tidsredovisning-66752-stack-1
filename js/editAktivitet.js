window.onload = () => {

    let queryString = window.location.search


    let parameters = new URLSearchParams(queryString)

    if (parameters.has('id')) {
        fillForm(parameters.get('id'))
    } else {

        emptyForm()
    }
}

function fillForm(id) {
    // Hämta data (just nu all data och sen hitta rätt, senare hämta bara rätt data)
    fetch(`api/activity/${id}`)
        .then(response => {
            if (response.ok) {
                return response.json()
            }
            // response är inte ok...
            return response.json()
                .catch(() => null) // Är svaret inte json händer inget
                .then(message => {

                    let fel = {

                        status: response.status,
                        text: response.statusText,
                        url: response.url,
                        message
                    }
                    //töm formuläret
                    emptyForm()
                    
                    throw fel
                })
        })
        .then(data => {

            // Fyll formuläret och se till att ID syns
            document.getElementById('valueId').innerText = data.id
            document.getElementById('labelId').style.display = 'initial'
            document.getElementById('inputAktivitet').value = data.activity
        })
        
        .catch (error => {
    console.error(error)
})
}


function emptyForm() {
    // Göm ID-fältet
    document.getElementById('labelId').style.display = 'none'
    // Töm inmatningsfältet och sätt fokus
    document.getElementById('inputAktivitet').value = ''
    document.getElementById('inputAktivitet').focus()
}

function verifieraForm() {
    //sätt standard returkod
    let returKod =true

    //återställ alla fält
    document.getElementById('inputAktivitet_Err').innerText = ""
    document.getElementById('inputAktivitet').setCustomValidity("")
    // kontrollera indata
    let aktivitet = window.document.getElementById("inputAktivitet").value;
    if(allaAktiviteter.find(a => {
        //returnera aktiviteten om den finns
        return a.activity.toLocaleLowerCase() === aktivitet.toLocaleLowerCase()
    })) {
        document.getElementById('inputAktivitet_Err').innerText = "aktiviteten finns redan"
        document.getElementById('inputAktivitet').setCustomValidity("aktiviteten finns redan")
        returKod=false
        
    }
    //returnera svarskod

    return returKod
}