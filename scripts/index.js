document.getElementById('feedbackForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('feedback-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        document.getElementById('responseMessage').innerText = data;
        this.reset(); // optional: clears the form
    })
    .catch(err => {
        document.getElementById('responseMessage').innerText = "Something went wrong.";
        console.error(err);
    });
});