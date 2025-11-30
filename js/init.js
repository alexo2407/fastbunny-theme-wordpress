document.addEventListener("DOMContentLoaded", function () {
    let trackingInput = document.getElementById('trackingNumber');

    if (trackingInput) {
        trackingInput.addEventListener("click", function () {
            this.value = ""; // 📌 Borra el input cuando el usuario haga clic
        });
    }
});



document.addEventListener("DOMContentLoaded", function () {
    // 1. Código para el formulario de tracking
    let trackingForm = document.getElementById('trackingForm');
    if (trackingForm) {
        trackingForm.addEventListener('submit', function (event) {
            event.preventDefault();

            let trackingNumber = document.getElementById('trackingNumber').value.trim();
            if (trackingNumber === '') {
                alert("Por favor ingrese un número de guía válido.");
                return;
            }

            let formData = new FormData();
            formData.append('action', 'get_tracking_info');
            formData.append('trackingNumber', trackingNumber);

            let trackingResult = document.getElementById('trackingResult');
            trackingResult.innerHTML = "<p>Cargando datos de rastreo...</p>";

            fetch(tracking_ajax.ajaxurl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    trackingResult.innerHTML = `<p style="color: red;">${data.error}</p>`;
                } else {
                    console.log(data.html); // Para depuración
                    trackingResult.innerHTML = data.html;
                    
                    // Mostrar el modal de Bootstrap, si existe el elemento
                    let trackingModalEl = document.getElementById('trackingModal');
                    if (trackingModalEl) {
                        let trackingModal = new bootstrap.Modal(trackingModalEl);
                        trackingModal.show();
                    }
                }
            })
            .catch(error => {
                trackingResult.innerHTML = "<p>Error al obtener la información.</p>";
            });
        });
    }

    // 2. Código para el smooth scroll en los enlaces del navbar
    document.querySelectorAll('.navbar .nav-link').forEach(enlace => {
        enlace.addEventListener('click', function (e) {
            e.preventDefault();
            let targetSelector = this.getAttribute('href');
            let targetElement = document.querySelector(targetSelector);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // 3. Código para cambiar la clase del header al hacer scroll
    window.addEventListener('scroll', function () {
        let scroll = window.scrollY;
        const headerScroll = document.querySelector('#navegacion-principal');
        if (headerScroll) {
            if (scroll > 100) {
                headerScroll.classList.add('bg-success');
            } else {
                headerScroll.classList.remove('bg-success');
            }
        }
    });
});

// 4. Código para la cuenta regresiva con jQuery y plugin countdown
jQuery(document).ready(function($) {
    if ($('#fecha').length > 0) {
        $('#fecha').countdown('2018/08/23', function(event) {
            $(this).html(event.strftime(
                '<p><span> %D </span> días <span> %H </span> horas <span> %M </span> minutos <span> %S </span> segundos</p>'
            ));
        });
    }
});


document.querySelectorAll('.navbar-toggler').forEach(btn => {
    btn.addEventListener('click', function() {
      this.blur();
    });
  });
  