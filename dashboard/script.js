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
function openAdoptModal(id,name){
document.getElementById("adoptModal").style.display="flex";
document.getElementById("animal_id").value=id;
document.getElementById("animal_name").value=name;
}
function closeModal(){
document.getElementById("adoptModal").style.display="none";
}
// edit Modal
function openEditModal(id, name, species, color, age, gender, health_status) {
    // فتح النافذة المنبثقة
    document.getElementById('editmodal').style.display = 'block';
    
    // تعبئة الحقول بالبيانات الحالية
    document.getElementById('edit_animal_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_species').value = species;
    document.getElementById('edit_color').value = color;
    document.getElementById('edit_age').value = age;
    document.getElementById('edit_gender').value = gender;
    document.getElementById('edit_health_status').value = health_status;
}
