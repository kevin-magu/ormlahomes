document.addEventListener('DOMContentLoaded', () => {
    const deleteBtn = document.getElementById('deleteBtn');
  
    if (!deleteBtn) return;
  
    deleteBtn.addEventListener('click', function (e) {
      e.preventDefault();
  
      const propertyId = document.querySelector('input[name="property_id"]')?.value;
      if (!propertyId) {
        showResponseMessage('Property ID not found.');
        return;
      }
  
      if (!confirm('Are you sure you want to delete this property? This action cannot be undone.')) {
        return;
      }
  
      fetch('./deleteProperty', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ property_id: propertyId })
      })
        .then(res => res.json())
        .then(data => {
          showResponseMessage(data.message || 'Unknown response');
          if (data.success) {
            setTimeout(() => {
              window.location.href = '/orlmahomes/dashboard'; // change to your actual page
            }, 1500);
          }
        })
        .catch(error => {
          console.error('Delete failed:', error);
          showResponseMessage('An error occurred while deleting the property.');
        });
    });
  });
  