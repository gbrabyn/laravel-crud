
let formEl = document.getElementById('deleteUser');
let init = {method: formEl.getAttribute('method'), body: new FormData(formEl)};

document.querySelectorAll('.deleteBtn').forEach(function(el){
    el.addEventListener('click', function(e) {
        if(confirm(this.dataset.confirmMessage)){
            deleteUser(this.dataset.url);
        }
    });
});

function deleteUser(deleteUrl){

    fetch(deleteUrl, init)
    .then(function(response){
        if (!response.ok) {
            throw Error(response.statusText);
        }

        return response;
    })
    .then((response) => response.json())
    .then(function(data) {
        if(data.success !== true){
            throw Error(JSON.stringify(data));
        }

        return data;
    })
    .then(function(data) {
        window.location.reload();
    })
    .catch(function(error) {
        console.log(error);
        alert('Delete failed');
    }); 
}