// JavaScript functions for dashboard interactions
// This file contains modal management, search/filter functionality,
// sidebar controls, and form validation for the admin dashboard

// Delete Modal
(function(){
    var formToDelete = null;
    function openDeleteModal(form){
        formToDelete = form;
        document.getElementById('deleteModal').style.display = 'flex';
    }
    function closeDeleteModal(){
        formToDelete = null;
        document.getElementById('deleteModal').style.display = 'none';
    }
    document.addEventListener('DOMContentLoaded', function(){
        document.querySelectorAll('.remove-btn').forEach(function(btn){
            btn.addEventListener('click', function(e){
                var form = btn.closest('form');
                if(form) openDeleteModal(form);
            });
        });
        var cancel = document.getElementById('cancelDeleteBtn');
        if(cancel) cancel.addEventListener('click', closeDeleteModal);
        var confirm = document.getElementById('confirmDeleteBtn');
        if(confirm) confirm.addEventListener('click', function(){
            if(formToDelete) formToDelete.submit();
        });
        var modal = document.getElementById('deleteModal');
        if(modal) modal.addEventListener('click', function(e){ if(e.target === modal) closeDeleteModal(); });
    });
})();
// Adopt Modal
function openAdoptModal(id, name) {
    document.getElementById("adoptModal").style.display = "flex";
    document.getElementById("animal_id").value = id;
    if(document.getElementById("animal_name")) {
        document.getElementById("animal_name").value = name;
    }
}
function closeModal() {
    document.getElementById("adoptModal").style.display = "none";
}



// edit Modal
function openEditModal(id, name, species, color, age, gender, health_status) {
    // open modal  
    document.getElementById('editmodal').style.display = 'block';
    // old values    
    document.getElementById('edit_animal_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_species').value = species;
    document.getElementById('edit_color').value = color;
    document.getElementById('edit_age').value = age;
    document.getElementById('edit_gender').value = gender;
    document.getElementById('edit_health_status').value = health_status;
}
// add new admin modal
    function openAddAdminModal() {
    document.getElementById('addAdminModal').style.display = 'block';
}

// Sidebar functions
function expandSidebar() {
    document.getElementById("mySidebar").classList.add("expanded");
    document.body.classList.add("sidebar-open");
}
function collapseSidebar() {
    document.getElementById("mySidebar").classList.remove("expanded");
    document.body.classList.remove("sidebar-open");
}
function toggleSidebar() {
    collapseSidebar();
}

// Search and filter functions
function instantsearch() {
    let input = document.getElementById('searchInput');
    let filter = input.value.toLowerCase();
    let table = document.getElementById('animalsTable');
    if (!table) return;
    let tr = table.getElementsByTagName('tr');
    for (let i = 1; i < tr.length; i++) {
        let nameCell = tr[i].getElementsByTagName('td')[1];
        let infoCell = tr[i].getElementsByTagName('td')[2];
        if (nameCell || infoCell) {
            let nameText = nameCell.textContent || nameCell.innerText;
            let infoText = infoCell.textContent || infoCell.innerText;
            if (nameText.toLowerCase().indexOf(filter) > -1 || 
                infoText.toLowerCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}

function filterbyspecies() {
    let species = document.getElementById('speciesfilter');
    let selectedSpecies = species.value;
    let table = document.getElementById('animalsTable');
    if (!table) return;
    let tr = table.getElementsByTagName('tr');
    for (let i = 1; i < tr.length; i++) {
        let speciesCell = tr[i].getElementsByTagName('td')[2];
        if (speciesCell) {
            let speciesText = speciesCell.textContent || speciesCell.innerText;
            if (selectedSpecies === "" || speciesText.toLowerCase().indexOf(selectedSpecies.toLowerCase()) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}

// Phone validation
document.addEventListener('DOMContentLoaded', function() {
    const phone = document.getElementById('adopter_phone');
    const error = document.getElementById('error');
    const submit = document.getElementById("submit");
    if (phone) {
        phone.addEventListener('input', () => {
            const value = phone.value;
            const onlyNumbersRegex = /^(0|\+213)[567]\d{8}$/;
            if (value === ""){
                error.style.display = "none";
                phone.style.border = "1.5px solid #e2e8f0";
                submit.disabled = true;
            }
            else if (!onlyNumbersRegex.test(value)) {
                error.style.display = "block";
                phone.style.border = "1.5px solid red";
                submit.disabled = true;
            }
            else {
                error.style.display = "none";
                phone.style.border = "1.5px solid #16a34a";
                submit.disabled = false;
            }   
        });
    }
});




