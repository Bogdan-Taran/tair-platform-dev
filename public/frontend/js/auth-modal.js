function openRegisterModal() {
    document.getElementById("loginModal").style.display = "none";
    document.getElementById('registerModal').style.display = 'flex';
}

function openLoginModal() {
    document.getElementById("registerModal").style.display = "none";
    document.getElementById('loginModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('registerModal').style.display = 'none';
    document.getElementById('loginModal').style.display = 'none';
    document.getElementById('registerForm').reset();
    document.getElementById('loginForm').reset();
    clearErrors();
}
function clearErrors() {
    document.getElementById('modalErrors').style.display = 'none';
    document.querySelector('.field-error').forEach(el => el.innerText = '');
}

const form = document.getElementById('loginForm');

form.addEventListener('submit', function(e) {
    e.preventDefault(); // Останавливаем стандартную отправку

    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        }
    })
        .then(response => response.json())
        .then(data => { /* обработка ответа */ });
});


// document.getElementById('registerForm').addEventListener('submit', function(e) {
//     e.preventDefault();
//     clearErrors();
//
//     const form = e.target;
//     const formData = new FormData(form);
//
//     fetch(form.action, {
//         method: 'POST',
//         body: formData,
//         headers: {
//             'X-Requested-With': 'XMLHttpRequest', // Говорим Laravel, что это AJAX-запрос
//             'Accept': 'application/json'          // Просим вернуть JSON в случае ошибки
//         }
//     })
//         .then(response => {
//             if (response.ok) {
//                 window.location.reload();
//             } else {
//                 return response.json();
//             }
//         })
//         .then(data => {
//             if (data && data.errors) {
//                 Object.keys(data.errors).forEach(key => {
//                     const errorEl = document.getElementById(`error_${key}`);
//                     if (errorEl) {
//                         errorEl.innerText = data.errors[key][0];
//                     }
//                 });
//             } else if (data && data.message) {
//                 const summary = document.getElementById('modalErrors');
//                 summary.innerText = data.message;
//                 summary.style.display = 'block';
//             }
//         })
//         .catch(error => console.error('Ошибка:', error));
// });


