document.getElementById('userForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    const response = await fetch('./takedata', {
      method: 'POST',
      body: formData
    });

    const result = await response.text();
    document.getElementById('response').textContent = result;
  });