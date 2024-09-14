document.addEventListener("DOMContentLoaded", () => {
    // Handle Edit button click
    document.getElementById('myTable').addEventListener('click', (e) => {
        if (e.target.classList.contains('edit')) {
            const noteRow = e.target.closest('tr'); // Get the closest table row
            const title = noteRow.getElementsByTagName("td")[0].innerText;
            const description = noteRow.getElementsByTagName("td")[1].innerText;

            // Update modal input values
            document.getElementById('titleEdit').value = title;
            document.getElementById('descEdit').value = description;
            document.getElementById('snoEdit').value = e.target.id;

            // Show the modal
            const editModal = new bootstrap.Modal(document.getElementById('editModel'));
            editModal.show();
        }
    });
    
    // Handle Delete button click
    document.getElementById('myTable').addEventListener('click', (e) => {
        if (e.target.classList.contains('delete')) {
            const sno = e.target.getAttribute('data-sno'); // Get the sno from data attribute
            if (confirm("Are you sure you want to delete this note?")) {
                window.location = `./index.php?delete=${sno}`; // Redirect to delete the note
            }
        }
    });
});
