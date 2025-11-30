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
                } else if (data.events && data.events.length > 0) {
                    // 📌 Renderizar Timeline con Tarjetas Detalladas
                    let timelineHtml = '<div class="tracking-timeline">';
                    
                    data.events.forEach((event, index) => {
                        // Determinar si es el último evento (el más reciente) para destacarlo
                        let activeClass = index === 0 ? 'active' : '';
                        
                        // Extraer imagen si existe y hacerla clicable
                        let imageContent = '';
                        if (event.image_html) {
                            // Crear un elemento temporal para extraer el src
                            let tempDiv = document.createElement('div');
                            tempDiv.innerHTML = event.image_html;
                            let imgEl = tempDiv.querySelector('img');
                            
                            if (imgEl) {
                                let imgSrc = imgEl.src;
                                imageContent = `
                                    <div class="timeline-image mt-3">
                                        <a href="#" class="d-block" onclick="openImageModal('${imgSrc}'); return false;">
                                            <img src="${imgSrc}" class="img-fluid rounded border" style="max-height: 100px;" alt="Evidencia">
                                            <div class="small text-muted mt-1"><i class="fa fa-search-plus"></i> Ver imagen</div>
                                        </a>
                                    </div>`;
                            }
                        }

                        timelineHtml += `
                            <div class="timeline-item ${activeClass}">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <span class="timeline-date"><i class="fa fa-calendar"></i> ${event.date}</span>
                                        <span class="badge badge-primary">${event.status}</span>
                                    </div>
                                    <div class="timeline-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong><i class="fa fa-cube"></i> Contenido:</strong> ${event.content}</p>
                                                <p class="mb-1"><strong><i class="fa fa-barcode"></i> Tracking:</strong> ${event.tracking}</p>
                                                <p class="mb-1"><strong><i class="fa fa-weight-hanging"></i> Peso:</strong> ${event.weight}</p>
                                                <p class="mb-1"><strong><i class="fa fa-shipping-fast"></i> Envío:</strong> ${event.shipping_type}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong><i class="fa fa-id-card"></i> Volid:</strong> ${event.volid}</p>
                                                <p class="mb-1"><strong><i class="fa fa-box-open"></i> Casillero:</strong> ${event.locker}</p>
                                                <p class="mb-1"><strong><i class="fa fa-file-alt"></i> Manifiesto:</strong> ${event.manifest}</p>
                                            </div>
                                        </div>
                                        ${event.comment ? `<div class="alert alert-secondary mt-2 mb-0"><small><strong>Comentario:</strong> ${event.comment}</small></div>` : ''}
                                        ${imageContent}
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    
                    timelineHtml += '</div>';
                    trackingResult.innerHTML = timelineHtml;

                    // Mostrar el modal
                    let trackingModalEl = document.getElementById('trackingModal');
                    if (trackingModalEl) {
                        let trackingModal = new bootstrap.Modal(trackingModalEl);
                        trackingModal.show();
                    }

                } else if (data.html) {
                    // 📌 Fallback: Mostrar HTML crudo si no se pudo parsear
                    trackingResult.innerHTML = data.html;
                    
                    let trackingModalEl = document.getElementById('trackingModal');
                    if (trackingModalEl) {
                        let trackingModal = new bootstrap.Modal(trackingModalEl);
                        trackingModal.show();
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
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

// Función global para abrir el modal de imagen
function openImageModal(src) {
    let modalImg = document.getElementById('previewImage');
    if (modalImg) {
        modalImg.src = src;
        let imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
        imageModal.show();
    }
}