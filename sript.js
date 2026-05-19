const form = document.querySelector('form');
const nameInput = document.querySelector('#name');

form.addEventListener('submit', function(event) {
    event.preventDefault();

    if (nameInput.value.trim() === '') {
        alert('Please enter your name before submitting the form.');
        return;
    }

    document.getElementById("message").textContent = 'Thank you for contacting us, ' + nameInput.value + '! We will get back to you shortly.';

    alert('Hello, ' + nameInput.value + '! Your form has been submitted.');
});